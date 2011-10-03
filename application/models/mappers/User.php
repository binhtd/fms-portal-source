<?php
class Application_Model_Mapper_User{
	protected $_dbTable;
 
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_Users');
        }
        return $this->_dbTable;
    }
		
	public function getPaginator($page, $itemsPerPage){	
		$paginator = Zend_Paginator::factory($this->getDbTable()->select());
		
		$paginator->setCurrentPageNumber($page);
		
		$paginator->setItemCountPerPage($itemsPerPage);

		$paginator->setPageRange(5);
		
		return $paginator; 
	}
	
	public function getPaginatorData($page, $itemsPerPage){
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->order("UserID desc")->limit($itemsPerPage, ($page-1) * $itemsPerPage));
		
		return $this->createResultSetEntity($resultSet);
	}
	
	public function find($userID, Application_Model_User $user){
		$result = $this->getDbTable()->find($userID); 
		if (0 == count($result)){
			return;
		}		
		
		$row = $result->current();
        $user->setUserID($row->UserID)
                  ->setUserName($row->UserName)
                  ->setUserEmail($row->UserEmail)
				  ->setUserLoginName($row->UserLoginName)
				  ->setUserPassword($row->UserPassword)
				  ->setUserRootUploadDirectory($row->UserRootUploadDirectory)
				  ->setUserLastActivity($row->UserLastActivity)
				  ->setUserIsClient($row->UserIsClient)
				  ->setUserIsJonckersPM($row->UserIsJonckersPM)
				  ->setUserIsActive($row->UserIsActive)
				  ->setJtepmEmail($row->JtepmEmail);
	}
	
	public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        
		return $this->createResultSetEntity($resultSet);
    }
	
	public function save(Application_Model_User $user){			
		$data = array(
				"userName" => $user->UserName,
				"userEmail" => $user->UserEmail,
				"userLoginName" =>$user->UserLoginName,
				"userRootUploadDirectory" =>$user->UserRootUploadDirectory,
				"userLastActivity" => date("Y-m-d : H:i:s", time()), 
				"userIsClient" => $user->UserIsClient,
				"userIsJonckersPM" => $user->UserIsJonckersPM,
				"userIsActive" => $user->UserIsActive,
				"jtepmEmail" => $user->JtepmEmail
        );
 
        if (null === ($userID = $user->getUserID())) {
            unset($data['UserID']);
			$data["userPassword"] =	md5($user->UserPassword);
            return $this->getDbTable()->insert($data);			
		}
		
		if ($user->UserPassword != ""){
			$data["userPassword"] =	md5($user->UserPassword);
		}

		return $this->getDbTable()->update($data, array('UserID = ?' => $userID));        
	}
		
	public function deleteUser($userID){
		$auth = Zend_Auth::getInstance();
		$users = $this->getDbTable()->find( (int)$userID);
        
		if(!$this->isEqualWithDefaultAdmin($auth->getIdentity()->UserLoginName)){
			Zend_Auth::getInstance()->clearIdentity();	
		}
		
		$this->deleteSessionForUser($users[0]->UserLoginName);
		$users[0]->delete();
		
	}

	public function getAllUserActive(){
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where(" UserIsActive = 'Y' ")->order("UserID desc"));
        
		return $this->createResultSetEntity($resultSet);
	}
	
	public function getAllClientActive(){
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where(" (UserIsClient = 'Y' and UserIsActive = 'Y') or (UserLoginName = '" . $config->fmsadmin->username . "') ")->order("UserID desc"));
        
		return $this->createResultSetEntity($resultSet);
	}
	
	public function getUserByUserLoginName($userID, $userLoginName){
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where("UserLoginName ='$userLoginName' and not exists ( select userID from Users where UserID = $userID)")->order("UserID desc"));
            
		return $this->createResultSetEntity($resultSet);
	}
	
	public function getUserByUserEmail($userEmail, $userID){
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where("UserEmail ='$userEmail' and UserID<> $userID")->order("UserID desc"));
        
		return $this->createResultSetEntity($resultSet);
	}
		
	public function isEqualWithDefaultAdmin($userName){
		$config = new Zend_Config_Ini( APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		
		if(!isset($config->fmsadmin->username)){
			return false;
		}
		
		if($config->fmsadmin->username == $userName){
			return true;
		}
		
		return false;
	}
	
	public function getAvailableJtepmAccount(){
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where(" UserIsJonckersPM  ='Y' and UserIsActive='Y' "  )->order("UserID desc"));
        
		return $this->createResultSetEntity($resultSet);
	}
	
	public  function getAvailableJtepmEmail(){				
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->from("users", "UserEmail")->where(" UserIsJonckersPM  ='Y' and UserIsActive='Y' "  )->order("UserID desc"));
		
		$entries   = array();
		foreach($resultSet as $row){
			$entries[] = $row->UserEmail;
		}
		
		return $entries;
	}
	
	private function createResultSetEntity($resultSet){
		$entries   = array();
		foreach ($resultSet as $row) {
			$entry = new Application_Model_User();
			$entry->setUserID($row->UserID)
                  ->setUserName($row->UserName)
                  ->setUserEmail($row->UserEmail)
				  ->setUserLoginName($row->UserLoginName)
				  ->setUserPassword($row->UserPassword)
				  ->setUserRootUploadDirectory($row->UserRootUploadDirectory)
				  ->setUserLastActivity($row->UserLastActivity)
				  ->setUserIsClient($row->UserIsClient)
				  ->setUserIsJonckersPM($row->UserIsJonckersPM)
				  ->setUserIsActive($row->UserIsActive)
				  ->setAllowModifyUserRecord($this->isAllowModifyUser($row->UserID))
				  ->setJtepmEmail($row->JtepmEmail);
				
			$entries[] = $entry;
		}
		
		return $entries;
	}
	
	public function isAllowModifyUser($userID){
		$auth = Zend_Auth::getInstance();
		$userMapper = new Application_Model_Mapper_User();
		$user = new Application_Model_User();		
		$userMapper->find($userID, $user);
		
		if(!$auth->hasIdentity()){
			return false;
		}
		
		if($this->isEqualWithDefaultAdmin($auth->getIdentity()->UserLoginName)){
			return true;
		}
			
		if (null <> $user->UserID){
			return ($auth->getIdentity()->UserLoginName == $user->UserLoginName);
		}
		
		if($auth->getIdentity()->UserIsJonckersPM == 'Y' ){
			return true;
		}
		
		return false;
	}
	
	private function deleteSessionForUser($userLoginName){
		$sessionMapper = new Application_Model_Mapper_Session();
		$listSession = $sessionMapper->fetchAll();
		
		$sessionHandler = Zend_Session::getSaveHandler();
		foreach($listSession as $session){
			$user = $this->unserializeSessionContainsUserInfo($session->Data);
	
			if(!isset($user["Zend_Auth"]["storage"])){
				continue;
			}
			
			if($user["Zend_Auth"]["storage"]->UserLoginName == $userLoginName){
				$sessionHandler->destroy($session->id);
			}
		}
	}
	
	private function unserializeSessionContainsUserInfo($data) {
        $vars = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/', $data,-1,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $numElements = count($vars);
        for($i=0; $numElements > $i && $vars[$i]; $i++) {
            $result[$vars[$i]]=unserialize($vars[++$i]);
        }
		
        return $result;
    }
}