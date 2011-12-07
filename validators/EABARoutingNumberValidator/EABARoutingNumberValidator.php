<?php
/**
 * 
 * EABARoutingNumberValidator class
 * 
 * ABA Routing Transit Number Validator
 *
 * 
 * 
 * References of the check-digit validation
 * @link http://en.wikipedia.org/wiki/Routing_transit_number
 * @link http://www.brainjar.com/js/validation/
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
class EABARoutingNumberValidator extends CValidator{
	
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
		
		$return = $this->validateABARoutingNumber($value);
		
		if( true !== $return )
		{
			$message=$this->message!==null? $this->message:Yii::t('EABAValidator',"'{value}' has failed the ABA Routine Number check", array('{value}'=>$value));
			$this->addError($object,$attribute,$message);
		}
	}
	/**
	 * 
	 * The check-digit Routing Number function
	 * @param string $routingNumber
	 * @see http://www.brainjar.com/js/validation/
	 */
	function validateABARoutingNumber( $routingNumber ) {
	    $routingNumber = preg_replace('[\D]', '', $routingNumber); 
	    
	    $len = strlen($routingNumber);
	    
	    if( $len !== 9 ) return false;   
	    
	    $checkSum = 0;
	    
	    for ($i = 0; $i < $len; $i+= 3 ) {
	        $checkSum += ($routingNumber[$i] * 3)
	        		  +  ($routingNumber[$i+1] * 7)
	        	 	  +  ($routingNumber[$i+2]);
	    }
	                
	    return ($checkSum !== 0 && ($checkSum % 10) === 0);
	}
}