<?php
/**
 * 
 * EGMapKeyDragZoom Class
 * 
 * @link http://google-maps-utility-library-v3.googlecode.com/svn/tags/keydragzoom/2.0.5/docs/reference.html
 * from utility library KeyDragZoom Google Maps V3
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
class EGMapKeyDragZoom extends EGMapBase
{
	/**
	 * 
	 * key trigger definitions
	 * @var string 
	 */
	const KEY_SHIFT = '"shift"';
	const KEY_ALT	= '"alt"';
	/**
	 * 
	 * Supported events
	 * @var string
	 */
	const EVENT_ACTIVATE 	= 'activate';
	const EVENT_DEACTIVATE 	= 'deactivate';
	const EVENT_DRAGSTART	= 'dragstart';
	const EVENT_DRAG		= 'drag';
	const EVENT_DRAGEND		= 'dragend';
	
	protected $options = array(
		// An object literal or named array defining the css styles of the zoom box. The default 
		// is {border: "4px solid #736AFF"}. Border widths must be specified in pixel units 
		// (or as thin, medium, or thick).
		'boxStyle' => null,
		// The hot key to hold down to activate a drag zoom, shift | ctrl | alt. The default 
		// is shift. NOTE: Do not use Ctrl as the hot key with Google Maps JavaScript API V3 since, 
		// unlike with V2, it causes a context menu to appear when running on the Macintosh. Also 
		// note that the alt hot key refers to the Option key on a Macintosh.
		'key' => self::KEY_SHIFT,
		// An object literal or named array defining the css styles of the veil pane which covers the map when a 
		// drag zoom is activated. The previous name for this property was paneStyle but the use of 
		// this name is now deprecated. The default is {backgroundColor: "gray", opacity: 0.25, cursor: "crosshair"}.
		'veilStyle' => null,
		// The name of the CSS class defining the styles for the visual control. To prevent the visual control 
		// from being printed, set this property to the name of a class, defined inside a @media print rule, 
		// which sets the CSS display style to none.
		'visualClass' => null,
		// A flag indicating whether a visual control is to be used. The default is false.
		'visualEnabled' => null,
		// The position of the visual control. The default position is on the left side of the map below other 
		// controls in the top left Ñ i.e., a position of google.maps.ControlPosition.LEFT_TOP.
		'visualPosition' => EGMapControlPosition::LEFT_TOP,
		// The index of the visual control. The index is for controlling the placement of the control relative 
		// to other controls at the position given by visualPosition; controls with a lower index are placed 
		// first. Use a negative value to place the control before any default controls. No index is generally 
		// required; the default is null.
		'visualPositionIndex'=> null,
		// The width and height values provided by this property are the offsets (in pixels) from the location 
		// at which the control would normally be drawn to the desired drawing location. The default is (35,0).
		'visualPositionOffset'=>null,
		// The width and height values provided by this property are the size (in pixels) of each of the images 
		// within visualSprite. The default is (20,20).
		'visualSize'=>null,
		// The URL of the sprite image used for showing the visual control in the on, off, and hot (i.e., when 
		// the mouse is over the control) states. The three images within the sprite must be the same size and 
		// arranged in on-hot-off order in a single row with no spaces between images. The default is 
		// http://maps.gstatic.com/mapfiles/ftr/controls/dragzoom_btn.png.
		'visualSprite'=>null,
		// An object literal defining the help tips that appear when the mouse moves over the visual control. 
		// The off property is the tip to be shown when the control is off and the on property is the tip to be 
		// shown when the control is on. The default values are "Turn on drag zoom mode" and "Turn off drag zoom mode", respectively.
		'visualType'=> null
	);
	/**
	 * 
	 * Collection of events
	 * @var array
	 */
	protected $events = array();
	
	/**
	 * 
	 * Class constructor
	 * @param array options
	 */
	public function __construct( $options = array() )
	{
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
			throw new CException( Yii::t('EGMap', 'KeyDrEGMapKeyDragZoomagZoom options must be of type array!'));
		$this->options = CMap::mergeArray($this->options, $options);
		
	}
	public function setVeilStyle( $options ){
		if(!is_array($options))
			throw new CException(Yii::t('EGMap', 'Property "{class}.{property}" {e}.',
				array('{class}'=>get_class($this), '{property}'=>'veilStyle','{e}'=>'must be of type array')));
	}
	public function setBoxStyle( $options ){
		if(!is_array($options))
			throw new CException(Yii::t('EGMap', 'Property "{class}.{property}" {e}.',
				array('{class}'=>get_class($this), '{property}'=>'boxStyle','{e}'=>'must be of type array')));
	}
	public function setVisualSize( EGMapSize $size ){
		$this->options['visualSize'] = $size;
	}
	public function setVisualPositionOffset( EGMapSize $offset ){
		$this->options['visualPositionOffset'] = $offset;
	}
	
	/**
	 * 
	 * Adds an event to the plugin
	 * Note that the event must be a supported one
	 * @param string $trigger
	 * @param string $function
	 * @param boolean $encapsulate_function
	 * @throws CException
	 */
	public function addEvent( $trigger,$function,$encapsulate_function=true ){
		if(	$trigger != self::EVENT_ACTIVATE && 
			$trigger != self::EVENT_DEACTIVATE && 
			$trigger != self::EVENT_DRAG && 
			$trigger != self::EVENT_DRAGEND && 
			$trigger != self::EVENT_DRAGSTART )
			throw new CException( Yii::t('EGMap', 'Unrecognized EGMapKeyDragZoom event!'));
			
		$this->events[] = new EGMapEvent( $trigger, $function, $encapsulate_function );
		
	}
	/**
	 * @return string Javascript code to return the Point
	 */
	public function toJs( $map_js_name = 'map' )
	{
		foreach(array('veilStyle','boxStyle','visualClass','visualType') as $key)
			if(isset($this->options[$key])) $this->options[$key] = CJavaScript::encode($this->options[$key]);
			
		$return = $map_js_name.'.enableKeyDragZoom('.EGMap::encode($this->options).');';
		
		if (count($this->events)){
			$return .= 'var '.$this->getJsName().'='.$map_js_name.'.getDragZoomObject();';
			foreach($this->events as $e){
				$return .= $e->toJs($this->getJsName());
			}
		}
		return  $return;
  	}
}