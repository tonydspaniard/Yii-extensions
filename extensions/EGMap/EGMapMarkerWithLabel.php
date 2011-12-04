<?php
/**
 * 
 * EGMapMarkerWithLabel Class
 * 
 * @author Antonio Ramirez
 * @link www.ramirezcobos.com 
 * 
 * @link http://google-maps-utility-library-v3.googlecode.com/svn/tags/markerwithlabel/1.1/docs/reference.html
 * from utility library MarkerWithLabel Google Maps V3
 * 
 * 
 * @copyright 
 * 
 * Copyright (c) 2011 Antonio Ramirez Cobos
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
class EGMapMarkerWithLabel extends EGMapMarker{
	
	protected $label_options = array(
		// By default, a label is drawn with its anchor point at (0,0) so 
		// that its top left corner is positioned at the anchor point of the 
		// associated marker. Use this property to change the anchor point of 
		// the label. For example, to center a 50px-wide label beneath a marker, 
		// specify a labelAnchor of google.maps.Point(25, 0). (Note: x-values 
		// increase to the right and y-values increase to the top.)
		'labelAnchor'=> null,
		// The name of the CSS class defining the styles for the label. Note 
		// that style values for position, overflow, top, left, zIndex, display, 
		// marginLeft, and marginTop are ignored; these styles are for internal use only.
		'labelClass'=> null,
	     // The content of the label (plain text or an HTML DOM node).
		'labelContent'=> null,
		// A flag indicating whether a label that overlaps its associated marker 
		// should appear in the background (i.e., in a plane below the marker). 
		// The default is false, which causes the label to appear in the foreground.
		'labelInBackGround' => null,
		// An object literal whose properties define specific CSS style values to be 
		// applied to the label. Style values defined here override those that may be 
		// defined in the labelClass style sheet. If this property is changed after the 
		// label has been created, all previously set styles (except those defined in 
		// the style sheet) are removed from the label before the new style values are 
		// applied. Note that style values for position, overflow, top, left, zIndex, 
		// display, marginLeft, and marginTop are ignored; these styles are for internal use only.
		'labelStyle'=> null,
		// A flag indicating whether the label is to be visible. The default is true. 
		// Note that even if labelVisible is true, the label will not be visible unless the 
		// associated marker is also visible (i.e., unless the marker's visible property is true).
		'labelVisible'=> null,
		// A flag indicating whether the label and marker are to be raised when the marker is dragged. 
		// The default is true. If a draggable marker is being created and a version of Google 
		// Maps API earlier than V3.3 is being used, this property must be set to false.
		'raiseOnDrag'=>null
		
	);
	/**
   	* @param  string $js_name Javascript name of the marker
   	* @param  float $lat Latitude
   	* @param  float $lng Longitude
   	* @param  EGMapIcon $icon
   	* @param  EGmapEvent[] array of GoogleMap Events linked to the marker
   	* @author Antonio Ramirez
   	*/
  	public function __construct( $lat, $lng, $options = array(), $js_name='marker',$events=array() )
  	{
    	$this->marker_object = 'MarkerWithLabel';	
    	
    	$options = array_merge($this->label_options, $this->encodeOptions($options));
    	
    	parent::__construct( $lat, $lng, $options, $js_name, $events );
    	
  	}
  	/**
  	 * 
  	 * Sets the anchor of the label
  	 * @param EGMapPoint $anchor
  	 */
  	public function setLabelAnchor( EGMapPoint $anchor ){
  	
  		$this->options['labelAnchor'] = $anchor;
  	}
  	/**
  	 * 
  	 * Sets the label HTML content
  	 * @param string $content
  	 */
  	public function setLabelContent( $content ){
  		
    	$this->options['labelContent']='"'.$content.'"';
  	}
  	/**
  	 * 
  	 * Set the style class name for the label
  	 * @param unknown_type $class
  	 */
  	public function setLabelClass( $class ){
  		
  		$this->options['labelClass'] = '"'.$class.'"';
  		
  	}
  	/**
  	 * 
  	 * Sets label style
  	 * position, overflow, top, left, zIndex, 
  	 * display, marginLeft, and marginTop are ignored
  	 * @param array $styleOptions
  	 * @throws CException
  	 */
  	public function setLabelStyle( $styleOptions ){
  		if(!is_array( $styleOptions )) 
			throw new CException( Yii::t('EGMap', 'EGMapMarkerWithLabel label style options must be of type array!'));
		$this->options['labelStyle'] = CJavaScript::encode($styleOptions);
  	}
  	/**
  	 * (non-PHPdoc)
  	 * @see EGMapMarker::setOptions()
  	 */
  	public function setOptions( $options ){
  		parent::setOptions( $this->encodeOptions($options) );
  	}
  	/**
  	 * 
  	 * Encodes options appropiatelly
  	 * @param array $options
  	 */
  	private function encodeOptions( $options ){
  		if(!is_array( $options )) 
			throw new CException( Yii::t('EGMap', 'EGMapMarkerWithLabel.encodeOptions parameter must be of type array!'));
		foreach(array('labelContent', 'labelClass', 'labelStyle') as $key )
    		if(isset($options[$key])) 	$options[$key] 	= CJavaScript::encode($options[$key]);
    	
    	return $options;
  	}
}
