<?php

class Application_Model_Activity
{
	private $_activityID;
	private $_userName;
	private $_userActivity;
	private $_userActivityDateTime;
	private $_module;
	
	public function __construct(array $options = null){
		if (is_array($options)){
			$this->setOptions($options);
		}
	}
	
	public function __set($name, $value){
		$method = 'set' . $name;
		
		if (('mapper' == $name)  || !method_exists($this, $method)){
			throw new Exception('Invalid language property');
		}
		
		$this->$method($value);
	}
	
	public function __get($name){
		$method = 'get' . $name;
		
		if (('mapper' == $name) || !method_exists($this, $method)){
			throw new Exception('Invalid language property');
		}
		
		return $this->$method();
	}
	
	public function setOptions(array $options){
		$methods = get_class_methods($this);
		foreach($options as $key => $value){
			$method = 'set' . ucfirst($key);
			if(in_array($method, $methods)){
				$this->$method($value);
			}
		}
		return $this;
	}
	
	public function setActivityID($activityID){
		$this->_activityID = (int)$activityID;
		return $this;
	}
	
	public function getActivityID(){
		return $this->_activityID;
	}
	
	public function setUserName($userName){
		$this->_userName = (string)$userName;
		return $this;
	}	
	
	public function getUserName(){
		return $this->_userName;
	}
	
	public function setUserActivity($userActivity){
		$this->_userActivity = (string)$userActivity;
		return $this;
	}
	
	public function getUserActivity(){
		return $this->_userActivity;
	}
	
	public function setUserActivityDateTime($userActivityDateTime){
		$this->_userActivityDateTime = (string)$userActivityDateTime;
		return $this;
	}
	
	public function getUserActivityDateTime(){
		return $this->_userActivityDateTime;
	}

	public function setModule($module){
		$this->_module = (string)$module;
		return $this;
	}
	
	public function getModule(){
		return $this->_module;
	}	
}
