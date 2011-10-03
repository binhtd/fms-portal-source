<?php

class HOController extends Zend_Controller_Action
{
	const SAVE_UPLOAD = 'Save & upload files';
	const SAVE_UPLOAD_LATER = "Save";
	const CANCEL = "Cancel";
	const HIDE_CLOSED_HOS = "HideClosedHos";
		
    public function init()
    {        
		$this->view->SAVE_UPLOAD = self::SAVE_UPLOAD;
		$this->view->SAVE_UPLOAD_LATER = self::SAVE_UPLOAD_LATER;
		$this->view->CANCEL = self::CANCEL;		
		$this->view->HIDE_CLOSED_HOS = self::HIDE_CLOSED_HOS; 
		
		$languageMapper = new Application_Model_Mapper_Language();		
		$hoMapper = new Application_Model_Mapper_HO();
		$this->view->sourceListlanguage = $languageMapper->getSourceLanguageActive();
		$this->view->targetListlanguage = $languageMapper->getTargetLanguageActive();		
		
		$this->view->activeHo = true;
    }

    public function indexAction()
    {
		$hoMapper= new Application_Model_Mapper_HO();
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		$this->view->hideClosedHoCheckedButton = false;
		$this->view->showAddNewHoButton = $hoMapper->isAllowAddNewHo();
				
		if($this->_getParam( self::HIDE_CLOSED_HOS, $default="") == self::HIDE_CLOSED_HOS){			
			$this->view->hideClosedHoCheckedButton = true;
		}
		
		$this->view->paginator = $hoMapper->getPaginator($this->getRequest()->getParam('page', 1), $this->getRequest()->getParam("limit", (int) $config->paginator->itemCountPerPage), $this->view->hideClosedHoCheckedButton);
		$this->view->resultSet = $hoMapper->getPaginatorData($this->getRequest()->getParam('page', 1), $this->getRequest()->getParam("limit", (int) $config->paginator->itemCountPerPage), $this->view->hideClosedHoCheckedButton);	 
    }

    public function editAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
			
	    $form = new Application_Form_HO();		
		$hoMapper = new Application_Model_Mapper_HO();
		$handOffTargetLanguageMapper = new Application_Model_Mapper_HandOffTargetLanguage();
		$ho = new Application_Model_HO();
		$auth = Zend_Auth::getInstance();
		$this->view->form = $form;
			
		$hoMapper->find($this->_getParam("handoffid", 0), $ho);
		if($ho->HandOffID == null){
			throw new Exception("Please choose correct handoffid for edit ho");
		}						
		
		if(!$hoMapper->isAllowEditHo($ho->HandOffStatus, $ho->UserID) || !$hoMapper->isAllowShowEditDepentOnRoleAndHoStatus($ho->HandOffStatus)){
			throw new Exception("you do not have a permission to edit this ho record, please login with different account");
		}
		
		$this->view->allowEditFieldInHoRecord = $hoMapper->isAllowEditFieldInHoRecord($ho->HandOffStatus); 
		$this->view->hostatus = $hoMapper->getHOStatus($ho->HandOffStatus); 
		$this->view->validHOStatusForSaveUpload = $hoMapper->isValidHOStatusForSaveUpload($ho->HandOffStatus);
		
		if(!$this->getRequest()->isPost()){					
			$form->populate(array("HandOffID" => $ho->HandOffID, "UserID"=> $ho->UserID, "HandOffTitle" => $ho->HandOffTitle,
							"HandOffTotalNumberOfUploadFiles" => $ho->HandOffTotalNumberOfUploadFiles, "HandOffStartProject" => $ho->HandOffStartProject, "CheckboxExpectedHandbackDate" => $this->_getParam("checkboxExpectedHandbackDate", ""), "ExpectedHandBackDate" => $this->_getParam("ExpectedHandBackDate", ""),
							"HandOffStatus" => $ho->HandOffStatus, "HandOffSourceLanguageID" => $ho->HandOffSourceLanguageID, "HandOffListTargetLanguageID" => $handOffTargetLanguageMapper->getTargetLanguages($ho->HandOffID),
							"HandOffFolderLocation" => $ho->HandOffFolderLocation));
			
			$form->content->setValue($ho->HandOffInstruction);			
			return;
		}				
		
		if($this->getRequest()->getPost("action") == self::CANCEL){
			$this->_helper->redirector("index");
			return;
		}	
				
		$formData = $this->getRequest()->getPost();
		$formData["content"] = $this->_helper->WebEditorContentFilter(trim($formData["content"]));					
		$this->view->hbStatusAndCountFileUploadInvalidMessage = null;		
		$this->view->hoStatusAndUploadInvalidMessage = null;
		$this->view->hoStatusAndCountFileUploadInvalidMessage = null;
		
		if(!$hoMapper->isAllowEditFieldInHoRecord($ho->HandOffStatus)){
			$formData = array("HandOffID" => $ho->HandOffID, "UserID"=> $ho->UserID, "HandOffTitle" => $ho->HandOffTitle,
							"HandOffTotalNumberOfUploadFiles" => $ho->HandOffTotalNumberOfUploadFiles, "HandOffStartProject" => $ho->HandOffStartProject,
							"HandOffStatus" => $this->_getParam("HandOffStatus", ""), "CheckboxExpectedHandbackDate" => $this->_getParam("checkboxExpectedHandbackDate", ""), "ExpectedHandBackDate" => $this->_getParam("ExpectedHandBackDate", ""),
							"HandOffSourceLanguageID" => $ho->HandOffSourceLanguageID, "HandOffListTargetLanguageID" => $handOffTargetLanguageMapper->getTargetLanguages($ho->HandOffID),
							"HandOffFolderLocation" => $ho->HandOffFolderLocation, "content" => $ho->HandOffInstruction);
		}

		if(!$hoMapper->isValidHOStatusAndTotalUploadFile($ho->HandOffTotalNumberOfUploadFiles, $this->getRequest()->getPost("HandOffStatus", ""))){
			$this->view->hoStatusAndCountFileUploadInvalidMessage = "You must upload as least one file before changing the HO Status to HO-Uploaded";
		}
		
		if(!$hoMapper->isValidHOStatusForSaveUpload($this->getRequest()->getPost("HandOffStatus", "")) && $this->getRequest()->getPost("action") == self::SAVE_UPLOAD){
			$this->view->hoStatusAndUploadInvalidMessage = "you must finish upload files before change HO-Status to HO - Uploaded";
		}
		
		if(!$hoMapper->isValidHBStatusAndTotalUploadFile($ho->HandOffID, $this->getRequest()->getPost("HandOffStatus", ""), $this->view)){
			$this->view->hbStatusAndCountFileUploadInvalidMessage = "Cannot set as HB-Completed since No files were uploaded";
		}
				
		if (!$form->isValid($formData) || !$hoMapper->isValidHOStatusAndTotalUploadFile($ho->HandOffTotalNumberOfUploadFiles, $this->getRequest()->getPost("HandOffStatus", ""))
		||(!$hoMapper->isValidHOStatusForSaveUpload($this->getRequest()->getPost("HandOffStatus", "")) && $this->getRequest()->getPost("action") == self::SAVE_UPLOAD)
		||(!$hoMapper->isValidHBStatusAndTotalUploadFile($ho->HandOffID, $this->getRequest()->getPost("HandOffStatus", ""), $this->view))
		){	
			return $form->populate($formData);
		}
		
		if($hoMapper->isAllowEditFieldInHoRecord($ho->HandOffStatus)){
			$ho->HandOffID = (int)$this->_getParam("handoffid", 0);
			$ho->HandOffTitle = $form->getValue("HandOffTitle");
			$ho->HandOffTotalNumberOfUploadFiles = (int) $form->getValue("HandOffTotalNumberOfUploadFiles");
			$ho->HandOffUploadDate = date("Y-m-d : H:i:s", time());
			$ho->HandOffStartProject = $form->getValue("HandOffStartProject") == "" ? date("Y-m-d : H:i:s", time()) : $form->getValue("HandOffStartProject");				
			$ho->HandOffSourceLanguageID = $form->getValue("HandOffSourceLanguageID");
			$ho->HandOffFolderLocation = $form->getValue("HandOffFolderLocation");
			$ho->HandOffInstruction = htmlspecialchars($form->getValue("content"));
		}
		
		$ho->HandOffClosedDate = $form->getValue("HandOffStatus") == Application_Model_DbTable_HOs::HO_CLOSED ? date("Y-m-d : H:i:s", time()) : null;
		
		if( $form->getValue("HandOffStatus") == Application_Model_DbTable_HOs::HO_RECEIVED){		
			$ho->ExpectedHandBackDate = $form->getValue("ExpectedHandBackDate") != "" ? $form->getValue("ExpectedHandBackDate") : null;			
		}
		
		$ho->HandOffStatus =  $form->getValue("HandOffStatus");			
		$hoMapper->save($ho);
		$handOffTargetLanguageMapper->delete($ho->HandOffID);
		
		foreach($form->getValue("HandOffListTargetLanguageID") as $languageID){
			$handOffTargetLanguageMapper->save($ho->HandOffID, $languageID);
		}
		
		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User Edit HandOffID " .  $ho->HandOffID)
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);		
		
		$hoMapper->sendEmailNotifyHo($this->_getParam("handoffid", 0), $form->getValue("HandOffStatus"), ($this->getRequest()->getParam("NotifyHoStatusChange", "") != "") && ($form->getValue("HandOffStatus")!= Application_Model_DbTable_HOs::HO_CREATED) ? true : false);
		if($this->getRequest()->getPost("action") == self::SAVE_UPLOAD && $this->view->validHOStatusForSaveUpload){
			$this->_helper->redirector->gotoRoute(array("controller"=> "upload", "action"=> "ho", "handoffid"=> (int)$this->_getParam("handoffid", 0)));			
			return;
		}
				
		$this->_helper->redirector("index");			
    }

    public function deleteAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();
	
		$fmMapper = new Application_Model_Mapper_FM();
		$hoMapper = new Application_Model_Mapper_HO();		
		$ho = new Application_Model_HO();
		$handOffID = $this->_getParam("handoffid", 0);
		$hoMapper->find($handOffID, $ho);
		
		if($ho->HandOffID == null){
			throw new Exception("Please choose correct handoffid to delete record");
		}						
		
		if(!$hoMapper->isAllowDeleteHo($ho->HandOffStatus, $ho->UserID)){
			throw new Exception("you do not have a permission to delete this ho record");
		}
		
        if(!$this->getRequest()->isPost()){
		
			$this->view->ho = $ho;
			return;
		}

		if($this->getRequest()->getPost("del") == "Yes"){
			$handOffID = $this->getRequest()->getPost("handoffid");			
			
			$fmMapper->unlinkRecursive( $ho->HandOffFolderLocation, true);
			$hoMapper->deleteHO($handOffID);
			
			$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User Delete HandOffID " .  $handOffID)
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
			$activityMapper->save($activity);		  
		}		
		
		$this->_helper->redirector("index");
    }

    public function viewdetailAction()
    {        
		$hoMapper = new Application_Model_Mapper_HO();
		$ho = new Application_Model_HO();
		$hoMapper->find($this->_getParam("handoffid", 0), $ho);
		
		if($ho->HandOffID == null){
			throw new Exception("Please choose correct handoffid for viewdetail this ho record");
		}
		
		if(!$hoMapper->isAllowViewDetail($ho->UserID)){
			throw new Exception("you do not have a permission to viewdetail this ho record");
		}
		
		$this->view->ho = $ho;
		
    }

    public function addAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();
	
        $form = new Application_Form_HO();		
		$handOffTargetLanguageMapper = new Application_Model_Mapper_HandOffTargetLanguage();
		$auth = Zend_Auth::getInstance();
		$this->view->form = $form;
	
		if(!$this->getRequest()->isPost()){
			$form->HandOffStartProject->setValue(date("Y-m-d", time()));
			return;			
		}			
		
		if($this->getRequest()->getPost("action") == self::CANCEL){
			$this->_helper->redirector("index");
			return;
		}
			
		$formData = $this->getRequest()->getPost();
		$formData["content"] = $this->_helper->WebEditorContentFilter(trim( isset($formData["content"]) ? $formData["content"]:"" ));
		if(!$form->isValid($formData)){
			$form->populate($formData);
			return;
		}
		
		$hoMapper = new Application_Model_Mapper_HO();
		$ho = new Application_Model_HO();		
		
		$ho->UserID = $auth->getIdentity()->UserID;
		$ho->HandOffTitle = $form->getValue("HandOffTitle");
		$ho->HandOffTotalNumberOfUploadFiles = $form->getValue("HandOffTotalNumberOfUploadFiles");
		$ho->HandOffUploadDate = date("Y-m-d : H:i:s", time());
		$ho->HandOffStartProject = $form->getValue("HandOffStartProject") == "" ? date("Y-m-d : H:i:s", time()) : $form->getValue("HandOffStartProject");
		$ho->HandOffStatus =  Application_Model_DbTable_HOs::HO_CREATED;
		$ho->HandOffSourceLanguageID = $form->getValue("HandOffSourceLanguageID");
		$ho->HandOffFolderLocation = $hoMapper->getHandOffPath();
		$ho->HandOffInstruction = htmlspecialchars($form->getValue("content"));
				
		$handOffID = $hoMapper->save($ho);

		foreach($form->getValue("HandOffListTargetLanguageID") as $languageID){
			$handOffTargetLanguageMapper->save($handOffID, $languageID);
		}
		
		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User Add New HandOffID " .  $handOffID)
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);	
		
		if($this->getRequest()->getPost("action") == self::SAVE_UPLOAD){
			$this->_helper->redirector->gotoRoute(array("controller"=> "upload", "action"=> "ho", "handoffid"=> $handOffID));			
			return;
		}
				
		$this->_helper->redirector("index");	
    }		
}









