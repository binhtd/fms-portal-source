<?php

class Application_Model_Mapper_HO
{
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
            $this->setDbTable('Application_Model_DbTable_HOs');
        }
        return $this->_dbTable;
    }
		
	public function getPaginator($page, $itemsPerPage, $hideCloseHO){	
		$paginator = Zend_Paginator::factory($this->getDbTable()->select()->where($this->buildPaginatorWhereQuery($hideCloseHO)));
		
		$paginator->setCurrentPageNumber($page);
		
		$paginator->setItemCountPerPage($itemsPerPage);

		$paginator->setPageRange(5);
		
		return $paginator; 
	}
	
	public function getPaginatorData($page, $itemsPerPage, $hideCloseHO){														
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where($this->buildPaginatorWhereQuery($hideCloseHO))->order("HandOffID desc")
														->limit($itemsPerPage, ($page-1) * $itemsPerPage));
		
		return $this->CreateResultSetEntity($resultSet);
	}
	
	public function getHOStatus($currentHoStatus){
		switch($currentHoStatus){
			case Application_Model_DbTable_HOs::HO_CREATED:
				return array(Application_Model_DbTable_HOs::HO_CREATED => Application_Model_DbTable_HOs::HO_CREATED, Application_Model_DbTable_HOs::HO_CANCELLED => Application_Model_DbTable_HOs::HO_CANCELLED, Application_Model_DbTable_HOs::HO_UPLOADED => Application_Model_DbTable_HOs::HO_UPLOADED);
			case Application_Model_DbTable_HOs::HO_UPLOADED:
				return array(Application_Model_DbTable_HOs::HO_RECEIVED => Application_Model_DbTable_HOs::HO_RECEIVED);
			case Application_Model_DbTable_HOs::HO_RECEIVED:	
				return array(Application_Model_DbTable_HOs::HB_COMPLETED => Application_Model_DbTable_HOs::HB_COMPLETED);	
			case Application_Model_DbTable_HOs::HB_COMPLETED:	
				return array(Application_Model_DbTable_HOs::HO_CLOSED => Application_Model_DbTable_HOs::HO_CLOSED);	
		}
	}
	
	public function isAllowShowEditDepentOnRoleAndHoStatus($currentHoStatus){
		$auth = Zend_Auth::getInstance();
		
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		
		if (($auth->getIdentity()->UserIsJonckersPM == "Y") && ($auth->getIdentity()->UserLoginName == $config->fmsadmin->username)) {
			return true;
		}
		
		switch($currentHoStatus){
			case Application_Model_DbTable_HOs::HO_CREATED:
				if ($auth->getIdentity()->UserIsClient == "Y"){
					return true;
				}
				
				return false;
			case Application_Model_DbTable_HOs::HO_UPLOADED:
				if ($auth->getIdentity()->UserIsClient == "Y"){
					return false;
				}
				
				return true;				
			case Application_Model_DbTable_HOs::HO_RECEIVED:	
				if ($auth->getIdentity()->UserIsClient == "Y"){
					return false;
				}
				
				return true;				
			case Application_Model_DbTable_HOs::HB_COMPLETED:	
				if ($auth->getIdentity()->UserIsClient == "Y"){
					return true;
				}
				
				return false;				
		}
	}
	
	public function find($handOffID, Application_Model_HO $ho){
		$result = $this->getDbTable()->find($handOffID); 
		$languageMapper = new Application_Model_Mapper_Language();
		$language = new Application_Model_Language();
		$HandOffTargetLanguageMapper = new Application_Model_Mapper_HandOffTargetLanguage();
		if (0 == count($result)){
			return;
		}		
		
		$row = $result->current();
        $languageMapper->find($row->HandOffSourceLanguageID, $language);
		$ho->setHandOffID($row->HandOffID)
		   ->setUserID($row->UserID)
		   ->setHandOffTitle($row->HandOffTitle)
		   ->setHandOffTotalNumberOfUploadFiles($row->HandOffTotalNumberOfUploadFiles)
		   ->setHandOffUploadDate($row->HandOffUploadDate)	
		   ->setHandOffStartProject($row->HandOffStartProject)
		   ->setHandOffClosedDate($row->HandOffClosedDate)
		   ->setExpectedHandBackDate($row->ExpectedHandBackDate)
		   ->setHandOffStatus($row->HandOffStatus)
		   ->setHandOffSourceLanguageID($row->HandOffSourceLanguageID)
		   ->setHandOffSourceLanguageName($language->LanguageName)
		   ->setHandOffTargetLanguageName($HandOffTargetLanguageMapper->getTargetLanguageNames($row->HandOffID))
		   ->setHandOffFolderLocation($row->HandOffFolderLocation)
		   ->setHandOffInstruction($row->HandOffInstruction);
	}
		
	public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        
		return $this->CreateResultSetEntity($resultSet);
    }

	public function save(Application_Model_HO $ho){			
		$data = array(
            "HandOffID" => $ho->HandOffID, "UserID" => $ho->UserID,
		    "HandOffTitle" => $ho->HandOffTitle, "HandOffTotalNumberOfUploadFiles" => $ho->HandOffTotalNumberOfUploadFiles,
		    "HandOffUploadDate" => $ho->HandOffUploadDate, "HandOffStartProject" => $ho->HandOffStartProject,
			"HandOffClosedDate" => $ho->HandOffClosedDate, "ExpectedHandBackDate" => $ho->ExpectedHandBackDate,
		    "HandOffStatus" => $ho->HandOffStatus, "HandOffSourceLanguageID" => $ho->HandOffSourceLanguageID, "HandOffFolderLocation" => $ho->HandOffFolderLocation, 
			"HandOffInstruction" => $ho->HandOffInstruction
        );
 
        if (null === ($handOffID = $ho->getHandOffID())) {
            unset($data['HandOffID']);
            return $this->getDbTable()->insert($data);
        } 

		return $this->getDbTable()->update($data, array('HandOffID = ?' => $handOffID));
	}	
	
	public function deleteHO($handOffID){
		$hos = $this->getDbTable()->find( (int)$handOffID);
		$hos[0]->delete();
	}
			
	public function getAvailabeHOForOneUser($userID){
	   $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
	   $userMapper = new Application_Model_Mapper_User();	   
	   $adminAccount = $userMapper->getUserByUserLoginName( 0, $config->fmsadmin->username);	   
	   $adminId = $adminAccount[0]->UserID;
	   
	   $resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where(" (UserID = $userID)  and (HandOffStatus <>'" . Application_Model_DbTable_HOs::HO_CLOSED . "')" . " and (HandOffStatus <>'" . Application_Model_DbTable_HOs::HO_CANCELLED . "') and " .
	   'not exists ( select HandBackID from handbacks where handbacks.HandOffID = handoffs.HandOffID)' . " and HandOffStatus ='". Application_Model_DbTable_HOs::HO_RECEIVED . "'"
	   )->order("HandOffID desc"));

		return $this->createArrayData($resultSet);
	}
	
	public function isValidHOStatusSetup($ho, $hoStatus){				
		if((Application_Model_DbTable_HOs::getHOStatusOrder($ho->HandOffStatus) == Application_Model_DbTable_HOs::getHOStatusOrder(Application_Model_DbTable_HOs::HO_CREATED)) && (Application_Model_DbTable_HOs::getHOStatusOrder($hoStatus) == Application_Model_DbTable_HOs::getHOStatusOrder(Application_Model_DbTable_HOs::HO_UPLOADED))){
			return true;
		}
		
		if(Application_Model_DbTable_HOs::getHOStatusOrder($ho->HandOffStatus) == Application_Model_DbTable_HOs::getHOStatusOrder($hoStatus)){
			return true;
		}
		
		if(Application_Model_DbTable_HOs::getHOStatusOrder($ho->HandOffStatus) + 1 == Application_Model_DbTable_HOs::getHOStatusOrder($hoStatus)){
			return true;
		}
		
		return false;
	}

	public function isValidHOStatusAndTotalUploadFile($handOffTotalNumberOfUploadFiles, $hoStatus){		
		if (((0== $handOffTotalNumberOfUploadFiles) && (Application_Model_DbTable_HOs::getHOStatusOrder($hoStatus) == Application_Model_DbTable_HOs::getHOStatusOrder(Application_Model_DbTable_HOs::HO_UPLOADED)))){
			return false;
		}
		
		return true;
	}
	
	public function isValidHBStatusAndTotalUploadFile($handOffId, $hoStatus, $view){
		$hoMapper = new Application_Model_Mapper_HO();
		$ho = new Application_Model_HO();
		$hbMapper = new Application_Model_Mapper_HB();
		
		$hoMapper->find($handOffId, $ho);
		
		if($ho->HandOffID == null){
			throw new Exception("Please chooose correct handoffid");
		}
		
		$result = $hbMapper->getAvailabeHandbackForHandOffID($ho->HandOffID);
		
		if (0 == count($result) && (Application_Model_DbTable_HOs::getHOStatusOrder($hoStatus) == Application_Model_DbTable_HOs::getHOStatusOrder(Application_Model_DbTable_HOs::HB_COMPLETED))){
			$view->mappingBetweenHoAndHbMessage = "Please create one handback for this handoff";
		}
		
		if (Application_Model_DbTable_HOs::getHOStatusOrder($hoStatus) == Application_Model_DbTable_HOs::getHOStatusOrder(Application_Model_DbTable_HOs::HB_COMPLETED)){
			return (0 != count($result) ? (0!= $result[0]->HandBackTotalNumberOfUploadFiles) : false);
		}
		
		return true;
	}
	
	public function isAllowEditHo($hoStatus, $userID){
		$auth = Zend_Auth::getInstance();
		
		if (($hoStatus == Application_Model_DbTable_HOs::HO_CANCELLED) || ($hoStatus == Application_Model_DbTable_HOs::HO_CLOSED) || 
		(($auth->getIdentity()->UserIsClient == "Y") && ($userID != $auth->getIdentity()->UserID))){
			return false;
		}
		 
		return true;
	}
			
	public function isAllowDeleteHo($hoStatus, $userID){
		$auth = Zend_Auth::getInstance();
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		
		if ($auth->getIdentity()->UserLoginName == $config->fmsadmin->username){
			return true;
		}
		
		if ((($auth->getIdentity()->UserIsClient == "Y") && ($hoStatus != Application_Model_DbTable_HOs::HO_CREATED)) || (($auth->getIdentity()->UserIsJonckersPM == "Y") && ($auth->getIdentity()->UserLoginName != $config->fmsadmin->username)) || ($userID!= $auth->getIdentity()->UserID)) {
			return false;
		}
		
		return true;
	}
	
	public function isAllowViewDetail($userID){
		$auth = Zend_Auth::getInstance();
		
		if (($auth->getIdentity()->UserIsClient == "Y") && ($userID != $auth->getIdentity()->UserID)) {
			return false;
		}
		
		return true;
	}
	
	public function isAllowAddNewHo(){
		$auth = Zend_Auth::getInstance();
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		if ($auth->getIdentity()->UserIsJonckersPM == 'Y' && ($auth->getIdentity()->UserLoginName != $config->fmsadmin->username)){
			return false;	
		}
		
		return true;
	}
	
	public function isAllowEditFieldInHoRecord($handOffStatus){
		$auth = Zend_Auth::getInstance();
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		if ($auth->getIdentity()->UserIsJonckersPM == 'Y' && ($auth->getIdentity()->UserLoginName == $config->fmsadmin->username)){
			return true;	
		}
		
		if (($auth->getIdentity()->UserIsClient == 'Y') && ($handOffStatus!= Application_Model_DbTable_HOs::HB_COMPLETED)){
			return true;	
		}
		
		return false;
	}
	
	public function isValidHOStatusForSaveUpload($hoStatus){
		return (($hoStatus <> Application_Model_DbTable_HOs::HO_CANCELLED) && (Application_Model_DbTable_HOs::getHOStatusOrder($hoStatus) < 	Application_Model_DbTable_HOs::getHOStatusOrder(Application_Model_DbTable_HOs::HO_UPLOADED)));
	}
	
	public function getHandOffPath(){
		$auth = Zend_Auth::getInstance();
		$handOffPath = APPLICATION_PATH . '/../public/uploads/'. 
		str_replace( array("\\", "/", ":", "*", "?", "\"", "<", ">" , "|"), "_", $auth->getIdentity()->UserLoginName  . "_" . $auth->getIdentity()->UserRootUploadDirectory) ;
		
		if(!file_exists($handOffPath)){
			mkdir($handOffPath, 0777);
		}
		
		$handOffPath = $handOffPath . "/" . date("Y_m_d_H_i_s", time());
		if(!file_exists($handOffPath)){
			mkdir($handOffPath, 0777);
		}
		
		return $handOffPath;
	}
	
	public function sendEmailNotifyHo($handOffID, $handOffStatus, $notifyHoStatusChange){
		$emailTemplateMapper = new Application_Model_Mapper_EmailTemplate();
		$auth = Zend_Auth::getInstance();
		if (Application_Model_DbTable_HOs::HO_UPLOADED == $handOffStatus){			
			$emailTemplateMapper->sendEmail((int)$handOffID, $handOffStatus);
			return;
		}
				
		if ((Application_Model_DbTable_HOs::HB_COMPLETED == $handOffStatus) && ($auth->getIdentity()->UserIsJonckersPM == "Y")){
			$emailTemplateMapper->sendEmail( (int)$handOffID, $handOffStatus);
		}

		if($notifyHoStatusChange){
			$emailTemplateMapper->sendEmail( (int)$handOffID, $handOffStatus);
		}
	}
	
	public function deleteHandoffClosedAfterSpecifyDay($numberOfDay){
		$mileStoneDate = date("Y-m-d : H:i:s", (time()-(24*60*60*$numberOfDay)));
		
		$hos = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where("(HandOffStatus = '" . Application_Model_DbTable_HOs::HO_CLOSED . "') and (HandOffStatus <>'" . Application_Model_DbTable_HOs::HO_CANCELLED . "') and (HandOffClosedDate >= '$mileStoneDate')"));
		
		foreach($hos as $ho){
			$fm = new Application_Model_Mapper_FM();
			$fm->unlinkRecursive($ho->HandOffFolderLocation, true);		
			$ho->delete();
		}
		
		return count($hos);
	}
	
	
	private function createArrayData($resultSet){
		$entries = array();
		
		foreach ($resultSet as $row) {
			$entry = array("HandOffID" => $row->HandOffID, "UserID" => $row->UserID, "HandOffTitle" => $row->HandOffTitle,
					"HandOffTotalNumberOfUploadFiles" =>  $row->HandOffTotalNumberOfUploadFiles,
					"HandOffUploadDate" => $row->HandOffUploadDate, "HandOffStartProject" => $row->HandOffStartProject,
					"HandOffClosedDate" => $row->HandOffClosedDate, "ExpectedHandBackDate" => $row->ExpectedHandBackDate,
		            "HandOffStatus" => $row->HandOffStatus, "HandOffSourceLanguageID" => $row->HandOffSourceLanguageID,
					"HandOffFolderLocation" => $row->HandOffFolderLocation, "HandOffInstruction" => $row->HandOffInstruction);

			$entries[] = $entry;
		}
		
		return $entries;
	}
	
	private function CreateResultSetEntity($resultSet){
		$entries   = array();
		
		foreach ($resultSet as $row) {
			$entry = new Application_Model_HO();
			$userMapper = new Application_Model_Mapper_User();
			$user = new Application_Model_User();
			$userMapper->find($row->UserID, $user);
			$entry->setHandOffID($row->HandOffID)
				  ->setUserID($row->UserID)
				  ->setHandOffTitle($row->HandOffTitle)
				  ->setHandOffTotalNumberOfUploadFiles($row->HandOffTotalNumberOfUploadFiles)
				  ->setHandOffUploadDate($row->HandOffUploadDate)
				  ->setHandOffStartProject($row->HandOffStartProject)
				  ->setHandOffClosedDate($row->HandOffClosedDate)
				  ->setExpectedHandBackDate($row->ExpectedHandBackDate)
				  ->setHandOffStatus($row->HandOffStatus)
		          ->setHandOffSourceLanguageID($row->HandOffSourceLanguageID)
		          ->setHandOffFolderLocation($row->HandOffFolderLocation)
				  ->setHandOffInstruction($row->HandOffInstruction)
				  ->setAllowEditHoRecord($this->isAllowEditHO($row->HandOffStatus, $row->UserID))
				  ->setAllowDeleteHoRecord($this->isAllowDeleteHO($row->HandOffStatus, $row->UserID))
				  ->setAllowShowEditDepentOnRoleAndHoStatus($this->isAllowShowEditDepentOnRoleAndHoStatus($row->HandOffStatus))
				  ->setUserName($user->UserName);

			$entries[] = $entry;
		}
		
		return $entries;
	}		
			
	private function buildPaginatorWhereQuery($hideCloseHO){
		$auth = Zend_Auth::getInstance();
		
		if($auth->getIdentity()->UserIsClient == 'Y'){
			return $this->buildPaginatorWhereQueryForClient($hideCloseHO);
		}
		
		return $this->buildPaginatorWhereQueryForAdmin($hideCloseHO);
	}
	
	private function buildPaginatorWhereQueryForClient($hideCloseHO){
		$auth = Zend_Auth::getInstance();
		$whereStatement = " UserID = " . $auth->getIdentity()->UserID;
		
		if($hideCloseHO){			
			$whereStatement = $whereStatement ." and HandOffStatus <> '" . Application_Model_DbTable_HOs::HO_CLOSED ."'";
		}

		return $whereStatement;
	}
	
	private function buildPaginatorWhereQueryForAdmin($hideCloseHO){
		$whereStatement = ' 1 = 1';
		if($hideCloseHO){			
			$whereStatement = $whereStatement ." and HandOffStatus <> '" . Application_Model_DbTable_HOs::HO_CLOSED ."'";
		}
		
		return $whereStatement;
	}
		
}
