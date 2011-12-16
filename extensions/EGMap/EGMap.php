<?php

/**
 * 
 * EGMap Google Map class
 * Inspired from
 * the amazing work of Symphony
 * GMap class. 
 * 
 * I try to keep comments of the authors to
 * functions
 * 
 * @link https://github.com/fabriceb/sfEasyGMapPlugin
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
class EGMap extends EGMapBase {
	// This map type displays a normal street map.
	const TYPE_ROADMAP = 'google.maps.MapTypeId.ROADMAP';
	// This map type displays maps with physical features such as terrain and vegetation.
	const TYPE_TERRAIN = 'google.maps.MapTypeId.TERRAIN';
	// This map type displays a transparent layer of major streets on satellite images.
	const TYPE_HYBRID = 'google.maps.MapTypeId.HYBRID';
	// This map type displays satellite images.
	const TYPE_SATELLITE = 'google.maps.MapTypeId.SATELLITE';

	// displays the array of controls as buttons in a horizontal bar as is shown on Google Maps.
	const MAPTYPECONTROL_STYLE_HORIZONTAL_BAR = 'google.maps.MapTypeControlStyle.HORIZONTAL_BAR';
	// displays a single button control allowing you to select the map type via a dropdown menu.
	const MAPTYPECONTROL_STYLE_DROPDOWN_MENU = 'google.maps.MapTypeControlStyle.DROPDOWN_MENU';
	// displays the "default" behavior, which depends on screen size and may change in future versions of the API
	const MAPTYPECONTROL_STYLE_DEFAULT = 'google.maps.MapTypeControlStyle.DEFAULT';

	// displays a mini-zoom control, consisting of only + and - buttons. This 
	// style is appropriate for small maps. On touch devices, this control displays 
	// as + and - buttons that are responsive to touch 
	const ZOOMCONTROL_STYLE_SMALL = 'google.maps.ZoomControlStyle.SMALL';
	// displays the standard zoom slider control. On touch devices, this control 
	// displays as + and - buttons that are responsive to touch events.
	const ZOOMCONTROL_STYLE_LARGE = 'google.maps.ZoomControlStyle.LARGE';
	// picks an appropriate zoom control based on the map's size and the device on 
	// which the map is running.
	const ZOOMCONTROL_STYLE_DEFAULT = 'google.maps.ZoomControlStyle.DEFAULT';

	/**
	 * 
	 * Available plugins
	 * @var array
	 */
	private $plugins = array(
		'EGMapMarkerWithLabel' => array('js' => array('markerwithlabel_packed.js'), 'flag' => false),
		'EGMapKeyDragZoom' => array('js' => array('keydragzoom_packed.js'), 'flag' => false),
		'EGMapMarkerClusterer' => array('js' => array('markerclusterer_packed.js'), 'flag' => false),
		'EGMapLatLonControl' => array('js' => array('latloncontrol.js'), 'flag' => false),
		'EGMapKMLService' => array('js' => array('geoxml3.js'), 'flag' => false),
		'EGMapInfoBox' => array('js'=> array('infobox_packed.js'), 'flag'=> false)
	);

	/**
	 * 
	 * Folder reference to the registered plugin assets
	 * @var string
	 */
	private $pluginDir = null;

	/**
	 * 
	 * HTML document Id
	 * @var string
	 */
	private $_containerId;

	/**
	 * 
	 * Container HTML attributes 
	 * @var array
	 */
	private $_htmlOptions = array();

	/**
	 * 
	 * Container CSS options
	 * <pre>
	 * 	array('width'=>'512px','height'=>'512px');
	 * </pre>
	 * @var array
	 */
	private $_styleOptions = array('width' => '512px', 'height' => '512px');

	/**
	 * 
	 * default Google Map Options
	 * @var array
	 */
	protected $options = array(
		// boolean  If true, do not clear the contents of the Map div.  
		'noClear ' => null,
		// Enables/disables zoom and center on double click. true by default.
		'disableDoubleClickZoom' => null,
		// string Color used for the background of the Map div. This color will be visible when tiles have not yet loaded as a user pans.  
		'backgroundColor' => null,
		// string The name or url of the cursor to display on a draggable object.  
		'draggableCursor' => null,
		// string The name or url of the cursor to display when an object is dragging.  
		'draggingCursor' => null,
		// boolean If false, prevents the map from being dragged. Dragging is enabled by default.  
		'draggable' => null,
		// boolean If true, enables scrollwheel zooming on the map. The scrollwheel is disabled by default.  
		'scrollwheel' => null,
		// boolean If false, prevents the map from being controlled by the keyboard. Keyboard shortcuts are enabled by default.  
		'keyboardShortcuts' => null,
		// LatLng The initial Map center. Required.  
		'center' => null,
		// number The initial Map zoom level. Required.  
		'zoom' => null,
		// The maximum zoom level which will be displayed on the map. If omitted, or set to 
		// null, the maximum zoom from the current map type is used instead.
		'maxZoom' => null,
		// The minimum zoom level which will be displayed on the map. If omitted, or set to 
		// null, the minimum zoom from the current map type is used instead.
		'minZoom' => null,
		// The enabled/disabled state of the zoom control.
		// true by default
		'zoomControl' => null,
		// http://code.google.com/intl/en-EN/apis/maps/documentation/javascript/reference.html#ZoomControlStyle
		'zoomControlStyle' => null,
		// Of type named array
		// http://code.google.com/intl/en-EN/apis/maps/documentation/javascript/reference.html#ZoomControlOptions
		'zoomControlOptions' => null,
		// The initial enabled/disabled state of the Street View pegman control.
		'streetViewControl' => null,
		// The initial display options for the Street View pegman control.
		// Of type named array
		// http://code.google.com/intl/en-EN/apis/maps/documentation/javascript/reference.html#streetViewControlOptions
		'streetViewControlOptions' => null,
		// string The initial Map mapTypeId. Required.  
		'mapTypeId' => self::TYPE_ROADMAP,
		// boolean Enables/disables all default UI. May be overridden individually.  
		'disableDefaultUI' => null,
		// boolean The initial enabled/disabled state of the Map type control.  
		'mapTypeControl' => null,
		// MapTypeControl options The initial display options for the Map type control.  
		// Of type named array 
		// http://code.google.com/intl/en-EN/apis/maps/documentation/javascript/reference.html#MapTypeControlOptions
		'mapTypeControlOptions' => null,
		// The enabled/disabled state of the pan control.
		'panControl' => null,
		// The display options for the pan control.
		// http://code.google.com/intl/en-EN/apis/maps/documentation/javascript/reference.html#PanControlOptions
		'panControlOptions' => null,
		// boolean The initial enabled/disabled state of the scale control.  
		'scaleControl' => null,
		// ScaleControl options The initial display options for the scale control.  
		// http://code.google.com/intl/en-EN/apis/maps/documentation/javascript/reference.html#ScaleControlOptions
		// Of type named array
		'scaleControlOptions' => null,
		// boolean The initial enabled/disabled state of the navigation control.  
		'navigationControl' => null,
		// NavigationControl options The initial display options for the navigation control.  
		// http://code.google.com/intl/en-EN/apis/maps/documentation/javascript/3.2/reference.html#NavigationControlOptions
		// Of type named array
		'navigationControlOptions' => null
	);

	/**
	 * 
	 * Where the map should be appended to
	 * (refer to registerMap Script)
	 * It can be any valid javascript #id identifier
	 */
	private $_appendTo = null;

	/**
	 * 
	 * If enabled will hold a reference to a 
	 * EGMapKeyDragZoom object
	 * @var EGMapKeyDragZoom
	 */
	private $_keyDrag = null;

	/**
	 * 
	 * If enabled will hold a reference to a 
	 * EGMapClusteredManager object
	 */
	private $_markerClusterer = null;

	/**
	 * 
	 * If enabled will hold a reference to a 
	 * EGMapLatLonControl object
	 */
	private $_latLonControl = null;

	/**
	 * 
	 * If enabled will hold a reference to a 
	 * EGMapKMLService object
	 */
	private $_kmlService = null;

	/**
	 * @todo replace following variables to
	 * a CMap object with CTypedList | CTypedMap Collections
	 * $resources = new CMap()
	 * $resources['markers'] = new CTypedList('EGMapMarker');
	 * $resources['variables'] = new CMap();
	 * $resources['events']
	 */
	protected $resources;

	/**
	 * the interface to the Google Maps API web service
	 */
	protected $gMapClient = null;

	/**
	 * Constructs a Google Map PHP object
	 *
	 * @param array $options Google Map Options
	 * @param array $htmlOptions Container HTML attributes
	 */
	public function __construct($options=array(), $htmlOptions=array())
	{

		$this->resources = new CMap();

		$this->setOptions($options);
		$this->setHtmlOptions($htmlOptions);

		$this->gMapClient = new EGMapClient();
	}

	/**
	 * 
	 * Sets the HTML attributes of the container
	 * @param array $options
	 */
	public function setHtmlOptions($options)
	{
		if (is_scalar($options))
			return;
		$this->_htmlOptions = array_merge($this->_htmlOptions, $options);
	}

	/**
	 * 
	 * Returns the HTML attributes of the container
	 * @return array
	 */
	public function getHtmlOptions()
	{
		return $this->_htmlOptions;
	}

	/**
	 * 
	 * Sets Google Map Options
	 * @param array $options
	 * 
	 */
	public function setOptions($options)
	{
		$this->options = CMap::mergeArray($this->options, $options);
	}

	/**
	 * 
	 * Returns the Google API key
	 * @see EGMapClient
	 * @return string $key
	 */
	public function getAPIKey()
	{
		return $this->getGMapClient()->getAPIKey();
	}

	/**
	 * 
	 * Sets a Google API key for a specific domain
	 * @param string $domain
	 * @param string $key
	 */
	public function setAPIKey($domain, $key)
	{
		$this->getGMapClient()->setAPIKey($domain, $key, true);
	}

	/**
	 * Gets an instance of the interface to the Google Map web geocoding service
	 *
	 * @return EGMapClient
	 */
	public function getGMapClient()
	{
		if (null === $this->gMapClient)
			$this->gMapClient = new EGMapClient();

		return $this->gMapClient;
	}

	/**
	 * Sets an instance of the interface to the Google Map web geocoding service
	 *
	 * @param EGMapClient
	 */
	public function setGMapClient($gMapClient)
	{
		$this->gMapClient = $gMapClient;
	}

	/**
	 * Geocodes an address
	 * @param string $address
	 * @return GMapGeocodedAddress
	 * @author Fabrice Bernhard
	 */
	public function geocode($address)
	{
		$address = trim($address);

		$gMapGeocodedAddress = new EGMapGeocodedAddress($address);
		$accuracy = $gMapGeocodedAddress->geocode($this->getGMapClient());

		if ($accuracy)
			return $gMapGeocodedAddress;

		return null;
	}

	/**
	 * Geocodes an address and returns additional normalized information
	 * @param string $address
	 * @return GMapGeocodedAddress
	 * @author Fabrice Bernhard
	 * @since 2010-12-22 Yii Modified Antonio Ramirez
	 */
	public function geocodeXml($address)
	{
		$address = trim($address);

		$gMapGeocodedAddress = new EGMapGeocodedAddress($address);
		$gMapGeocodedAddress->geocodeXml($this->getGMapClient());

		return $gMapGeocodedAddress;
	}

	/**
	 * Returns the ID of the widget or generates a new one if requested.
	 * @param boolean $autoGenerate whether to generate an ID if it is not set previously
	 * @return string id of the widget.
	 * @author Antonio Ramirez
	 */
	public function getContainerId($autoGenerate=true)
	{
		if ($this->_containerId !== null)
			return $this->_containerId;
		else if ($autoGenerate)
			return $this->_containerId = 'EGMapContainer' . parent::$_counter++;
	}

	/**
	 * 
	 * Sets content layer ID
	 * @param integer $id
	 * @author Antonio Ramirez
	 */
	public function setContainerId($id)
	{
		$this->_containerId = $id;
	}

	/**
	 * 
	 * Sets the id of the layer where the maps should be rendered
	 * @param string $id ie. #idcontainer
	 */
	public function appendMapTo($id)
	{
		if (substr(ltrim($id), 0, 1) != '#' && $id != 'body')
			throw new CException(Yii::t('EGMap', 'The id of the layer doesnt seem a correct ID (not CSS selector) <br/>Function: ' . __FUNCTION__));
		$this->_appendTo = $id;
	}

	/**
	 * Defines one attributes of the div container
	 * Styles are defined differently 
	 * @link $this->setStyles
	 * @param array $htmlOptions of attributes
	 * @author Antonio Ramirez
	 */
	public function setContainerOptions($htmlOptions)
	{
		if (is_scalar($htmlOptions))
			throw new CException(Yii::t('EGMap', 'setContainerOptions: $htmlOptions must be an array'));

		if (isset($htmlOptions['id']))
			$this->setContainerId($htmlOptions['id']);
		$this->_htmlOptions = $htmlOptions;
	}

	/**
	 * 
	 * Returns the attribute options of the container
	 * @return array htmlOptions
	 * @author Antonio Ramirez
	 */
	public function getContainerOptions()
	{
		return $this->_htmlOptions;
	}

	/**
	 * returns the Html for the Google map container
	 * @param Array $options Style options of the HTML container
	 * @return string $container
	 * @since 2010-12-22 Yii modified Antonio Ramirez
	 */
	public function getContainer($styles=array(), $attributes=array())
	{
		$options = array_merge($this->_htmlOptions, array('id' => $this->getContainerId()));
		if (!isset($options['style']))
			$options['style'] = '';

		foreach ($this->_styleOptions as $style => $value)
		{
			$options['style'] .= $style . ':' . $value . ';';
		}

		return CHtml::tag('div', $options, '', true);
	}

	/**
	 * 
	 * @return string
	 * @author fabriceb
	 * @since 2009-08-20
	 * @since 2011-01-21 Modified by Antonio Ramirez
	 * 		 Improved algorithm
	 */
	public function optionsToJs()
	{
		return $this->encode($this->options);
	}

	/**
	 * 
	 * Registers the Javascript required for the Google map
	 * @param array $afterInit -javascript code to be rendered after init call
	 * @param string $language -preferred language setting for the results
	 * @param string $region -top level geographic domain 
	 * @param ClientScript::CONSTANT $position -where to render the script
	 * @since 2010-12-22 Antonio Ramirez (inspired by sfGMap Plugin of Fabriceb)
	 * @since 2011-01-09 Antonio Ramirez 
	 * 		  removed deprecated initialization procedures //$init_events[] = $this->getIconsJs();
	 * @since 2011-01-22 Antonio Ramirez
	 * 		  Added support for key drag and marker clusterer plugin
	 * @since 2011-03-10 Matt Kay
  	 * 		  Added polygon support (added to init_events)
	 * @since 2011-03-23 Antonio Ramirez
	 *		  Added circles and rectangles support
	 */
	public function registerMapScript($afterInit=array(), $language = null, $region = null, $position = CClientScript::POS_LOAD)
	{
		// TODO: include support in the future
		$params = 'sensor=false';

		if ($language !== null)
			$params .= '&language=' . $language;
		if ($region !== null)
			$params .= '&region=' . $region;

		CGoogleApi::init();
		CGoogleApi::register('maps', '3', array('other_params' => $params));

		$this->registerPlugins();

		$js = '';

		$init_events = array();
		if (null !== $this->_appendTo)
		{
			$init_events[] = "$('{$this->getContainer()}').appendTo('{$this->_appendTo}');" . PHP_EOL;
		}
		$init_events[] = 'var mapOptions = ' . $this->encode($this->options) . ';' . PHP_EOL;
		$init_events[] = $this->getJsName() . ' = new google.maps.Map(document.getElementById("' . $this->getContainerId() . '"), mapOptions);' . PHP_EOL;


		// add some more events
		$init_events[] = $this->getEventsJs();
		$init_events[] = $this->getMarkersJs();
		$init_events[] = $this->getDirectionsJs();
		$init_events[] = $this->getPluginsJs();
		$init_events[] = $this->getPolygonsJs();
		$init_events[] = $this->getCirclesJs();
		$init_events[] = $this->getRectanglesJs();

		if (is_array($afterInit))
		{
			foreach ($afterInit as $ainit)
				$init_events[] = $ainit;
		}
		if ($this->getGlobalVariable($this->getJsName() . '_info_window'))
			$init_events[] = $this->getJsName() . '_info_window=new google.maps.InfoWindow();';
		if ($this->getGlobalVariable($this->getJsName() . '_info_box') && $this->resources->itemAt('infobox_config'))
			$init_events[] = $this->getJsName (). '_info_box=new InfoBox('.
				$this->resources->itemAt('infobox_config').');';
		
		// declare the Google Map Javascript object as global
		$this->addGlobalVariable($this->getJsName(), 'null');

		$js = $this->getGlobalVariables();

		Yii::app()->getClientScript()->registerScript('EGMap_' . $this->getJsName(), $js, CClientScript::POS_HEAD);

		$js = 'function ' . $this->_containerId . '_init(){' . PHP_EOL;
		foreach ($init_events as $init_event)
		{
			if ($init_event)
			{
				$js .= $init_event . PHP_EOL;
			}
		}
		$js .= '
			  } google.maps.event.addDomListener(window, "load",' . PHP_EOL . $this->_containerId . '_init);' . PHP_EOL;

		Yii::app()->getClientScript()->registerScript($this->_containerId . time(), $js, CClientScript::POS_END);
	}

	/**
	 * @return string javascript code from plugins
	 */
	public function getPluginsJs()
	{
		$return = '';
		if (null !== $this->_markerClusterer)
			$return .= $this->_markerClusterer->toJs($this->getJsName());
		if (null !== $this->_keyDrag)
			$return .= $this->_keyDrag->toJs($this->getJsName());
		if (null !== $this->_latLonControl)
			$return .= $this->_latLonControl->toJs($this->getJsName());
		if (null !== $this->_kmlService)
			$return .= $this->_kmlService->toJs($this->getJsName());
		return $return;
	}

	/**
	 * 
	 * Enables LatLonControl plugin
	 * 
	 */
	public function enableKMLService($url, $localhost = false)
	{
		if (true === $localhost)
			$this->registerPlugin('EGMapKMLService');
		$this->_kmlService = new EGMapKMLService($url);
	}

	/**
	 * 
	 * Disables LatLonControl plugin
	 */
	public function disableKMLService()
	{
		$this->registerPlugin('EGMapKMLService', false);
		$this->_kmlService = null;
	}

	/**
	 * 
	 * Enables LatLonControl plugin
	 * 
	 */
	public function enableLatLonControl()
	{
		$this->registerPlugin('EGMapLatLonControl');
		$this->_latLonControl = new EGMapLatLonControl();
	}

	/**
	 * 
	 * Disables LatLonControl plugin
	 */
	public function disableLatLonControl()
	{
		$this->registerPlugin('EGMapLatLonControl', false);
		$this->_latLonControl = null;
	}

	/**
	 * 
	 * Enables Marker Clusterer Plugin
	 * @param EGMapMarkerClusterer $markerclusterer
	 * @author Antonio Ramirez
	 */
	public function enableMarkerClusterer(EGMapMarkerClusterer $markerclusterer)
	{
		$this->registerPlugin('EGMapMarkerClusterer');
		$this->_markerClusterer = $markerclusterer;
	}

	/**
	 * 
	 * Disables Marker Clusterer Plugin
	 * @author Antonio Ramirez
	 */
	public function disableMarkerClusterer()
	{
		$this->registerPlugin('EGMapMarkerClusterer');
		$this->_markerClusterer = null;
	}

	/**
	 * 
	 * Enables Key drag Zoom plugin
	 * @param EGMapKeyDragZoom $dragzoom
	 * @author Antonio Ramirez
	 */
	public function enableKeyDragZoom(EGMapKeyDragZoom $dragzoom)
	{
		$this->registerPlugin('EGMapKeyDragZoom');
		$this->_keyDrag = $dragzoom;
	}

	/**
	 * 
	 * Disables Key Drag Zoom Plugin
	 */
	public function disableKeyDragZoom()
	{
		$this->registerPlugin('EGMapKeyDragZoom', false);
		$this->_keyDrag = null;
	}

	/**
	 * 
	 * Lazy Programmer's function to register the javascript needed and display HTML
	 * map container
	 * @param array $afterInit -javascript code to be rendered after init call
	 * @param string $language -preferred language setting for the results
	 * @param string $region -top level geographic domain 
	 * @param ClientScript::CONSTANT $position -where to render the script
	 */
	public function renderMap($afterInit=array(), $language = null, $region = null, $position = CClientScript::POS_LOAD)
	{

		$this->registerMapScript($afterInit, $language, $region, $position);
		if (null === $this->_appendTo)
			echo $this->getContainer();
	}

	/**
	 * @param EGMapMarker $marker a marker to be put on the map
	 * @since 2011-01-11 added support for global infowindow
	 * @since 2011-01-22 added support for EGMapMarkerWithLabel plugin
	 * @since 2011-01-23 fixed info window shared
	 */
	public function addMarker(EGMapMarker $marker)
	{
		if (null === $this->resources->itemAt('markers'))
			$this->resources->add('markers', new CTypedList('EGMapMarker'));
		if ($marker->getHtmlInfoWindow() && $marker->htmlInfoWindowShared() && !$this->getGlobalVariable($this->getJsName() . '_info_window'))
			$this->addGlobalVariable($this->getJsName() . '_info_window', 'null');
		if ($marker->getHtmlInfoBox() && $marker->htmlInfoBoxShared() && !$this->getGlobalVariable($this->getJsName() . '_info_box'))
		{
			$this->addGlobalVariable($this->getJsName() . '_info_box', 'null');
			$this->resources->add('infobox_config',$marker->getHtmlInfoBox()->getEncodedOptions());
			$this->registerPlugin('EGMapInfoBox');
		}
		if ($marker instanceof EGMapMarkerWithLabel && !$this->pluginRegistered('EGMapMarkerWithLabel'))
			$this->registerPlugin('EGMapMarkerWithLabel');
		$this->resources->itemAt('markers')->add($marker);
	}
	
	/**
	 * @param EGMapPolygon $polygon a polygon to be put on the map
	 * @since 2011-03-10 Matt Kay
	 * 		Added this function for polygons based on addMarker
	 * @since 2011-17-12 Added info window support
	 */
	public function addPolygon(EGMapPolygon $polygon)
	{
		if (null === $this->resources->itemAt('polygons'))
			$this->resources->add('polygons', new CTypedList('EGMapPolygon'));
		if ($polygon->getHtmlInfoWindow() && $polygon->htmlInfoWindowShared() && !$this->getGlobalVariable($this->getJsName() . '_info_window'))
			$this->addGlobalVariable($this->getJsName() . '_info_window', 'null');
		$this->resources->itemAt('polygons')->add($polygon);
	}

	/**
	 * @param EGMapCircle $circle a circle to be put on the map
	 * @since 2011-03-23 Antonio Ramirez Cobos
	 */
	public function addCircle(EGMapCircle $circle)
	{
		if (null === $this->resources->itemAt('circles'))
			$this->resources->add('circles', new CTypedList('EGMapCircle'));
		if ($circle->getHtmlInfoWindow() && $circle->htmlInfoWindowShared() && !$this->getGlobalVariable($this->getJsName() . '_info_window'))
			$this->addGlobalVariable($this->getJsName() . '_info_window', 'null');
		$this->resources->itemAt('circles')->add($circle);
	}

	/**
	 * @param EGMapRectangle $rectangle a rectangle to be put on the map
	 * @since 2011-03-23 Antonio Ramirez Cobos
	 */
	public function addRectangle(EGMapRectangle $rectangle)
	{
		if (null === $this->resources->itemAt('rectangles'))
			$this->resources->add('rectangles', new CTypedList('EGMapRectangle'));
		if ($rectangle->getHtmlInfoWindow() && $rectangle->htmlInfoWindowShared() && !$this->getGlobalVariable($this->getJsName() . '_info_window'))
			$this->addGlobalVariable($this->getJsName() . '_info_window', 'null');
		$this->resources->itemAt('rectangles')->add($rectangle);
	}
	/**
	 * @param EGMapMarker[] $markers marker to be put on the map
	 * @since 2011-01-22 Antonio Ramirez
	 * 		 Added support for EGMapMarkerWithLabel plugin
	 */
	public function setMarkers(CTypedList $markers)
	{
		foreach ($markers as $marker)
		{
			if (!$marker instanceof EGMapMarker)
				throw new CException(Yii::t('EGMap', 'Markers collection must be of base class EGMapMarker'));
			if ($marker instanceof EGMapMarkerWithLabel && !$this->pluginRegistered('EGMapMarkerWithLabel'))
				$this->registerPlugin('EGMapMarkerWithLabel');
		}
		$this->resources->add('markers', $markers);
	}

	/**
	 * @param EGMapEvent $event an event to be attached to the map
	 */
	public function addEvent(EGMapEvent $event)
	{
		if (null === $this->resources->itemAt('events'))
			$this->resources->add('events', new CTypedList('EGMapEvent'));

		$this->resources->itemAt('events')->add($event);
	}

	/**
	 * $directions getter
	 *
	 * @return array $directions
	 * @author Vincent Guillon 
	 * @since 2009-11-13 17:18:29
	 */
	public function getDirections()
	{

		return $this->resources->itemAt('directions');
	}

	/**
	 * $directions setter
	 *
	 * @param CTypedList $directions
	 * @author Vincent Guillon 
	 * @since 2009-11-13 17:21:18
	 */
	public function setDirections($directions = null)
	{

		if ($directions instanceof CTypedList)
			$this->resources->add('directions', $directions);
	}

	/**
	 * Add direction to list ($this->directions)
	 *
	 * @param EGMapDirection $directions
	 * @author Antonio Ramirez
	 */
	public function addDirection(EGMapDirection $direction)
	{
		if (null === $this->resources->itemAt('directions'))
			$this->resources->add('directions', new CTypedList('EGMapDirection'));

		$this->resources->itemAt('directions')->add($direction);
	}

	/**
	 * Returns the javascript string which defines the markers
	 * @return string
	 * @since 2011-01-22 modified Antonio Ramirez
	 * 		 Added support for marker clusterer
	 */
	public function getMarkersJs()
	{
		$return = '';
		if (null !== $this->resources->itemAt('markers'))
		{
			foreach ($this->resources->itemAt('markers') as $marker)
			{
				$return .= $marker->toJs($this->getJsName());
				if (null !== $this->_markerClusterer)
					$this->_markerClusterer->addMarker($marker);
				$return .= "\n      ";
			}
		}
		return $return;
	}

	/**
	 * Returns the javascript string which defines events linked to the map
	 * 
	 * @return string
	 * @since 2011-01-21 handles different type of events now
	 */
	public function getEventsJs()
	{

		$return = '';
		if (null !== $this->resources->itemAt('events'))
		{
			foreach ($this->resources->itemAt('events') as $event)
			{
				$return .= $event->toJs($this->getJsName());
				$return .= "\n";
			}
		}
		return $return;
	}
	
	/**
	 * Returns the javascript string which defines the polygons
	 * @return string
	 * @since 2011-03-10 Matt Kay
	 * 		 Added function based on getMarkersJs
	 */
	public function getPolygonsJs()
	{
		$return = '';
		if (null !== $this->resources->itemAt('polygons'))
		{
			foreach ($this->resources->itemAt('polygons') as $polygon)
			{
				$return .= $polygon->toJs($this->getJsName());
				$return .= "\n      ";
			}
		}
		return $return;
	}

	/**
	 * Returns the javascript string which defines the circles
	 * @return string
	 * @since 2011-03-23 Antonio Ramirez
	 * 	
	 */
	public function getCirclesJs()
	{
		$return = '';
		if (null !== $this->resources->itemAt('circles'))
		{
			foreach ($this->resources->itemAt('circles') as $circle)
			{
				$return .= $circle->toJs($this->getJsName());
				$return .= "\n      ";
			}
		}
		return $return;
	}

	/**
	 * Returns the javascript string which defines rectangles
	 * @return string
	 * @since 2011-03-23 Antonio Ramirez
	 * 	
	 */
	public function getRectanglesJs()
	{
		$return = '';
		if (null !== $this->resources->itemAt('rectangles'))
		{
			foreach ($this->resources->itemAt('rectangles') as $rectangle)
			{
				$return .= $rectangle->toJs($this->getJsName());
				$return .= "\n      ";
			}
		}
		return $return;
	}
	
	/**
	 * Get the directions javascript code
	 *
	 * @return string $js_code
	 * @author Antonio Ramirez
	 */
	public function getDirectionsJs()
	{
		$js_code = '';
		if (null !== $this->resources->itemAt('directions'))
		{
			foreach ($this->resources->itemAt('directions') as $direction)
			{
				$js_code .= $direction->toJs($this->getJsName());
				$js_code .= "\n      ";
			}
		}
		return $js_code;
	}

	/**
	 * 
	 * Adds global variables to be set before init function
	 * @param string $name
	 * @param mixed $value
	 */
	public function addGlobalVariable($name, $value='null')
	{
		if (null === $this->resources->itemAt('variables'))
			$this->resources->add('variables', new CMap());

		$this->resources->itemAt('variables')->add($name, $value);
	}

	/**
	 * 
	 * @return global variable if set 
	 */
	public function getGlobalVariable($name)
	{
		if (null === $this->resources->itemAt('variables'))
			return null;

		return $this->resources->itemAt('variables')->itemAt($name);
	}

	/**
	 * 
	 * Removes a global variable
	 * @param string $name of the variable to remove
	 */
	public function removeGlobalVariable($name)
	{
		if (null === $this->resources->itemAt('variables'))
			return;

		$this->resources->itemAt('variables')->remove($name);
	}

	/**
	 * 
	 * @return string global variables in JS format
	 */
	public function getGlobalVariables()
	{
		$return = '';
		if (null !== $this->resources->itemAt('variables'))
		{
			foreach ($this->resources->itemAt('variables') as $name => $value)
			{
				$return .='
  					var ' . $name . ' = ' . $value . ';';
			}
		}
		return $return;
	}

	/**
	 * Defines one style of the div container
	 * @param string $style_tag name of css tag
	 * @param string $style_value value of css tag
	 * @since 2010-12-22 modified for Yii Antonio Ramirez
	 */
	public function setContainerStyle($style_tag, $style_value)
	{
		if (!is_array($this->_styleOptions))
			$this->_styleOptions = array();

		$this->_styleOptions = array_merge($this->_styleOptions, array($style_tag => $style_value));
	}

	/**
	 *
	 * Gets one style of the Google Map div
	 * @param string $style_tag name of css tag
	 * @since 2010-12-22 modified Antonio Ramirez
	 */
	public function getContainerStyle($style_tag)
	{
		if (isset($this->_styleOptions[$style_tag]))
			return $this->_styleOptions[$style_tag];
		return false;
	}

	/**
	 * Sets the center of the map at the beginning
	 *
	 * @param float $lat
	 * @param float $lng
	 * @since 2010-12-22 modified for Yii Antonio Ramirez
	 */
	public function setCenter($lat=null, $lng=null)
	{
		$coord = new EGMapCoord($lat, $lng);
		$this->options['center'] = $coord;
	}

	/**
	 *
	 * @return EGMapCoord
	 */
	public function getCenterCoord()
	{
		return $this->options['center'];
	}

	/**
	 *
	 * @return float Latitude
	 */
	public function getCenterLat()
	{

		return isset($this->options['center']) ? $this->getCenterCoord()->getLatitude() : null;
	}

	/**
	 *
	 * @return float Longitude
	 */
	public function getCenterLng()
	{
		return isset($this->options['center']) ? $this->getCenterCoord()->getLongitude() : null;
	}

	/**
	 * gets the width of the map in pixels according to container style
	 * @return integer
	 * @since 2010-12-22 code reduction Yii Antonio Ramirez
	 */
	public function getWidth()
	{
		if (substr($this->getContainerStyle('width'), -2, 2) != 'px')
			return false;
		return intval(substr($this->getContainerStyle('width'), 0, -2));
	}

	/**
	 * gets the width of the map in pixels according to container style
	 * @return integer
	 * @since 2010-12-22 code reduction Antonio Ramirez
	 */
	public function getHeight()
	{
		if (substr($this->getContainerStyle('height'), -2, 2) != 'px')
			return false;

		return intval(substr($this->getContainerStyle('height'), 0, -2));
	}

	/**
	 * sets the width of the map in pixels
	 *
	 * @param integer | string
	 */
	public function setWidth($width)
	{
		if (is_numeric($width))
		{
			$width = $width . 'px';
		}
		$this->setContainerStyle('width', $width);
	}

	/**
	 * sets the width of the map in pixels
	 *
	 * @param integer | string
	 */
	public function setHeight($height)
	{
		if (is_numeric($height))
		{
			$height = $height . 'px';
		}
		$this->setContainerStyle('height', $height);
	}

	/**
	 * Returns the URL of a static version of the map (when JavaScript is not active)
	 * Supports only markers and basic parameters: center, zoom, size.
	 * @param string $map_type = 'mobile'
	 * @param string $hl Language (fr, en...)
	 * @return string URL of the image
	 * @author Laurent Bachelier
	 * @since 2010-12-22  inserted http_build_query modified Antonio Ramirez
	 */
	public function getStaticMapUrl($maptype='mobile', $hl='es')
	{
		$params = array(
			'maptype' => $maptype,
			'zoom' => $this->getZoom(),
			'key' => $this->getAPIKey(),
			'center' => $this->getCenterLat() . ',' . $this->getCenterLng(),
			'size' => $this->getWidth() . 'x' . $this->getHeight(),
			'hl' => $hl,
			'markers' => $this->getMarkersStatic()
		);
		$pairs = array();

		$params = http_build_query($params);

		return 'http://maps.google.com/staticmap?' . $params; //implode('&',$pairs);
	}

	/**
	 * Returns the static code to create markers
	 * @return string
	 * @author Laurent Bachelier
	 * @since 2010-12-22 Yii modified Antonio Ramirez
	 */
	protected function getMarkersStatic()
	{
		$markers_code = array();
		if (null !== $this->resources->itemAt('markers'))
		{
			foreach ($this->resources->itemAt('markers') as $marker)
			{
				$markers_code[] = $marker->getMarkerStatic();
			}
		}
		return implode('|', $markers_code);
	}

	/**
	 *
	 * calculates the center of the markers linked to the map
	 *
	 * @return EGMapCoord
	 * @since 2010-12-22 modified for Yii Antonio Ramirez
	 */
	public function getMarkersCenterCoord()
	{
		if (null === $this->resources->itemAt('markers'))
			throw new CException(Yii::t('EGMap', 'At least one more marker is necessary for getMarkersCenterCoord to work'));
		//todo: check for markers existence
		return EGMapMarker::getCenterCoord($this->resources->itemAt('markers'));
	}

	/**
	 * sets the center of the map at the center of the markers
	 * @since 2010-12-22 modified for Yii Antonio Ramirez
	 */
	public function centerOnMarkers()
	{
		$center = $this->getMarkersCenterCoord();

		$this->setCenter($center->getLatitude(), $center->getLongitude());
	}

	/**
	 *
	 * calculates the zoom which fits the markers on the map
	 *
	 * @param integer $margin a scaling factor around the smallest bound
	 * @return integer $zoom
	 * @author fabriceb
	 * @since 2009-05-02
	 * @since 2010-12-22 modified for Yii Antonio Ramirez
	 */
	public function getMarkersFittingZoom($margin = 0, $default_zoom = 14)
	{
		if (null === $this->resources->itemAt('markers'))
			throw new CException(Yii::t('EGMap', 'At least one more marker is necessary for getMarkersFittingZoom to work'));
		//todo check for markers existence
		$bounds = EGMapBounds::getBoundsContainingMarkers($this->resources->itemAt('markers'), $margin);

		return $bounds->getZoom(min($this->getWidth(), $this->getHeight()), $default_zoom);
	}

	/**
	 * sets the zoom of the map to fit the markers (uses mercator projection to guess the size in pixels of the bounds)
	 * WARNING : this depends on the width in pixels of the resulting map
	 *
	 * @param integer $margin a scaling factor around the smallest bound
	 * @author fabriceb
	 * @since 2009-05-02
	 */
	public function zoomOnMarkers($margin = 0, $default_zoom = 14)
	{
		$this->options['zoom'] = $this->getMarkersFittingZoom($margin, $default_zoom);
	}

	/**
	 * sets the zoom and center of the map to fit the markers (uses mercator projection to guess the size in pixels of the bounds)
	 *
	 * @param integer $margin a scaling factor around the smallest bound
	 * @author fabriceb
	 * @since 2009-05-02
	 */
	public function centerAndZoomOnMarkers($margin = 0, $default_zoom = 14)
	{
		$this->centerOnMarkers();
		$this->zoomOnMarkers($margin, $default_zoom);
	}

	/**
	 *
	 * @return EGMapBounds
	 * @author fabriceb
	 * @since Jun 2, 2009 fabriceb
	 * @since 2010-12-22 modified for Yii Antonio Ramirez
	 */
	public function getBoundsFromCenterAndZoom()
	{
		return EGMapBounds::getBoundsFromCenterAndZoom($this->getCenterCoord(), $this->zoom, $this->getWidth(), $this->getHeight());
	}

	/**
	 * backwards compatibility
	 * @param string[] $api_keys
	 * @return string
	 * @author fabriceb
	 * @since Jun 17, 2009 fabriceb
	 * @since 2010-12-22 modified for Yii Antonio Ramirez
	 */
	public static function guessAPIKey($api_keys = null)
	{
		return EGMapClient::guessAPIKey($api_keys);
	}

	/**
	 * 
	 * Loops through the plugins and registers its required
	 * assets
	 * @author Antonio Ramirez
	 */
	private function registerPlugins()
	{
		$assetDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
		$assetUrl = Yii::app()->assetManager->publish($assetDir);


		$cs = Yii::app()->getClientScript();

		foreach ($this->plugins as $p)
		{

			if ($p['flag'])
			{
				foreach ($p['js'] as $js)
					$cs->registerScriptFile($assetUrl . "/" . $js, CClientScript::POS_END);
			}
		}
	}

	/**
	 * 
	 * Flags a plugin to register its assets
	 * @param string $plugin name
	 * @param boolean $register
	 */
	private function registerPlugin($plugin, $register=true)
	{
		$this->plugins[$plugin]["flag"] = $register;
	}

	/**
	 * 
	 * Checks whether a plugin has been flagged to be
	 * registered or not
	 * @param string $plugin name
	 * @return boolean true|false
	 */
	private function pluginRegistered($plugin)
	{
		return $this->plugins[$plugin]["flag"];
	}

	/**
	 * 
	 * Encodes an option array into 
	 * appropiate Javascript object
	 * representation
	 * @param mixed $value
	 * @author Antonio Ramirez
	 */
	public static function encode($value)
	{

		if (is_string($value))
		{
			if (strpos($value, 'js:') === 0)
				return substr($value, 3);
			else
				return $value;
		}
		else if ($value === null)
			return 'null';
		else if (is_bool($value))
			return $value ? 'true' : 'false';
		else if (is_integer($value))
			return "$value";
		else if (is_float($value))
		{
			if ($value === -INF)
				return 'Number.NEGATIVE_INFINITY';
			else if ($value === INF)
				return 'Number.POSITIVE_INFINITY';
			else
				return rtrim(sprintf('%.16F', $value), '0');  // locale-independent representation
		}
		else if (is_object($value))
		{
			if (method_exists($value, 'toJs'))
				return $value->toJs();
			return self::encode(get_object_vars($value));
		}
		else if (is_array($value))
		{
			$es = array();
			if (($n = count($value)) > 0 && array_keys($value) !== range(0, $n - 1))
			{

				foreach ($value as $k => $v)
				{
					if (null === $v)
						continue;
					$es[] = $k . ":" . self::encode($v);
				}

				return '{' . implode(',' . PHP_EOL, $es) . '}';
			}
			else
			{
				foreach ($value as $v)
					$es[] = self::encode($v);
				return '[' . implode(',', $es) . ']';
			}
		}
		else
			return '';
	}

}
