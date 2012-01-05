<?php
/**
 * EScriptBoost class
 * 
 * Compresses CSS and JS script source with different adapters
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
$assets = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';

Yii::setPathOfAlias('scriptboostjs', $assets . DIRECTORY_SEPARATOR . 'js');
Yii::setPathOfAlias('scriptboostcss', $assets . DIRECTORY_SEPARATOR . 'css');
Yii::setPathOfAlias('scriptboosthtml', $assets . DIRECTORY_SEPARATOR . 'html');

Yii::import('scriptboostjs.*');
Yii::import('scriptboostcss.*');
Yii::import('scriptboosthtml.HTMLMin');

class EScriptBoost extends CComponent {

	const CSS_COMPRESSOR = 'CssCompressor';
	const CSS_MIN = 'CssMin';
	const CSS_MINIFIER = 'CssMinifier';
	
	const JS_MIN = 'JSMin';
	const JS_MIN_PLUS = 'JSMinPlus';
	
	/**
	* CssMin filter options. Default values according cssMin doc.
	*/
	protected static $cssMinFilters = array
	(
		'ImportImports'                 => false,
		'RemoveComments'                => true,
		'RemoveEmptyRulesets'           => true,
		'RemoveEmptyAtBlocks'           => true,
		'ConvertLevel3AtKeyframes'      => false,
		'ConvertLevel3Properties'       => false,
		'RemoveLastDelarationSemiColon' => true
	);
	
	/**
	* CssMin plugin options. Maximum compression and conversion.
	*/
	protected static $cssMinPlugins = array
	(
		'Variables'                => true,
		'ConvertFontWeight'        => false,
		'ConvertHslColors'         => false,
		'ConvertRgbColors'         => false,
		'ConvertNamedColors'       => false,
		'CompressColorValues'      => false,
		'CompressUnitValues'       => true,
		'CompressExpressionValues' => false,
	);

	/**
	 *
	 * @param string $content the content to compres
	 * @param string $adapter the adapter to use to minify 
	 * @param array $options the options for the adapter
	 * @return compressed content if successful | false otherwise
	 */
	public static function minifyCss($content, $adapter = self::CSS_MIN, $options = array())
	{
		if(is_string($adapter))
		{
			switch($adapter)
			{
				case self::CSS_COMPRESSOR: 
					return call_user_func_array(array($adapter,'process'), array($content, $options));
					break;
				case self::CSS_MIN:
					// check css/CssMin class for options
					$filters = isset($options['filters']) ? array_merge(self::$cssMinFilters, $options['filters']) : self::$cssMinFilters;
					$plugins = isset($options['plugins']) ? array_merge(self::$cssMinPlugins, $options['plugins']) : self::$cssMinPlugins;
					return call_user_func_array(array($adapter, 'minify'), array($content, $filters, $plugins));
					break;
				case self::CSS_MINIFIER:
					// check css/CssMinifier for options
					return call_user_func_array(array($adapter, 'minify'), array($content, $options));
					break;
			}
		}
		return false;
	}
	
	/**
	 *
	 * @param string $content the content to minify
	 * @param string $adapter the adapter to use to minify
	 * @param array $options the options to the adapter
	 * @return compressed code if successful | false otherwise
	 */
	public static function minifyJs($content, $adapter = self::JS_MIN, $options = array())
	{
		if(is_string($adapter) && ($adapter == self::JS_MIN || $adapter == self::JS_MIN_PLUS))
		{
			return call_user_func_array(array($adapter, 'minify'), array($content));
		}
		return false;
	}
	
	/**
	 * 
	 * @param string $content the content to parse
	 * @param array $options 
	 * @see HTMLMin class to check the options
	 * @return minified content
	 */
	public static function minifyHTML($content, $options = array())
	{
		return HTMLMin::minify($content, $options);
	}
	
	/**
	 *
	 * @param string $content the content to pack
	 * @param string | integer $encoding the compression type 
	 * @see JavaScriptPacker class
	 * @param boolean $fastDecode
	 * @param boolean $specialChars
	 * @return packed content
	 */
	public static function packJs($content, $encoding = 'Normal', $fastDecode = true, $specialChars = false)
	{
		$jsSize = strlen($content);
		if ($jsSize > 256 && $jsSize < 1048576)
		{ // prevent memory error 
			$packer = new JavaScriptPacker($content, $encoding, $fastDecode, $specialChars);
			return $packer->pack();
		} 
		return $content;
	}
	
	/**
	 * Helper function to register compressed script to CClientScript
	 * @param string $id the id of the script
	 * @param script code $script
	 * @param integer $cacheDuration
	 * @param integer $position the position where to register the script
	 */
	public static function registerScript($id, $script, $cacheDuration=0, $position=CClientScript::POS_READY)
	{
		$js = Yii::app()->cache->get($id);
		if(!$js)
		{
			$js = self::minifyJs($script);
			Yii::app()->cache->set($id, $js, $cacheDuration);
		}
		Yii::app()->clientScript->registerScript($id, $js, $position);
	}
}