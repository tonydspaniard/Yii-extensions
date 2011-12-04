<?php
/**
 * EGMapDirectionRenderer class
 * 
 * @author Antonio Ramirez
 * @since 2010-12-24
 * @link http://www.ramirezcobos.com
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
class EGMapDirectionRenderer extends EGMapBase
{  
	
  	protected $options = array(
	    // If true, allows the user to drag and modify the paths of 
	    // routes rendered by this DirectionsRenderer.
	    'draggable' 	=> false,
	    // This property indicates whether the renderer should provide 
	    // UI to select amongst alternative routes. By default, this flag
	    // is false and a user-selectable list of routes will be shown in 
	    // the directions' associated panel. To hide that list, set 
	    // hideRouteList to true.
	    'hideRouteList' => null,
  		// The InfoWindow in which to render text information when a marker
  		// is clicked. Existing info window content will be overwritten and 
  		// its position moved. If no info window is specified, the DirectionsRenderer 
  		// will create and use its own info window. This property will be ignored 
  		// if suppressInfoWindows is set to true.
	    'infoWindow' => null,
  		// Map on which to display the directions.
  		// Will be overriden when enabled to a EGMapDirection
  		'map' => null,
  		// review marker options
  		'markerOptions'=>null,
	    // The element (node getElementById().)
	    'panel' => null,
	    // Options for the polylines. All polylines rendered by the DirectionsRenderer 
	    // will use these options.
	    // Full reference on http://code.google.com/intl/en-EN/apis/maps/documentation/javascript/reference.html#PolylineOptions
	    'polylineOptions'  => 
  			array(
  				  'clickable'=>null,
  				  'strokeColor'=>null,
  				  'strokeOpacity'=>null,
  				  'strokeWeight'=>null,
  				  'zIndex'=>null),
  		//  By default, the input map is centered and zoomed to the bounding box of 
  		// this set of directions. If this option is set to true, the viewport is left 
  		//unchanged, unless the map's center and zoom were never set.
	    'preserveViewPort' => null,
  		// The index of the route within the DirectionsResult object. The default value is 0.
  		'routeIndex'=>null,
	    // Suppress the rendering of the BicyclingLayer when bicycling directions are requested.
	    'suppressBicyclingLayer' => null,
  		// Suppress the rendering of the BicyclingLayer when bicycling directions are requested.
	    'suppressInfoWindows' => null,
  		// Suppress the rendering of markers.
  		'suppressMarkers' => null,
  		// Suppress the rendering of polylines
  		'suppressPolylines' => null
  	);
  
	/**
	* Construct GMapDirectionRenderer object
	*
	* @param string $js_name The js var name
	* @param array $options Array of options
	* @author Antonio Ramirez
	* @since 2011-01-24
	*/
  	public function __construct( $js_name = 'gmap_direction', $options = array())
  	{
    	$this->setOptions($options);
    	if( $js_name !=='gmap_direction' ) 
    		$this->setJsName($js_name);
  	}
  	
  	public function setOptions( $options ){
  		if(isset($options['polylineOptions'])){
  			$this->setPolylineOptions($options['polylineOptions']);
  			unset($options['polylineOptions']);
  		}
  		$this->options = array_merge( $this->options, $options );
  	}
  	
  	public function setPolylineOptions( $value )
  	{
  		if(!is_array($value))
  		{
  			throw new CException(Yii::t('EGMap','Property "{class}.{property}" must be of type array.',
				array('{class}'=>get_class($this), '{property}'=>'polylineOptions')));
  		}
  		$this->options['polylineOptions'] = array_merge($this->options['polylineOptions'],$value);
  	}
  	
  	public function toJs(){
  		if( null !== $this->panel )
  			$this->panel = "document.getElementById('".$this->panel."')";
  			
  		if(count($this->options['polylineOptions'])) $this->options['polylineOptions'] = CJavaScript::encode($this->options['polylineOptions']);
  		
  		return 'var '.$this->getJsName().' = new google.maps.DirectionsRenderer('.EGMap::encode($this->options).');'."\n";
  	}
  

}