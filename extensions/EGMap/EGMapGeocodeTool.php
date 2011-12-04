<?php
/**
 * EGMapGeocodeTool class
 * 
 * Renders a tool for geocoding and reverse geocoding, that also can work
 * in synchronization with different form fields. 
 *
 * @author antonio
 */
class EGMapGeocodeTool 
{
	public $latitude = 39.737827146489174;
	public $longitude = 3.2830574338912477;
	public $zoom = 4;
	public $popupOptions;
	public $searchBox=false;
	public $width = '100%';
	public $height = '100px';
	public $geocodeOnShow = false;
	public $afterInitEvents = array();
	/**
	 * ID's of fields for input that can be filled automatically by the reverse geocoding
	 */
	public $formId = null;
	public $latId = null;
	public $lngId = null;
	public $addressId = null;
	public $zipId = null;
	public $cityId = null;
	public $regionId = null;
	public $countryId = null;

	/**
	 * @param array $config the configuration for initializing the GeocodeTool... 
	 * any attribute from this class can be set by giving it a value in this array
	 */
	public function __construct($config)
	{
		if(is_array($config))
			foreach($config as $name=>$value)
				$this->{$name} = $value;
	}
	
	public function render()
	{
		$this->registerScripts();
		$this->renderMap();

		if ($this->popupOptions !== null)
		{
			$this->renderPopupMap();
		}
	}

	protected function renderMap()
	{
		$gMap = new EGMap();
		$gMap->setJsName('geomap');
		$gMap->width = $this->width;
		$gMap->height = $this->height;
		$gMap->zoom = $this->zoom;
		$gMap->setCenter($this->latitude, $this->longitude);
		$gMap->addGlobalVariable('geocoder');
		$gMap->addGlobalVariable('geomap', 'null');
		if ($this->popupOptions === null)
		{
			$gMap->addGlobalVariable('hgeomap', 'null');
			$gMap->addGlobalVariable('hgmarker', 'null');
		}
		$gMap->addEvent(new EGMapEvent('click', 'function(e){panToMap(geomap,e.latLng,hgeomap);}', false));
		$gMap->appendMapTo('#geomap');

		$afterInit = array(
			'geocoder = new google.maps.Geocoder();',
			'gmarker = new google.maps.Marker({
				position: geomap.getCenter(),
				map: geomap,
				draggable: true
			});',
			'google.maps.event.addListener(gmarker, "dragend", function(e) {
				panToMap(geomap,e.latLng,hgeomap);
			});'
		);

		echo CHtml::openTag('div', array( 'style' => 'width:' . $this->width));

		if($this->searchBox)
		{
			$this->renderAddressSearchBox ('geoaddress');
			$afterInit[] = 'qDefaultText.init({"geoaddress": "Enter address here"});';
		}
		if($this->geocodeOnShow)
			$afterInit[] = 'panToMap(geomap,geomap.getCenter(),hgeomap)';

		$gMap->renderMap($afterInit);

		echo '<div style="clear:both;height:1px"></div>';
		echo CHtml::openTag('ul', array('class' => 'message', 'style' => 'margin-bottom:-2px;margin-right:-2px'));
		echo '<li>Drag the marker or click the map to specify new address.</li>';
		echo CHtml::closeTag('ul');
		echo '<div id="geomap" style="width:' . $this->width . ';height:' . $this->height . 'px"></div>';
		if ($this->popupOptions !== null)
			echo '<a href="#" id="open-big-map" class="btn-big" style="margin-top:3px;margin-right:30px">Open Big Map</a>';

		echo CHtml::closeTag('div');
	}

	protected function renderPopupMap()
	{
		$gMap = new EGMap();
		$gMap->setJsName('hgeomap');
		$gMap->addGlobalVariable('hgmarker');
		$gMap->width = isset($this->popupOptions['mapWidth']) ? $this->popupOptions['mapWidth'] : '100%';
		$gMap->height = isset($this->popupOptions['mapHeight']) ? $this->popupOptions['mapHeight'] : '600px';
		$gMap->zoom = 6;
		$gMap->setCenter($this->latitude, $this->longitude);
		$gMap->addEvent(new EGMapEvent('click', 'function(e){panToMap(geomap,e.latLng,hgeomap);}', false));
		$gMap->appendMapTo('#popup-map');
		$this->popupOptions['width'] = isset($this->popupOptions['width']) ? $this->popupOptions['width'] : '800px';

		$afterInit = array(
			'hgmarker = new google.maps.Marker({
				position: hgeomap.getCenter(),
				map: hgeomap,
				draggable: true
			});',
			'google.maps.event.addListener(hgmarker, "dragend", function(e) {
				panToMap(geomap,e.latLng,hgeomap);
			});',
			'$("#open-big-map").click(function(e){
				e.preventDefault();
				$("#hgeomap").dialog({resizable:false,title:"Location",width:"'.$this->popupOptions['width'].'"});
				google.maps.event.trigger(hgeomap, "resize");
				hgeomap.setCenter(geomap.getCenter());
				return false;
			});'
		);

		echo CHtml::openTag('div', array('id'=>'hgeomap','style' => 'display:none'));

		if(isset($this->popupOptions['searchBox']) && $this->popupOptions['searchBox'])
			$this->renderAddressSearchBox ('hgeoaddress');
		if(!is_array($this->afterInitEvents)) 
			$this->afterInitEvents = array($this->afterInitEvents);
		
		$gMap->renderMap(array_merge($this->afterInitEvents,$afterInit));

		echo '<div id="popup-map" ></div>';
		echo CHtml::closeTag('div');
	}

	protected function renderAddressSearchBox($id)
	{
		echo '<div class="set-row-2">';
		echo CHtml::textField($id, '', array('class'=>'input-2','id'=>$id));
		echo CHtml::link('Go to','#', array('class'=>'btn-medium', 'onclick'=>'return geocode("'.$id.'");'));
		echo '</div>';
		echo '<div style="clear:both;height:1px"></div>';
	}

	protected function registerScripts()
	{
		$updateFieldsJS='';
		$fields=array();
		if($this->latId !== null )
			$updateFieldsJS.="$('#{$this->latId}').val(ll.lat());";
		if($this->lngId !== null)
			$updateFieldsJS.="$('#{$this->lngId}').val(ll.lng());";
		$updateFieldsJS.="reverseGeocode();";

		$reverseJS='';
		if($this->addressId !== null){
			$reverseJS.="$('#{$this->addressId}').val(geoValue('route'));";
			$fields[] = '#'.$this->addressId;
		}
		if($this->zipId !== null){
			$reverseJS.="$('#{$this->zipId}').val(geoValue('postal_code'));";
			$fields[]  = '#'.$this->zipId;
		}
		if($this->cityId !== null)
		{
			$reverseJS.="var city = geoValue('locality');";
			$reverseJS.="if(city=='') city = geoValue('sublocality');";
			$reverseJS.="$('#{$this->cityId}').val(city);";
			$fields[] = '#'.$this->cityId;
		}
		if($this->regionId !== null)
		{
			$reverseJS.="$('#{$this->regionId}').val(geoValue('administrative_area_level_1'));";
			$fields[] = '#'.$this->regionId;
		}
		if($this->countryId !== null)
		{
			$reverseJS.="var country = geoValue('country');";
			$reverseJS.="$('#{$this->countryId}').val(country);";
			$fields[] = '#'.$this->countryId;
		}
		if($this->formId !== null)
		{
			$reverseJS.=<<<EOJS
var form=$('#{$this->formId}');
if(form.length && $.fn.yiiactiveform){
	var settings = form.data('settings');
	$.each(settings.attributes, function(){this.status = 3;});
	$.fn.yiiactiveform.validate(form,function(data){
		$.each(settings.attributes, function(i, attribute){
			$.fn.yiiactiveform.updateInput(attribute, data, form);
		});
	});
}
EOJS;
		}
		$fieldsStr = implode(',', $fields);
		$fieldsJS = 
			"$('#{$this->latId}, #{$this->lngId}').change(function(){
				var latLng = new google.maps.LatLng($('#{$this->latId}').val(), $('#{$this->lngId}').val());
				panToMap(geomap,latLng,hgeomap);
			});
			//var fields = ['".implode("','", $fields)."'];
			//$('{$fieldsStr}').change(function(){
			//	$('#{$this->latId}, #{$this->lngId}').val('');
			//});
			";
	
		$js = <<<EOD
			function panToMap(m,latLng,m2){
				m.panTo(latLng);
				if(m2!=null) m2.panTo(latLng);
				if(gmarker.getPosition().lat()!=latLng.lat())
					gmarker.setPosition(latLng);
				if(hgmarker!=null && hgmarker.getPosition().lat()!=latLng.lat())
					hgmarker.setPosition(latLng);
				updateFields(latLng);
			}
			function dragEnd(e){
				panToMap(geomap,e.latLng,hgeomap);
			}
			function updateFields(ll){
				$updateFieldsJS
			}
			function reverseGeocode() {
				lastReverseGeocode = new Date();
				geocoder.geocode({latLng:geomap.getCenter()},reverseGeocodeResult);
			}
			function reverseGeocodeResult(results, status) {
				currentReverseGeocodeResponse = results;
				if(status == 'OK') {
					if(results.length == 0)
						return;
					else{
						var str = results[0].formatted_address;
						if($('#geoaddress').length) $('#geoaddress').val(str);
						if($('#hgeoaddress').length) $('#hgeoaddress').val(str);
						$reverseJS
					}
				} else
					return;
			}
			function geocode(id) {
				var address = $("#"+id).val();
				if ($.trim(address)=='Enter address here') return false;
				geocoder.geocode({'address': address, 'partialmatch': true}, geocodeResult);
				return false;
			}
			function geocodeResult(results, status) {
				if (status == 'OK' && results.length > 0){
					if(geomap){ 
						geomap.fitBounds(results[0].geometry.viewport);
						gmarker.setPosition(geomap.getCenter());
						updateFields(geomap.getCenter());
					}
					if(hgeomap){
						hgeomap.fitBounds(results[0].geometry.viewport);
						hgmarker.setPosition(geomap.getCenter());
					}
					reverseGeocodeResult(results, status);
				}else
					qAlert("Geocode was not successful for the following reason: " + status);
			}
			function geoValue(type){
				var i, j, result, types, results;
				results = currentReverseGeocodeResponse[0].address_components;
				// Loop through the Geocoder result set. Note that the results
				// array will change as this loop can self iterate.
				for (i = 0; i < results.length; i++) {
					result = results[i];
					types = result.types;
					for (j = 0; j < types.length; j++) {
						if (types[j] === type) 
							return result.long_name || '';
					}
				}
				return '';
			}
EOD;
		$cs = Yii::app()->clientScript;
		$cs->registerScript('EGMapGeocodeToolJS', $js, CClientScript::POS_END);
		if($this->latId && $this->lngId) 
			$cs->registerScript('EGeocodeToolJSor',$fieldsJS,  CClientScript::POS_READY);
	}
}

?>