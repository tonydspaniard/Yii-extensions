<?php

/**
 * File: EWebBrowser.php
 * Author: Antonio Ramirez 
 * 
 * Based on Browser class
 * Author: Chris Schuld (http://chrisschuld.com/)
 * Last Modified: August 20th, 2010
 * @version 1.9
 * @package PegasusPHP
 *
 * Copyright (C) 2008-2010 Chris Schuld  (chris@chrisschuld.com)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details at:
 * http://www.gnu.org/copyleft/gpl.html
 *
 *
 */
class EWebBrowser extends CComponent {

	const BROWSER_UNKNOWN = 'unknown';
	const VERSION_UNKNOWN = 'unknown';

	const BROWSER_OPERA = 'Opera';       // http://www.opera.com/
	const BROWSER_OPERA_MINI = 'Opera Mini';    // http://www.opera.com/mini/
	const BROWSER_WEBTV = 'WebTV';       // http://www.webtv.net/pc/
	const BROWSER_IE = 'Internet Explorer';     // http://www.microsoft.com/ie/
	const BROWSER_POCKET_IE = 'Pocket Internet Explorer';     // http://en.wikipedia.org/wiki/Internet_Explorer_Mobile
	const BROWSER_KONQUEROR = 'Konqueror';      // http://www.konqueror.org/
	const BROWSER_ICAB = 'iCab';	 // http://www.icab.de/
	const BROWSER_OMNIWEB = 'OmniWeb';   // http://www.omnigroup.com/applications/omniweb/
	const BROWSER_FIREBIRD = 'Firebird';	// http://www.ibphoenix.com/
	const BROWSER_FIREFOX = 'Firefox';   // http://www.mozilla.com/en-US/firefox/firefox.html
	const BROWSER_ICEWEASEL = 'Iceweasel';      // http://www.geticeweasel.org/
	const BROWSER_SHIRETOKO = 'Shiretoko';      // http://wiki.mozilla.org/Projects/shiretoko
	const BROWSER_MOZILLA = 'Mozilla';   // http://www.mozilla.com/en-US/
	const BROWSER_AMAYA = 'Amaya';       // http://www.w3.org/Amaya/
	const BROWSER_LYNX = 'Lynx';	 // http://en.wikipedia.org/wiki/Lynx
	const BROWSER_SAFARI = 'Safari';     // http://apple.com
	const BROWSER_IPHONE = 'iPhone';     // http://apple.com
	const BROWSER_IPOD = 'iPod';	 // http://apple.com
	const BROWSER_IPAD = 'iPad';	 // http://apple.com
	const BROWSER_CHROME = 'Chrome';     // http://www.google.com/chrome
	const BROWSER_ANDROID = 'Android';   // http://www.android.com/
	const BROWSER_GOOGLEBOT = 'GoogleBot';      // http://en.wikipedia.org/wiki/Googlebot
	const BROWSER_SLURP = 'Yahoo! Slurp';       // http://en.wikipedia.org/wiki/Yahoo!_Slurp
	const BROWSER_W3CVALIDATOR = 'W3C Validator';      // http://validator.w3.org/
	const BROWSER_BLACKBERRY = 'BlackBerry';    // http://www.blackberry.com/
	const BROWSER_ICECAT = 'IceCat';     // http://en.wikipedia.org/wiki/GNU_IceCat
	const BROWSER_NOKIA_S60 = 'Nokia S60 OSS Browser'; // http://en.wikipedia.org/wiki/Web_Browser_for_S60
	const BROWSER_NOKIA = 'Nokia Browser';      // * all other WAP-based browsers on the Nokia Platform
	const BROWSER_MSN = 'MSN Browser';   // http://explorer.msn.com/
	const BROWSER_MSNBOT = 'MSN Bot';    // http://search.msn.com/msnbot.htm
	// http://en.wikipedia.org/wiki/Msnbot  (used for Bing as well)

	const BROWSER_NETSCAPE_NAVIGATOR = 'Netscape Navigator';  // http://browser.netscape.com/ (DEPRECATED)
	const BROWSER_GALEON = 'Galeon';     // http://galeon.sourceforge.net/ (DEPRECATED)
	const BROWSER_NETPOSITIVE = 'NetPositive';  // http://en.wikipedia.org/wiki/NetPositive (DEPRECATED)
	const BROWSER_PHOENIX = 'Phoenix';   // http://en.wikipedia.org/wiki/History_of_Mozilla_Firefox (DEPRECATED)

	const PLATFORM_UNKNOWN = 'unknown';
	const PLATFORM_WINDOWS = 'Windows';
	const PLATFORM_WINDOWS_CE = 'Windows CE';
	const PLATFORM_APPLE = 'Apple';
	const PLATFORM_LINUX = 'Linux';
	const PLATFORM_OS2 = 'OS/2';
	const PLATFORM_BEOS = 'BeOS';
	const PLATFORM_IPHONE = 'iPhone';
	const PLATFORM_IPOD = 'iPod';
	const PLATFORM_IPAD = 'iPad';
	const PLATFORM_BLACKBERRY = 'BlackBerry';
	const PLATFORM_NOKIA = 'Nokia';
	const PLATFORM_FREEBSD = 'FreeBSD';
	const PLATFORM_OPENBSD = 'OpenBSD';
	const PLATFORM_NETBSD = 'NetBSD';
	const PLATFORM_SUNOS = 'SunOS';
	const PLATFORM_OPENSOLARIS = 'OpenSolaris';
	const PLATFORM_ANDROID = 'Android';

	const OPERATING_SYSTEM_UNKNOWN = 'unknown';

	protected $attributes;

	public function __construct($useragent=null, $eventHandler=null)
	{
		$this->attributes = new CAttributeCollection();
		$this->attributes->add('agent', '');
		$this->attributes->add('browser_name', '');
		$this->attributes->add('version', '');
		$this->attributes->add('platform', '');
		$this->attributes->add('os', '');
		$this->attributes->add('is_aol', false);
		$this->attributes->add('is_mobile', false);
		$this->attributes->add('is_robot', false);
		$this->attributes->add('aol_version', '');
		
		if(null !== $eventHandler)
			$this->onDetection = $eventHandler;
		
		if (null !== $useragent)
			$this->setUserAgent($useragent0);
		else
		{
			$this->reset();
			$this->determine();
		}
	}
	/**
	 * Event raised when user agent has been parsed for browser discovery
	 * @param  $event 
	 */
	public function onDetection($event)
	{
		$this->raiseEvent('onDetection', $event);
	}

	/**
	 * Reset all properties
	 */
	public function reset()
	{
		$this->attributes->agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$this->attributes->browser_name = self::BROWSER_UNKNOWN;
		$this->attributes->version = self::VERSION_UNKNOWN;
		$this->attributes->platform = self::PLATFORM_UNKNOWN;
		$this->attributes->os = self::OPERATING_SYSTEM_UNKNOWN;
		$this->attributes->is_aol = false;
		$this->attributes->is_mobile = false;
		$this->attributes->is_robot = false;
		$this->attributes->aol_version = self::VERSION_UNKNOWN;
	}

	/**
	 * Check to see if the specific browser is valid
	 * @param string $browserName
	 * @return True if the browser is the specified browser
	 */
	function isBrowser($browserName)
	{
		return( 0 == strcasecmp($this->attributes->browser_name, trim($browserName)));
	}

	/**
	 * The name of the browser.  All return types are from the class contants
	 * @return string Name of the browser
	 */
	public function getBrowser()
	{
		return $this->attributes->browser_name;
	}

	/**
	 * Set the name of the browser
	 * @param $browser The name of the Browser
	 */
	public function setBrowser($browser)
	{
		return $this->attributes->browser_name = $browser;
	}

	/**
	 * The name of the platform.  All return types are from the class contants
	 * @return string Name of the browser
	 */
	public function getPlatform()
	{
		return $this->attributes->platform;
	}

	/**
	 * Set the name of the platform
	 * @param $platform The name of the Platform
	 */
	public function setPlatform($platform)
	{
		return $this->attributes->platform = $platform;
	}

	/**
	 * The version of the browser.
	 * @return string Version of the browser (will only contain alpha-numeric characters and a period)
	 */
	public function getVersion()
	{
		return $this->attributes->version;
	}

	/**
	 * Set the version of the browser
	 * @param $version The version of the Browser
	 */
	public function setVersion($version)
	{
		$this->attributes->version = preg_replace('/[^0-9,.,a-z,A-Z-]/', '', $version);
	}

	/**
	 * The version of AOL.
	 * @return string Version of AOL (will only contain alpha-numeric characters and a period)
	 */
	public function getAolVersion()
	{
		return $this->attributes->aol_version;
	}

	/**
	 * Set the version of AOL
	 * @param $version The version of AOL
	 */
	public function setAolVersion($version)
	{
		$this->attributes->aol_version = preg_replace('/[^0-9,.,a-z,A-Z]/', '', $version);
	}

	/**
	 * Is the browser from AOL?
	 * @return boolean True if the browser is from AOL otherwise false
	 */
	public function getIsAol()
	{
		return $this->attributes->is_aol;
	}
	/**
	 * Set the browser to be from AOL
	 * @param $isAol
	 */
	protected function setIsAol($isAol)
	{
		$this->attributes->is_aol = $isAol;
	}
	
	/**
	 * Is the browser from a mobile device?
	 * @return boolean True if the browser is from a mobile device otherwise false
	 */
	public function getIsMobile()
	{
		return $this->attributes->is_mobile;
	}
	/**
	 * Set the Browser to be mobile
	 * @param boolean $value is the browser a mobile brower or not
	 */
	protected function setMobile($value)
	{
		$this->attributes->is_mobile = $value;
	}
	/**
	 * Is the browser from a robot (ex Slurp,GoogleBot)?
	 * @return boolean True if the browser is from a robot otherwise false
	 */
	public function getIsRobot()
	{
		return $this->attributes->is_robot;
	}
	/**
	 * Set the Browser to be a robot
	 * @param boolean $value is the browser a robot or not
	 */
	protected function setRobot($value)
	{
		$this->attributes->is_robot = $value;
	}

	/**
	 * Get the user agent value in use to determine the browser
	 * @return string The user agent from the HTTP header
	 */
	public function getUserAgent()
	{
		return $this->attributes->agent;
	}

	/**
	 * Set the user agent value (the construction will use the HTTP header value - this will overwrite it)
	 * @param $agent_string The value for the User Agent
	 */
	public function setUserAgent($agent_string)
	{
		$this->reset();
		$this->attributes->agent = $agent_string;
		$this->determine();
	}

	/**
	 * Used to determine if the browser is actually "chromeframe"
	 * @since 1.7
	 * @return boolean True if the browser is using chromeframe
	 */
	public function isChromeFrame()
	{
		return( strpos($this->attributes->agent, "chromeframe") !== false );
	}

	/**
	 * Magic Method
	 * Returns a formatted string with a summary of the details of the browser.
	 * @return string formatted string with a summary of the browser
	 */
	public function __toString()
	{
		return "<strong>Browser Name:</strong>{$this->getBrowser()}<br/>\n" .
			"<strong>Browser Version:</strong>{$this->getVersion()}<br/>\n" .
			"<strong>Browser User Agent String:</strong>{$this->getUserAgent()}<br/>\n" .
			"<strong>Platform:</strong>{$this->getPlatform()}<br/>";
	}

	/**
	 * Protected routine to calculate and determine what the browser is in use (including platform)
	 */
	protected function determine()
	{
		$this->checkPlatform();
		$this->checkBrowsers();
		$this->checkForAol();
	
		$event = new EWebBrowserEvent($this);
		
		foreach($this->attributes as $key=>$value)
			$event->{$key}=$value;
			
		$this->onDetection($event);
	}

	/**
	 * Protected routine to determine the browser type
	 * @return boolean True if the browser was detected otherwise false
	 */
	protected function checkBrowsers()
	{
		return (
			// well-known, well-used
			// Special Notes:
			// (1) Opera must be checked before FireFox due to the odd
			//     user agents used in some older versions of Opera
			// (2) WebTV is strapped onto Internet Explorer so we must
			//     check for WebTV before IE
			// (3) (deprecated) Galeon is based on Firefox and needs to be
			//     tested before Firefox is tested
			// (4) OmniWeb is based on Safari so OmniWeb check must occur
			//     before Safari
			// (5) Netscape 9+ is based on Firefox so Netscape checks
			//     before FireFox are necessary
			$this->checkBrowserWebTv() ||
			$this->checkBrowserInternetExplorer() ||
			$this->checkBrowserOpera() ||
			$this->checkBrowserGaleon() ||
			$this->checkBrowserNetscapeNavigator9Plus() ||
			$this->checkBrowserFirefox() ||
			$this->checkBrowserChrome() ||
			$this->checkBrowserOmniWeb() ||
			// common mobile
			$this->checkBrowserAndroid() ||
			$this->checkBrowseriPad() ||
			$this->checkBrowseriPod() ||
			$this->checkBrowseriPhone() ||
			$this->checkBrowserBlackBerry() ||
			$this->checkBrowserNokia() ||
			// common bots
			$this->checkBrowserGoogleBot() ||
			$this->checkBrowserMSNBot() ||
			$this->checkBrowserSlurp() ||
			// WebKit base check (post mobile and others)
			$this->checkBrowserSafari() ||
			// everyone else
			$this->checkBrowserNetPositive() ||
			$this->checkBrowserFirebird() ||
			$this->checkBrowserKonqueror() ||
			$this->checkBrowserIcab() ||
			$this->checkBrowserPhoenix() ||
			$this->checkBrowserAmaya() ||
			$this->checkBrowserLynx() ||
			$this->checkBrowserShiretoko() ||
			$this->checkBrowserIceCat() ||
			$this->checkBrowserW3CValidator() ||
			$this->checkBrowserMozilla() /* Mozilla is such an open standard that you must check it last */
			);
	}

	/**
	 * Determine if the user is using a BlackBerry (last updated 1.7)
	 * @return boolean True if the browser is the BlackBerry browser otherwise false
	 */
	protected function checkBrowserBlackBerry()
	{
		if (stripos($this->getUserAgent(), 'blackberry') !== false)
		{
			$aresult = explode("/", stristr($this->getUserAgent(), "BlackBerry"));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_BLACKBERRY);
			$this->setMobile(true);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the user is using an AOL User Agent (last updated 1.7)
	 * @return boolean True if the browser is from AOL otherwise false
	 */
	protected function checkForAol()
	{
		$this->setIsAol(false);
		$this->setAolVersion(self::VERSION_UNKNOWN);

		if (stripos($this->getUserAgent(), 'aol') !== false)
		{
			$aversion = explode(' ', stristr($this->getUserAgent(), 'AOL'));
			$this->setIsAol(true);
			$this->setAolVersion(preg_replace('/[^0-9\.a-z]/i', '', $aversion[1]));
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is the GoogleBot or not (last updated 1.7)
	 * @return boolean True if the browser is the GoogletBot otherwise false
	 */
	protected function checkBrowserGoogleBot()
	{
		if (stripos($this->getUserAgent(), 'googlebot') !== false)
		{
			$aresult = explode('/', stristr($this->getUserAgent(), 'googlebot'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion(str_replace(';', '', $aversion[0]));
			$this->setBrowser(self::BROWSER_GOOGLEBOT);
			$this->setRobot(true);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is the MSNBot or not (last updated 1.9)
	 * @return boolean True if the browser is the MSNBot otherwise false
	 */
	protected function checkBrowserMSNBot()
	{
		if (stripos($this->getUserAgent(), "msnbot") !== false)
		{
			$aresult = explode("/", stristr($this->getUserAgent(), "msnbot"));
			$aversion = explode(" ", $aresult[1]);
			$this->setVersion(str_replace(";", "", $aversion[0]));
			$this->setBrowser(self::BROWSER_MSNBOT);
			$this->setRobot(true);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is the W3C Validator or not (last updated 1.7)
	 * @return boolean True if the browser is the W3C Validator otherwise false
	 */
	protected function checkBrowserW3CValidator()
	{
		if (stripos($this->getUserAgent(), 'W3C-checklink') !== false)
		{
			$aresult = explode('/', stristr($this->getUserAgent(), 'W3C-checklink'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_W3CVALIDATOR);
			return true;
		} else if (stripos($this->getUserAgent(), 'W3C_Validator') !== false)
		{
			// Some of the Validator versions do not delineate w/ a slash - add it back in
			$ua = str_replace("W3C_Validator ", "W3C_Validator/", $this->getUserAgent());
			$aresult = explode('/', stristr($ua, 'W3C_Validator'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_W3CVALIDATOR);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is the Yahoo! Slurp Robot or not (last updated 1.7)
	 * @return boolean True if the browser is the Yahoo! Slurp Robot otherwise false
	 */
	protected function checkBrowserSlurp()
	{
		if (stripos($this->getUserAgent(), 'slurp') !== false)
		{
			$aresult = explode('/', stristr($this->getUserAgent(), 'Slurp'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_SLURP);
			$this->setRobot(true);
			$this->setMobile(false);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Internet Explorer or not (last updated 1.7)
	 * @return boolean True if the browser is Internet Explorer otherwise false
	 */
	protected function checkBrowserInternetExplorer()
	{
		$agent = $this->getUserAgent();
		// Test for v1 - v1.5 IE
		if (stripos($agent, 'microsoft internet explorer') !== false)
		{
			$this->setBrowser(self::BROWSER_IE);
			$this->setVersion('1.0');
			$aresult = stristr($this->getUserAgent(), '/');
			if (preg_match('/308|425|426|474|0b1/i', $aresult))
			{
				$this->setVersion('1.5');
			}
			return true;
		}
		// Test for versions > 1.5
		else if (stripos($agent, 'msie') !== false && stripos($agent, 'opera') === false)
		{
			// See if the browser is the odd MSN Explorer
			if (stripos($agent, 'msnb') !== false)
			{
				$aresult = explode(' ', stristr(str_replace(';', '; ', $agent), 'MSN'));
				$this->setBrowser(self::BROWSER_MSN);
				$this->setVersion(str_replace(array('(', ')', ';'), '', $aresult[1]));
				return true;
			}
			$aresult = explode(' ', stristr(str_replace(';', '; ', $agent), 'msie'));
			$this->setBrowser(self::BROWSER_IE);
			$this->setVersion(str_replace(array('(', ')', ';'), '', $aresult[1]));
			return true;
		}
		// Test for Pocket IE
		else if (stripos($agent, 'mspie') !== false || stripos($agent, 'pocket') !== false)
		{
			$aresult = explode(' ', stristr($agent, 'mspie'));
			$this->setPlatform(self::PLATFORM_WINDOWS_CE);
			$this->setBrowser(self::BROWSER_POCKET_IE);
			$this->setMobile(true);

			if (stripos($agent, 'mspie') !== false)
			{
				$this->setVersion($aresult[1]);
			} else
			{
				$aversion = explode('/', $agent);
				$this->setVersion($aversion[1]);
			}
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Opera or not (last updated 1.7)
	 * @return boolean True if the browser is Opera otherwise false
	 */
	protected function checkBrowserOpera()
	{
		$agent = $this->getUserAgent();
		if (stripos($agent, 'opera mini') !== false)
		{
			$resultant = stristr($agent, 'opera mini');
			if (preg_match('/\//', $resultant))
			{
				$aresult = explode('/', $resultant);
				$aversion = explode(' ', $aresult[1]);
				$this->setVersion($aversion[0]);
			} else
			{
				$aversion = explode(' ', stristr($resultant, 'opera mini'));
				$this->setVersion($aversion[1]);
			}
			$this->setBrowser(self::BROWSER_OPERA_MINI);
			$this->setMobile(true);
			return true;
		} else if (stripos($agent, 'opera') !== false)
		{
			$resultant = stristr($agent, 'opera');
			if (preg_match('/Version\/(10.*)$/', $resultant, $matches))
			{
				$this->setVersion($matches[1]);
			} else if (preg_match('/\//', $resultant))
			{
				$aresult = explode('/', str_replace("(", " ", $resultant));
				$aversion = explode(' ', $aresult[1]);
				$this->setVersion($aversion[0]);
			} else
			{
				$aversion = explode(' ', stristr($resultant, 'opera'));
				$this->setVersion(isset($aversion[1]) ? $aversion[1] : "");
			}
			$this->setBrowser(self::BROWSER_OPERA);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Chrome or not (last updated 1.7)
	 * @return boolean True if the browser is Chrome otherwise false
	 */
	protected function checkBrowserChrome()
	{
		if (stripos($this->getUserAgent(), 'Chrome') !== false)
		{
			$aresult = explode('/', stristr($this->getUserAgent(), 'Chrome'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_CHROME);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is WebTv or not (last updated 1.7)
	 * @return boolean True if the browser is WebTv otherwise false
	 */
	protected function checkBrowserWebTv()
	{
		if (stripos($this->getUserAgent(), 'webtv') !== false)
		{
			$aresult = explode('/', stristr($this->getUserAgent(), 'webtv'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_WEBTV);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is NetPositive or not (last updated 1.7)
	 * @return boolean True if the browser is NetPositive otherwise false
	 */
	protected function checkBrowserNetPositive()
	{
		if (stripos($this->getUserAgent(), 'NetPositive') !== false)
		{
			$aresult = explode('/', stristr($this->getUserAgent(), 'NetPositive'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion(str_replace(array('(', ')', ';'), '', $aversion[0]));
			$this->setBrowser(self::BROWSER_NETPOSITIVE);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Galeon or not (last updated 1.7)
	 * @return boolean True if the browser is Galeon otherwise false
	 */
	protected function checkBrowserGaleon()
	{
		if (stripos($this->getUserAgent(), 'galeon') !== false)
		{
			$aresult = explode(' ', stristr($this->getUserAgent(), 'galeon'));
			$aversion = explode('/', $aresult[0]);
			$this->setVersion($aversion[1]);
			$this->setBrowser(self::BROWSER_GALEON);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Konqueror or not (last updated 1.7)
	 * @return boolean True if the browser is Konqueror otherwise false
	 */
	protected function checkBrowserKonqueror()
	{
		if (stripos($this->getUserAgent(), 'Konqueror') !== false)
		{
			$aresult = explode(' ', stristr($this->getUserAgent(), 'Konqueror'));
			$aversion = explode('/', $aresult[0]);
			$this->setVersion($aversion[1]);
			$this->setBrowser(self::BROWSER_KONQUEROR);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is iCab or not (last updated 1.7)
	 * @return boolean True if the browser is iCab otherwise false
	 */
	protected function checkBrowserIcab()
	{
		if (stripos($this->getUserAgent(), 'icab') !== false)
		{
			$aversion = explode(' ', stristr(str_replace('/', ' ', $this->getUserAgent()), 'icab'));
			$this->setVersion($aversion[1]);
			$this->setBrowser(self::BROWSER_ICAB);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is OmniWeb or not (last updated 1.7)
	 * @return boolean True if the browser is OmniWeb otherwise false
	 */
	protected function checkBrowserOmniWeb()
	{
		if (stripos($this->getUserAgent(), 'omniweb') !== false)
		{
			$aresult = explode('/', stristr($this->getUserAgent(), 'omniweb'));
			$aversion = explode(' ', isset($aresult[1]) ? $aresult[1] : "");
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_OMNIWEB);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Phoenix or not (last updated 1.7)
	 * @return boolean True if the browser is Phoenix otherwise false
	 */
	protected function checkBrowserPhoenix()
	{
		if (stripos($this->getUserAgent(), 'Phoenix') !== false)
		{
			$aversion = explode('/', stristr($this->getUserAgent(), 'Phoenix'));
			$this->setVersion($aversion[1]);
			$this->setBrowser(self::BROWSER_PHOENIX);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Firebird or not (last updated 1.7)
	 * @return boolean True if the browser is Firebird otherwise false
	 */
	protected function checkBrowserFirebird()
	{
		if (stripos($this->getUserAgent(), 'Firebird') !== false)
		{
			$aversion = explode('/', stristr($this->getUserAgent(), 'Firebird'));
			$this->setVersion($aversion[1]);
			$this->setBrowser(self::BROWSER_FIREBIRD);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Netscape Navigator 9+ or not (last updated 1.7)
	 * NOTE: (http://browser.netscape.com/ - Official support ended on March 1st, 2008)
	 * @return boolean True if the browser is Netscape Navigator 9+ otherwise false
	 */
	protected function checkBrowserNetscapeNavigator9Plus()
	{
		if (stripos($this->getUserAgent(), 'Firefox') !== false && preg_match('/Navigator\/([^ ]*)/i', $this->getUserAgent(), $matches))
		{
			$this->setVersion($matches[1]);
			$this->setBrowser(self::BROWSER_NETSCAPE_NAVIGATOR);
			return true;
		} else if (stripos($this->getUserAgent(), 'Firefox') === false && preg_match('/Netscape6?\/([^ ]*)/i', $this->getUserAgent(), $matches))
		{
			$this->setVersion($matches[1]);
			$this->setBrowser(self::BROWSER_NETSCAPE_NAVIGATOR);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Shiretoko or not (https://wiki.mozilla.org/Projects/shiretoko) (last updated 1.7)
	 * @return boolean True if the browser is Shiretoko otherwise false
	 */
	protected function checkBrowserShiretoko()
	{
		if (stripos($this->getUserAgent(), 'Mozilla') !== false && preg_match('/Shiretoko\/([^ ]*)/i', $this->getUserAgent(), $matches))
		{
			$this->setVersion($matches[1]);
			$this->setBrowser(self::BROWSER_SHIRETOKO);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Ice Cat or not (http://en.wikipedia.org/wiki/GNU_IceCat) (last updated 1.7)
	 * @return boolean True if the browser is Ice Cat otherwise false
	 */
	protected function checkBrowserIceCat()
	{
		if (stripos($this->getUserAgent(), 'Mozilla') !== false && preg_match('/IceCat\/([^ ]*)/i', $this->getUserAgent(), $matches))
		{
			$this->setVersion($matches[1]);
			$this->setBrowser(self::BROWSER_ICECAT);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Nokia or not (last updated 1.7)
	 * @return boolean True if the browser is Nokia otherwise false
	 */
	protected function checkBrowserNokia()
	{
		if (preg_match("/Nokia([^\/]+)\/([^ SP]+)/i", $this->getUserAgent(), $matches))
		{
			$this->setVersion($matches[2]);
			if (stripos($this->getUserAgent(), 'Series60') !== false || strpos($this->getUserAgent(), 'S60') !== false)
			{
				$this->setBrowser(self::BROWSER_NOKIA_S60);
			} else
			{
				$this->setBrowser(self::BROWSER_NOKIA);
			}
			$this->setMobile(true);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Firefox or not (last updated 1.7)
	 * @return boolean True if the browser is Firefox otherwise false
	 */
	protected function checkBrowserFirefox()
	{
		if (stripos($this->getUserAgent(), 'safari') === false)
		{
			if (preg_match("/Firefox[\/ \(]([^ ;\)]+)/i", $this->getUserAgent(), $matches))
			{
				$this->setVersion($matches[1]);
				$this->setBrowser(self::BROWSER_FIREFOX);
				return true;
			} else if (preg_match("/Firefox$/i", $this->getUserAgent(), $matches))
			{
				$this->setVersion("");
				$this->setBrowser(self::BROWSER_FIREFOX);
				return true;
			}
		}
		return false;
	}

	/**
	 * Determine if the browser is Firefox or not (last updated 1.7)
	 * @return boolean True if the browser is Firefox otherwise false
	 */
	protected function checkBrowserIceweasel()
	{
		if (stripos($this->getUserAgent(), 'Iceweasel') !== false)
		{
			$aresult = explode('/', stristr($this->getUserAgent(), 'Iceweasel'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_ICEWEASEL);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Mozilla or not (last updated 1.7)
	 * @return boolean True if the browser is Mozilla otherwise false
	 */
	protected function checkBrowserMozilla()
	{
		$agent = $this->getUserAgent();
		if (stripos($agent, 'mozilla') !== false && preg_match('/rv:[0-9].[0-9][a-b]?/i', $agent) && stripos($agent, 'netscape') === false)
		{
			$aversion = explode(' ', stristr($agent, 'rv:'));
			preg_match('/rv:[0-9].[0-9][a-b]?/i', $agent, $aversion);
			$this->setVersion(str_replace('rv:', '', $aversion[0]));
			$this->setBrowser(self::BROWSER_MOZILLA);
			return true;
		} else if (stripos($agent, 'mozilla') !== false && preg_match('/rv:[0-9]\.[0-9]/i', $agent) && stripos($agent, 'netscape') === false)
		{
			$aversion = explode('', stristr($agent, 'rv:'));
			$this->setVersion(str_replace('rv:', '', $aversion[0]));
			$this->setBrowser(self::BROWSER_MOZILLA);
			return true;
		} else if (stripos($agent, 'mozilla') !== false && preg_match('/mozilla\/([^ ]*)/i', $agent, $matches) && stripos($agent, 'netscape') === false)
		{
			$this->setVersion($matches[1]);
			$this->setBrowser(self::BROWSER_MOZILLA);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Lynx or not (last updated 1.7)
	 * @return boolean True if the browser is Lynx otherwise false
	 */
	protected function checkBrowserLynx()
	{
		if (stripos($this->getUserAgent(), 'lynx') !== false)
		{
			$aresult = explode('/', stristr($this->getUserAgent(), 'Lynx'));
			$aversion = explode(' ', (isset($aresult[1]) ? $aresult[1] : ""));
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_LYNX);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Amaya or not (last updated 1.7)
	 * @return boolean True if the browser is Amaya otherwise false
	 */
	protected function checkBrowserAmaya()
	{
		if (stripos($this->getUserAgent(), 'amaya') !== false)
		{
			$aresult = explode('/', stristr($this->getUserAgent(), 'Amaya'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_AMAYA);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Safari or not (last updated 1.7)
	 * @return boolean True if the browser is Safari otherwise false
	 */
	protected function checkBrowserSafari()
	{
		if (stripos($this->getUserAgent(), 'Safari') !== false && stripos($this->getUserAgent(), 'iPhone') === false && stripos($this->getUserAgent(), 'iPod') === false)
		{
			$aresult = explode('/', stristr($this->getUserAgent(), 'Version'));
			if (isset($aresult[1]))
			{
				$aversion = explode(' ', $aresult[1]);
				$this->setVersion($aversion[0]);
			} else
			{
				$this->setVersion(self::VERSION_UNKNOWN);
			}
			$this->setBrowser(self::BROWSER_SAFARI);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is iPhone or not (last updated 1.7)
	 * @return boolean True if the browser is iPhone otherwise false
	 */
	protected function checkBrowseriPhone()
	{
		if (stripos($this->getUserAgent(), 'iPhone') !== false)
		{
			$aresult = explode('/', stristr($this->getUserAgent(), 'Version'));
			if (isset($aresult[1]))
			{
				$aversion = explode(' ', $aresult[1]);
				$this->setVersion($aversion[0]);
			} else
			{
				$this->setVersion(self::VERSION_UNKNOWN);
			}
			$this->setMobile(true);
			$this->setBrowser(self::BROWSER_IPHONE);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is iPod or not (last updated 1.7)
	 * @return boolean True if the browser is iPod otherwise false
	 */
	protected function checkBrowseriPad()
	{
		if (stripos($this->getUserAgent(), 'iPad') !== false)
		{
			$aresult = explode('/', stristr($this->getUserAgent(), 'Version'));
			if (isset($aresult[1]))
			{
				$aversion = explode(' ', $aresult[1]);
				$this->setVersion($aversion[0]);
			} else
			{
				$this->setVersion(self::VERSION_UNKNOWN);
			}
			$this->setMobile(true);
			$this->setBrowser(self::BROWSER_IPAD);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is iPod or not (last updated 1.7)
	 * @return boolean True if the browser is iPod otherwise false
	 */
	protected function checkBrowseriPod()
	{
		if (stripos($this->getUserAgent(), 'iPod') !== false)
		{
			$aresult = explode('/', stristr($this->getUserAgent(), 'Version'));
			if (isset($aresult[1]))
			{
				$aversion = explode(' ', $aresult[1]);
				$this->setVersion($aversion[0]);
			} else
			{
				$this->setVersion(self::VERSION_UNKNOWN);
			}
			$this->setMobile(true);
			$this->setBrowser(self::BROWSER_IPOD);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Android or not (last updated 1.7)
	 * @return boolean True if the browser is Android otherwise false
	 */
	protected function checkBrowserAndroid()
	{
		if (stripos($this->getUserAgent(), 'Android') !== false)
		{
			$aresult = explode(' ', stristr($this->getUserAgent(), 'Android'));
			if (isset($aresult[1]))
			{
				$aversion = explode(' ', $aresult[1]);
				$this->setVersion($aversion[0]);
			} else
			{
				$this->setVersion(self::VERSION_UNKNOWN);
			}
			$this->setMobile(true);
			$this->setBrowser(self::BROWSER_ANDROID);
			return true;
		}
		return false;
	}

	/**
	 * Determine the user's platform (last updated 1.7)
	 */
	protected function checkPlatform()
	{
		$agent = $this->getUserAgent();

		if (stripos($agent, 'windows') !== false)
			$this->attributes->platform = self::PLATFORM_WINDOWS;
		else if (stripos($agent, 'iPad') !== false)
			$this->attributes->platform = self::PLATFORM_IPAD;
		else if (stripos($agent, 'iPod') !== false)
			$this->attributes->platform = self::PLATFORM_IPOD;
		else if (stripos($agent, 'iPhone') !== false)
			$this->attributes->platform = self::PLATFORM_IPHONE;
		elseif (stripos($agent, 'mac') !== false)
			$this->attributes->platform = self::PLATFORM_APPLE;
		elseif (stripos($agent, 'android') !== false)
			$this->attributes->platform = self::PLATFORM_ANDROID;
		elseif (stripos($agent, 'linux') !== false)
			$this->attributes->platform = self::PLATFORM_LINUX;
		else if (stripos($agent, 'Nokia') !== false)
			$this->attributes->platform = self::PLATFORM_NOKIA;
		else if (stripos($agent, 'BlackBerry') !== false)
			$this->attributes->platform = self::PLATFORM_BLACKBERRY;
		elseif (stripos($agent, 'FreeBSD') !== false)
			$this->attributes->platform = self::PLATFORM_FREEBSD;
		elseif (stripos($agent, 'OpenBSD') !== false)
			$this->attributes->platform = self::PLATFORM_OPENBSD;
		elseif (stripos($agent, 'NetBSD') !== false)
			$this->attributes->platform = self::PLATFORM_NETBSD;
		elseif (stripos($agent, 'OpenSolaris') !== false)
			$this->attributes->platform = self::PLATFORM_OPENSOLARIS;
		elseif (stripos($agent, 'SunOS') !== false)
			$this->attributes->platform = self::PLATFORM_SUNOS;
		elseif (stripos($agent, 'OS\/2') !== false)
			$this->attributes->platform = self::PLATFORM_OS2;
		elseif (stripos($agent, 'BeOS') !== false)
			$this->attributes->platform = self::PLATFORM_BEOS;
		elseif (stripos($agent, 'win') !== false)
			$this->attributes->platform = self::PLATFORM_WINDOWS;
	}

}
/**
 * Class event to report on detection event
 */
class EWebBrowserEvent extends CEvent{
	public $agent;
	public $browser_name;
	public $version;
	public $platform;
	public $os;
	public $is_aol;
	public $is_mobile;
	public $is_robot;
	public $aol_version;
}
?>
