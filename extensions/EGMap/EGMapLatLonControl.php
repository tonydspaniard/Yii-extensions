<?php
/**
 * 
 * EGMapLatLonControl Class
 * 
 * @link http://gmaps-samples-v3.googlecode.com/svn/trunk/latlng-to-coord-control/latlng-to-coord-control.html
 * 
 * 
 * @author Antonio Ramirez Cobos
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
class EGMapLatLonControl extends EGMapBase 
{
	/**
	 * @return string Create new control to display latlng and coordinates under mouse.
	 */
	public function toJs( $map_js_name = 'map' )
	{
		$return = 'var '.$this->getJsName().'= new LatLngControl('.$map_js_name.');'.PHP_EOL;
		$return .= 'google.maps.event.addListener('.$map_js_name.', "mouseover", function(e) {'.$this->getJsName().'.set("visible", true);});'.PHP_EOL;
		$return .= 'google.maps.event.addListener('.$map_js_name.', "mouseout", function(e) {'.$this->getJsName().'.set("visible", true);});'.PHP_EOL;
		$return .= 'google.maps.event.addListener('.$map_js_name.', "mousemove", function(e) {'.$this->getJsName().'.updatePosition(e.latLng);});'.PHP_EOL;
		return  $return;
  	}
}