<?php

class MyaccountController extends Zend_Controller_Action
{

    public function init()
    {
        $auth = Zend_Auth::getInstance();
		$userMapper = new Application_Model_Mapper_User();
		$users = $userMapper->getUserByUserLoginName(0, $auth->getIdentity()->UserLoginName);
		$this->view->user = $users[0];		
		$this->view->myAccount = true;
    }

    public function indexAction()
    {
		$auth = Zend_Auth::getInstance();
		$userMapper = new Application_Model_Mapper_User();
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$form = new Application_Form_User();			
		$this->view->form = $form;
		$form->JtepmEmail->setRequired(false);	
        $users = $userMapper->getUserByUserLoginName(0, $auth->getIdentity()->UserLoginName);	
		if(!$this->getRequest()->isPost()){
			$this->view->user = $users[0];		
			return;
		}
		
		$formData = array("UserName" => $users[0]->UserName, "UserEmail" => $this->getRequest()->getPost("UserEmail"), "UserLoginName" => $users[0]->UserLoginName,
						  "UserPassword"=> $this->getRequest()->getPost("UserPassword"), "UserConfirmPassword" => $this->getRequest()->getPost("UserConfirmPassword"),
						  "UserIsClient" => $users[0]->UserIsClient, "UserIsActive" => $users[0]->UserIsActive, "UserID" => $users[0]->UserID,
						  "UserRootUploadDirectory" => $users[0]->UserRootUploadDirectory);
		
		if($this->getRequest()->getPost("UserPassword") == ""){
			$form->UserPassword->setRequired(false);	
			$form->UserConfirmPassword->setRequired(false);				
		}
		
		if(!$form->isValid($formData)){
			$users[0]->UserEmail = $this->getRequest()->getPost("UserEmail");
			$this->view->user = $users[0];		
			return;
		}
		
		$users[0]->UserEmail = $this->getRequest()->getPost("UserEmail");					
		$users[0]->UserPassword = $this->getRequest()->getPost("UserPassword");	
		$userID = $userMapper->save($users[0]);
		
		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User Edit UserID " .  $userID)
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);	
		
		$this->_helper->redirector->gotoRoute(array("controller"=> "ho", "action"=> "index"));			
			
    }
}

