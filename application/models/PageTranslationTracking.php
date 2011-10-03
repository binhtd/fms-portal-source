<?php

class Application_Model_PageTranslationTracking
{
	private $_pageUrl;
	private $_translationtDate;
	private $_translationLanguage;
	private $_countTranslation;
	
	public function __construct(array $options = null){
		if (is_array($options)){
			$this->setOptions($options);
		}
	}
	
	public function __set($name, $value){
		$method = 'set' . $name;
		
		if (('mapper' == $name)  || !method_exists($this, $method)){
			throw new Exception('Invalid PageTranslationTracking property');
		}
		
		$this->$method($value);
	}
	
	public function __get($name){
		$method = 'get' . $name;
		
		if (('mapper' == $name) || !method_exists($this, $method)){
			throw new Exception('Invalid PageTranslationTracking property');
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
	
	
	public function setPageUrl($pageUrl){
		$this->_pageUrl = (string)$pageUrl;
		return $this;
	}	
	
	public function getPageUrl(){
		return $this->_pageUrl;
	}
	
	public function setTranslationtDate($translationtDate){
		$this->_translationtDate = (string)$translationtDate;
		return $this;
	}
	
	public function getTranslationtDate(){
		return $this->_translationtDate;
	}
	
	public function setTranslationLanguage($translationLanguage){
		$this->_translationLanguage = (string)$translationLanguage;
		return $this;
	}
	
	public function getTranslationLanguage(){
		return $this->_translationLanguage;
	}	
	
	public function setCountTranslation($countTranslation){
		$this->_countTranslation = (string)$countTranslation;
		return $this;
	}
	
	public function getCountTranslation(){
		return $this->_countTranslation;
	}		
}
