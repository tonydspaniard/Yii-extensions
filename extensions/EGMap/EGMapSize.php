<?php
/**
 * 
 * EGMapSize Class
 * 
 * Two-dimensonal size, where width is the distance on the x-axis, and height is the distance on the y-axis.
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
class EGMapSize {
	
	/**
	 * 
	 * The height along the y-axis, in pixels.
	 * @var integer height
	 */
	private $_height;
	/**
	 * 
	 * The width along the x-axis, in pixels.
	 * @var integer width
	 */
	private $_width;
	/**
	 * 
	 * Class constructor
	 * @param integer $height
	 * @param integer $width
	 */
	public function __construct( $height=0, $width=0 )
	{
		$this->setHeight($height);
		$this->setWidth($width);
	}
	/**
	 * 
	 * Sets Height of the Size
	 * @param integer $height
	 */
	public function setHeight( $height  ){
		if( !is_numeric($height) ) 
			throw new CException(Yii::t('EGMap','Height must be a numeric string or a number!'));
		
		$this->_height = $height;
	}
	/**
	 * 
	 * Sets the Width of the Size
	 * @param integer $width
	 */
	public function setWidth( $width  ){
		if( !is_numeric($width) ) 
			throw new CException(Yii::t('EGMap','Width must be a numeric string or a number!'));
		
		$this->_width = $width;
	}
	/**
	 * 
	 * returns array representation of the size
	 */
	public function toArray(){
		return array('width'=>$this->_width, 'height'=>$this->_height);
	}
	/**
	 * @return string Javascript code to return the Size
	 */
	public function toJs()
	{
		return ' new google.maps.Size('.$this->_width.','.$this->_height.')';
  	}
}