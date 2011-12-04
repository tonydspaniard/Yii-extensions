<?php

/**
 * EGMapDirection class
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
class EGMapDirection extends EGMapBase {
	const TRAVEL_MODE_DRIVING = 'google.maps.DirectionsTravelMode.DRIVING';
	const TRAVEL_MODE_WALKING = 'google.maps.DirectionsTravelMode.WALKING';
	const TRAVEL_MODE_BICYCLING = 'google.maps.DirectionsTravelMode.BICYCLING';

	const UNIT_SYSTEM_IMPERIAL = 'google.maps.DirectionsUnitSystem.IMPERIAL';
	const UNIT_SYSTEM_METRIC = 'google.maps.DirectionsUnitSystem.METRIC';

	protected $renderer = null;
	protected $options = array(
		// Whether or not trip alternatives should be provided.
		'avoidHighways' => null,
		// If true, instructs the Directions service to avoids toll roads where possible. Optional.
		'avoidTolls' => null,
		// Location of destination. This can be specified as either a string to be geocoded or a LatLng. Required.
		'destination' => null,
		// If set to true, the DirectionService will attempt to re-order the supplied intermediate waypoints to 
		// minimize overall cost of the route. If waypoints are optimized, inspect DirectionsRoute.waypoint_order 
		// in the response to determine the new ordering.
		'optimizeWaypoints' => null,
		// Location of origin. This can be specified as either a string to be geocoded or a LatLng. Required.
		'origin' => null,
		// Whether or not route alternatives should be provided. Optional.
		'provideRouteAlternatives' => null,
		// Region code used as a bias for geocoding requests.
		'region' => null,
		// Travel mode [DRIVING, WALKING, BICYCLING]
		'travelMode' => self::TRAVEL_MODE_WALKING,
		// Preferred unit system to use when displaying distance. 
		// Defaults to the unit system used in the country of origin.
		'unitSystem' => null,
		// Array of intermediate waypoints. Directions will be calculated from the origin to the destination by way of each waypoint in this array.
		'waypoints' => array(),
	);

	/**
	 * Construct GMapDirection object
	 *
	 * @param EGMapCoord $origin The coordinates of origin
	 * @param EGMapCoord $destination The coordinates of destination
	 * @param string $js_name The js var name
	 * @param array $options Array of option
	 * @author Vincent Guillon <vincentg@theodo.fr>
	 * @since 2009-10-30 17:20:47
	 * @since 2011-01-24 by Antonio Ramirez www.ramirezcobos.com
	 * 		New algorithms
	 * 
	 */
	public function __construct(EGMapCoord $origin, EGMapCoord $destination, $js_name = 'gmap_direction', $options = array())
	{

		$this->origin = $origin;
		$this->destination = $destination;
		$this->setOptions(array_merge($this->options, $options));
		if ($js_name !== 'gmap_direction')
			$this->setJsName($js_name);
	}

	public function setRenderer(EGMapDirectionRenderer $renderer)
	{
		$this->renderer = $renderer;
	}

	public function setOrigin(EGMapCoord $origin)
	{
		$this->options['origin'] = $origin;
	}

	public function setDestination(EGMapCoord $destination)
	{
		$this->options['destination'] = $destination;
	}

	/**
	 * Options setter
	 *
	 * @param array $options
	 * @author Vincent Guillon <vincentg@theodo.fr>
	 * @since 2009-11-13 15:39:46
	 * @since 2011-01-24 by Antonio Ramirez
	 * 		 Modified algorithm
	 */
	public function setOptions($options = null)
	{
		if (isset($options['origin']))
		{
			$this->setOrigin($options['origin']);
			unset($options['origin']);
		}
		if (isset($options['destination']))
		{
			$this->setDestination($options['destination']);
			unset($options['destination']);
		}
		$this->options = array_merge($this->options, $options);
	}

	/**
	 * Options getter
	 *
	 * @return array $this->options 
	 * @author Vincent Guillon <vincentg@theodo.fr>
	 * @since 2009-11-13 15:38:46
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * Generate js code for direction
	 * Inspired by the work of Vincent Guillon <vincentg@theodo.fr>
	 * 
	 * @param string $map_js_name The google map js var name
	 * @return $js_code The generated js to display direction
	 * @author Antonio Ramirez
	 * @since 2010-01-24
	 * 
	 */
	public function toJs($map_js_name = 'map')
	{
		if (null === $this->renderer)
			throw new CException(Yii::t('EGMap', 'No Renderer Service has been provided'));

		$options = $this->getOptions();
		$js_name = $this->getJsName();

		// set map to renderer
		$this->renderer->map = $map_js_name;

		// Construct js code
		$js_code = '';
		$js_code .= $this->renderer->toJs();
		$js_code .= 'var ' . $js_name . ' = new google.maps.DirectionsService();' . "\n";

		// building Request
		$js_code .= 'var ' . $js_name . 'Request = ' . EGMap::encode($this->options) . ';' . "\n";

		$js_code .= $js_name . '.route(' . $js_name . 'Request, function(response, status)' . "\n";
		$js_code .= '{' . "\n";
		$js_code .= '  if (status == google.maps.DirectionsStatus.OK)' . "\n";
		$js_code .= '  {' . "\n";
		$js_code .= '    ' . $this->renderer->getJsName() . '.setDirections(response);' . "\n";
		$js_code .= '  }' . "\n";
		$js_code .= '});' . "\n";

		return $js_code;
	}

}