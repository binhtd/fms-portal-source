<?php

class Application_Model_EmailTemplate
{
	private $_emailTemplateID;
	private $_emailTemplateSubject;
	private $_emailTemplateContent;
	private $_emailTemplateStatus;
	private $_emailTemplateIsActive;
	
	public function __construct(array $options = null){
		if(is_array($options)){
			$this->setOptions($options);
		}
	}
	
	public function __set($name, $value){
		$method = 'set' . $name;
		if ('mapper' == $name || !method_exists($this, $method)){
			throw new Exception('Invalid user property');
		}
		
		$this->$method($value);
	}
		
	public function __get($name){
		$method = 'get' . $name;

		if (('mapper' == $name) || !method_exists($this, $method)){
			throw new Exception('Invalid user property');
		}
		
		return $this->$method();
	}
	
	public function setOptions(array $options){
		$methods = get_class_methods($this);
		foreach($methods as $key => $value){
			$method = 'set' . ucfirst($key);			
			if(in_array($method, $methods)){
				$this->$method($value);
			}
		}
		return $this;
	}
	
	public function setEmailTemplateID($emailTemplateID){
		$this->_emailTemplateID = (int)$emailTemplateID;
		return $this;
	}
	
	public function getEmailTemplateID(){
		return $this->_emailTemplateID;
	}
	
	public function setEmailTemplateContent($emailTemplateContent){
		$this->_emailTemplateContent = (string)$emailTemplateContent;
		
		return $this;
	}
	
	public function getEmailTemplateContent(){
		return $this->_emailTemplateContent;
	}
			
	public function setEmailTemplateStatus($emailTemplateStatus){
		$this->_emailTemplateStatus = (string)$emailTemplateStatus;
		
		return $this;
	}
	
	public function getEmailTemplateStatus(){
		return $this->_emailTemplateStatus;
	}	
	
	public function setEmailTemplateIsActive($emailTemplateIsActive){
		$this->_emailTemplateIsActive = (string)$emailTemplateIsActive;
		
		return $this;
	}
	
	public function getEmailTemplateIsActive(){
		return $this->_emailTemplateIsActive;
	}

	public function setEmailTemplateSubject($emailTemplateSubject){
		$this->_emailTemplateSubject = (string)$emailTemplateSubject;
		
		return $this;
	}
	
	public function getEmailTemplateSubject(){
		return $this->_emailTemplateSubject;
	}	
}

