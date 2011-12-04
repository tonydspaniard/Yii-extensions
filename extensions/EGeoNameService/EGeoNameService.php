<?php 
/**
 * 
 * EGeoNameService Class 
 * 
 * Simplifies the access to the GeoNames web services 
 * @see http://www.geonames.org
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
/**
 * @method array astergdem()					astergdem(array $params)
 * @method array children()                   	children(array $params)
 * @method array cities()                     	cities(array $params)
 * @method array countryCode()                	countryCode(array $params)
 * @method array countryInfo()                	countryInfo(array $params)
 * @method array countrySubdivision()         	countrySubdivision(array $params)
 * @method array earthquakes()                	earthquakes(array $params)
 * @method array extendedFindNearby()         	extendedFindNearby(array $params)
 * @method array findNearby()                 	findNearby(array $params)
 * @method array findNearbyPlaceName()        	findNearbyPlaceName(array $params)
 * @method array findNearbyPostalCodes()      	findNearbyPostalCodes(array $params)
 * @method array findNearbyStreets()          	findNearbyStreets(array $params)
 * @method array findNearbyStreetsOSM()       	findNearbyStreetsOSM(array $params)
 * @method array findNearByWeather()          	findNearByWeather(array $params)
 * @method array findNearbyWikipedia()        	findNearbyWikipedia(array $params)
 * @method array findNearestAddress()         	findNearestAddress(array $params)
 * @method array findNearestIntersection()    	findNearestIntersection(array $params)
 * @method array findNearestIntersectionOSM() 	findNearestIntersectionOSM(array $params)
 * @method array get()                        	get(array $params)
 * @method array gtopo30()                    	gtopo30(array $params)
 * @method array hierarchy()                  	hierarchy(array $params)
 * @method array neighbourhoud()              	neighbourhoud(array $params)
 * @method array neighbours()                 	neighbours(array $params)
 * @method array postalCodeCountryInfo()      	postalCodeCountryInfo(array $params)
 * @method array postalCodeLookup()           	postalCodeLookup(array $params)
 * @method array search()                     	search(array $params)
 * @method array siblings()                   	siblings(array $params)
 * @method array srtm3()                      	srtm3(array $params)
 * @method array timezone()                   	timezone(array $params)
 * @method array weather()                    	weather(array $params)
 * @method array weatherIcao()                	weatherIcao(array $params)
 * @method array wikipediaBoundingBox()       	wikipediaBoundingBox(array $params)
 * @method array wikipediaSearch()            	wikipediaSearch(array $params)
 */
class EGeoNameService extends CComponent {
  	/**
  	 * 
  	 * Geonames api url
  	 * @var string
  	 */
	protected $_api = 'http://api.geonames.org/';
	/**
	 * 
	 * Username to access the API
	 * @see http://www.geonames.org/export/web-services.html
	 * @var string username
	 */
	protected $_username;
	/**
	 * 
	 * Returned results of a call to the service
	 * @var array
	 */
	protected $_results;
	/**
	 * 
	 * Supported Web Service methods
	 * @var array supprtedMethods
	 */
	protected $_supportedMethods = array(
		
		// Elevation - Aster Global Digital Elevation Model
		// Info: http://www.geonames.org/export/web-services.html#astergdem
		// Expected Results: view-source:http://api.geonames.org/astergdemJSON?lat=50.01&lng=10.2&username=demo
        'astergdem' => array(
			'parameters'=>array('lat','lng')
        ),
        // Returns the children (admin divisions and populated places) for a given geonameId
        // Info: http://www.geonames.org/export/place-hierarchy.html#children
        // Expected Results: view-source:http://api.geonames.org/children?geonameId=3175395&username=demo
        'children' => array(
            'parameters'=>array('geonameId','maxRows'),
            'root'   => 'geonames',
        ),
        // returns a list of cities and placenames in the bounding box, ordered by relevancy (capital/population)
        // Info: http://www.geonames.org/export/JSON-webservices.html#citiesJSON
        // Expected Results: view-source:http://api.geonames.org/citiesJSON?north=44.1&south=-9.9&east=-22.4&west=55.2&lang=de&username=demo 
        'cities' => array(
            'parameters'=>array('north','south','east','west','lang','maxRows'),
            'root'   => 'geonames',
        ),
        // Result : returns the iso country code for the given latitude/longitude
        // Info: http://www.geonames.org/export/web-services.html#countrycode
        // Expected Results: view-source:http://api.geonames.org/countryCodeJSON?formatted=true&lat=47.03&lng=10.2&username=demo&style=full
        'countryCode' => array(
            'parameters'=>array('lat','lng','type','lang','radius')
        ),
        // Result : Country information : Capital, Population, Area in square km, Bounding Box of mainland (excluding offshore islands)
        // Info: http://www.geonames.org/export/web-services.html#countryInfo
        // Expected Results: view-source:http://api.geonames.org/countryInfoJSON?formatted=true&lang=it&country=DE&username=demo&style=full
        'countryInfo' => array(
            'parameters'=>array('country','lang'),
            'root'   => 'geonames',
        ),
        // Result : returns the country and the administrative subdivison (state, province,...) for the given latitude/longitude
        // Info: http://www.geonames.org/export/web-services.html#countrysubdiv
        // Expected Results: view-source:http://api.geonames.org/countrySubdivisionJSON?formatted=true&lat=47.03&lng=10.2&username=demo&style=full
        'countrySubdivision' => array(
           'parameters'=>array('lat','lng','lang','radius','level')
        ),
        // Result : returns a list of earthquakes, ordered by magnitude
        // Info: http://www.geonames.org/export/JSON-webservices.html#earthquakesJSON
        // Expected Results: view-source:http://api.geonames.org/earthquakesJSON?formatted=true&north=44.1&south=-9.9&east=-22.4&west=55.2&username=demo&style=full
        'earthquakes' => array(
            'parameters'=>array('north','south','east','west','minMagnitude','maxRows'), // 'date' cannot be empty, please check info
            'root'   => 'earthquakes',
        ),
       /* 'extendedFindNearby' => array(
            'output' => 'xml', // not supported
        ),*/
        // Result : returns the closest toponym for the lat/lng query as xml document
        // Info: http://www.geonames.org/export/web-services.html#findNearby
        // Expected Results: view-source:http://api.geonames.org/findNearbyJSON?formatted=true&lat=48.865618158309374&lng=2.344207763671875&fclass=P&fcode=PPLA&fcode=PPL&fcode=PPLC&username=demo&style=full
        'findNearby' => array(
        	// http://www.geonames.org/export/codes.html -featureCode / featureClass
        	//  
        	'parameters'=>array('lat','lng','featureClass','featureCode','radius','style','maxRows'),
        	'root'   => 'geonames',
        ),
        // Result : returns the closest populated place for the lat/lng query as xml document. The unit of the distance element is 'km'. 
        // Info: http://www.geonames.org/export/web-services.html#findNearbyPlaceName
        // Expected Results: view-source:http://api.geonames.org/findNearbyPlaceNameJSON?formatted=true&lat=47.3&lng=9&username=demo&style=full
        'findNearbyPlaceName' => array(
            'parameters'=>array('lat','lng','lang'=>'en','style','radius','maxRows'),
            'root'   => 'geonames',
        ),
        // Result : returns a list of postalcodes and places for the lat/lng query as xml document.
        // Info: http://www.geonames.org/export/web-services.html#findNearbyPostalCodes
        // Expected Results: view-source:http://api.geonames.org/findNearbyPostalCodesJSON?formatted=true&postalcode=8775&country=CH&radius=10&username=demo&style=full
        'findNearbyPostalCodes' => array(
            'parameters'=>array('lat','lng','radius','maxRows','style','country','localCountry','postalcode'), // or postalcode,country, radius (in Km), maxRows (default = 5)
            'root'   => 'postalCodes',
        ),
        // US Only
        // Result : returns the nearest street segments for the given latitude/longitude
        // Info: http://www.geonames.org/maps/us-reverse-geocoder.html#findNearbyStreets
        // Expected Results: view-source:http://api.geonames.org/findNearbyStreetsJSON?formatted=true&lat=37.451&lng=-122.18&username=demo&style=full
        'findNearbyStreets' => array(
            'parameters'=>array('lat','lng'),
            'root'   => 'streetSegment',
        ),
        // Result : returns the nearest street segments for the given latitude/longitude
        // Info: http://www.geonames.org/maps/osm-reverse-geocoder.html#findNearbyStreetsOSM
        // Expected Results: view-source:http://api.geonames.org/findNearbyStreetsOSMJSON?formatted=true&lat=37.451&lng=-122.18&username=demo&style=full
        'findNearbyStreetsOSM' => array(
            'parameters'=>array('lat','lng'),
            'root'   => 'streetSegment',
        ),
        // Result : returns a weather station with the most recent weather observation
        // Info: http://www.geonames.org/export/JSON-webservices.html#findNearByWeatherJSON
        // Expected Results
        'findNearByWeather' => array(
            'parameters' => array('lat','lng'),
            'root'   => 'weatherObservation',
        ),
        // Result : returns a list of wikipedia entries
        // Info: http://www.geonames.org/export/wikipedia-webservice.html#findNearbyWikipedia
        // Expected Resuls: view-source:http://api.geonames.org/findNearbyWikipediaJSON?formatted=true&lat=47&lng=9&username=demo&style=full
        'findNearbyWikipedia' => array(
            'parameters' => array('lat','lng','radius','maxRows','lang'),
            'root'   => 'geonames',
        ),
        // US Only
        // Result : returns the nearest address for the given latitude/longitude, the street number is an 'educated guess' using an interpolation of street number at the end of a street segment.
        // Info: http://www.geonames.org/maps/us-reverse-geocoder.html#findNearestAddress
        // Expected Results: view-source:http://api.geonames.org/findNearestAddressJSON?formatted=true&lat=37.451&lng=-122.18&username=demo&style=full
        'findNearestAddress' => array(
            'parameters' => array('lat','lng'),
            'root'   => 'address',
        ),
        // US Only
        // Result : returns the nearest intersection for the given latitude/longitude
        // Info: http://www.geonames.org/maps/us-reverse-geocoder.html#findNearestIntersection
        // Expected Results: view-source:http://api.geonames.org/findNearestIntersectionJSON?formatted=true&lat=37.451&lng=-122.18&username=demo&style=full
        'findNearestIntersection' => array(
            'parameters' => array('lat','lng'),
            'root'   => 'intersection',
        ),
        // Result : returns the nearest intersection for the given latitude/longitude
        // Info: http://www.geonames.org/maps/osm-reverse-geocoder.html#findNearestIntersectionOSM
        // Expected Results: view-source:http://api.geonames.org/findNearestIntersectionOSMJSON?formatted=true&lat=37.451&lng=-122.18&username=demo&style=full
        'findNearestIntersectionOSM' => array(
            'parameters' => array('lat','lng'),
            'root'   => 'intersection',
        ),
        // Returns : geoname information for the given geonameId
        // Info: none
        // Expected Results: view-source:http://api.geonames.org/getJSON?formatted=true&geonameId=6295610&username=demo&style=full
        'get' => array(
            'parameters' => array('geonameId'),
        ),
        // GTOPO30 is a global digital elevation model (DEM) with a horizontal grid spacing of 30 arc seconds (approximately 1 kilometer). GTOPO30 was derived from several raster and vector sources of topographic information
        // Info: http://www.geonames.org/export/web-services.html#gtopo30
        // Expected Results: view-source:http://api.geonames.org/gtopo30JSON?formatted=true&lat=47.01&lng=10.2&username=demo&style=full
        'gtopo30' => array(
            'parameters' => array('lat','lng'),
        ),
        // Result : returns a list of GeoName records, ordered by hierarchy level. The top hierarchy (continent) is the first element in the list 
        // Info: http://www.geonames.org/export/place-hierarchy.html#hierarchy
        // Expected Results: view-source:http://api.geonames.org/hierarchyJSON?formatted=true&geonameId=2657896&username=demo&style=full
        'hierarchy' => array(
            'parameters' => array('geonameId'),
            'root'   => 'geonames',
        ),
        // US Only
        // Result : returns the neighbourhood for the given latitude/longitude
        // Info: http://www.geonames.org/export/web-services.html#neighbourhood
        // Expected Results: view-source:http://api.geonames.org/neighbourhoodJSON?formatted=true&lat=40.78343&lng=-73.96625&username=demo&style=full
        'neighbourhoud' => array(
            'parameters' => array('lat','lng'),
            'root'   => 'neighbourhood',
        ),
        // Result : returns the neighbours of a toponym, currently only implemented for countries
        // Info: http://www.geonames.org/export/place-hierarchy.html#neighbours
        // Expected Results: view-source:http://api.geonames.org/neighboursJSON?formatted=true&geonameId=2658434&username=demo&style=full
        'neighbours' => array(
            'parameters' => array('geonameId'),
            'root'   => 'geonames',
        ),
        // Result : returns the ocean or sea for the given latitude/longitude
        // Info: http://www.geonames.org/export/web-services.html#ocean
        // Expected Results: view-source:http://api.geonames.org/oceanJSON?formatted=true&lat=40.78343&lng=-43.96625&username=demo&style=full
        'ocean'=>array(
        	'parameters' => array('lat','lng'),
        	'root'	=> 'ocean'
        ),
        // Result : countries for which postal code geocoding is available.
        // Info: http://www.geonames.org/export/web-services.html#postalCodeCountryInfo 
        // Expected Results: view-source:http://api.geonames.org/postalCodeCountryInfoJSON?formatted=true&&username=demo&style=full
        'postalCodeCountryInfo' => array(
            'root'   => 'geonames',
        ),
        // Result : returns a list of places for the given postalcode in JSON format 
        // Info: /web-services.html#postalCodeLookupJSON
        // Expected Results: view-source:http://api.geonames.org/postalCodeLookupJSON?formatted=true&postalcode=6600&country=AT&username=demo&style=full
        'postalCodeLookup' => array(
            'parameters' => array('postalcode','country' ,'maxRows','callback', 'charset'),
            'root'   => 'postalcodes',
        ),
        // Result : returns a list of postal codes and places for the placename/postalcode query as xml document 
        // Info: http://www.geonames.org/export/web-services.html#postalCodeSearch
        // Expected Results: view-source:http://api.geonames.org/postalCodeSearchJSON?formatted=true&postalcode=9011&maxRows=10&username=demo&style=full
        'postalCodeSearch' => array(
            'parameters' => array(	'postalcode','postalcode_startsWith','placename',
            						'placename_startsWith','country','countryBias',
            						'maxRows','style','operator','charset','isReduced'),
            'root'   => 'postalCodes',
        ),
        // Result : returns the names found for the searchterm as xml or json document, the search is using an AND operator
        // Info: http://www.geonames.org/export/geonames-search.html
        // Expected Results: view-source:http://api.geonames.org/searchJSON?formatted=true&q=london&maxRows=10&lang=es&username=demo&style=full
        'search' => array(
            'parameters' => array(	'q','name','name_startsWith','name_equals','maxRows',
        							'startRow','country','countryBias','continentCode',
        							'adminCode1','adminCode2','adminCode3','featureClass',
        							'featureCode','lang','type','style','isNameRequired',
        							'tag','operator','charset','fuzzy'),
            'root'   => 'geonames',
        ),
        // Result : Returns all siblings of a GeoNames toponym.
        // Info: http://www.geonames.org/export/place-hierarchy.html#siblings
        // Expected Results: view-source:http://api.geonames.org/siblingsJSON?formatted=true&geonameId=3017382&username=demo&style=full
        'siblings' => array(
            'parameters' => array('geonameId'),
            'root'   => 'geonames',
        ),
        // Result : This web service is using Shuttle Radar Topography Mission (SRTM) data with data points located every 3-arc-second (approximately 90 meters) on a latitude/longitude grid
        // Info: http://www.geonames.org/export/web-services.html#srtm3
        // Expected Results: view-source:http://api.geonames.org/srtm3JSON?formatted=true&lat=50.01&lng=10.2&username=demo&style=full
        'srtm3' => array(
            'parameters' => array('lat','lng'),
        ),
        // Result : the timezone at the lat/lng with gmt offset (1. January) and dst offset (1. July)
        // Info: http://www.geonames.org/export/web-services.html#timezone
        // Expected Results:  view-source:http://api.geonames.org/timezoneJSON?formatted=true&lat=47.01&lng=10.2&username=demo&style=full
        'timezone' => array(
           'parameters' => array('lat','lng', 'radius', 'date'), 
        ),
        // Result : returns a list of weather stations with the most recent weather observation
        // Info: http://www.geonames.org/export/JSON-webservices.html#weatherJSON
        // Expected Results: view-source:http://api.geonames.org/weatherJSON?formatted=true&north=44.1&south=-9.9&east=-22.4&west=55.2&username=demo&style=full
        'weather' => array(
            'parameters' => array('north','south','east','west','maxRows'),
            'root'   => 'weatherObservations',
        ),
        // Result : returns the weather station and the most recent weather observation for the ICAO code
        // Info: http://www.geonames.org/export/JSON-webservices.html#weatherIcaoJSON
        // Expected Results: view-source:http://api.geonames.org/weatherIcaoJSON?formatted=true&ICAO=LSZH&username=demo&style=full
        'weatherIcao' => array(
            'parameters' => array('ICAO'),
            'root'   => 'weatherObservation',
        ),
        // Result : returns the wikipedia entries within the bounding box 
        // Info: http://www.geonames.org/export/wikipedia-webservice.html#wikipediaBoundingBox
        // Expected Results: view-source:http://api.geonames.org/wikipediaBoundingBoxJSON?formatted=true&north=44.1&south=-9.9&east=-22.4&west=55.2&username=demo&style=full
        'wikipediaBoundingBox' => array(
            'parameters' => array('north','south','east','west','maxRows','lang'),
            'root'   => 'geonames',
        ),
        // Result : returns the wikipedia entries found for the searchterm
        // Info: http://www.geonames.org/export/wikipedia-webservice.html#wikipediaSearch
        // Expected Results: view-source:http://api.geonames.org/wikipediaSearchJSON?formatted=true&q=london&maxRows=10&username=demo&style=full
        'wikipediaSearch' => array(
   			'parameters' => array('q','title','lang','maxRows'),
            'root'   => 'geonames',
        ),
    );
	/**
	 * 
	 * class constructor
	 * @param string $username optional
	 */
	public function __construct( $username='' ){
		$this->setUsername($username);
	}
	/**
	 * 
	 * Property set username
	 * @param string $username sets the username to use with the webservice
	 */
    public function setUsername( $username ){
    	$this->_username = $username;
    }
    /**
     * 
     * Property get username
     */
    public function getUsername(){
    	return $this->_username;
    }
    /**
     * 
     * Returns array of supported methods by the webservice
     */
    public function getSupportedMethods(){
    	return $this->_supportedMethods;
    }
    /**
     * 
     * Returns the array of parameters for specific methdo
     * @param unknown_type $method
     */
    public function methodParams($method){
    	if(!array_key_exists($method, $this->supportedMethods))
    		return false;
    	return isset($this->supportedMethods[$method]['parameters'])?$this->supportedMethods[$method]['parameters']:array();
    }
    /**
     * (non-PHPdoc)
     * @see CComponent::__get()
     */
    public function __get($name){
    	if(null !== $this->_results && isset($this->_results[$name])){
	    	return $this->_results[$name];
    	}
    	
    	return parent::__get($name);
    }
    /**
     * (non-PHPdoc)
     * @see CComponent::__isset()
     */
    public function __isset($name){
    	if(isset($this->_results[$name])) return true;
    	
    	return parent::__isset($name);
    }
    /**
     * (non-PHPdoc)
     * @see CComponent::__call()
     */
    public function __call($name, $parameters=array()){
    	if(!array_key_exists($name, $this->supportedMethods)){
    		return parent::__call($name, $parameters);
    	}
    	$count = count($parameters);
    	
    	$result = $this->_request($name, ($count?$parameters[0]:array()));
    	
    	$this->_results = $result;
    	
    	$this->_checkResponse();
    	
    	if(isset($this->supportedMethods[$name]['root'])) $this->_parseRoot($this->supportedMethods[$name]['root']);
    	
    	// returns as array
    	return $result;
    }
    /**
     * 
     * Parses the array to convert results to 
     * EGeoNameResult class to easy the access to 
     * the array of results. Only transforms first level
     * of results, subsequent arrays are left as they are
     * @param array $root
     */
    private function _parseRoot($root){
    	if(isset($this->_results[$root]) && is_array($this->_results[$root])){
    		$e = current($this->_results[$root]);
    		if(is_array($e)){
    			$cnt = count($this->_results[$root]);
	    		for($i=0; $i<$cnt; $i++){
	    			$this->_results[$root][$i] = new EGeoNameResult($this->_results[$root][$i]);
	    		}
    		}else $this->_results[$root] = new EGeoNameResult($this->_results[$root]);
    	}
    }
    /**
     * 
     * Checks for webervice errors
     * @throws CException
     */
 	private function _checkResponse(){
	    if (isset($this->_results['status']['value'])
            && isset($this->_results['status']['message'])) {
         
            throw new CException('Geonames error response:  ' . 
            	$this->_results['status']['message'],
                $this->_results['status']['value']
            );
        }
    }
    /**
     * 
     * Calls webservice
     * @param string $method name to make the request
     * @param array $parameters
     */
    private function _request($method, $parameters)
    {
    	if(!is_array($parameters)) $params = split('&', $parameters);
    	
    	$parameters = array_merge(array_fill_keys($this->methodParams($method),null),$parameters);
    	$pairs = '';
    	
    	
    	$uri = $this->_api.$method.'JSON';
    	
    	if(null !== $this->getUsername()){
    		$parameters['username'] = $this->getUsername();
    	}
    	
    	foreach($parameters as $key=>$val){
    		if(null === $val) continue;
    		$pairs[] = urlencode($key).'='.urlencode($val);
    	}
    	
        if( function_exists('curl_version')  ){
	    	$ch = curl_init();
	 
			curl_setopt($ch, CURLOPT_URL, $uri);
			curl_setopt($ch, CURLOPT_HEADER,0);
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch,  CURLOPT_POSTFIELDS, implode('&',$pairs));
		 
			$raw_data = curl_exec($ch);
			curl_close($ch);
	  	}
	  	else // no CUrl, try differently
	    	$raw_data = file_get_contents($uri.'?'.implode('&',$pairs));
   
        return CJSON::decode($raw_data);
    }
}
/**
 * 
 * EGeoNameResult
 * 
 * Easies the access of EGeoNameService results by
 * allowing array elements to be accessed as properties 
 * of an object
 * @author antonio
 *
 */
class EGeoNameResult {
	/**
	 * 
	 * Holds array elements of a result
	 * @var array
	 */
	protected $_vars = array();
	/**
	 * 
	 * Class constructor
	 * @param array $props
	 */
	public function __construct( $props ){
		if(is_array($props))
			$this->_vars = $props;
	}
	/**
	 * 
	 * Returns a property value. Do not call this method. This is a PHP magic method
	 * overrided to allow using the following syntax
	 * <pre>
	 * 	$egeoresult->propertyName;
	 * </pre>
	 * @param string $name the property name
	 * @throws CException
	 */
	public function __get($name){
		if(array_key_exists($name, $this->_vars)) return $this->_vars[$name];
		
		throw new CException(Yii::t('EGeoNames','Property "{class}.{property}" is not defined.',
			array('{class}'=>get_class($this), '{property}'=>$name)));
	}
	/**
	 * 
	 * Checks if a property value is null.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using isset() to detect if a component property is set or not.
	 * @param string $name the property to check
	 */
	public function __isset($name){
		return isset($this->_vars[$name]);
	}
}