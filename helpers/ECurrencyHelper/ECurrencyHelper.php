<?php
/**
 * 
 * ECurrencyHelper class
 * 
 * Allows currency conversions based on the daily currency rates from the 
 * official European Central bank (http://www.ecb.int/ecb/html/index.en.html)
 * 
 * The ECB is the central bank for Europe's single currency, the euro.
 * 
 * This extension also works with Yahoo Finance.
 * Special thanks to Aphraoh for its contribution 
 * -http://www.yiiframework.com/forum/index.php?/user/6213-aphraoh/
 * 
 * The currencies are far too much to include them as constants. Please refer 
 * to the following list for a reference: http://en.wikipedia.org/wiki/List_of_circulating_currencies
 * 
 * All conversions are approx. due to that every bank has its own 'perception'
 * and policies of the rates.
 * 
 * @author Antonio Ramirez Cobos
 * @link http://www.ramirezcobos.com
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
class ECurrencyHelper
{
	// ISO 4217 -three letter currencies supported
	// by ECurrencyHelper
	// http://www.ecb.int/stats/exchange/eurofxref/html/index.en.html
	const EUROPEAN_EURO	= 'EUR';
	const US_DOLLAR		= 'USD';
	const JAPANESE_YEN	= 'JPY';
	const BULGARIAN_LEV	= 'BGN';
	const CZECH_KORUNA	= 'CZK';
	const DANISH_KRONE	= 'DKK';
	const POUND_STERLING	= 'GBP';
	const HUNGARIAN_FORINT	= 'HUF';
	const LITHUANIAN_LITAS	= 'LTL';
	const LATVIAN_LATS	= 'LVL';
	const POLISH_ZLOTY	= 'PLN';
	const NEW_ROMANIAN_LEU	= 'RON';
	const SWEDISH_KRONA	= 'SEK';
	const SWISS_FRANC	= 'CHF';
	const NORWEIGIAN_KRONE	= 'NOK';
	const CROATIAN_KUNA	= 'HRK';
	const RUSSIAN_ROUBLE	= 'RUB';
	const TURKISH_LIRA	= 'TRY';
	const AUSTRALIAN_DOLLAR = 'AUD';
	const BRASILIAN_REAL	= 'BRL';
	const CANADIAN_DOLLAR	= 'CAD';
	const CHINESE_YUAN_RENMINBI = 'CNY';
	const HONG_KONG_DOLLAR	= 'HKD';
	const INDONESIAN_RUPIAH = 'IDR';
	const ISRAELI_SHEKEL	= 'ILS';
	const INDIAN_RUPEE	= 'INR';
	const SOUTH_KOREAN_WON	= 'KRW';
	const MEXICAN_PESO	= 'MXN';
	const MALAYSIAN_RINGGIT = 'MYR';
	const NEW_ZEALAND_DOLLAR = 'NZD';
	const PHILIPPINE_PESO	= 'PHP';
	const SINGAPORE_DOLLAR	= 'SGD';
	const THAI_BAHT		= 'THB';
	const SOUTH_AFRICAN_RAND = 'ZAR';
	
	const USE_CENTRAL_EUROPEAN_BANK = 'BCE';
	const USE_GOOGLE = 'Google';
	const USE_YAHOO = 'Yahoo';
	
	/**
	 *
	 * @var string $uri the URL to access the daily rates from 
	 */
	protected $uri = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';
	/**
	 *
	 * @var string $yahoo the Yahoo URL to make the conversion
	 */
	protected $yahoo = 'http://quote.yahoo.com/d/quotes.csv?s={FROM}{TO}=X&f=l1&e=.csv';
	/**
	 *
	 * @var string $google the Google URL to make the conversion
	 */
	protected $google = 'http://www.google.com/ig/calculator?hl=en&q={AMOUNT}{FROM}=?{TO}';
	/**
	 *
	 * @var array $currencies the loaded currency rates
	 */
	protected $currencies = array();
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->load();
	}
	/**
	 * Returns a specific rate value
	 * IMPORTANT: Rates are those from Central European Bank Only
	 * @param string $currencyCode the three letter currency code
	 * @return float the currency rate, boolean false otherwise
	 */
	public function getRate($currencyCode)
	{
		if(array_key_exists($currencyCode, $this->currencies))
			return $this->currencies[$currencyCode];
		return false;
	}
	/**
	 * Returns loaded currency rates
	 * IMPORTANT: Rates are those from Central European Bank Only
	 * @return array the currencies rates loaded 
	 */
	public function getRates()
	{
		return $this->currencies;
	}
	/**
	 * Loads daily currency conversion rates from the European Central Bank
	 * @return boolean true if successful, false otherwise
	 */
	public function load()
	{
		$xml = $this->_request($this->uri);
		
		$dom = new DOMDocument();
		$dom->preserveWhiteSpace = false;
		$dom->validateOnParse = false;
		$dom->loadXML($xml);
		
		$currencies = $dom->getElementsByTagName('Cube');
		
		if($currencies)
		{
			foreach($currencies as $c)
			{
				if($c->hasAttribute('currency') && $c->hasAttribute('rate'))
					$this->currencies[$c->getAttribute("currency")] = $c->getAttribute("rate");
			}
			$this->currencies['EUR'] = 1;
			return true;
		}
		return false;
	}
	/**
	 * Converts one currency to another based on the EURO conversion rate
	 * @param string $from the currency code to convert from
	 * @param string $to the currency code to convert to
	 * @param float $amount the amount to convert
	 * @return float the converted amount
	 */
	public function convert($from, $to, $amount, $engine=self::USE_CENTRAL_EUROPEAN_BANK )
	{
		if( $engine !== self::USE_CENTRAL_EUROPEAN_BANK &&
			$engine !== self::USE_GOOGLE &&
			$engine !== self::USE_YAHOO )
			throw new CException ('ECurrencyHelper', 'Unsupported conversion engine');
		
		$result;
		if($engine===self::USE_CENTRAL_EUROPEAN_BANK)
			$result = $this->euroConvert ($from, $to, $amount);
		elseif($engine===self::USE_GOOGLE)
			$result = $this->googleConvert ($from, $to, $amount);
		else
			$result = $this->yahooConvert ($from, $to, $amount);
		
		return $result;
	}
	/**
	 * Converts one currency to another based on the EURO conversion rate
	 * @param string $from the currency code to convert from
	 * @param string $to the currency code to convert to
	 * @param float $amount the amount to convert
	 * @return float the converted amount
	 */
	protected function euroConvert($from, $to, $amount)
	{
		if(!array_key_exists($from, $this->currencies) || !array_key_exists($to, $this->currencies))
			throw new CException('ECurrencyHelper','Unsupported currency type');
		
		if($to === 'EUR')
			return (float) $amount / $this->currencies[$from];
		if($from === 'EUR')
			return (float) $amount * $this->currencies[$to];
		
		return (float) ($amount / $this->currencies[$from]) * $this->currencies[$to];
	}
	/**
	 *
	 * Converts one currency to another based on Yahoo finance conversion rate
	 * Special thanks to *Aphraoh* for its contribution 
	 * -http://www.yiiframework.com/forum/index.php?/user/6213-aphraoh/
	 * @param string $from the currency code to convert from
	 * @param string $to the currency code to convert to
	 * @param float $amount the amount to convert
	 * @return float the converted amount false if not successful
	 */
	protected function yahooConvert($from, $to, $amount)
	{
		$uri = str_replace(
			array('{FROM}', '{TO}'), 
			array($from, $to), $this->yahoo);
		//sleep(1); //Be nice to Yahoo, they don't have a lot of hi-spec servers
		$rate = $this->_request($uri);
		return $rate ? (float) $rate * $amount : false;
	}
	/**
	 * Converts one currency to another based on the Google conversion calculator
	 * @param string $from the currency code to convert from
	 * @param string $to the currency code to convert to
	 * @param float $amount the amount to convert
	 * @return float the converted amount
	 */
	protected function googleConvert($from, $to, $amount)
	{
		$uri = str_replace(
			array('{FROM}', '{TO}', '{AMOUNT}'), 
			array($from, $to, $amount), $this->google);
		$raw = $this->_request($uri);
		$data = explode('"', $raw);
		$data = explode(' ', $data['3']);
		
		return trim($data[0]) !== ""? (float) $data[0] : false;
	}
	/**
	 * Requests a specific url and returns its contents
	 * @param string $url the uri to call
	 * @return string raw contents
	 */
	private function _request($url)
	{
	    if (function_exists('curl_version')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$raw_data = curl_exec($ch);
		curl_close($ch);
	    } else // no CUrl, try differently
		$raw_data = file_get_contents($url);
	    return $raw_data;
	}

}
