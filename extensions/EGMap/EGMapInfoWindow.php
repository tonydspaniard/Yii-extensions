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
 *}
 *Then change the listener:
 *
 *   google.maps.event.addListener(marker, 'click', function() {
 *     infowindow.close();
 *     load_content(marker, a.aula.id);
 *   });
 *   markers.push(marker);
 * });
 * 
 * EGMapInfoWindow class
 * A GoogleMap InfoWindow
 * 
 * @since 2011-01-22 Modified by Antonio Ramirez
 * @link http://www.ramirezcobos.com
 * 
 * @TODO: change $events to CTypeMap('EGMapEvent') type
 * 
 * @author Maxime Picaud
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
class EGMapInfoWindow extends EGMapBase
{
  	/**
   	* 
   	* Config options
   	* @link http://code.google.com/intl/en-EN/apis/maps/documentation/javascript/reference.html#InfoWindow
   	* @var array 
   	*/
  	protected $options = array(
	    // String   Content to display in the InfoWindow. This can be an HTML element, 
	    // a plain-text string, or a string containing HTML. The InfoWindow will be 
	    // sized according to the content. To set an explicit size for the content, 
	    // set content to be a HTML element with that size.
	    'content'  => null,
	  
	    // boolean   Disable auto-pan on open. By default, the info window will pan 
	    // the map so that it is fully visible when it opens.
	    'disableAutoPan' => null,
	  
	    // number  Maximum width of the infowindow, regardless of content's width. 
	    // This value is only considered if it is set before a call to open. To change 
	    // the maximum width when changing content, call close, setOptions, and then open.
	    'maxWidth' => null,
	  
	     // Size  The offset, in pixels, of the tip of the info window from the point on 
	     // the map at whose geographical coordinates the info window is anchored. If an 
	     // InfoWindow is opened with an anchor, the pixelOffset will be calculated from 
	     // the top-center of the anchor's bounds.
	    'pixelOffset' => null,
	    
	     // LatLng  The LatLng at which to display this InfoWindow. If the InfoWindow is 
	     // opened with an anchor, the anchor's position will be used instead.
	    'position' => null,
	  
	     //number  All InfoWindows are displayed on the map in order of their zIndex, with 
	     // higher values displaying in front of InfoWindows with lower values. By default, 
	     // InfoWinodws are displayed according to their latitude, with InfoWindows of lower 
	     // latitudes appearing in front of InfoWindows at higher latitudes. InfoWindows are 
	     // always displayed in front of markers.
	    'zIndex' => null,
  	);
  
  	protected $events  = array();
  	protected $custom_properties = array();
  
  	/**
   	* @param string content
   	* @param array $options
   	* @param string $js_name
   	* @param array $events
   	* @author Maxime Picaud
   	* @since 7 sept. 2009
   	*/
  	public function __construct( $content, $js_name='info_window', $options = array(), $events=array() )
  	{
	    if( $js_name !== 'info_window' )
	    	$this->setJsName($js_name);
	    	
	    $this->setContent($content);
	    
	    $this->setOptions($options);
	    $this->events  = $events;    
  	}
  
  	/**
   	* 
   	* @param string $content
   	* @author Maxime Picaud
   	* @since 7 sept. 2009
   	* @since 2011-01-25 by Antonio Ramirez
   	* 		 Included support to have content
   	* 		 from a DOM node
   	*/
  	public function setContent( $content )
  	{
  		if(strpos( strtolower($content), 'getelementbyid')>0)
  		{
  			$this->options['content'] = $content;
  		}
  		else
  		{
    		$content = preg_replace('/\r\n|\n|\r/', "\\n", $content);
    		$content = preg_replace('/(["\'])/', '\\\\\1', $content);
    
    		$this->options['content'] = '"'.$content.'"';
  		}
  	}
  
	public function getContent( )
	{
		return $this->options['content'];
	}
  
  	/**
   	* @param array $options
   	* @author fabriceb
   	* @since 2009-08-21
   	* @since 2011-01-25 by Antonio Ramirez
   	* 		 Modified to check for correct values for specific options
   	*/
  	public function setOptions($options)
  	{	
  		if(isset($options['pixelOffset']))
  		{
  			if(!$options['pixelOffset'] instanceof EGMapSize)
  				throw new CException(Yii::t('EGMap', 'Property "{class}.{property}" {e}.',
					array('{class}'=>get_class($this), '{property}'=>'pixelOffset','{e}'=>'must be of type EGMapSize')));
  		}
  		if(isset($options['position']))
  		{
  			if(!$options['position'] instanceof EGMapCoord )
  				throw new CException(Yii::t('EGMap', 'Property "{class}.{property}" {e}.',
					array('{class}'=>get_class($this), '{property}'=>'pixelOffset','{e}'=>'must be of type EGMapCoord')));
  		}
  		if(isset($options['content'])){
  			$this->setContent($options['content']);
  			unset($options['content']);
  		}
    	$this->options = array_merge($this->options,$options);
  	}
  	/**
  	 * 
  	 * Property setter for pixelOffset
  	 * to ensure its type
  	 * @param EGMapSize $pixelOffset
  	 * @author Antonio Ramirez
  	 */
  	public function setPixelOffset( EGMapSize $pixelOffset ){
  		$this->options['pixelOffset'] = $pixelOffset;
  	}
  	/**
  	 * 
  	 * Property setter position 
  	 * to ensure its type
  	 * @param EGMapCoord $position
  	 * @author Antonio Ramirez
  	 */
  	public function setPosition( EGMapCoord $position ){
  		$this->options['position'] = $position;
  	}
  
  	/**
  	* @param string $map_js_name 
  	* @return string Javascript code to create the marker
  	* @author Fabrice Bernhard
  	* @since 2009-08-21
  	* @since 2011-01-25 by Antonio Ramirez
  	* 		 Modified options encoding
  	*/
  	public function toJs($map_js_name = 'map')
  	{
    	
    	$return = '';
    	$return .= $this->getJsName().' = new google.maps.InfoWindow('.EGMap::encode($this->options).');'.PHP_EOL;
    	
    	foreach ($this->custom_properties as $attribute=>$value)
    	{
      		$return .= 'var '.$this->getJsName().".".$attribute." = '".$value."';".PHP_EOL;
    	}
    	foreach ($this->events as $event)
    	{
      		$return .= $event->getEventJs($this->getJsName());
    	}   
    	return $return;
  	}
  
  	/**
   	* Adds an event listener to the marker
   	*
   	* @param EGMapEvent $event
   	*/
  	public function addEvent($event)
  	{
    	array_push($this->events,$event);
  	}
  	/**
   	* 
   	* Sets custom properties to the Info Window
   	* @param array $custom_properties
   	*/
  	public function setCustomProperties($custom_properties)
  	{
  		if(!is_array($custom_properties))
  			throw new CException(Yii::t('EGMap','EGMapInfoWindow custom properties must of type array to be set'));
    	$this->custom_properties=$custom_properties;
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
  	public function setCustomProperty($name,$value)
  	{
    	$this->custom_properties[$name] = $value;
  	}
}
