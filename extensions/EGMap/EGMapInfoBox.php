<?php

/**
 * TODO: add load content via ajax functionality
 * function load_content(marker, id){
 * $.ajax({
 *   url: 'aulas/show/' + id,
 *   success: function(data){
 *     infowindow.setContent(data);
 *     infowindow.open(map, marker);
 *   }
 * });
 * }
 * Then change the listener:
 *
 *   google.maps.event.addListener(marker, 'click', function() {
 *     infobox.close();
 *     load_content(marker, a.aula.id);
 *   });
 *   markers.push(marker);
 * });
 * 
 * EGMapInfoBox class
 * A GoogleMap InfoBox
 * 
 * @since 2011-10-12
 * @link http://www.ramirezcobos.com
 * 
 * @author Antonio Ramirez Cobos 
 *
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
class EGMapInfoBox extends EGMapInfoWindow {

	/**
	 * 
	 * Config options
	 * @link http://code.google.com/intl/en-EN/apis/maps/documentation/javascript/reference.html#InfoWindow
	 * @var array 
	 */
	protected $box_options = array(
		// Align the bottom left corner of the InfoBox to the position 
		// location (default is false which means that the top left corner 
		// of the InfoBox is aligned).
		'alignBottom' => null,
		// The name of the CSS class defining the styles for the InfoBox container. 
		// The default name is infoBox.
		'boxClass' => null,
		// An object literal whose properties define specific CSS style 
		// values to be applied to the InfoBox. Style values defined 
		// here override those that may be defined in the boxClass style 
		// sheet. If this property is changed after the InfoBox has been 
		// created, all previously set styles (except those defined in 
		// the style sheet) are removed from the InfoBox before the new 
		// style values are applied.
		'boxStyle' => null,
		// The CSS margin style value for the close box. The default is 
		// "2px" (a 2-pixel margin on all sides).
		'closeBoxMargin' => '"2px"',
		// The URL of the image representing the close box. Note: The 
		// default is the URL for Google's standard close box. Set this 
		// property to "" if no close box is required.
		'closeBoxUrl'=>null,
		// Propagate mousedown, click, dblclick, and contextmenu events 
		// in the InfoBox (default is false to mimic the behavior of a 
		// google.maps.InfoWindow). Set this property to true if the InfoBox 
		// is being used as a map label. iPhone note: This property setting 
		// has no effect; events are always propagated.
		'enableEventPropagation'=>null,
		// Minimum offset (in pixels) from the InfoBox to the map edge 
		// after an auto-pan.
		'infoBoxClearance'=>null,
		// Hide the InfoBox on open (default is false).
		'isHidden'=>null,
		// The pane where the InfoBox is to appear (default is "floatPane"). 
		// Set the pane to "mapPane" if the InfoBox is being used as a map 
		// label. Valid pane names are the property names for the google.maps.MapPanes object.
		'pane'=>null,
	);

	/**
	 * @param string content
	 * @param array $options
	 * @param string $js_name
	 * @param array $events
	 * @author Maxime Picaud
	 * @since 7 sept. 2009
	 */
	public function __construct($content, $js_name='info_box', $options = array(), $events=array())
	{
		$this->options = CMap::mergeArray($this->options, $this->box_options);
		
		if ($js_name !== 'info_box')
			$this->setJsName($js_name);

		$this->setContent($content);

		$this->setOptions($options);
		$this->events = $events;
	}

	/**
	 * @param string $map_js_name 
	 * @return string Javascript code to create the infoBox
	 * @author Fabrice Bernhard
	 * @since 2011-10-12 by Antonio Ramirez
	 */
	public function toJs($map_js_name = 'map')
	{

		$return = '';
		$return .= $this->getJsName() . ' = new InfoBox(' . EGMap::encode($this->options) . ');' . PHP_EOL;

		foreach ($this->custom_properties as $attribute => $value)
		{
			$return .= 'var ' . $this->getJsName() . "." . $attribute . " = '" . $value . "';" . PHP_EOL;
		}
		foreach ($this->events as $event)
		{
			$return .= $event->getEventJs($this->getJsName());
		}
		return $return;
	}
	
	public function getEncodedOptions()
	{
		return EGMap::encode(parent::getOptions());
	}
}
