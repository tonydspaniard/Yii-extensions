<?php
/**
 * 
 * ECCValidator class
 * 
 * Credit Card Validator
 *
 * The validator implements Mod 10 algorithm to validate a credit card number. 
 * Also checks if its prefix and expiration date are correct.
 * 
 * References of the Mod 10 algorithm
 * http://en.wikipedia.org/wiki/Luhn_algorithm#Mod_10.2B5_Variant
 *
 *
 *
 * @see      http://www.yiiframework.com
 * @version  1.0
 * @access   public
 * @author   Antonio Ramirez http://www.ramirezcobos.com
 */

class ECCValidator extends CValidator{
	
    /**
     * 
     * Detected Credit Card list
     * @var string
     * @link http://en.wikipedia.org/wiki/Bank_card_number#cite_note-NoMoreBankCard-4
     */
    const MAESTRO          = 'Maestro';
    const SOLO             = 'Solo';
    const VISA             = 'Visa';
    const ELECTRON		   = 'Electron';
	const AMERICAN_EXPRESS = 'American_Express';
	const MASTERCARD       = 'Mastercard';
	const DISCOVER         = 'Discover';
	const JCB              = 'JCB';
	const VOYAGER		   = 'Voyager';
	const DINERS_CLUB      = 'Diners_Club';
	const SWITCH_CARD	   = 'Switch';
	const LASER			   = 'Laser';
	const ALL              = 'All';
	/**
	 * 
	 * @var array holds the regex patterns to check for valid 
	 * Credit Card number prefixes
	 */
	protected $patterns = array(
		self::MASTERCARD=>'/^5[1-5][0-9]{14}$/',
		self::VISA=>'/^4[0-9]{12}([0-9]{3})?$/',
		self::AMERICAN_EXPRESS=>'/^3[47][0-9]{13}$/',
		self::DINERS_CLUB=>'/^3(0[0-5]|[68][0-9])[0-9]{11}$/',
		self::DISCOVER=>'/^(6011\d{12}|65\d{14})$/',
		self::JCB=>'/^(3[0-9]{4}|2131|1800)[0-9]{11}$/',
		self::VOYAGER=>'/^8699[0-9]{11}$/',
		self::SOLO=>'/^(6334[5-9][0-9]|6767[0-9]{2})\\d{10}(\\d{2,3})?$/',
		self::MAESTRO=>'/^(?:5020|6\\d{3})\\d{12}$/',
		self::SWITCH_CARD=>'/^(?:49(03(0[2-9]|3[5-9])|11(0[1-2]|7[4-9]|8[1-2])|36[0-9]{2})\\d{10}(\\d{2,3})?)|(?:564182\\d{10}(\\d{2,3})?)|(6(3(33[0-4][0-9])|759[0-9]{2})\\d{10}(\\d{2,3})?)$/',
		self::ELECTRON=>'/^(?:417500|4026\\d{2}|4917\\d{2}|4913\\d{2}|4508\\d{2}|4844\\d{2})\\d{10}$/',
		self::LASER=>'/^(?:6304|6706|6771|6709)\\d{12}(\\d{2,3})?$/',
		self::ALL=>'/^(5[1-5][0-9]{14}|4[0-9]{12}([0-9]{3})?|3[47][0-9]{13}|3(0[0-5]|[68][0-9])[0-9]{11}|(6011\d{12}|65\d{14})|(3[0-9]{4}|2131|1800)[0-9]{11}|2(?:014|149)\\d{11}|8699[0-9]{11}|(6334[5-9][0-9]|6767[0-9]{2})\\d{10}(\\d{2,3})?|(?:5020|6\\d{3})\\d{12}|56(10\\d\\d|022[1-5])\\d{10}|(?:49(03(0[2-9]|3[5-9])|11(0[1-2]|7[4-9]|8[1-2])|36[0-9]{2})\\d{10}(\\d{2,3})?)|(?:564182\\d{10}(\\d{2,3})?)|(6(3(33[0-4][0-9])|759[0-9]{2})\\d{10}(\\d{2,3})?)|(?:417500|4026\\d{2}|4917\\d{2}|4913\\d{2}|4508\\d{2}|4844\\d{2})\\d{10}|(?:417500|4026\\d{2}|4917\\d{2}|4913\\d{2}|4508\\d{2}|4844\\d{2})\\d{10})$/'
	);
	/**
	 * 
	 * @var string set with selected Credit Card type to check -ie ECCValidator::MAESTRO
	 */
	public $format = self::ALL;
	/**
	 * @var boolean whether the attribute value can be null or empty. Defaults to true,
	 * meaning that if the attribute is empty, it is considered valid.
	 */
	public $allowEmpty=true;
	/**
	 * (non-PHPdoc)
	 * @see CValidator::validateAttribute()
	 */
	protected function validateAttribute($object,$attribute){
		
		$value=$object->$attribute;
		if($this->allowEmpty && $this->isEmpty($value))
			return;
			
		
		if(!$this->validateNumber($value))
		{
			$message=$this->message!==null?$this->message:Yii::t('ECCValidator','{attribute} is not a valid Credit Card number.');
			$this->addError($object,$attribute,$message);
		}
	}
	/**
	 * 
	 * Validates a Credit Card number
	 * @param string $creditCardNumber
	 */
	public function validateNumber($creditCardNumber){
		
		if(!$this->checkType())
			throw new CException(Yii::t('ECCValidator','The "format" property must be specified with a supported Credit Card format.'));
			
		 $creditCardNumber = preg_replace('/[ -]+/', '', $creditCardNumber);
		 
		 return $this->checkFormat($creditCardNumber) && $this->mod10($creditCardNumber);
	}
	/**
	 * 
	 * Validates a Credit Card date
	 * @param integer $creditCardExpiredMonth
	 * @param integer $creditCardExpiredYear
	 */
	public function validateDate($creditCardExpiredMonth, $creditCardExpiredYear){
		
		$currentYear = intval(date('Y'));
		
		if(is_scalar($creditCardExpiredMonth)) $creditCardExpiredMonth = intval($creditCardExpiredMonth);
		if(is_scalar($creditCardExpiredYear)) $creditCardExpiredYear = intval($creditCardExpiredYear);
		
		return 	is_integer($creditCardExpiredMonth) && $creditCardExpiredMonth >= 1 && $creditCardExpiredMonth <= 12 && 
				is_integer( $creditCardExpiredYear ) && $creditCardExpiredYear > $currentYear && $creditCardExpiredYear < $currentYear+10;
	}
	/**
	 * 
	 * Validates Credit Card holder
	 * @param string $creditCardHolder
	 */
	public function validateName($creditCardHolder){
		
		return !empty( $creditCardHolder ) && eregi('^[A-Z ]+$', $creditCardHolder);
	}
	/**
	 * 
	 * Validates holder, number, and dates of Credit Card numbers
	 * 
	 * @param string $creditCardHolder
	 * @param string $creditCardNumber
	 * @param integer $creditCardExpiredMonth
	 * @param integer $creditCardExpiredYear
	 */
	public function validateAll($creditCardHolder, $creditCardNumber, $creditCardExpiredMonth, $creditCardExpiredYear){
		
		return $this->validateName($creditCardHolder) && $this->validateNumber($creditCardNumber) && $this->validateDate($creditCardExpiredMonth, $creditCardExpiredYear);
		
	}
	/**
     * 
     * Checks Credit Card Prefixes
     *
     * @access  private
     * @param   string  cardNumber
     * @return  boolean true|false
     */
    protected function checkFormat($cardNumber)
    {
        return preg_match('/^[0-9]+$/',$cardNumber) && preg_match( $this->patterns[$this->format], $cardNumber );
    }
	/**
     * 
     * Check credit card number by Mod 10 algorithm
     *
     * @access  private
     * @param   string   carNumber
     * @return  boolean
     * @see     http://en.wikipedia.org/wiki/Luhn_algorithm#Mod_10.2B5_Variant
     */
    protected function mod10($cardNumber)
    {
        $cardNumber = strrev($cardNumber);
        $numSum = 0;
        for($i = 0; $i < strlen($cardNumber); $i++) {
            $currentNum = substr($cardNumber, $i, 1);
            if ($i % 2 == 1) {
                $currentNum *= 2;
            }
            if ($currentNum > 9) {
                $firstNum = $currentNum % 10;
                $secondNum = ($currentNum - $firstNum) / 10;
                $currentNum = $firstNum + $secondNum;
            }
            $numSum += $currentNum;
        }
        return ($numSum % 10 == 0);
    }
    /**
     * 
     * Checks if Credit Card Format is a supported one
     * and builds new pattern format in case user has
     * a mixed match search (mastercard|visa)
     * 
     * @access private
     * @return boolean
     */
    protected function checkType(){
    	
    	if(is_scalar($this->format)){
    		return array_key_exists($this->format, $this->patterns);
    	}
    	else if (is_array($this->format)){
    		$pattern = array();
    		foreach($this->format as $f){
    			if(!array_key_exists($f, $this->patterns)) return false;
    			$pattern[] = substr($this->patterns[$f], 2,strlen($this->patterns[$f])-4);
    		}
    		$this->format = 'custom';
    		$this->patterns[$this->format] = '/^('.join('|',$pattern).')$/';
    		return true;
    	}
    	return false;
    	
    }
}