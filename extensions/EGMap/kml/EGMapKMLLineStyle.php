<?php
/**
 * 
 * EGMapKMLLineStyle Class 
 * 
 * KML LineStyle tag object
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
class EGMapKMLLineStyle extends EGMapKMLNode{
	/**
	 * 
	 * Style id
	 * @var string
	 */
	public $id;
	/**
	 * 
	 * Color node
	 * Color and opacity (alpha) values are expressed in hexadecimal notation. 
	 * The range of values for any one color is 0 to 255 (00 to ff). For alpha, 00 is fully transparent and ff is fully opaque. 
	 * The order of expression is aabbggrr, where aa=alpha (00 to ff); bb=blue (00 to ff); gg=green (00 to ff); rr=red (00 to ff). 
	 * For example, if you want to apply a blue color with 50 percent opacity to an overlay, you would specify the following: <color>7fff0000</color>, 
	 * where alpha=0x7f, blue=0xff, green=0x00, and red=0x00.
	 * 
	 * @var string color
	 */
	public $color;
	/**
	 * 
	 * Width of the line, in pixels.
	 * @var numeric string
	 */
	public $width;
	/**
	 * 
	 * Class constructor
	 * @param string $id
	 * @param string $lineId
	 * @param string $color
	 * @param string $width
	 */
	public function __construct($id, $lineId = null, $color = null, $width = null){
		
		$this->tag = 'LineStyle';
		$this->id = $id;
		$this->tagId = $lineId;
		$this->color = $color;
		$this->width = $width;
	}
	/**
	 * (non-PHPdoc)
	 * @see EGMapKMLNode::toXML()
	 */
	public function toXML(){
		
		$this->checkNode( 'color' );
		$this->checkNode( 'width' );
		
		$result = CHtml::openTag( 'Style', array( 'id'=>$this->id ) );
		$result .= parent::toXML();
		$result .= CHtml::closeTag('Style');
		
		return $result;
	}
}