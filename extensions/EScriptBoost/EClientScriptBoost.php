<?php
/**
 * EClientScriptBoost class
 * 
 * Is an extended version of CClientScript to compress registered scripts
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
 */
class EClientScriptBoost extends CClientScript
{
	public $cacheDuration = 0;
	
	public function registerScript($id, $script, $position=self::POS_READY)
	{
		// assumed config includes the required path aliases to use
		// EScriptBoost
		$compressed = YII_DEBUG ? $script : Yii::app()->cache->get($id);
		
		if($compressed === false)
		{
			$compressed = EScriptBoost::minifyJs($script);
			Yii::app()->cache->set($id, $compressed, $this->cacheDuration);
		}
		parent::registerScript($id, $compressed, $position);
	}
}
