<?php

Yii::import('zii.widgets.jui.CJuiInputWidget');
/**
 * EDateRangePicker displays a datepicker.
 *
 * EDateRangePicker encapsulates the 
 * {@link http://www.filamentgroup.com/lab/date_range_picker_using_jquery_ui_16_and_jquery_ui_css_framework/ 
 * Date Range Picker} plugin.
 *
 * To use this widget, you may insert the following code in a view:
 * <pre>
 * $this->widget('EDateRangePicker', array(
 *     'name'=>'publishDate',
 *     // additional javascript options for the date picker plugin
 *     'options'=>array(
 *         'arrows'=>true,
 *     ),
 *     'htmlOptions'=>array(
 *         'style'=>'height:20px;'
 *     ),
 * ));
 * </pre>
 *
 * By configuring the {@link options} property, you may specify the options
 * that need to be passed to the daterangepicker plugin. Please refer to
 * the {@link http://www.filamentgroup.com/lab/date_range_picker_using_jquery_ui_16_and_jquery_ui_css_framework/ 
 * Date Range Picker} documentation
 * for possible options (name-value pairs).
 *
 * @author Antonio Ramirez <http://www.ramirezcobos.com>
 *
 */
class EDateRangePicker extends CJuiInputWidget {

	/**
	 * @var string the locale ID (eg 'fr', 'de') for the language to be used by the date picker.
	 * If this property is not set, I18N will not be involved. That is, the date picker will show in English.
	 * You can force English language by setting the language attribute as '' (empty string)
	 */
	public $language;

	/**
	 * @var string The i18n Jquery UI script file. It uses scriptUrl property as base url.
	 */
	public $i18nScriptFile = 'jquery-ui-i18n.min.js';

	public function init()
	{
		parent::init();
		$this->registerScripts();
	}

	/**
	 * Run this widget.
	 * This method registers necessary javascript and renders the needed HTML code.
	 */
	public function run()
	{

		list($name, $id) = $this->resolveNameID();

		if (isset($this->htmlOptions['id']))
			$id = $this->htmlOptions['id'];
		else
			$this->htmlOptions['id'] = $id;
		if (isset($this->htmlOptions['name']))
			$name = $this->htmlOptions['name'];
		else
			$this->htmlOptions['name'] = $name;

		if ($this->hasModel())
			echo CHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);
		else
			echo CHtml::textField($name, $this->value, $this->htmlOptions);


		$options = CJavaScript::encode($this->options);
		$js = "jQuery('#{$id}').daterangepicker($options);";


		$cs = Yii::app()->getClientScript();
		if ($this->language != '' && $this->language != 'en')
		{
			$this->registerScriptFile($this->i18nScriptFile);
			$js .= "setTimeout(function(){jQuery('.range-start, .range-end').datepicker('option', jQuery.datepicker.regional['{$this->language}']);},500);";
		}
		$cs->registerScript(__CLASS__ . '#' . $id, $js, CClientScript::POS_READY);
	}

	/**
	 * Registers required scripts
	 */
	protected function registerScripts()
	{
		$basePath = dirname(__FILE__) . DIRECTORY_SEPARATOR .
			'assets' . DIRECTORY_SEPARATOR .
			'daterange' . DIRECTORY_SEPARATOR;
		$baseUrl = Yii::app()->getAssetManager()->publish($basePath, false, 0, YII_DEBUG);

		$scriptFile = '/jquery.daterangepicker.js';
		$cssFile = '/ui.daterangepicker.css';

		$cs = Yii::app()->clientScript;
		$cs->registerScriptFile($baseUrl . $scriptFile);
		$cs->registerCssFile($baseUrl . $cssFile);
	}


}

?>
