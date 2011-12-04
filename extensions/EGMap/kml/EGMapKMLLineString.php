<?php
/**
 * 
 * EGMapKMLLineString Class 
 * 
 * KML LineString tag object
 *
 * Defines a connected set of line segments. Use <LineStyle> to specify the color, color mode, and width 
 * of the line. When a LineString is extruded, the line is extended to the ground, forming a polygon that 
 * looks somewhat like a wall or fence. For extruded LineStrings, the line itself uses the current LineStyle, 
 * and the extrusion uses the current PolyStyle.
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
class EGMapKMLLineString extends EGMapKMLNode{
	/**
	 * 
	 * Two or more coordinate tuples, each consisting of floating point values for longitude, latitude, and 
	 * altitude. The altitude component is optional. Insert a space between tuples. 
	 * Do not include spaces within a tuple.
	 * @var array 
	 */
	protected $coordinates = array();
	/**
	 * 
	 * Class constructor
	 * @param array $coordinates
	 */
	public function __construct( $coordinates = array() ){
		$this->tag = 'LineString';
		if(is_array($coordinates)) $this->coordinates = $coordinates;
	}
	/**
	 * 
	 * Adds a coordenate to the array
	 * @param float | numeric string $latitude
	 * @param float | numeric string $longitude
	 * @param float | numeric string $elevation
	 */
	public function addCoordenate( $latitude, $longitude, $elevation ){
		$this->coordinates[] = $longitude.','.$latitude.','.$elevation;
	}
	/**
	 * 
	 * Adds array of coordenates 
	 * @param array $coords
	 * @throws CException
	 */
	public function addCoordenates( $coords ){
		if(!is_array( $coords) )
			throw new CException( Yii::t('EGMap','Coordinates parameter must be of type array and are required'));
		foreach($coords as $coord){
			$this->coordinates[] = $coord;
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see EGMapKMLNode::toXML()
	 */
	public function toXML(){
		$this->addChild(new EGMapKMLNode('altitudeMode','relative'));
		$this->addChild( new EGMapKMLNode('coordinates', $this->coordinates) );
		return parent::toXML();
	}
	
}