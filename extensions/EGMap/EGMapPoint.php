<?php
/**
 * 
 * EGMapPoint Class
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
class EGMapPoint {
	
	/**
	 * 
	 * X coordenate
	 * @var integer x
	 */
	private $_x;
	/**
	 * 
	 * Y coordenate
	 * @var integer y
	 */
	private $_y;
	/**
	 * 
	 * Class constructor
	 * @param integer $x
	 * @param integer $y
	 */
	public function __construct( $x=0, $y=0 )
	{
		$this->setCoordX($x);
		$this->setCoordY($y);
	}
	/**
	 * 
	 * Sets X coordenate of the point
	 * @param integer $x
	 */
	public function setCoordX( $x  ){
		if( !is_numeric($x) ) 
			throw new CException(Yii::t('EGMap','X Coordenate must be a numeric string or a number!'));
		
		$this->_x = $x;
	}
	/**
	 * 
	 * Sets Y coordenate of the point
	 * @param integer $y
	 */
	public function setCoordY( $y  ){
		if( !is_numeric($y) ) 
			throw new CException(Yii::t('EGMap','Y Coordenate must be a numeric string or a number!'));
		
		$this->_y = $y;
	}
	/**
	 * 
	 * returns array representation of the coords
	 */
	public function toArray(){
		return array('x'=>$this->_x, 'y'=>$this->_y);
	}
	/**
	 * @return string Javascript code to return the Point
	 */
	public function toJs()
	{
		return ' new google.maps.Point('.$this->_x.','.$this->_y.')';
  	}
}