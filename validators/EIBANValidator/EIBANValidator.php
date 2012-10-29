<?php
/**
 * 
 * EIBANValidator class
 * 
 * International Bank Account Number Validator
 *
 * The validator implements Mod 10 algorithm to validate a IBAN Number. 
 * Also checks if its length correct.
 * 
 * References of the Mod97-10 validation
 * http://en.wikipedia.org/wiki/International_Bank_Account_Number#Validating_the_IBAN
 *
 *
 * @version  1.0
 * @access   public
 * @author   Antonio Ramirez http://www.ramirezcobos.com
 *
 * @copyright 
 * 
 * Copyright (c) 2011 Antonio Ramirez Cobos
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
class EIBANValidator extends CValidator
{
	const NOTSUPPORTED 		= 'IBAN_Not_Supported';
	const WRONGFORMAT		= 'Wrong_IBAN_Format';
	const WRONGLENGTH		= 'Wrong_IBAN_Length';
	const CHECKFAILED		= 'IBAN_Check_Failed';

	 /**
	 * Validation failure message template definitions
	 *
	 * @var array
	 */
	protected $_messages = array(
		self::NOTSUPPORTED => "Unknown country within the IBAN '{value}'",
		self::WRONGFORMAT  => "'{value}' has a false IBAN format",
		self::WRONGLENGTH  => "'{value}' has wrong IBAN length for specified country",
		self::CHECKFAILED  => "'{value}' has failed the IBAN check",
	);

	/**
	 * IBAN lengths for each country
	 * 
	 * @var array
	 */
	protected $_lengths = array(
		'AD'=>24,'AT'=>20,'BA'=>20,'BE'=>16,'BG'=>22,
		'CH'=>21,'CS'=>22,'CY'=>28,'CZ'=>24,'DE'=>22,
		'DK'=>18,'EE'=>20,'ES'=>24,'FR'=>27,'FI'=>18,
		'GB'=>22,'GI'=>23,'GR'=>27,'HR'=>21,'HU'=>28,
		'IE'=>22,'IS'=>26,'IT'=>27,'LI'=>21,'LU'=>20,
		'LT'=>20,'LV'=>21,'MC'=>27,'ME'=>22,'MU'=>30,
		'MK'=>19,'MT'=>31,'NC'=>27,'NL'=>18,'NO'=>15,
		'PF'=>27,'PL'=>28,'PT'=>25,'PM'=>27,'RO'=>24,
		'RS'=>22,'SA'=>24,'SE'=>24,'SI'=>19,'SK'=>24,
		'SM'=>27,'TF'=>27,'TN'=>24,'TR'=>26,'YT'=>27,
		'WF'=>27
	);

	/**
	 * IBAN patterns for each country
	 * 
	 * @var array
	 */
	protected $_patterns = array(
		'AD' => '/^AD[0-9]{2}[0-9]{8}[A-Z0-9]{12}$/',				// Andorra
		'AT' => '/^AT[0-9]{2}[0-9]{5}[0-9]{11}$/',					// Austria
		'BA' => '/^BA[0-9]{2}[0-9]{6}[0-9]{10}$/',					// Bosnia and Herzegovina
		'BE' => '/^BE[0-9]{2}[0-9]{3}[0-9]{9}$/',					// Belgium
		'BG' => '/^BG[0-9]{2}[A-Z]{4}[0-9]{4}[0-9]{2}[A-Z0-9]{8}$/', // Bulgaria
		'CH' => '/^CH[0-9]{2}[0-9]{5}[A-Z0-9]{12}$/',				// Switzerland
		//  CS to Serbia and Montenegro until the split into rs (Serbia) and me (Montenegro)
		'CS' => '/^CS[0-9]{2}[0-9]{3}[0-9]{15}$/', 					// Serbia and Montenegro
		'CY' => '/^CY[0-9]{2}[0-9]{8}[A-Z0-9]{16}$/',				// Cyrus
		'CZ' => '/^CZ[0-9]{2}[0-9]{4}[0-9]{16}$/',					// Czech Republic
		'DE' => '/^DE[0-9]{2}[0-9]{8}[0-9]{10}$/',					// Germany
		'DK' => '/^DK[0-9]{2}[0-9]{4}[0-9]{10}$/',					// Denmark
		'EE' => '/^EE[0-9]{2}[0-9]{4}[0-9]{12}$/',					// Estonia
		'ES' => '/^ES[0-9]{2}[0-9]{8}[0-9]{12}$/',					// Spain
		'FR' => '/^FR[0-9]{2}[0-9]{10}[A-Z0-9]{13}$/',				// France
		'FI' => '/^FI[0-9]{2}[0-9]{6}[0-9]{8}$/',					// Finland
		'GB' => '/^GB[0-9]{2}[A-Z]{4}[0-9]{14}$/',					// United Kingdom
		'GI' => '/^GI[0-9]{2}[A-Z]{4}[A-Z0-9]{15}$/',				// Gibraltar
		'GR' => '/^GR[0-9]{2}[0-9]{7}[A-Z0-9]{16}$/',				// Greece
		'HR' => '/^HR[0-9]{2}[0-9]{7}[0-9]{10}$/',					// Croatia
		'HU' => '/^HU[0-9]{2}[0-9]{7}[0-9]{1}[0-9]{15}[0-9]{1}$/',	// Hungary
		'IE' => '/^IE[0-9]{2}[A-Z0-9]{4}[0-9]{6}[0-9]{8}$/',		// Ireland
		'IS' => '/^IS[0-9]{2}[0-9]{4}[0-9]{18}$/',					// Iceland
		'IT' => '/^IT[0-9]{2}[A-Z]{1}[0-9]{10}[A-Z0-9]{12}$/',		// Italy
		'LI' => '/^LI[0-9]{2}[0-9]{5}[A-Z0-9]{12}$/',				// Liechtenstein
		'LU' => '/^LU[0-9]{2}[0-9]{3}[A-Z0-9]{13}$/',				// Luxembourg
		'LT' => '/^LT[0-9]{2}[0-9]{5}[0-9]{11}$/',					// Lithuania
		'LV' => '/^LV[0-9]{2}[A-Z]{4}[A-Z0-9]{13}$/',				// Latvia
		'MC' => '/^MC(\d{2})(\d{5})(\d{5})([A-Za-z0-9]{11})(\d{2})$/',	// Monaco
		'ME' => '/^ME(\d{2})(\d{3})(\d{13})(\d{2})$/',				// Montenegro
		'MU' => '/^MU(\d{2})([A-Z]{4})(\d{2})(\d{2})(\d{12})(\d{3})([A-Z]{3})$/',// Mauritius	
		'MK' => '/^MK(\d{2})(\d{3})([A-Za-z0-9]{10})(\d{2})$/',			// Macedonia MK07 250 1200000589 84 3n,10c,2n
		'MT' => '/^MT[0-9]{2}[A-Z]{4}[0-9]{5}[A-Z0-9]{18}$/',			// Malta
		'NC' => '/^NC(\d{2})(\d{5})(\d{5})([A-Za-z0-9]{11})(\d{2})$/',	// New Caledonia
		'NL' => '/^NL[0-9]{2}[A-Z]{4}[0-9]{10}$/',						// The Netherlands
		'NO' => '/^NO[0-9]{2}[0-9]{4}[0-9]{7}$/',					// Norway
		'PF' => '/^PF(\d{2})(\d{5})(\d{5})([A-Za-z0-9]{11})(\d{2})$/',	// French Polynesia
		'PL' => '/^PL[0-9]{2}[0-9]{8}[0-9]{16}$/',					// Poland
		'PM' => '/^PM(\d{2})(\d{5})(\d{5})([A-Za-z0-9]{11})(\d{2})$/',	// Saint Pierre et Miquelon
		'PT' => '/^PT[0-9]{2}[0-9]{8}[0-9]{13}$/',					// Portugal
		'RO' => '/^RO[0-9]{2}[A-Z]{4}[A-Z0-9]{16}$/',				// Romania
		'RS' => '/^RS(\d{2})(\d{3})(\d{13})(\d{2})$/',				// Serbia
		'SA' => '/^SA(\d{2})(\d{2})([A-Za-z0-9]{18})$/',			// Saudi Arabia
		'SE' => '/^SE[0-9]{2}[0-9]{3}[0-9]{17}$/',					// Sweden
		'SI' => '/^SI[0-9]{2}[0-9]{5}[0-9]{8}[0-9]{2}$/', 			// Slovenia
		'SK' => '/^SK[0-9]{2}[0-9]{4}[0-9]{16}$/',					// Slovak Republic
		'SM' => '/^SM(\d{2})([A-Z]{1})(\d{5})(\d{5})([A-Za-z0-9]{12})$/',	// San Marino	
		'TF' => '/^TF(\d{2})(\d{5})(\d{5})([A-Za-z0-9]{11})(\d{2})$/',		// French Southern Territories
		'TN' => '/^TN[0-9]{2}[0-9]{5}[0-9]{15}$/',					// Tunisia
		'TR' => '/^TR[0-9]{2}[0-9]{5}[A-Z0-9]{17}$/',				// Turkey
		'YT' => '/^YT(\d{2})(\d{5})(\d{5})([A-Za-z0-9]{11})(\d{2})$/',		// Mayotte
		'WF' => '/^WF(\d{2})(\d{5})(\d{5})([A-Za-z0-9]{11})(\d{2})$/'		// Wallis and Futuna Islands
	);

	/**
	 * @var boolean whether the attribute value can be null or empty. Defaults to true,
	 * meaning that if the attribute is empty, it is considered valid.
	 */
	public $allowEmpty = true;

	/**
	 * Validates a single attribute.
	 * 
	 * @param CModel $object the data object being validated
	 * @param string $attribute the name of the attribute to be validated.
	 */
	protected function validateAttribute($object, $attribute)
	{
		$value=$object->$attribute;
		if ($this->isEmpty($value))
		{
			if ($this->allowEmpty)
			{
				return;
			}
			else
			{
				$arrValidators = $object->getValidators($attribute);
				foreach ($arrValidators as $objValidator)
				{ // do not duplicate error message if attribute is already required
					if ($objValidator instanceof CRequiredValidator)
						return;
				}

				$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} cannot be blank.');
				$this->addError($object,$attribute,$message);
			}
		}
		else
		{
			$return = $this->validateIBAN($value);

			if (true !== $return)
			{
				$message=$this->message!==null?$this->message:$this->getErrorMessage($return, $value);
				$this->addError($object,$attribute,$message);
			}
		}
	}

	/**
	 * Validates IBAN Number
	 * 
	 * @param string $ibanNumber
	 * @return bool|string true if validated, else returns error message.
	 */
	public function validateIBAN($ibanNumber)
	{
		// remove non-basic roman letter or digit characters
		$ibanNumber =  preg_replace('/[^A-Z0-9]/', '', ltrim(strtoupper($ibanNumber)));
		// remove IBAN if any
		$ibanNumber = preg_replace('/^IBAN/','',$ibanNumber);

		// get country part
		$country = substr($ibanNumber, 0, 2);
		if (!array_key_exists($country, $this->_patterns))
			return self::NOTSUPPORTED;

		// check pattern
		if (!preg_match($this->_patterns[$country], $ibanNumber))
			return self::WRONGFORMAT;

		// check length
		if (strlen($ibanNumber) != $this->_lengths[$country])
			return self::WRONGLENGTH;

		// verify checksum
		// move first four chars (country code and checksum) to the end of the string
		$format = substr($ibanNumber, 4) . substr($ibanNumber, 0, 4);

		$format = str_replace(
			range('A','Z'),
			array('10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22',
				  '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35'),
			$format
		);
		// perform MOD97-10 checksum calculation
		$temp = intval(substr($format,0,1));
		$len  = strlen($format);
		for ($pos = 1; $pos < $len; ++$pos){
			$temp *= 10;
			$temp += intval(substr($format,$pos,1));
			$temp %=97;
		}

		if ($temp != 1)
			return self::CHECKFAILED;

		return true;
	}

	/**
	 * 
	 * Returns a formatted error message
	 * 
	 * @param string $error
	 * @param string $ibanNumber
	 * @return string translated error message
	 */
	public function getErrorMessage($error, $ibanNumber)
	{
		return Yii::t('EIBANValidator',$this->_messages[$error], array('{value}'=>$ibanNumber));
	}
}