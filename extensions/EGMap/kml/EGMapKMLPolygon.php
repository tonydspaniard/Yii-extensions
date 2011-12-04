<?php
/**
 * 
 * EGMapKMLPolygon Class 
 * 
 * KML Polygon tag object
 *
 * A Polygon is defined by an outer boundary and 0 or more inner boundaries. The boundaries, in turn, are defined 
 * by LinearRings. When a Polygon is extruded, its boundaries are connected to the ground to form additional polygons, 
 * which gives the appearance of a building or a box. Extruded Polygons use <PolyStyle> for their color, color mode, and fill.
 * The <coordinates> for polygons must be specified in counterclockwise order. Polygons follow the "right-hand rule," 
 * which states that if you place the fingers of your right hand in the direction in which the coordinates are specified, 
 * your thumb points in the general direction of the geometric normal for the polygon. 
 * 
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
class EGMapKMLPolygon extends EGMapKMLNode{
	/**
	 * <Polygon>
     * <innerBoundaryIs> // only supported here
     *   <LinearRing> 
     *     <coordinates>
     *       -122.366212,37.818977,30
     *       -122.365424,37.819294,30
     *     </coordinates>
     *   </LinearRing>
     * </innerBoundaryIs>
   	 * </Polygon>
   	 * Another way
   	 * <MultiGeometry>
     * <Polygon>
     *   <outerBoundaryIs>
     *     <LinearRing>
     *       <coordinates>-122.1,37.4,0 -122.0,37.4,0 -122.0,37.5,0 -122.1,37.5,0 -122.1,37.4,0</coordinates>
     *     </LinearRing>
     *   </outerBoundaryIs>
     * </Polygon>
     * </MultiGeometry>
     * 
     * <outerBoundaryIs> (required)
     * Contains a <LinearRing> element.
     * <innerBoundaryIs>
     * Contains a <LinearRing> element. 
     * A Polygon can contain multiple <innerBoundaryIs> elements, 
     * which create multiple cut-outs inside the Polygon.
	 */
	/**
	 * 
	 * Coordinates array
	 * @var array
	 */
	protected $coordinates = array();
	/**
	 * 
	 * outerBoundaryIs = true
	 * innerBoundaryIs = false
	 * @var boolean
	 */
	protected $boundary;
	
	/**
	 * 
	 * Enter description here ...
	 * @param boolean $outerBoundaryIs
	 * @param array $coordinates
	 */
	public function __construct( $outerBoundaryIs = false, $coordinates = array() ){
		
		$this->tag = 'Polygon';
		$this->boundary = $outerBoundaryIs;
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
			throw new CException( Yii::t('EGMap','Coordinates parameter must be of type array'));
		foreach($coords as $coord){
			$this->coordinates[] = $coord;
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see EGMapKMLNode::toXML()
	 */
	public function toXML(){
		$node = new EGMapKMLNode('LinearRing');
		$node->addChild(new EGMapKMLNode('coordinates', $this->coordinates ));
		$parentNode = new EGMapKMLNode(($this->boundary?'outerBoundaryIs':'innerBoundaryIs'));
		$parentNode->addChild( $node );
		$this->addChild( $parentNode );
		
		return parent::toXML();
	}
	
}