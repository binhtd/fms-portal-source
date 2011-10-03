<?php

class Application_Model_Language
{
	private $_languageID;
	private $_languageName;
	private $_languageIsActive;
	private $_languageIsShowInSourceList;
	private $_languageIsShowInTargetList;
	
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
	
	public function setLanguageID($languageID){
		$this->_languageID = (int)$languageID;
		return $this;
	}
	
	public function getLanguageID(){
		return $this->_languageID;
	}
	
	public function setLanguageName($languageName){
		$this->_languageName = (string)$languageName;
		return $this;
	}	
	
	public function getLanguageName(){
		return $this->_languageName;
	}
	
	public function setLanguageIsActive($languageIsActive){
		$this->_languageIsActive = (string)$languageIsActive;
		return $this;
	}
	
	public function getLanguageIsActive(){
		return $this->_languageIsActive;
	}
	
	public function setLanguageIsShowInSourceList($languageIsShowInSourceList){
		$this->_languageIsShowInSourceList = (string)$languageIsShowInSourceList;
		return $this;
	}
	
	public function getLanguageIsShowInSourceList(){
		return $this->_languageIsShowInSourceList;
	}
	
	
	public function setLanguageIsShowInTargetList($languageIsShowInTargetList){
		$this->_languageIsShowInTargetList = (string)$languageIsShowInTargetList;
		return $this;
	}
	
	public function getLanguageIsShowInTargetList(){
		return $this->_languageIsShowInTargetList;
	}
}
