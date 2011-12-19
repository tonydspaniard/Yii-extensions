<?php

/**
 *
 * EGMapClient Class
 * A class to communicate with Google Maps
 * Inspired on the work of Fabrice Bernhard
 *
 * @author Antonio Ramirez Cobos
 * @link http://www.ramirezcobos.com
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
class EGMapClient
{
	/**
	 * The URL for the RESTful geocode API.
	 * @since 2011-03-23 Matt Cheale Updated URL from v2 to v3 of the API.
	 * @since 2011-04-21 Matt Cheale Removed the format option so it can be customised in the geocoding methods.
	 * @since 2011-12-19 Antonio Ramirez renamed to make use of more APIs
	*/
	const API_GEOCODE_URL = 'http://maps.googleapis.com/maps/api/geocode/';
	/**
	 * The URL for the RESTful elevation API
	 */
	const API_ELEVATION_URL = 'http://maps.googleapis.com/maps/api/elevation/';

	/**
	 *
	 * Constructor
	 * If $key parameter is set, it will try to add it
	 * to the collection. Array should be in the format of
	 * <pre>
	 *     $gmapclient = new EGMapClient( array('domain'=>'googlekeyhere') );
	 * </pre>
	 * @param array $key
	 * @since 2011-04-21 Matt Cheale $key parameter deprecated
	 */
	public function __construct($key = array()){}

	/**
	 * Sets the Google Maps API key
	 * @param string $key
	 * @deprecated
	 * @since 2011-04-21 Matt Cheale Deprecated as API Keys are no longer required.
	 */
	public function setAPIKey($domain, $key, $setAsDefault = false){}

	/**
	 *
	 * Sets default API key
	 * @param string $domain
	 * @deprecated
	 * @since 2011-04-21 Matt Cheale Deprecated as API Keys are no longer required.
	 */
	public function setDomain($domain){}

	/**
	 * Gets the Google Maps API key
	 * @return string $key
	 * @deprecated
	 * @since 2011-04-21 Matt Cheale Deprecated as API Keys are no longer required.
	 */
	public function getAPIKey($domain = null){}

	/**
	 * Guesses and sets default API Key
	 * @deprecated
	 * @since 2011-04-21 Matt Cheale Deprecated as API Keys are no longer required.
	 */
	protected function guessAndSetAPIKey($key){}

	/**
	 * Guesses the current domain
	 * @return string $domain
	 * @author Antonio Ramirez Cobos
	 * @deprecated
	 * @since 2011-04-21 Matt Cheale Deprecated as API Keys are no longer required.
	 */
	public static function guessDomain(){}

	/**
	 * Returns the collection of API keys
	 * @return CMap
	 * @deprecated
	 * @since 2011-04-21 Matt Cheale Deprecated as API Keys are no longer required.
	 */
	public function getAPIKeys()
	{
		return new CMap();
	}

	/**
	 *
	 * Sets the API keys collection
	 * @param CMap $api_keys
	 * @return false if $api_keys is not of class CMap
	 * @author Antonio Ramirez Cobos
	 * @deprecated
	 * @since 2011-04-21 Matt Cheale Deprecated as API Keys are no longer required.
	 */
	public function setAPIKeys($api_keys){}

	/**
	 *
	 * Changes default geocoding template
	 * Just in case google changes its API
	 * current is of default: {api}&output={format}&key={key}&q={address}
	 * @param string $template
	 * @author Antonio Ramirez Cobos
	 * @deprecated
	 * @since 2011-04-21 Matt Cheale Deprecated as latest code is not making any use of this.
	 */
	public function setGeoCodingTemplate($template){}

	/**
	 *
	 * Connection to Google Maps' API web service
	 *
	 * Modified to include a template for api
	 * just in case the url changes in future releases
	 * Includes template parsing and CURL calls
	 * @author Antonio Ramirez Cobos
	 * @since 2010-12-21
	 *
	 * @param string $address
	 * @param string $format 'csv' or 'xml' or 'json'
	 * @return string
	 * @author fabriceb
	 * @since 2009-06-17
	 * @since 2010-12-22 cUrl and Yii adaptation Antonio Ramirez
	 * @since 2011-04-21 Matt Cheale Updated to API V3 and moved HTTP call to another function.
	 *
	 */
	public function getGeocodingInfo($address, $format = 'json')
	{
		$apiURL = self::API_GEOCODE_URL . $format . '?address=' . urlencode($address) . '&sensor=false';
		return $this->callApi($apiURL);
	}

	/**
	 * Reverse geocoding info
	 *
	 * @return string
	 * @author Vincent Guillon <vincentg@theodo.fr>
	 * @since 2010-03-04
	 * @since 2010-12-22 modified by Antonio Ramirez (CUrl call)
	 * @since 2011-03-23 Matt Cheale Updated the query string to use v3 API variables.
	 * @since 2011-04-21 Matt Cheale Added format option and moved HTTP call to another function.
	 * @since 2011-12-19 Antonio Ramirez modified API call
	 */
	public function getReverseGeocodingInfo($lat, $lng, $format = 'json')
	{
		$apiURL = self::API_GEOCODE_URL . $format . '?latlng=' . $lat . ',' . $lng . '&sensor=false';
		return $this->callApi($apiURL);
	}
	/**
	 * Elevation info request
	 * 
	 * @param string $locations the coordinates array to get elevation info from
	 * @param string $format 'xml' or 'json'
	 * @return string
	 * @author Antonio Ramirez
	 */
	public function getElevationInfo($locations, $format = 'json')
	{
		$apiURL = self::API_ELEVATION_URL . $format . '?locations=' . $locations  . '&sensor=false';
		return $this->callApi($apiURL);
	}
	/**
	 * Takes the $apiURL and performs that HTTP request to Google, returning the
	 * raw data.
	 *
	 * @param string $apiURL
	 * @return string
	 * @author Matt Cheale
	 * @since 2011-04-21
	 * @since 2011-12-17 Modified to fix open_basedir restrictions
	 */
	private function callApi($apiUrl)
	{
		if (function_exists('curl_version'))
		{
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $apiUrl);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
			$raw_data = $this->_curl_exec_follow($ch);
			curl_close($ch);
		}
		else // no CUrl, try differently
			$raw_data = file_get_contents($apiUrl);

		return $raw_data;
	}

	/**
	 * This function handles redirections with CURL if safe_mode or open_basedir 
	 * is enabled. 
	 * @param resource $h the curl handle
	 * @param integer $maxredirections 
	 */
	private function _curl_exec_follow(&$ch, $maxredirections = 5)
	{
		if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off')
		{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $maxredirections > 0);
			curl_setopt($ch, CURLOPT_MAXREDIRS, $maxredirections);
		} else
		{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
			if ($maxredirections > 0)
			{
				$new_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

				$rch = curl_copy_handle($ch);
				curl_setopt($rch, CURLOPT_HEADER, true);
				curl_setopt($rch, CURLOPT_NOBODY, true);
				curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
				curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
				do
				{
					curl_setopt($rch, CURLOPT_URL, $new_url);
					$header = curl_exec($rch);

					if (curl_errno($rch))
						$code = 0;
					else
					{
						$code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
						if ($code == 301 || $code == 302)
						{
							preg_match('/Location:(.*?)\n/', $header, $matches);
							$new_url = trim(array_pop($matches));
						}
						else
							$code = 0;
					}
				} while ($code && --$maxredirections);

				curl_close($rch);

				if (!$maxredirections)
				{
					if ($maxredirections === null)
						throw new CHttpException(301, 'Too many redirects. When following redirects, libcurl hit the maximum amount.');
					else
						$maxredirections = 0;
					return false;
				}
				curl_setopt($ch, CURLOPT_URL, $new_url);
			}
		}
		return curl_exec($ch);
	}

}