<?php
/**
 * 
 * EGMapKMLPoint Class 
 * 
 * KML Point tag object
 * 
 * A geographic location defined by longitude, latitude, and (optional) altitude. 
 * When a Point is contained by a Placemark, the point itself determines the position 
 * of the Placemark's name and icon. When a Point is extruded, it is connected to the 
 * ground with a line. This "tether" uses the current LineStyle.
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
class EGMapKMLPoint extends EGMapKMLNode{
	/**
	 * 
	 * Latitude coord
	 * @var string
	 */
	public $latitude;
	/**
	 * 
	 * Longitude coord
	 * @var string
	 */
	public $longitude;
	/**
	 * 
	 * Elevation
	 * @var unknown_type
	 */
	public $elevation;
	/**
	 * 
	 * Boolean value. Specifies whether to connect the point to the ground with a line. To extrude a Point, 
	 * the value for <altitudeMode> must be either relativeToGround, relativeToSeaFloor, or absolute. 
	 * The point is extruded toward the center of the Earth's sphere.
	 * Enter description here ...
	 * @var boolean
	 */
	public $extrude;
	/**
	 * 
	 * Enter description here ...
	 * @param string $latitude
	 * @param string $longitude
	 * @param string $elevation
	 */
	public function __construct($latitude, $longitude, $elevation = 0){
		$this->tag = 'Point';
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		$this->elevation = $elevation;
	}
	/**
	 * (non-PHPdoc)
	 * @see EGMapKMLNode::toXML()
	 */
	public function toXML(){
		$this->checkNode('extrude');
		/**
		 * coordinate
		 * A single tuple consisting of floating point values for longitude, latitude, and altitude (in that order). 
		 * Longitude and latitude values are in degrees
		 * altitude values (optional) are in meters above sea level
		 * Do not include spaces between the three values that describe a coordinate.
		 */
		if(!is_null($this->latitude) && !is_null($this->longitude))
			$this->addChild(new EGMapKMLNode('coordinates', $this->longitude.','.$this->latitude.','.$this->elevation));
		
		
		return parent::toXML();
	}
}