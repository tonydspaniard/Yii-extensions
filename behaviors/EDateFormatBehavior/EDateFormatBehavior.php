<?php
/**
 * EDateFormatBehavior class
 *
 * @author Antonio Ramirez <antonio@ramirezcobos.com>
 */
class EDateFormatBehavior extends CActiveRecordBehavior
{
	//.. array of columns that have dates to be converted
	public $dateColumns = array();
	public $dateTimeColumns = array();
	
	public $dateFormat = 'm/d/Y';
	public $dateTimeFormat = 'm/d/Y H:i';
	/**
	 * Convert from $dateFormat to UNIX timestamp dates before saving
	 */
	public function beforeSave($event)
	{
		$this->format($this->dateColumns, $this->dateFormat);
		$this->format($this->dateTimeColumns, $this->dateTimeFormat);
		return parent::beforeSave($event);
	}
	/**
	 * Converts UNIX timestamp dates to $dateFormat after read from database
	 */
	public function afterFind($event)
	{
		$this->format($this->dateColumns, $this->dateFormat, false);
		$this->format($this->dateTimeColumns, $this->dateTimeFormat, false);
		return parent::afterFind($event);
	}
	/**
	 *
	 * Formats to UNIX timestamp or $dateFormat as specified. Note that 
	 * if using $dateFormat then assumed timestamp value
	 * @param array $columns the columns attributes to format
	 * @param string $format the format to convert the date to
	 * @param boolean $strtotime if boolean, will convert to UNIX timestamp
	 * @return void 
	 */
	protected function format($columns, $format, $strtotime=true)
	{
		if(empty($columns)) return;
		
		foreach($this->getOwner()->getAttributes() as $key=>$value)
		{
			if(in_array($key, $columns) && !empty($value))
			{
				$dt = $this->getOwner()->{$key};
				$this->getOwner()->{$key} = $strtotime ? strtotime($dt) : date($format,$dt);
			}
		}
	}
}
