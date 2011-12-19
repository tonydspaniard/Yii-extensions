<?php

/**
 *
 * EGMapGeocodedAddress
 *
 * Modified by Antonio Ramirez Cobos
 * @link http://www.ramirezcobos.com
 *
 * A class to geocode addresses
 * @author Fabrice Bernhard
 *
 * @copyright
 * info as this library is a modified version of Fabrice Bernhard
 *
 * Copyright (c) 2008 Fabrice Bernhard
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial
 * portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT
 * LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
 * NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */
class EGMapGeocodedAddress
{

	protected $raw_address = null;
	protected $lat = null;
	protected $lng = null;
	protected $accuracy = null;
	protected $geocoded_city = null;
	protected $geocoded_country_code = null;
	protected $geocoded_country = null;
	protected $geocoded_address = null;
	protected $geocoded_street = null;
	protected $geocoded_postal_code = null;

	/**
	 * Constructs a gMapGeocodedAddress object from a given $raw_address String
	 *
	 * @param string $raw_address
	 * @author Fabrice Bernhard
	 */
	public function __construct($raw_address)
	{
		$this->raw_address = $raw_address;
	}

	/**
	 *
	 * @return string $raw_address
	 * @author fabriceb
	 * @since 2009-06-17
	 */
	public function getRawAddress()
	{

		return $this->raw_address;
	}

	/**
	 * Geocodes the address using the Google Maps CSV webservice
	 *
	 * @param  EGMapClient $gmap_client
	 * @return integer $accuracy
	 * @author Fabrice Bernhard
	 * @since 2011-04-21 Matt Cheale Updated to parse API V3 JSON response.
	 */
	public function geocode($gmap_client)
	{	
		$raw_data = $gmap_client->getGeocodingInfo($this->getRawAddress());
		$data = CJSON::decode($raw_data);

		if ('OK' != $data['status'])
		{
			return false;
		}

		$location = $data['results'][0]['geometry'];

		$this->lat = $location['location']['lat'];
		$this->lng = $location['location']['lng'];
		$this->accuracy = $location['location_type'];

		return $this->accuracy;
	}

	/**
	 * Reverse geocoding
	 *
	 * @return integer
	 * @author Vincent Guillon <vincentg@theodo.fr>
	 * @since 2010-03-04
	 * @since 2011-03-23 Matt Cheale Updated mapping from v2 to v3 of the API result format.
	 */
	public function reverseGeocode($gmap_client)
	{
		$raw_data = $gmap_client->getReverseGeocodingInfo($this->getLat(), $this->getLng());
		$geocoded_array = CJSON::decode($raw_data, true);

		if ($geocoded_array['status'] != 'OK')
		{
			return false;
		}

		$result = $geocoded_array['results'][0];
		$address_components = $result['address_components'];
		$this->raw_address = $result['formatted_address'];
		$this->geocoded_address = $result['formatted_address'];
		$this->accuracy = $result['types'][0];

		$map = array(
			'street_address' => 'geocoded_street',
			'route' => 'geocoded_street',
			'country' => 'geocoded_country',
			'locality' => 'geocoded_city',
			'postal_code' => 'geocoded_postal_code',
		);

		foreach ($address_components as $component)
		{
			foreach ($component['types'] as $type)
			{
				switch ($type)
				{
					case 'street_address':
					case 'route':
						$this->geocoded_street = $component['long_name'];
						break;

					case 'country':
						$this->geocoded_country = $component['long_name'];
						$this->geocoded_country_code = $component['short_name'];
						break;

					case 'locality':
						$this->geocoded_city = $component['long_name'];
						break;

					case 'postal_code':
						$this->geocoded_postal_code = $component['long_name'];
						break;

					default:
					// Do nothing
				}
			}
		}

		return $this->accuracy;
	}

	/**
	 * Geocodes the address using the Google Maps XML webservice, which has more information.
	 * Unknown values will be set to NULL.
	 * @todo Change to SimpleXML
	 * @param EGMapClient $gmap_client
	 * @return integer $accuracy
	 * @author Fabrice Bernhard
	 * @since 2010-12-22 modified by utf8_encode removed Antonio Ramirez
	 * @since 2011-04-21 Matt Cheale Updated to parse API V3 JSON response.
	 */
	public function geocodeXml($gmap_client)
	{
		$raw_data = $gmap_client->getGeocodingInfo($this->getRawAddress(), 'xml');
		$xml = simplexml_load_string($raw_data);

		if ('OK' != $xml->status)
		{
			return false;
		}

		foreach ($xml->result->address_component as $component)
		{
			$longName = (string) $component->long_name;
			$shortName = (string) $component->short_name;
			foreach ($component->type as $type)
			{
				switch ($type)
				{
					case 'street_address':
					case 'route':
						$this->geocoded_street = $longName;
						break;

					case 'country':
						$this->geocoded_country = $longName;
						$this->geocoded_country_code = $shortName;
						break;

					case 'locality':
						$this->geocoded_city = $shortName;
						break;

					case 'postal_code':
						$this->geocoded_postal_code = $longName;
						break;

					default:
					// Do nothing
				}
			}
		}

		$this->lat = (double) $xml->result->geometry->location->lat;
		$this->lng = (double) $xml->result->geometry->location->lng;

		$this->accuracy = (string) $xml->result->geometry->location_type;

		return $this->accuracy;
	}

	/**
	 * Returns the latitude
	 * @return float $latitude
	 */
	public function getLat()
	{

		return $this->lat;
	}

	/**
	 * Returns the longitude
	 * @return float $longitude
	 */
	public function getLng()
	{

		return $this->lng;
	}

	/**
	 * Returns the Geocoding accuracy
	 * @return integer $accuracy
	 */
	public function getAccuracy()
	{

		return $this->accuracy;
	}

	/**
	 * Returns the address normalized by the Google Maps web service
	 * @return string $geocoded_address
	 */
	public function getGeocodedAddress()
	{

		return $this->geocoded_address;
	}

	/**
	 * Returns the city normalized by the Google Maps web service
	 * @return string $geocoded_city
	 */
	public function getGeocodedCity()
	{

		return $this->geocoded_city;
	}

	/**
	 * Returns the country code normalized by the Google Maps web service
	 * @return string $geocoded_country_code
	 */
	public function getGeocodedCountryCode()
	{

		return $this->geocoded_country_code;
	}

	/**
	 * Returns the country normalized by the Google Maps web service
	 * @return string $geocoded_country
	 */
	public function getGeocodedCountry()
	{

		return $this->geocoded_country;
	}

	/**
	 * Returns the postal code normalized by the Google Maps web service
	 * @return string $geocoded_postal_code
	 */
	public function getGeocodedPostalCode()
	{

		return $this->geocoded_postal_code;
	}

	/**
	 * Returns the street name normalized by the Google Maps web service
	 * @return string $geocoded_country_code
	 */
	public function getGeocodedStreet()
	{

		return $this->geocoded_street;
	}

	/**
	 * @param string $raw raw address to set
	 */
	public function setRawAddress($raw)
	{
		$this->raw_address = $raw;
	}

	/**
	 * @param float $lat latitude to set
	 */
	public function setLat($lat)
	{
		$this->lat = $lat;
	}

	/**
	 * @param float $lat longitude to set
	 */
	public function setLng($lng)
	{
		$this->lng = $lng;
	}

	/**
	 * @param float $lat accuracy to set
	 */
	public function setAccuracy($accuracy)
	{
		$this->accuracy = $accuracy;
	}

	/**
	 * @param string $val geocoded city
	 */
	public function setGeocodedCity($val)
	{
		$this->geocoded_city = $val;
	}

	/**
	 * @param string $val geocoded country code
	 */
	public function setGeocodedCountryCode($val)
	{
		$this->geocoded_country_code = $val;
	}

	/**
	 * @param string $val geocoded country code
	 */
	public function setGeocodedCountry($val)
	{
		$this->geocoded_country = $val;
	}

	/**
	 * @param string $val geocoded address
	 */
	public function setGeocodedAddress($val)
	{
		$this->geocoded_address = $val;
	}

	/**
	 * @param string $val geocoded street
	 */
	public function setGeocodedStreet($val)
	{
		$this->geocoded_street = $val;
	}

	/**
	 * @param string $val geocoded postal_code
	 */
	public function setGeocodedPostalCode($val)
	{
		$this->geocoded_postal_code = $val;
	}

}
