<?php

class Application_Model_Mapper_EmailTemplate
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
            $this->setDbTable('Application_Model_DbTable_EmailTemplates');
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
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->order("EmailTemplateID desc")->limit($itemsPerPage, ($page-1) * $itemsPerPage));
		
		return $this->createResultSetEntity($resultSet);
	}
	
	public function find($emailTemplateID, Application_Model_EmailTemplate $emailTemplate){
		$result = $this->getDbTable()->find($emailTemplateID); 
		if (0 == count($result)){
			return;
		}				
		$row = $result->current();

        $emailTemplate->setEmailTemplateID($row->EmailTemplateID)
					  ->setEmailTemplateContent($row->EmailTemplateContent)                      
				      ->setEmailTemplateStatus($row->EmailTemplateStatus)
				      ->setEmailTemplateIsActive($row->EmailTemplateIsActive)
					  ->setEmailTemplateSubject($row->EmailTemplateSubject);
	}
	
	public function findEmailTemplateByEmailTemplateStatus($emailTemplateStatus){
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where("(EmailTemplateStatus = '$emailTemplateStatus') and (EmailTemplateIsActive ='Y')")->order("EmailTemplateID desc"));
		
		return $this->createResultSetEntity($resultSet);
	}
	
	public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        
		return $this->createResultSetEntity($resultSet);
    }
	
	public function save(Application_Model_EmailTemplate $emailTemplate){			
		$data = array(
				"EmailTemplateID" => $emailTemplate->EmailTemplateID,
				"EmailTemplateContent" => $emailTemplate->EmailTemplateContent,
				"EmailTemplateStatus" => $emailTemplate->EmailTemplateStatus,
				"EmailTemplateIsActive" => $emailTemplate->EmailTemplateIsActive,
				"EmailTemplateSubject" => $emailTemplate->EmailTemplateSubject
        );
 		
		$this->automaticChangeStateOfEmailTemplateWhenDuplicateEmailTemplateStatus($emailTemplate->EmailTemplateStatus);
        if (0 === ($emailTemplateID = $emailTemplate->getEmailTemplateID())) {
            unset($data['EmailTemplateID']);			
            return $this->getDbTable()->insert($data);			
		}
		
		return $this->getDbTable()->update($data, array('EmailTemplateID = ?' => $emailTemplateID));        
	}	
		
	public function deleteEmailTemplate($emailTemplateID){		
		$this->getDbTable()->delete("EmailTemplateID = " . (int)$emailTemplateID);
	}

	public function sendEmail($handOffId, $handOffStatus){
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();
				
		$configXml = new Zend_Config_Xml(APPLICATION_PATH . '/configs/smtp.xml', APPLICATION_ENV);
		$sendingEmail = new Application_Model_Mapper_SendingEmail();
		$emailTemplates = $this->findEmailTemplateByEmailTemplateStatus($handOffStatus);		
		if(0 == count($emailTemplates)){
			throw new Exception("Please set emailtemplate for $handOffStatus");
		}
		$mail = new Zend_Mail('UTF-8');		
		
		$sendingEmail->sendingEmail($configXml->mail->server->sender, $this->getRecipientTo($handOffId, $handOffStatus), $this->getRecipientCc($handOffId, $handOffStatus), $configXml->mail->admin->alias, $emailTemplates[0]->EmailTemplateSubject,
		$this->getEmailBody($emailTemplates[0]->EmailTemplateContent, $handOffId, $handOffStatus));		

		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("Email Sending<$handOffStatus> To:".$this->getRecipientTo($handOffId, $handOffStatus). " CC:".$this->getRecipientCc($handOffId, $handOffStatus))
				  ->setModule(Application_Model_DbTable_Activities::SENDING_EMAIL)
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		
		$activityMapper->save($activity);			
	}

	private function getRecipientTo($handOffId, $handoffStatus){
		$user = $this->findUserByHandoffID($handOffId);		

		if (($handoffStatus == Application_Model_DbTable_HOs::HO_UPLOADED) || ($handoffStatus == Application_Model_DbTable_HOs::HO_CLOSED)){
				
			return $user->JtepmEmail;
		}
		
		return $user->UserEmail;
	}	

	private function getRecipientCc($handOffID, $handoffStatus){		
		$user = $this->findUserByHandoffID($handOffID);		
				
		if (($handoffStatus == Application_Model_DbTable_HOs::HO_UPLOADED) || ($handoffStatus == Application_Model_DbTable_HOs::HO_CLOSED)){
			return $user->UserEmail;			
		}
				
		return $user->JtepmEmail;		
	}
	
	private function findUserByHandoffID($handOffID){
		$hoMapper = new Application_Model_Mapper_HO();
		$ho = new Application_Model_HO();
		$hoMapper->find($handOffID, $ho);
		
		$userMapper = new Application_Model_Mapper_User();
		$user = new Application_Model_User();		
		$userMapper->find($ho->UserID, $user);
		
		return $user;
	}
		
	private function getEmailBody($emailTemplateContent, $handOffID, $handOffStatus){
		$hoMapper = new Application_Model_Mapper_HO();
		$ho = new Application_Model_HO();		
		$hoMapper->find($handOffID, $ho);
		$userMapper = new Application_Model_Mapper_User();
		$user = new Application_Model_User();		
		$auth = Zend_Auth::getInstance();
		$hbMapper = new Application_Model_Mapper_HB();
		
		if($ho->HandOffID == null){
			throw new Exception("Please input correct handoffID to get emailbody");
		}
		
		$result = $hbMapper->getAvailabeHandbackForHandOffID($ho->HandOffID);
		
		$htmlBody = $emailTemplateContent;
		$htmlBody = str_replace("{HOID}", "BENTO_" . str_pad($handOffID, 8 , '0', STR_PAD_LEFT) , $htmlBody);		
		$htmlBody = str_replace("{HOTITLE}", $ho->HandOffTitle , $htmlBody);
		$htmlBody = str_replace("{HBTITLE}", $ho->HandOffTitle , $htmlBody);		
		$htmlBody = str_replace("{LinkToHODetails}", str_replace("{LinkToHODetails}", $this->fullUrl("/ho/edit/handoffid/" .$handOffID), "<a href='{LinkToHODetails}' >{LinkToHODetails}</a>") , $htmlBody);			
		$htmlBody = str_replace("{HandoffInstructions}", $ho->HandOffInstruction, $htmlBody);
		
		$htmlBody = str_replace("{ExpectedHBDate}", $ho->ExpectedHandBackDate !=null ? $ho->ExpectedHandBackDate: "Not Set by the Project manager", $htmlBody);
		$htmlBody = str_replace("{LinkDownloadHo}", str_replace("{LinkDownloadHo}", $this->fullUrl("/fm/index/handoffid/" .$handOffID . "/download/ho"), "<a href='{LinkDownloadHo}' >{LinkDownloadHo}</a>") , $htmlBody);
		
		$htmlBody = str_replace("{LinkDownloadHb}", str_replace("{LinkDownloadHb}", $this->fullUrl("/fm/index/handoffid/" .$handOffID . "/download/hb"), "<a href='{LinkDownloadHb}' >{LinkDownloadHb}</a>") , $htmlBody);
		
		switch($handOffStatus){
			case Application_Model_DbTable_HOs::HO_UPLOADED:
			case Application_Model_DbTable_HOs:: HO_CLOSED:
				$htmlBody = str_replace("{UserName}", $auth->getIdentity()->UserName , $htmlBody);						
				break;
			case Application_Model_DbTable_HOs::HO_RECEIVED:	
			case Application_Model_DbTable_HOs::HB_COMPLETED:								
				$user = $this->findUserByHandoffID($handOffID);				
				$htmlBody = str_replace("{UserName}", $user->UserName , $htmlBody);					
				$htmlBody = str_replace("{JTEPMEmail}", $auth->getIdentity()->UserEmail , $htmlBody);
				break;
			default:
				break;
		}
						
		if (count($result) > 0){
			$htmlBody = str_replace("{HBID}", "Bento_" . str_pad($result[0]->HandBackID, 8 , '0', STR_PAD_LEFT) , $htmlBody);		
			$htmlBody = str_replace("{HandBackInstructions}", $result[0]->HandBackComments, $htmlBody);
		}						
		return html_entity_decode($htmlBody);
	}
	
	private function fullUrl($url){		
		$request = Zend_Controller_Front::getInstance()->getRequest();
        $url = $request->getScheme() . '://' . $request->getHttpHost() . $url;
        return $url;
	}
	
	private function createResultSetEntity($resultSet){
		$entries   = array();
		
		foreach ($resultSet as $row) {
			$entry = new Application_Model_EmailTemplate();
			$entry->setEmailTemplateID($row->EmailTemplateID)
				  ->setEmailTemplateContent($row->EmailTemplateContent)                  
				  ->setEmailTemplateStatus($row->EmailTemplateStatus)
				  ->setEmailTemplateIsActive($row->EmailTemplateIsActive)
				  ->setEmailTemplateSubject($row->EmailTemplateSubject);

			$entries[] = $entry;
		}
		
		return $entries;
	}	
	
	private function automaticChangeStateOfEmailTemplateWhenDuplicateEmailTemplateStatus($handOffStatus){
		$emailTemplate = $this->getDbTable();
		$emailTemplate->update(array("EmailTemplateIsActive" =>"N"), "EmailTemplateStatus='$handOffStatus'");
	}
}

