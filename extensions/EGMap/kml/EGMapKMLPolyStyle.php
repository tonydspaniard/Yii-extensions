<?php
/**
 * 
 * EGMapKMLPolyStyle Class 
 * 
 * KML PolyStyle tag object
 * 
 * @see EGMapKMLStyle
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
class EGMapKMLPolyStyle extends EGMapKMLLineStyle {
	/**
	 * 
	 * Values for <colorMode> are normal (no effect) and random. A value of random applies a random linear scale to the base <color> as follows.
	 * To achieve a truly random selection of colors, specify a base <color> of white (ffffffff).
	 * If you specify a single color component (for example, a value of ff0000ff for red), 
	 * random color values for that one component (red) will be selected. In this case, 
	 * the values would range from 00 (black) to ff (full red). If you specify values for two or 
	 * for all three color components, a random linear scale is applied to each color component, 
	 * with results ranging from black to the maximum values specified for each component.
	 * The opacity of a color comes from the alpha component of <color> and is never randomized.
	 * @var string color Mode
	 */
	public $colorMode;
	/**
	 * 
	 * Class Constructor
	 * @param string $id
	 * @param string $polyId
	 * @param string $color
	 * @param string $colorMode
	 */
	public function __construct($id, $polyId = null, $color = null, $colorMode = null){
		$this->tag = 'PolyStyle';
		$this->id = $id;
		$this->tagId = $polyId;
		$this->color = $color;
		$this->colorMode = $colorMode;
	}
}