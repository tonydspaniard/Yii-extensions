<?php
class EJsonBehavior extends CBehavior{
	
	private $owner;
	private $relations;
	
	public function toJSON(){
		$this->owner = $this->getOwner();
		
		if (is_subclass_of($this->owner,'CActiveRecord')){
			
			$attributes = $this->owner->getAttributes();
			$this->relations 	= $this->getRelated();
			
			$jsonDataSource = array('jsonDataSource'=>array('attributes'=>$attributes,'relations'=>$this->relations));
			
			return CJSON::encode($jsonDataSource);
		}
		return false;
	}
	private function getRelated()
	{	
		$related = array();
		
		$obj = null;
		
		$md=$this->owner->getMetaData();
		
		foreach($md->relations as $name=>$relation){
			
			$obj = $this->owner->getRelated($name);
			
			$related[$name] = $obj instanceof CActiveRecord ? $obj->getAttributes() : $obj;
		}
	    
	    return $related;
	}
}