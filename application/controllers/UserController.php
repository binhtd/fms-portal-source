<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
		$userMapper = new Application_Model_Mapper_User();		
		$this->view->activeUser = true;
		$this->view->allowModifyUser = $userMapper->isAllowModifyUser(0);
		$this->view->jtepmEmailList = $userMapper->getAvailableJtepmEmail();		
    }

    public function indexAction()
    {
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);			
        $userMapper = new Application_Model_Mapper_User();
		
		$this->view->paginator = $userMapper->getPaginator($this->getRequest()->getParam('page', 1), $this->getRequest()->getParam("limit", (int) $config->paginator->itemCountPerPage));	
		$this->view->resultSet = $userMapper->getPaginatorData($this->getRequest()->getParam('page', 1), $this->getRequest()->getParam("limit", (int) $config->paginator->itemCountPerPage));	 	
	}

    public function addAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();
	
		$form = new Application_Form_User();		
		$form->submit->setLabel("Save");
		$this->view->form = $form;
		$userMapper = new Application_Model_Mapper_User();
		$user = new Application_Model_User();
		
		if(!$userMapper->isAllowModifyUser(0)){
			throw new Exception("you do not have a permission to add new account");
		}
		
		if(!$this->getRequest()->isPost()){
			return;			
		}			

		if($this->getRequest()->getPost("UserIsClient") == "N"){
			$form->JtepmEmail->setRequired(false);	
		}
		
		$formData = $this->getRequest()->getPost();
		if(!$form->isValid($formData)){			
			$form->populate($formData);
			return;
		}				
		
		if(0 <> count($userMapper->getUserByUserLoginName(0, $form->getValue("UserLoginName")))){
			$this->view->insertErrorMessage = "can not insert duplicate user login name";
			$form->populate($formData);
			return;
		}
		
		if(0 <> count($userMapper->getUserByUserEmail($form->getValue("UserEmail"), 0))){
			$this->view->insertErrorMessage = "can not insert duplicate email address";
			$form->populate($formData);
			return;
		}
				
			
		$user->UserName = $form->getValue("UserName");			
		$user->UserEmail = $form->getValue("UserEmail");			
		$user->UserLoginName = $form->getValue("UserLoginName");			
		$user->UserPassword = $form->getValue("UserPassword");		
		$user->UserRootUploadDirectory = $form->getValue("UserRootUploadDirectory");
		$user->UserIsClient = $form->getValue("UserIsClient") == "Y" ? "Y" : "N" ;
		$user->UserIsJonckersPM = $form->getValue("UserIsClient") == "N" ? "Y" : "N" ;
		$user->UserIsActive = $form->getValue("UserIsActive") == "Y" ? "Y" : "N";		
		if($form->getValue("UserIsClient") == "Y"){
			$user->JtepmEmail = $form->getValue("JtepmEmail");
		}
		
		$userID = $userMapper->save($user);
		
		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User add UserID " .  $userID)
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);	
		
		$this->_helper->redirector("index");
    }

    public function editAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();
	
		$form = new Application_Form_User();			
		$form->submit->setLabel("Save");
		$this->view->form = $form;
		$userMapper = new Application_Model_Mapper_User();					
		$user = new Application_Model_User();
		if(!$userMapper->isAllowModifyUser($this->_getParam("userID", 0))){
			throw new Exception("you do not have a permission to edit this account");
		}
		
		if(!$this->getRequest()->isPost()){
			if($this->_getParam("userID", 0) <= 0){
				return ;//$this->render();		
			}	
			
			$userMapper->find($this->_getParam("userID", 0), $user);	
			$form->populate( array("UserID" => $user->UserID, "UserName" => $user->UserName ,"UserEmail" => $user->UserEmail,
				"UserLoginName" => $user->UserLoginName,"UserPassword" => $user->UserPassword, "UserRootUploadDirectory" => $user->UserRootUploadDirectory,
				"UserIsClient" => $user->UserIsClient, "UserIsActive" => $user->UserIsActive, "JtepmEmail" => $user->JtepmEmail));
			return;
		}				
				
		if($this->getRequest()->getPost("UserPassword") == ""){
			$form->UserPassword->setRequired(false);	
			$form->UserConfirmPassword->setRequired(false);				
		}
		
		if($this->getRequest()->getPost("UserIsClient") == "N"){
			$form->JtepmEmail->setRequired(false);	
		}
		
		$formData = $this->getRequest()->getPost();			
		if(!$form->isValid($formData)){
			return $form->populate($formData);	
		}
		
		
		if(0 <> count($userMapper->getUserByUserLoginName($form->getValue("UserID"), $form->getValue("UserLoginName")))){
			$this->view->updateErrorMessage = "can not update duplicate user login name";
			return $form->populate($formData);
		}
		
		if(0 <> count($userMapper->getUserByUserEmail($form->getValue("UserEmail"), $form->getValue("UserID")))){
			$this->view->updateErrorMessage = "can not update duplicate email address";
			$form->populate($formData);
			return;
		}
		
		$user->UserID = (int)$form->getValue("UserID");
	
		$user->UserName = $form->getValue("UserName");			
		$user->UserEmail = $form->getValue("UserEmail");			
		$user->UserLoginName = $form->getValue("UserLoginName");			
		$user->UserPassword = $form->getValue("UserPassword");		
		$user->UserRootUploadDirectory = $form->getValue("UserRootUploadDirectory");
		$user->UserIsClient = $form->getValue("UserIsClient") == "Y" ? "Y" : "N" ;
		$user->UserIsJonckersPM = $form->getValue("UserIsClient") == "N" ? "Y" : "N" ;
		$user->UserIsActive = $form->getValue("UserIsActive") == "Y" ? "Y" : "N";
		if($form->getValue("UserIsClient") == "Y"){
			$user->JtepmEmail = $form->getValue("JtepmEmail");
		}
	
		$userID = $userMapper->save($user);
		
		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User Edit UserID " .  $userID)
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);	
		
		$this->_helper->redirector("index");		
    }

    public function deleteAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();
		
		$userMapper = new Application_Model_Mapper_User();	
		$user = new Application_Model_User();
		if(!$userMapper->isAllowModifyUser($this->_getParam("userID", 0))){
			return $this->_helper->redirector("index");		
		}
		
		if(!$this->getRequest()->isPost()){
			$userID = $this->_getParam("userID", 0);
			$userMapper->find($userID, $user);
			$this->view->user = $user;				
			return;
		}

		if($this->getRequest()->getPost("del") == "Yes"){
			$userID = $this->getRequest()->getPost("userID");						
			$userMapper->deleteUser($userID);
			
			$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User Delete UserID " .  $userID)
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
			$activityMapper->save($activity);	
		}		
		
		$this->_helper->redirector("index");
    }

    public function viewdetailAction()
    {
        $userID = $this->_getParam("userID", 0);	
		if($userID > 0){
			$userMapper = new Application_Model_Mapper_User();	
			$user = new Application_Model_User();
			$userMapper->find($userID, $user);
			
			$this->view->user = $user;				
		}		
    }


}









