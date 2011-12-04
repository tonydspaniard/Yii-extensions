<?php
/**
 * 
 * EGMapApiKeyList Class 
 * 
 * Collection of Google API Keys
 *
 * @author Antonio Ramirez Cobos
 * @link www.ramirezcobos.com
 *
 * 
 * @copyright 
 * 
 * Copyright (c) 2010 Antonio Ramirez Cobos
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
class EGMapApiKeyList {
	
	/**
	 * 
	 * default API key (localhost)
	 * @var string API key
	 */
	private $_default = 'ABQIAAAAiNlS-KWUYtfPmXrWytgMmxT2yXp_ZAY8_ufC3CFXhHIE1NvwkxQlQzG8ekt6PEzv6dL5UtfryHSg8g';
	/**
	 * 
	 * Holds the collection of keys
	 * @var CMap
	 */
	private $_keys = null;
	/**
	 * 
	 * Class constructor
	 * @param string $domain
	 * @param string $key
	 */
	public function __construct( $domain=null, $key=null )
	{
		// set default API key
		$this->addAPIKey( 'localhost', $this->_default );
		
		if( $domain != null && $key != null )
			$this->add( $domain, $key );
	}
	/**
	 * 
	 * Adds a Google API key to collection
	 * @param string $domain
	 * @param string $key
	 */
	public function addAPIKey( $domain , $key ){
		if( null === $this->_keys ) 
			$this->_keys = new CMap();
		
		$this->_keys->add( $domain, $key );
	}
	/**
	 * 
	 * Returns Google API key if found in collection
	 * @param string $domain
	 * @return string Google API key
	 */
	public function getAPIKeyByDomain( $domain  )
	{
	   if( !$this->_keys->contains( $domain ) )
	       return false;
		return $this->_keys->itemAt( $domain );
	}
	/**
	 * Returns and google api key by domain name discovery
	 * @return Google API key
	 */
	public static function guessAPIKey( )
	{
		if (isset($_SERVER['SERVER_NAME']))
	    {
	      return $this->getAPIKeyByDomain( $_SERVER['SERVER_NAME'] );
	    }
	    else if (isset($_SERVER['HTTP_HOST']))
	    {
	      return $this->getAPIKeyByDomain( $_SERVER['HTTP_HOST'] );
	    }
	    return $this->getAPIKeyByDomain('localhost');
  	}
}