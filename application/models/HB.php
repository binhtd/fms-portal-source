<?php

class Application_Model_HB
{
	private $_handBackID;
	private $_handOffID;
	private $_handBackTotalNumberOfUploadFiles;
	private $_handBackUploadDate;
	private $_handBackFolderLocation;
	private $_handBackComments;
	private $_allowModifyHbRecord;
	
	public function __construct(array $options = null){
		if(is_array($options)){
			$this->setOptions($options);
		}
	}
	
	public function __set($name, $value){
		$method = 'set' . $name;
		if ('mapper' == $name || !method_exists($this, $method)){
			throw new Exception('Invalid hb property');
		}
		
		$this->$method($value);
	}
		
	public function __get($name){
		$method = 'get' . $name;

		if (('mapper' == $name) || !method_exists($this, $method)){
			throw new Exception('Invalid hb property');
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
	
	public function setHandBackID($handBackID){
		$this->_handBackID= (int)$handBackID;
		return $this;
	}
	
	public function getHandBackID(){
		return $this->_handBackID;
	}
	
	public function setHandOffID($handOffID){
		$this->_handOffID = (int)$handOffID;
		
		return $this;
	}
	
	public function getHandOffID(){
		return $this->_handOffID;
	}
	
	public function setHandBackTotalNumberOfUploadFiles($handBackTotalNumberOfUploadFiles){
		$this->_handBackTotalNumberOfUploadFiles = (int)$handBackTotalNumberOfUploadFiles;
		
		return $this;
	}
	
	public function getHandBackTotalNumberOfUploadFiles(){
		return $this->_handBackTotalNumberOfUploadFiles;
	}
	
	public function setHandBackUploadDate($handBackUploadDate){
		$this->_handBackUploadDate = (string)$handBackUploadDate;
		
		return $this;
	}
	
	public function getHandBackUploadDate(){
		return $this->_handBackUploadDate;
	}
	
	public function setHandBackFolderLocation($handBackFolderLocation){
		$this->_handBackFolderLocation = (string)$handBackFolderLocation;
		
		return $this;
	}
	
	public function getHandBackFolderLocation(){
		return $this->_handBackFolderLocation;
	}

	public function setHandBackComments($handBackComments){
		$this->_handBackComments = (string)$handBackComments;
		
		return $this;
	}
	
	public function getHandBackComments(){
		return $this->_handBackComments;
	}
	
	public function setAllowModifyHbRecord($allowModifyHbRecord){
		$this->_allowModifyHbRecord = (bool)$allowModifyHbRecord;
		
		return $this;
	}
	
	public function getAllowModifyHbRecord(){
		return $this->_allowModifyHbRecord;
	}
}

