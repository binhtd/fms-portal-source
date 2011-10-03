<?php

class Application_Model_Session
{
	private $_id;
	private $_modified;
	private $_lifetime;
	private $_data;

	public function __construct(array $options = null){
		if(is_array($options)){
			$this->setOptions($options);
		}
	}
	
	public function __set($name, $value){
		$method = 'set' . $name;
		if ('mapper' == $name || !method_exists($this, $method)){
			throw new Exception('Invalid session property');
		}
		
		$this->$method($value);
	}
		
	public function __get($name){
		$method = 'get' . $name;

		if (('mapper' == $name) || !method_exists($this, $method)){
			throw new Exception('Invalid session property');
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
	
	public function setId($id){
		$this->_id = (string)$id;
		return $this;
	}
	
	public function getId(){
		return $this->_id;
	}
	
	public function setModified($modified){
		$this->_modified = (int)$modified;
		
		return $this;
	}
	
	public function getModified(){
		return $this->_modified;
	}
	
	public function setLifetime($lifetime){
		$this->_lifetime = (int)$lifetime;
		
		return $this;
	}
	
	public function getLifetime(){
		return $this->_lifetime;
	}
	
	public function setData($data){
		$this->_data = (string)$data;
		return $this;
	}
	
	public function getData(){
		return $this->_data;
	}

}

