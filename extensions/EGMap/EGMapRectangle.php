<?php

/**
 * 
 * EGMapRectangle
 * A GoogleMap Rectangle
 * 
 * @author Antonio Ramirez Cobos
 * 
 * Example:
 * 
 * $bounds = new EGMapBounds(new EGMapCoord(25.774252, -80.190262),new EGMapCoord(32.321384, -64.75737) );
 * $rec = new EGMapRectangle($bounds);
 * $rec->addHtmlInfoWindow(new EGMapInfoWindow('Hey! I am a rectangle!'));
 * $gMap->addRectangle($rec);
 * 
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
class EGMapRectangle extends EGMapBase {

	protected $options = array(
		// LatLngBounds	The bounds.
		'bounds' => null, // EGMapBounds
		// Indicates whether this Rectangle handles click events. Defaults to true.
		'clickable' => true,
		// Map on which to display Circle.
		'map' => null,
		// stroke color of the edge of the rectangle
		'strokeColor' => '"#FF0000"',
		//stroke opacity of the edge of the rectangle
		'strokeOpacity' => 0.8,
		//stroke weight of the edge of the rectangle
		'strokeWeight' => 2,
		//fill color of the rectangle
		'fillColor' => '"#FF0000"',
		//fill opacity of the rectangle
		'fillOpacity' => 0.35,
		'zIndex' => 0
	);

	/**
	 * 
	 * Info window object attached to the object
	 * @var EGMapInfoWindow
	 */
	protected $info_window = null;

	/**
	 * 
	 * If the Info window is shared or not
	 * @var boolean
	 */
	protected $info_window_shared = false;

	/**
	 * 
	 * Events attached to the object
	 * @var array
	 */
	protected $events = null;

	/**
	 * 
	 * google maps js object name
	 * @var string
	 */
	protected $rectangle_object = 'google.maps.Rectangle';

	/**
	 * 
	 * @param EGMapBounds bounds coordinates of the rectangle
	 * @param string $js_name name of the rectangle variable
	 * @param array $options of the rectangle
	 * @param EGmapEvent[] array of GoogleMap Events linked to the rectangle
	 * @author Antonio Ramirez
	 */
	public function __construct(EGMapBounds $bounds, $options = array(), $js_name='rectangle', $events=array())
	{
		if ($js_name !== 'rectangle')
			$this->setJsName($js_name);

		$this->setOptions($options);

		$this->setBounds($bounds);

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
	 * Adds an event listener to the marker
	 *
	 * @param EGMapEvent $event
	 */
	public function addEvent(EGMapEvent $event)
	{
		$this->events->add($event);
	}

	/**
	 * @param array $options
	 * @author Antonio Ramirez
	 */
	public function setOptions($options)
	{

		$this->options = array_merge($this->options, $options);
	}

	/**
	 * gets the bounds of the rectangle
	 * 
	 * @return EGMapBounds | null $bounds
	 * @author Antonio Ramirez
	 */
	public function getBounds()
	{
		return $this->options['bounds'];
	}

	/**
	 * sets the bounds coordinates of the rectangle
	 * 
	 * @param  EGMapBounds $bounds
	 * @author Antonio Ramirez Cobos
	 */
	public function setBounds(EGMapBounds $bounds)
	{
		$this->options['bounds'] = $bounds;
	}

	/**
	 * 
	 * Return center of bounds
	 */
	public function getCenterOfBounds()
	{
		if (null !== $this->getBounds() && $this->getBounds() instanceof EGMapBounds)
			return $this->getBounds()->getCenterCoord();
		return null;
	}

	/**
	 * @param string $map_js_name 
	 * @return string Javascript code to create the rectangle
	 * @author Antonio Ramirez		  
	 */
	public function toJs($map_js_name = 'map')
	{
		$this->options['map'] = $map_js_name;

		$return = '';
		if (null !== $this->info_window)
		{
			if ($this->info_window_shared)
			{
				$info_window_name = $map_js_name . '_info_window';
				$this->addEvent(
					new EGMapEvent(
						'click',
						'if (' . $info_window_name . ') ' . $info_window_name . '.close();' . PHP_EOL .
						$info_window_name . ' = ' . $this->info_window->getJsName() . ';' . PHP_EOL .
						$info_window_name . ".setPosition(" . $this->getCenterOfBounds()->toJs() . ");" . PHP_EOL .
						$info_window_name . ".open(" . $map_js_name . ");" . PHP_EOL
				));
			}
			else
				$this->addEvent(new EGMapEvent('click',
						$this->info_window->getJsName() . ".setPosition(" . $this->getCenterOfBounds()->toJs() . ");" . PHP_EOL .
						$this->info_window->getJsName() . ".open(" . $map_js_name . ");" . PHP_EOL
				));
			$return .= $this->info_window->toJs();
		}

		$return .='var ' . $this->getJsName() . ' = new ' . $this->rectangle_object . '(' . EGMap::encode($this->options) . ');' . PHP_EOL;

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
	 */
	public function addHtmlInfoWindow(EGMapInfoWindow $info_window, $shared = true)
	{
		$this->info_window = $info_window;
		$this->info_window_shared = $shared;
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

}
