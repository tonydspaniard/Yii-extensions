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
	const API_URL = 'http://maps.google.com/maps/geo?';
	const JS_URL = 'http://maps.google.com/maps/api/js?sensor=false';
	/**
	 *
	 * getCoding parameter info template.
	 * @var string
	 */
	protected $geoCodingInfotemplate = '{api}&output={format}&key={key}&q={address}';
	/**
	 * API key array
	 *
	 * @var EGMapApiKeyList $api_keys
	 */
	protected $api_keys = null;
	/**
	 *
	 * Holds default domain
	 * domains specified active API key
	 * @var string
	 */
	private $_default_domain = 'localhost';

	/**
	 *
	 * Constructor
	 * If $key parameter is set, it will try to add it
	 * to the collection. Array should be in the format of
	 * <pre>
	 *     $gmapclient = new EGMapClient( array('domain'=>'googlekeyhere') );
	 * </pre>
	 * @param array $key
	 */
	public function __construct($key = array())
	{
		// $key = array( 'domain' => 'googlekeyhere' );
		$this->api_keys = new EGMapApiKeyList();

		if (!empty($key) && !is_scalar($key))
		{
			list( $domain, $key ) = each($key);
			$this->setAPIKey($domain, $key);
		}
	}

	/**
	 * Sets the Google Maps API key
	 * @param string $key
	 */
	public function setAPIKey($domain, $key, $setAsDefault = false)
	{
		if ($this->api_keys === null)
			$this->api_keys = new EGMapApiKeyList();


		$this->api_keys->addAPIKey($domain, $key);

		if (true === $setAsDefault)
			$this->setDomain($domain);
	}

	/**
	 *
	 * Sets default API key
	 * @param string $domain
	 */
	public function setDomain($domain)
	{

		$this->_default_domain = $domain;
	}

	/**
	 * Gets the Google Maps API key
	 * @return string $key
	 */
	public function getAPIKey($domain = null)
	{
		$domain = (null === $domain ? $this->_default_domain : $domain);
		return $this->api_keys->getAPIKeyByDomain($domain);
	}

	/**
	 * Guesses and sets default API Key
	 *
	 */
	protected function guessAndSetAPIKey($key)
	{
		$this->setAPIKey($this->guessDomain(), $key, true);
	}

	/**
	 * Guesses the current domain
	 * @return string $domain
	 * @author Antonio Ramirez Cobos
	 *
	 */
	public static function guessDomain()
	{
		if (isset($_SERVER['SERVER_NAME']))
			return $_SERVER['SERVER_NAME'];
		else if (isset($_SERVER['HTTP_HOST']))
			return $_SERVER['HTTP_HOST'];

		// nothing found, return default
		return $this->_default_domain;
	}

	/**
	 * Returns the collection of API keys
	 * @return CMap
	 */
	public function getAPIKeys()
	{
		return $this->api_keys;
	}

	/**
	 *
	 * Sets the API keys collection
	 * @param CMap $api_keys
	 * @return false if $api_keys is not of class CMap
	 * @author Antonio Ramirez Cobos
	 */
	public function setAPIKeys($api_keys)
	{
		if (!$api_keys instanceof CMap)
			return false;

		$this->api_keys = $api_keys;
	}

	/**
	 *
	 * Changes default geocoding template
	 * Just in case google changes its API
	 * current is of default: {api}&output={format}&key={key}&q={address}
	 * @param string $template
	 * @author Antonio Ramirez Cobos
	 */
	public function setGeoCodingTemplate($template)
	{
		$this->geoCodingInfotemplate = $template;
	}

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
	 * @param string $format 'csv' or 'xml'
	 * @return string
	 * @author fabriceb
	 * @since 2009-06-17
	 * @since 2010-12-22 cUrl and Yii adaptation Antonio Ramirez
	 *
	 */
	public function getGeocodingInfo($address, $format = 'csv')
	{

		$apiUrl = str_replace(
			array('{api}','{format}','{key}','{address}'),
			array(self::API_URL, $format, $this->getAPIKey(), urlencode($address)),
			$this->geoCodingInfotemplate);

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
	 * Reverse geocoding info
	 *
	 * @return string
	 * @author Vincent Guillon <vincentg@theodo.fr>
	 * @since 2010-03-04
	 * @since 2010-12-22 modified by Antonio Ramirez (CUrl call)
	 */
	public function getReverseGeocodingInfo($lat, $lng)
	{
		$apiURL = self::API_URL . 'll=' . $lat . ',' . $lng;
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
			$raw_data = file_get_contents($apiURL);

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

?>