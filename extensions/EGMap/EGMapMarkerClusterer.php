<?php
/**
 * 
 * EGMapMarkerClusterer Class
 * 
 * @link http://google-maps-utility-library-v3.googlecode.com/svn/tags/markerclusterer/1.0/docs/reference.html
 * from utility library MarkerClusterer Google Maps V3
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
class EGMapMarkerClusterer extends EGMapBase 
{
	
	protected $options = array(
		// The minimum number of markers to be in a cluster before the markers are hidden and a count
		'minimumClusterSize' => null,
		// Wether the center of each cluster should be the average of all markers in the cluster
		'averageCenetr' => null,
		// Whether the default behaviour of clicking on cluster is to zoom into it.
		'zoomOnClick' => null, 
		// The grid size of a cluster in pixel. Each cluster will be a square. If you want the algorithm to run faster, you can 
		// set this value larger. The default value is 60.
		'gridSize' => null,
		// The max zoom level monitored by a marker cluster. If not given, the marker cluster assumes the maximum map zoom level. 
		// When maxZoom is reached or exceeded all markers will be shown without cluster.
		'maxZoom' => null,
		// Custom styles for the cluster markers. The array should be ordered according to increasing cluster size, with the style 
		// for the smallest clusters first, and the style for the largest clusters last.
		// must be an array with any of the following options:
		// 
		// height			Number	Image height.
		// width			Number	Image width.
		// anchor			Array of Number	Anchor for label text, like [24, 12]. If not set, the text will align center and middle.
		// textColor			String	Text color. The default value is "black".
		// textSize			Number Text size.
		// url				String	Image url.
		// backgroundPosition		String The position of the background x, y
		// 
		'styles' => null
	);
	/**
	 * 
	 * Collection of EGMapMarkers markers
	 * @var array
	 */
	protected $markers;
	
	/**
	 * 
	 * Class constructor
	 * @param array options
	 */
	public function __construct( $options = array() )
	{
		$this->markers = new CTypedMap('EGMapMarker');
		
		$this->setOptions($options);
	}
	
  	/**
  	 * 
  	 * Sets plugin options
  	 * @param array $options
  	 * @throws CException
  	 */
	public function setOptions( $options ){
		if(!is_array( $options )) 
			throw new CException( Yii::t('EGMap', 'EGMapMarkerClusterer options must be of type array!'));
		if(isset($options['styles'])){
  			$this->setStyles($options['styles']);
  			unset($options['styles']);
  		}
		$this->options = array_merge($this->options, $options);
	}
	/**
	 * 
	 * Sets a plugin option
	 * @param string $name option
	 * @param mixed $value
	 */
	public function setStyles( $value ){
		$this->options['styles'] = CJavaScript::encode($value);
	}
	/**
	 * 
	 * Adds a marker to its internal collection
	 * @param EGMapMarker $marker
	 */
	public function addMarker( EGMapMarker $marker){
		
		$this->markers->add($marker->getJsName(), $marker);
	}
	
	/**
	 * @return string Javascript code to return the Point
	 */
	public function toJs( $map_js_name = 'map' )
	{
		$markers = array();
		if(count($this->markers)){
			foreach($this->markers as $m)
				$markers[] = $m->getJsName();
		}
		$return = 'var '.$this->getJsName().'= new MarkerClusterer('.$map_js_name.','.EGMap::encode($markers).','.EGMap::encode($this->options).');';
		
		return  $return;
  	}
}