<?php

/**
 * 
 * EProjekktor Class 
 * 
 * Widget helper to render the free and open source HTML5 Video Player Projekktor
 *
 * @author Antonio Ramirez Cobos
 * @link www.ramirezcobos.com
 * 
 * @see http://www.projekktor.com
 *
 * 
 * @copyright 
 * 
 * Copyright (c) 2012 Antonio Ramirez Cobos
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
class EProjekktor extends CWidget {
	
	/**
	 * Logo position constants
	 */
	const POS_TOP_LEFT = 'tl'; // top left
	const POS_TOP_RIGHT = 'tr'; // top right
	const POS_BOTTOM_LEFT = 'bl'; // bottom left
	const POS_BOTTOM_RIGHT = 'br'; // bottom right

	/**
	 * Controlbar themes
	 * @see http://www.projekktor.com/downloads.php#themes
	 */
	const THEME_APPLICIOUS = 'applicious';
	const THEME_CIBERGRAF = 'cybergraf';
	const THEME_MACCACO = 'maccaco';
	const THEME_MINIMUM = 'minimum';

	/**
	 * @var string the theme to use 
	 */
	public $style = self::THEME_MINIMUM;

	/**
	 * @var array the javascript options to configure the player
	 */
	public $options = array();

	/**
	 * @var array the html attributes to configure the video tag
	 */
	public $htmlOptions = array();

	/**
	 * @var boolean specifies whether is a youtube video source or not
	 */
	public $isYoutubeVideo = true;

	/**
	 * @var string the path for a logo image, null wont display
	 */
	public $logoImage;

	/**
	 * @var string the position where to display the logo
	 */
	public $logoPosition = self::POS_BOTTOM_RIGHT;

	/**
	 * @var integer the seconds to be played back before logo fades in, 0=instantly
	 */
	public $logoDelay = 1;

	/**
	 * @var array default attributes for the HTML video tag
	 */
	protected $defaultHtmlOptions;

	/**
	 * @var array default js configuration options
	 */
	protected $defaultJsOptions;

	/**
	 * Initializes the widget
	 */
	public function init()
	{
		if (isset($this->htmlOptions['id']))
			$this->setId($this->htmlOptions['id']);
		else
			$this->htmlOptions['id'] = $this->getId();

		if (!isset($this->htmlOptions['src']) && !isset($this->options['playlists']))
			throw new CException('You must set the "src" attribute on "htmlOptions" or "playlists" in the options configuration.');


		$this->defaultHtmlOptions = array('class' => 'projekktor', 'width' => '480', 'height' => '270');

		if (is_string($this->options))
		{
			$this->options = function_exists('json_decode') ? json_decode($this->options) : CJSON::decode($this->options);
			if (!$this->options)
				throw new CException(Yii::t('EProjekktor', 'The options parameter is not valid JSON.'));
		}

		$this->defaultJsOptions = array('debug' => false, 'controls' => true, 'autoplay' => false);

		if ($this->isYoutubeVideo)
		{
			$this->defaultHtmlOptions['type'] = 'video/youtube';
			$this->defaultJsOptions['useYTIframeAPI'] = true;
			
		}

		if (null !== $this->logoImage)
		{
			$this->defaultJsOptions['plugin_display'] = array(
				'logoPosition' => $this->logoPosition,
				'logoImage' => $this->logoImage,
				'logoDelay' => $this->logoDelay
			);
		}
		
		$this->htmlOptions = CMap::mergeArray($this->defaultHtmlOptions, $this->htmlOptions);
		
		$this->options = CMap::mergeArray($this->defaultJsOptions, $this->options);
	}

	/**
	 * Renders the widget.
	 */
	public function run()
	{
		$this->renderContent();
		$this->registerScripts();
	}

	/**
	 * Renders the video tag
	 */
	protected function renderContent()
	{
		echo CHtml::openTag('video', $this->htmlOptions);
		echo CHtml::closeTag('video');
	}

	/**
	 * Publishes and registers the necessary script files.
	 */
	protected function registerScripts()
	{
		$cs = Yii::app()->clientScript;

		$jsOptions = CJavaScript::encode($this->options);

		$basePath = dirname(__FILE__) . DIRECTORY_SEPARATOR .
			'assets' . DIRECTORY_SEPARATOR;

		$baseUrl = Yii::app()->getAssetManager()->publish($basePath, false, 1);

		$cs->registerCssFile($baseUrl . '/' . $this->style . '/style.css');

		$cs = Yii::app()->clientScript;
		$cs->registerCoreScript('jquery');
		$cs->registerScriptFile($baseUrl . '/projekktor.min.js', CClientScript::POS_END);

		$cs->registerScript(__CLASS__ . '#' . $this->getId(), "
			projekktor('#{$this->getId()}',{$jsOptions});
		", CClientScript::POS_READY);
	}

}