<?php
/**
 * 
 * EGMapEvent
 * 
 * Modified by Antonio Ramirez 
 * @since 2011-01-22 
 * @link http://www.ramirezcobos.com
 * 
 * changeLog: 
 * 
 * 2011-07-02 Included support for addDOMEventListenerOnce and addEventListenerOnce 
 * 			 modified by: Johnatan -http://www.yiiframework.com/forum/index.php?/user/7513-johnatan/)
 * 2011-01-22 Included toJs for custom event type selection (DOM or DEFAULT)
 * 
 * A googleMap Event
 * @author Fabrice Bernhard
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
class EGMapEvent
{
        const TYPE_EVENT_DEFAULT         = 'DEFAULT';
        const TYPE_EVENT_DEFAULT_ONCE    = 'DEFAULT_ONCE';
        const TYPE_EVENT_DOM             = 'DOM';
        const TYPE_EVENT_DOM_ONCE        = 'DOM_ONCE';
        
        protected $trigger;
        protected $function;
        protected $encapsulate_function;
        protected $type = self::TYPE_EVENT_DEFAULT;
  
  
  /**
   * @param string $trigger action that will trigger the event
   * @param string $function the javascript function to be executed
   * @param string $encapsulate_function
   * @author Fabrice Bernhard
   */
  public function __construct($trigger,$function,$encapsulate_function=true, $type=self::TYPE_EVENT_DEFAULT)
  {
    $this->trigger      = $trigger;
    $this->function     = $function;
    $this->encapsulate_function = $encapsulate_function;
    $this->setType($type);
  }
  /**
   * 
   * Sets the type of event, by default Google Event
   * @param string $type
   * @throws CException
   */
  public function setType( $type ){
        if( $type !== self::TYPE_EVENT_DEFAULT && $type !== self::TYPE_EVENT_DEFAULT_ONCE &&
                $type !== self::TYPE_EVENT_DOM && $type !== self::TYPE_EVENT_DOM_ONCE )
                throw new CException( Yii::t('EGMap', 'Unrecognized Event type') );
        $this->type = $type;
  }
  /**
   * 
   * Returns type of event
   * @return string
   */
  public function getType( ){
        return $this->type;
  }
  
  /**
   * @return string $trigger  action that will trigger the event   
   */
  public function getTrigger()
  {
    
    return $this->trigger;
  }
  /**   
   * @return string $function the javascript function to be executed
   */
  public function getFunction()
  {
    if (!$this->encapsulate_function)
        return $this->function;
        else  
      return 'function() {'.$this->function.'}';
  }
  
  /**
   * returns the javascript code for attaching a Google event to a javascript_object
   *
   * @param string $js_object_name
   * @return string
   * @author Fabrice Bernhard
   * @since 2011-07-02 Johnatan added Once support
   */
  public function getEventJs($js_object_name, $once=false)
  {
    $once = ($once)?'Once':'';
    return 'google.maps.event.addListener'.$once.'('.$js_object_name.', "'.$this->getTrigger().'", '.$this->getFunction().');'.PHP_EOL;
  }
  
   /**
   * returns the javascript code for attaching a dom event to a javascript_object
   *
   * @param string $js_object_name
   * @return string
   * @author Fabrice Bernhard
   * @since 2011-07-02 Johnatan
   */
  public function getDomEventJs($js_object_name, $once=false)
  {
    $once = ($once)?'Once':'';
    return 'google.maps.event.addDomListener'.$once.'('.$js_object_name.', "'.$this->getTrigger().'", '.$this->getFunction().');'.PHP_EOL;
  }
  /**
   * returns the javascript code for attaching a Google event or a dom event to a javascript_object
   *
   * @param  string $js_object_name
   * @return string of event type
   * @author Antonio Ramirez
   * @since 2011-07-02 Johnatan added Once Support
   */
  public function toJs( $js_object_name ){
      switch ($this->type) {
        case self::TYPE_EVENT_DEFAULT_ONCE:
            return $this->getEventJs($js_object_name, true);
        case self::TYPE_EVENT_DOM:
            return $this->getDomEventJs($js_object_name);
        case self::TYPE_EVENT_DOM_ONCE:
            return $this->getDomEventJs($js_object_name, true);
        case self::TYPE_EVENT_DEFAULT:
        default:
            return $this->getEventJs($js_object_name);
        }
  }
  
}