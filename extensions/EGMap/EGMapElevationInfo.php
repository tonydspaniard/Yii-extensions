<?php

/**
 *
 * EGMapElevationInfo
 *
 *
 * A class to elevation info service
 * @author Antonio Ramirez
 *
 * @copyright 
 * 
 * Copyright (c) 2011 Antonio Ramirez
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
class EGMapElevationInfo
{
	/**
	 *
	 * @var type 
	 */
	protected $coords = array();
	
	protected $locations = array();

	/**
	 * Constructs a EGMapElevationInfo object from a given coords String|EGMapCoord|Array
	 *
	 * @param mixed $coords the coordinates to get elevation info
	 * @author Antonio Ramirez
	 */
	public function __construct($coords = null)
	{
		$this->setCoords($coords);
	}

	/**
	 * Returns the coordinate array
	 * @return array 
	 */
	public function getCoords()
	{

		return $this->coords;
	}
	/**
	 * Sets the coordinates array
	 * @param array $coords 
	 * @author Antonio Ramirez
	 */
	public function setCoords($coords)
	{
		if(!is_array($coords))
			$coords = array($coords);
		
		foreach($coords as $coord)
			$this->addCoord($coord);
	}
	/**
	 * Adds a coordinate to the array, can be a string or a EGMapCoord object
	 * @param mixed $coord 
	 * @author Antonio Ramirez
	 */
	public function addCoord($coord)
	{
		if($coord instanceof EGMapCoord)
			$coord = $coord->__toString ();
		
		$this->coords[] = str_replace(' ','',$coord);
		
	}
	/**
	 * 
	 * @return array of EGMapElevationInfoResult objects if api call was 
	 *	successfull
	 */
	public function getLocations()
	{
		return $this->locations;
	}
	/**
	 * Request the elevation info using Google Maps' API JSON
	 *
	 * @param  EGMapClient $gmap_client
	 * @param $forceEncode whether to encode the lat/lng points
	 * @return array of EGMapElevationInfoResult if successful
	 * @author Antonio Ramirez
	 */
	public function elevationRequestJson($gmap_client, $forceEncode = true)
	{	
		if(empty($this->coords)) return false;
		
		$this->locations = array();
		
		$coords = array();
		
		/* At least 3 coords otherwise is not point */
		if($forceEncode && count($this->coords)>2)
			$coords = $this->prepareEncodedCoords($this->coords);
		else
			$coords = implode('|', $this->coords);

		$raw_data = $gmap_client->getElevationInfo($coords);

		$data = CJSON::decode($raw_data);

		if ('OK' != $data['status'])
		{
			return false;
		}

		foreach($data['results'] as $result)
		{
			$info = new EGMapElevationInfoResult();
			$info->lat = $result['location']['lat'];
			$info->lng = $result['location']['lng'];
			$info->elevation = $result['elevation'];
			$info->resolution = $result['resolution'];
			$this->locations[] = $info;
		}
		return $this->locations;
	}
	/**
	 * Request the elevation info using Google Maps' API XML
	 *
	 * @param  EGMapClient $gmap_client
	 * @param $forceEncode whether to encode the lat/lng points
	 * @return array of EGMapElevationInfoResult if successful
	 * @author Antonio Ramirez
	 */
	public function elevationRequestXml($gmap_client, $forceEncode = true)
	{
		if(empty($this->coords)) return false;
		
		$this->locations = array();
		
		$coords = array();
		
		/* At least 3 coords otherwise is not point */
		if($forceEncode && count($this->coords)>2)
			$coords = $this->prepareEncodedCoords($this->coords);
		else
			$coords = implode('|', $this->coords);
		
		$raw_data = $gmap_client->getElevationInfo($coords, 'xml');
		
		$xml = simplexml_load_string($raw_data);

		if ('OK' != $xml->status)
		{
			return false;
		}

		foreach ($xml->result as $component)
		{
			$info = new EGMapElevationInfoResult();
			$info->lat = $component->location->lat;
			$info->lng = $component->location->lng;
			$info->elevation = $component->elevation;
			$info->resolution = $component->resolution;
			$this->locations[] = $info;
		}
		return $this->locations;
	}
	/**
	 * Encodes coordinates based on Google polyline algorithm
	 * @see http://code.google.com/intl/es/apis/maps/documentation/utilities/polylinealgorithm.html
	 * @param array $coords
	 * @return string coordinates 
	 */
	protected function prepareEncodedCoords($coords)
	{
		$enc = new EGMapPolylineEncoder();
		$ecoords = array();
		foreach($coords as $coord)
		{
			$ecoords[] = explode(',', $coord);
		}
		return 'enc:' . $enc->encode($ecoords);
	}
}

/**
 * EGMapElevationInfoResult class
 * 
 * General object to fill with results of different adapters
 */
class EGMapElevationInfoResult {
	/**
	 *
	 * @var array $props that hold attributes
	 */
	protected $props;
	/**
	 * Magic method __set
	 * @param string $name
	 * @param string $value 
	 */
	public function __set($name, $value)
	{
		$this->props[$name] = $value;
	}
	/**
	 * Magic method __get
	 * @param string $name the attribute name
	 * @return attribute value | null if not exists
	 */
	public function __get($name)
	{
		return isset($this->props[$name])?$this->props[$name]:null;
	}

}
