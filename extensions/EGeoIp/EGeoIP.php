<?php
/**
 * EGeoIP Class file
 * 
 * @author Antonio Ramirez 
 * @link http://www.ramirezcobos.com 
 * 
 * This class uses the free online webservice of http://www.geoplugin.com/ 
 * in order to capture IP information
 * 
 * 
 * THIS SOFTWARE IS PROVIDED BY THE CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
class EGeoIP extends CComponent{
	/**
	 * 
	 * link to service
	 * @var string
	 */
	protected $_service ='http://www.geoplugin.net/php.gp?ip={IP}&base_currency={CURRENCY}';
	/**
	 * 
	 * reponse data received
	 * @var CMap
	 */
	protected $_data;
	/**
	 * 
	 * Base currency. CurrencyConversion property will be set based on this
	 * property and the returned by the request to the service
	 * @var string
	 * @link http://www.geoplugin.com/webservices/php#php_currency_converter
	 * @link http://www.geoplugin.com/iso4217
	 */
	protected $_currency = 'USD';
	/**
	 * 
	 * Constructor
	 */
	public function __construct(){
		$this->_data = new CMap();
	}
	/**
	 * 
	 * Locates IP information
	 * @param string $ip address. If null, it will locate the IP of request
	 */
	public function locate( $ip = null ){
		if( null === $ip ) $ip = $_SERVER['REMOTE_ADDR'];
		
		$host = str_replace('{IP}',$ip, $this->_service);
		$host = str_replace('{CURRENCY}', $this->_currency, $host );
		
		$response = $this->fetch($host);
		
		if(!is_null( $response) && is_array($response))
		{
			$this->_data->mergeWith($response);
			$this->_data->add('ip',$ip);
			return true;	
		}
		return true;
	}
	/**
	 * 
	 * Converts an amount to the located currency by using the 
	 * located currency converter value. This function can only be
	 * used after a call to locate function has been executed.
	 * @param float | integer $amount
	 * @param integer $float number of decimals
	 * @param boolean $symbol to display the currency symbol or not (true by default)
	 */
	public function currencyConvert($amount, $float=2, $symbol=true) {
		
		if( null === $this->getCurrencyConverter() || !is_numeric($amount) )
			return false;
		
		$converted = round( ($amount * $this->getCurrencyConverter()), $float );
		if ( $symbol === true ) {
			if($this->getCurrencySymbol() === 'USD')
				$converted   = $this->getCurrencySymbol().$converted;
			else $converted .= $this->getCurrencySymbol();
		} 
		
		return $converted;
	}
	/**
	 * 
	 * Set base property
	 * @param string $currency
	 * @link http://www.geoplugin.com/iso4217
	 */
	public function setBaseCurrency( $currency = 'USD' ){
		$this->_currency = $currency;
	}
	/**
	 * 
	 * Returns base currency
	 */
	public function getBaseCurrency(){
		return $this->_currency;
	}
	/**
	 * 
	 * Returns located IP
	 */
	public function getIp(){
		return $this->_data->itemAt('ip');
	}
	/**
	 * 
	 * Returns located City
	 */
	public function getCity(){
		return $this->_data->itemAt('geoplugin_city');
	}
	/**
	 * 
	 * Returns located region
	 */
	public function getRegion(){
		return $this->_data->itemAt('geoplugin_region');
	}
	/**
	 * 
	 * Returns located area code
	 */
	public function getAreaCode(){
		return $this->_data->itemAt('geoplugin_areaCode');
	}
	/**
	 * 
	 * Returns located DMA code
	 */
	public function getDma(){
		return $this->_data->itemAt('geoplugin_dmaCode');
	}
	/**
	 * 
	 * Returns located area code
	 */
	public function getCountryCode(){
		return $this->_data->itemAt('geoplugin_countryCode');
	}
	/**
	 * 
	 * Returns located country name
	 */
	public function getCountryName(){
		return $this->_data->itemAt('geoplugin_countryName');
	}
	/**
	 * 
	 * Returns located continent code
	 */
	public function getContinentCode(){
		return $this->_data->itemAt('geoplugin_continentCode');
	}
	/**
	 * 
	 * Returns located latitude
	 */
	public function getLatitude(){
		return $this->_data->itemAt('geoplugin_latitude');
	}
	/**
	 * 
	 * Returns located longitude
	 */
	public function getLongitude(){
		return $this->_data->itemAt('geoplugin_longitude');
	}
	/**
	 * 
	 * Returns located currency code
	 */
	public function getCurrencyCode(){
		return $this->_data->itemAt('geoplugin_currencyCode');
	}
	/**
	 * 
	 * Returns located currency symbol
	 */
	public function getCurrencySymbol(){
		return $this->_data->itemAt('geoplugin_currencySymbol');
	}
	/**
	 * 
	 * Returns located currency converter
	 */
	public function getCurrencyConverter(){
		return $this->_data->itemAt('geoplugin_currencyConverter');
	}
	/**
	 * 
	 * Fetches a URI and returns the contents of the call
	 * EHttpClient could also be used
	 * 
	 * @param string $host
	 * @see http://www.yiiframework.com/extension/ehttpclient/
	 */
	protected function fetch($host){
		
		$response = null;
		
		if ( function_exists('curl_init') ) {
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $host);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'EGeoIP Yii Extension Class v1.0');
			$response = curl_exec($ch);
			curl_close ($ch);
			
		} else if ( ini_get('allow_url_fopen') ) {
			
			$response = file_get_contents($host, 'r');
		}
		
		return unserialize($response);
	}

}