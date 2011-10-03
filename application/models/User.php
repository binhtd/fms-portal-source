<?php

class Application_Model_User
{
	private $_userID;
	private $_userName;
	private $_userEmail;
	private $_userLoginName;
	private $_userPassword;
	private $_userRootUploadDirectory;
	private $_userLastActivity;
	private $_userIsClient;
	private $_userIsJonckersPM;
	private $_userIsActive;
	private $_allowModifyUserRecord;
	private $_jtepmEmail;
	
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
	
	public function setUserID($userID){
		$this->_userID = (int)$userID;
		return $this;
	}
	
	public function getUserID(){
		return $this->_userID;
	}
	
	public function setUserName($userName){
		$this->_userName = (string)$userName;
		
		return $this;
	}
	
	public function getUserName(){
		return $this->_userName;
	}
	
	public function setUserEmail($userEmail){
		$this->_userEmail = (string)$userEmail;
		return $this;
	}
	
	public function getUserEmail(){
		return $this->_userEmail;
	}

	public function setUserLoginName($userLoginName){
		$this->_userLoginName = (string)$userLoginName;
		return $this;
	}
	
	public function getUserLoginName(){
		return $this->_userLoginName;
	}
	
	public function setUserPassword($userPassword){
		$this->_userPassword = (string)$userPassword;
		return $this;
	}
	
	public function getUserPassword(){
		return $this->_userPassword;
	}
	
	public function setUserRootUploadDirectory($userRootUploadDirectory){
		$this->_userRootUploadDirectory = (string)$userRootUploadDirectory;
		return $this;
	}
	
	public function getUserRootUploadDirectory(){
		return $this->_userRootUploadDirectory;
	}
	
	public function setUserLastActivity($userLastActivity){
		$this->_userLastActivity = (string)$userLastActivity;
		return $this;
	}
	
	public function getUserLastActivity(){
		return $this->_userLastActivity;
	}
	
	public function setUserIsClient($userIsClient){
		$this->_userIsClient = (string)$userIsClient;
		return $this;
	}
	
	public function getUserIsClient(){
		return $this->_userIsClient;
	}
	
	public function setUserIsJonckersPM($userIsJonckersPM){
		$this->_userIsJonckersPM = (string)$userIsJonckersPM;
		return $this;
	}
	
	public function getUserIsJonckersPM(){
		return $this->_userIsJonckersPM;
	}
	
	public function setUserIsActive($userIsActive){
		$this->_userIsActive = (string)$userIsActive;
		return $this;
	}
	
	public function getUserIsActive(){
		return $this->_userIsActive;
	}

	public function setAllowModifyUserRecord($allowModifyUserRecord){
		$this->_allowModifyUserRecord = (bool)$allowModifyUserRecord;
		return $this;
	}
	
	public function getAllowModifyUserRecord(){
		return $this->_allowModifyUserRecord;
	}	
	
	public function setJtepmEmail($jtepmEmail){
		$this->_jtepmEmail = (string)$jtepmEmail;
		return $this;
	}
	
	public function getJtepmEmail(){
		return $this->_jtepmEmail;
	}
}

