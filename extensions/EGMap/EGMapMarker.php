<?php

/**
 * 
 * EGMapMarker
 * A GoogleMap Marker
 * 
 * @author Antonio Ramirez
 * 
 * @since 2010-12-22 modified by Antonio Ramirez 
 * 
 * change log:
 * @since 2011-01-21 by Antonio Ramirez
 * - Included support for different types of Markers
 * - Implemented new and specially for EGMap modified version of CJavaScript::encode
 * - Fixed logic bug on setOption function
 * - Removed the need of optionsToJs function
 * - Included option for global info window
 * - included different types of Marker Object support
 * - EGMap::encode deprecates the use of optionsToJs
 * 
 * @TODO: modify $events to CTypeMap('EGMapEvent')
 * 
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
class EGMapMarker extends EGMapBase {

	const DROP = 'google.maps.Animation.DROP';
	const BOUNCE = 'google.maps.Animation.BOUNCE';
	
	protected $options = array(
		//  Map  Map on which to display Marker.  
		'map' => null,
		// LatLng  Marker position. Required.  
		'position' => null,
		// string  Rollover text  
		'title' => null,
		// Icon (string | Marker Image)  for the foreground  
		'icon' => null,
		// Shadow  image  
		'shadow' => null,
		// Object  Image map region for drag/click. Array of x/y values that define the perimeter of the icon.  
		'shape' => null,
		// string  Mouse cursor to show on hover  
		'cursor' => null,
		// boolean  If true, the marker can be clicked  
		'clickable' => null,
		// boolean  If true, the marker can be dragged.  
		'draggable' => null,
		// If false, disables raising and lowering the marker on drag. 
		// This option is true by default.
		'raiseOnDrag' => null,
		// boolean  If true, the marker is visible  
		'visible' => null,
		// boolean  If true, the marker shadow will not be displayed.  
		'flat' => null,
		// number  All Markers are displayed on the map in order of their zIndex, with higher values displaying in front of Markers with lower values. By default, Markers are displayed according to their latitude, with Markers of lower latitudes appearing in front of Markers at higher latitudes.  
		'zIndex' => null,
		//Which animation to play when marker is added to a map.
		'animation'=>null,
	);
	protected $info_window = null;
	protected $info_window_shared = false;
	protected $info_box = null;
	protected $info_box_shared = false;
	protected $events = null;
	protected $custom_properties = array();

	/**
	 * 
	 * Included support for different types of Markers
	 * @var string marker object type (defaults to Marker)
	 */
	protected $marker_object = 'google.maps.Marker';

	/**
	 * @param string $js_name Javascript name of the marker
	 * @param float $lat Latitude
	 * @param float $lng Longitude
	 * @param string $js_name name of the marker variable
	 * @param array $options of the marker
	 * @param EGmapEvent[] array of GoogleMap Events linked to the marker
	 * @author Fabrice Bernhard
	 * @since 2010-12-22 modified by Antonio Ramirez
	 */
	public function __construct($lat, $lng, $options = array(), $js_name='marker', $events=array())
	{
		if ($js_name !== 'marker')
			$this->setJsName($js_name);

		// position wont make any difference here
		// as it will be set afterwards by setPosition
		$this->setOptions($options);

		$this->setPosition(new EGMapCoord($lat, $lng));

		$this->events = new CTypedList('EGMapEvent');

		$this->setEvents($events);
	}

	/**
	 * 
	 * Batch set events by an array of EGMapEvents
	 * @param array $events
	 */
	public function setEvents($events)
	{
		if (!is_array($events))
			throw new CException(Yii::t('EGMap', 'Parameter of "{class}.{method}" {e}.', array('{class}' => get_class($this), '{method}' => 'setEvents', '{e}' => 'must be of type array')));
		if (null === $this->events)
			$this->events = new CTypedList('EGMapEvent');

		foreach ($events as $e)
		{
			$this->events->add($e);
		}
	}

	/**
	 * Sets the animation to the marker when it is rendered to the map
	 * @param string $animation 
	 */
	public function setAnimation( $animation=self::DROP )
	{
		if($animation==self::DROP || $animation==self::BOUNCE)
			$this->options['animation'] = $animation;
		else
			$this->options['animation'] = self::DROP;
	}
	
	/**
	 * Adds an event listener to the marker
	 *
	 * @param EGMapEvent $event
	 */
	public function addEvent(EGMapEvent $event)
	{
		$this->events->add($event);
	}

	/**
	 * Construct from a EGMapGeocodedAddress object
	 *
	 * @param string $js_name
	 * @param EGMapGeocodedAddress $gmap_geocoded_address
	 * @param array $options the marker options
	 * @return EGMapMarker
	 */
	public static function constructFromGMapGeocodedAddress(EGMapGeocodedAddress $gmap_geocoded_address, $options = array(), $js_name='marker')
	{
		return new EGMapMarker($gmap_geocoded_address->getLat(), $gmap_geocoded_address->getLng(), $options, $js_name);
	}

	/**
	 * @param array $options
	 * @author fabriceb
	 * @since 2009-08-21
	 * @modified by Antonio Ramirez
	 */
	public function setOptions($options)
	{
		if (isset($options['title']))
			$options['title'] = CJavaScript::encode($options['title']);

		$this->options = array_merge($this->options, $options);

		// double check position
		if (isset($options['position']))
		{
			$this->setPosition($options['position']);
		}
	}

	/**
	 * sets the coordinates object of the marker
	 * 
	 * @param EGMapCoord $position
	 * @author Antonio Ramirez
	 */
	public function setPosition(EGMapCoord $position)
	{
		$this->options['position'] = $position;
	}

	/**
	 * @return float $lat Javascript latitude  
	 * @author Antonio Ramirez
	 */
	public function getLat()
	{
		return null !== $this->options['position'] ? $this->options['position']->getLatitude() : null;
	}

	/**
	 * @return float $lng Javascript longitude  
	 * @author Antonio Ramirez
	 */
	public function getLng()
	{
		return null !== $this->options['position'] ? $this->options['position']->getLongitude() : null;
	}

	/**
	 * @param string $map_js_name 
	 * @return string Javascript code to create the marker
	 * @author Fabrice Bernhard
	 * @since 2009-08-21
	 * @since 2010-12-22 modified by Antonio Ramirez
	 * @since 2011-01-08 modified by Antonio Ramirez
	 * 		Removed EGMapMarkerImage conversion
	 * @since 2011-01-11 included option for global info window
	 * @since 2011-01-22 included different types of Marker Object support
	 * 				  EGMap::encode deprecates the use of optionsToJs
	 * @since 2011-01-23 fixed logic bug
	 * @since 2011-10-12 added info_box plugin feature
	 * 				  
	 */
	public function toJs($map_js_name = 'map')
	{
		$this->options['map'] = $map_js_name;

		$return = '';
		if (null !== $this->info_window || null !== $this->info_box)
		{
			if ($this->info_window_shared || $this->info_box_shared)
			{
				$info_window_name = $map_js_name . 
					($this->info_window_shared? '_info_window':'_info_box');
				
				$content = $this->info_window? $this->info_window->getContent() : $this->info_box->getContent();
				
				$this->addEvent(
					new EGMapEvent(
						'click',
						// closes automatically others opened :)
						//'if (' . $info_window_name . ') ' . $info_window_name . '.close();' . PHP_EOL .
						$info_window_name . '.setContent(' . $content . ');' . PHP_EOL .
						$info_window_name . '.open(' . $map_js_name . ',' . $this->getJsName() . ');' . PHP_EOL
				));
			} else
			{
				$name = $this->info_window? $this->info_window->getJsName() : $this->info_box->getJsName();
				$this->addEvent(new EGMapEvent('click', $this->info_window->getJsName() . ".open(" . $map_js_name . "," . $this->getJsName() . ");" . PHP_EOL));
				$return .= $this->info_window->toJs();
			}
		}

		$return .='var ' . $this->getJsName() . ' = new ' . $this->marker_object . '(' . EGMap::encode($this->options) . ');' . PHP_EOL;

		foreach ($this->custom_properties as $attribute => $value)
		{
			$return .= $this->getJsName() . "." . $attribute . " = '" . $value . "';" . PHP_EOL;
		}
		foreach ($this->events as $event)
		{
			$return .= $event->getEventJs($this->getJsName()) . PHP_EOL;
		}

		return $return;
	}

	/**
	 * Adds an onlick listener that open a html window with some text 
	 *
	 * @param EGMapInfoWindow $info_window
	 * @param boolean $shared among other markers (unique info_window display)
	 * 
	 * @author Antonio Ramirez
	 * @since 2011-01-23 Added shared functionality for infoWindows
	 */
	public function addHtmlInfoWindow(EGMapInfoWindow $info_window, $shared = true)
	{
		$this->info_window = $info_window;
		$this->info_window_shared = $shared;
		$this->info_box = null;
		$this->info_box_shared = false;
	}
	/**
	 * 
	 * @return boolean if info window is shared or not
	 */
	public function htmlInfoWindowShared()
	{
		return $this->info_window_shared;
	}

	/**
	 * @return EGMapInfoWindow
	 * @author Antonio Ramirez
	 */
	public function getHtmlInfoWindow()
	{
		return $this->info_window;
	}

	public function addHtmlInfoBox(EGMapInfoBox $info_box, $shared = true)
	{
		$this->info_box = $info_box;
		$this->info_box_shared = $shared;
		$this->info_window = null; 
		$this->info_window_shared = false;
	}
	public function htmlInfoBoxShared()
	{
		return $this->info_box_shared;
	}
	public function getHtmlInfoBox()
	{
		return $this->info_box;
	}
	
	/**
	 * Returns the coords code for the static version of Google Maps
	 * @TODO Add support for color and alpha-char
	 * @author Laurent Bachelier
	 * @return string
	 */
	public function getMarkerStatic()
	{

		return $this->getLat() . ',' . $this->getLng();
	}

	/**
	 * 
	 * Sets custom properties to the Marker
	 * @param array $custom_properties
	 */
	public function setCustomProperties($custom_properties)
	{
		if (!is_array($custom_properties))
			throw new CException(Yii::t('EGMap', 'EGMapMarker custom properties must of type array to be set'));
		$this->custom_properties = $custom_properties;
	}

	/**
	 * 
	 * @return array custom properties
	 */
	public function getCustomProperties()
	{

		return $this->custom_properties;
	}

	/**
	 * Sets a custom property to the generated javascript object
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function setCustomProperty($name, $value)
	{
		$this->custom_properties[$name] = $value;
	}

	/**
	 *
	 * @param EGMapMarker[] $markers array of Markers
	 * @return EGMapCoord
	 * @author fabriceb
	 * @since 2009-05-02
	 * @since 2011-01-25 modified by Antonio Ramirez
	 * */
	public static function getMassCenterCoord($markers)
	{
		$coords = array();
		foreach ($markers as $marker)
		{
			array_push($coords, $marker->position);
		}

		return EGMapCoord::getMassCenterCoord($coords);
	}

	/**
	 *
	 * @param EGMapMarker[] $markers array of MArkers
	 * @return EGMapCoord
	 * @author fabriceb
	 * @since 2009-05-02
	 * @since 2011-01-25 modified by Antonio Ramirez
	 * */
	public static function getCenterCoord($markers)
	{
		$bounds = EGMapBounds::getBoundsContainingMarkers($markers);

		return $bounds->getCenterCoord();
	}

	/**
	 * 
	 * @param EGMapBounds $gmap_bounds
	 * @return boolean $is_inside
	 * @author fabriceb
	 * @since Jun 2, 2009 fabriceb
	 */
	public function isInsideBounds(EGMapBounds $gmap_bounds)
	{

		return $this->getGMapCoord()->isInsideBounds($gmap_bounds);
	}

}
