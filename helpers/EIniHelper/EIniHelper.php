<?php
/**
 * 
 * EIniHelper Class
 * 
 * @author Antonio Ramirez
 * @see www.ramirezcobos.com 
 * 
 * Example INI file
 * 
 * ; This is the default layout for a settings file.
 * ; -----------------------------------------------
 * ; Default database connection. 
 * ; -----------------------------------------------
 * 
 * [Database]
 * connectionType = mysql
 * host = localhost
 * username = root
 * password = thisismypassword
 * database = dbmydatabase
 * 
 * ; The settings parameters
 * [Parameters]
 * language = es_es
 * adminemail = admin@email.com
 * 
 * Example of use (with above INI file)
 * 
 * $helper 		= EIniHelper::Load('pathtoInifile');
 * $dataConf 	= $helper->Get('Database');
 * $username 	= $helper->Get('Database','username');
 * 
 * $dataConf = EIniHelper::Load('pathtoInifile')->Get("Database");
 * 
 * $language = EIniHelper::Load('pathtoInifile')->Get("Parameters","language");
 * 
 * @copyright 
 * 
 * Copyright (c) 2011 Antonio Ramirez Cobos
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
class EIniHelper {
  
  	private $settingsfile, $settings;

	/**
	  * Constructor.
	  * Loads the settings by parsing the ini file passed 
	  * in the constructor parameter.
	  * @param string $settingsfile
	  */
	function __construct($settingsfile) 
	{
		if(!file_exists($settingsfile))
			throw new CException(Yii::t('EIniHelper','INI file not found'));
			
		$this->settingsfile = $settingsfile;
		$this->settings = parse_ini_file($this->settingsfile, true);
	
	}

	/**
	 * Settings::Load
	 *
	 * Singleton functionality that creates one instance per loaded settings 
	 * file so that ini parsing needs to happen only once.
	 * @param string $settingsfile file to read for settings
	 */
	public static function Load($settingsfile= 'settings.ini')
	{
		static $instances = array();
		if(!array_key_exists($settingsfile, $instances)) {
			$instances[$settingsfile] = new EIniHelper($settingsfile);
		}
		return($instances[$settingsfile]);
	}
  
	/**
	 * Settings::Get
	 * 
	 * Gets an array of parameters for a key of settings file or 
	 * gets one specific setting under a key.
	 * @param string $param 
	 * @return string subsection | array ini section
	 */
	function Get($section, $subsection = false)
	{
		if($this->settings === false) throw new CException(Yii::t('EIniHelper','error reading INI file')); 
		return ($subsection) ? $this->settings[$section][$subsection] : $this->settings[$section];
	}
  
}

?>