<?php

class EmailController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->activeEmail = true;
    }

    public function indexAction()
    {
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);			
		$emailTemplateMapper = new Application_Model_Mapper_EmailTemplate();
					
		$this->view->paginator = $emailTemplateMapper->getPaginator($this->getRequest()->getParam('page', 1), $this->getRequest()->getParam("limit", (int) $config->paginator->itemCountPerPage));
		$this->view->resultSet = $emailTemplateMapper->getPaginatorData($this->getRequest()->getParam('page', 1), $this->getRequest()->getParam("limit", (int) $config->paginator->itemCountPerPage));		
    }

    public function addAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();
				
        $form = new Application_Form_EmailTemplate();
		$form->submit->setLabel("Save");
		$this->view->form = $form;
		$emailTemplateMapper = new Application_Model_Mapper_EmailTemplate();
		$emailTemplate = new Application_Model_EmailTemplate();
		
		if(!$this->getRequest()->isPost()){
			return;			
		}			

		$formData = $this->getRequest()->getPost();
		$formData["content"] = $this->_helper->WebEditorContentFilter(trim($formData["content"]));
		
		if(!$form->isValid($formData)){
			$form->populate($formData);
			return;
		}				
		$emailTemplate->EmailTemplateID = (int)$form->getValue("EmailTemplateID");
		$emailTemplate->EmailTemplateContent = htmlspecialchars($form->getValue("content"));					
		$emailTemplate->EmailTemplateStatus = $form->getValue("EmailTemplateStatus");
		$emailTemplate->EmailTemplateIsActive = $form->getValue("EmailTemplateIsActive") == "Y" ? "Y" : "N";
		$emailTemplate->EmailTemplateSubject = $form->getValue("EmailTemplateSubject");	
		$emailTemplateID = $emailTemplateMapper->save($emailTemplate);
		
		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("Add Email Template" . $emailTemplateID)
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);		  
		
		$this->_helper->redirector("index");
    }

    public function editAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();
			
		$form = new Application_Form_EmailTemplate();
		$form->submit->setLabel("Save");
		$this->view->form = $form;
		$emailTemplateMapper = new Application_Model_Mapper_EmailTemplate();			    
		$emailTemplate = new Application_Model_EmailTemplate();			    			
		
		$emailTemplateMapper->find($this->_getParam("emailTemplateID", 0), $emailTemplate);
			
		if($emailTemplate->EmailTemplateID == null){
			throw new Exception("Please choose correct emailTemplateId for edit emailTemplate");
		}						
		
		if(!$this->getRequest()->isPost()){			
			return $form->populate(array("EmailTemplateID" => $emailTemplate->EmailTemplateID, "EmailTemplateSubject" => $emailTemplate->EmailTemplateSubject,
				"content" => $emailTemplate->EmailTemplateContent, "EmailTemplateStatus" => $emailTemplate->EmailTemplateStatus,
				"EmailTemplateIsActive" => $emailTemplate->EmailTemplateIsActive
			));						
		}
		
		$formData = $this->getRequest()->getPost();			
		$formData["content"] = $this->_helper->WebEditorContentFilter(trim($formData["content"]));
				
		if(!$form->isValid($formData)){			
			return $form->populate($formData);	
		}
				
		$emailTemplate->EmailTemplateID = (int)$form->getValue("EmailTemplateID");
		$emailTemplate->EmailTemplateSubject = $form->getValue("EmailTemplateSubject");	
		$emailTemplate->EmailTemplateContent = htmlspecialchars($form->getValue("content"));			
		$emailTemplate->EmailTemplateStatus = $form->getValue("EmailTemplateStatus");
		$emailTemplate->EmailTemplateIsActive = $form->getValue("EmailTemplateIsActive") == "Y" ? "Y" : "N";											
		$emailTemplateMapper->save($emailTemplate);
		
		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User Edit EmailTemplateID " . (int)$form->getValue("EmailTemplateID"))
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);		  
		
		$this->_helper->redirector("index");
    }

    public function deleteAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();
	
      	if(!$this->getRequest()->isPost()){
			$emailTemplateID = $this->_getParam("emailTemplateID", 0);
			$emailTemplateMapper= new Application_Model_Mapper_EmailTemplate();
			$emailTemplate= new Application_Model_EmailTemplate();
			$emailTemplateMapper->find($emailTemplateID, $emailTemplate);
			$this->view->emailTemplate = $emailTemplate;
			return;
		}

		if($this->getRequest()->getPost("del") == "Yes"){
			$emailTemplateID = $this->getRequest()->getPost("emailTemplateID");			
			$emailTemplateMapper = new Application_Model_Mapper_EmailTemplate();
			
			$emailTemplateMapper->deleteEmailTemplate($emailTemplateID);
		}		
		
		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User Delete EmailTemplateID " .  $this->getRequest()->getPost("emailTemplateID"))
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);		  
		
		$this->_helper->redirector("index");
    }
	
	public function viewdetailAction()
    {        
		$emailTemplateMapper = new Application_Model_Mapper_EmailTemplate();
		$emailTemplate = new Application_Model_EmailTemplate();
		$emailTemplateMapper->find($this->_getParam("emailTemplateID", 0), $emailTemplate);
		
		if($emailTemplate->EmailTemplateID == null){
			throw new Exception("Please choose correct emailTemplateID for viewdetail this ho record");
		}
			
		$this->view->emailTemplate = $emailTemplate;
		
    }
}







