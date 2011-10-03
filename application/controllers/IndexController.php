<?php

class IndexController extends Zend_Controller_Action
{

    public function indexAction()
    {
		$this->_helper->layout->setLayout('layout-login');		
    }
	
    public function contactAction()
    {	
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		
		$config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/smtp.xml', APPLICATION_ENV);					
		$sendingEmail = new Application_Model_Mapper_SendingEmail();	
        $this->_helper->layout->setLayout('layout-login');
		$this->view->activeContact = true;		
		
		$form = new Application_Form_SendEmailRequest();		
		$form->submit->setLabel("Send");				
		$this->view->form = $form;
		
		if(!$this->getRequest()->isPost()){
			return;			
		}
		
		$formData = $this->getRequest()->getPost();
		if(!$form->isValid($formData) || !$this->isValidInputCaptcha()){			
			return $form->populate($formData);
		}
		
		$sendingEmail->sendingEmail($config->mail->server->sender, $config->mail->admin->alias, $form->getValue("YourEmailAddress"), null, $form->getValue("YourEmailSubject"), $form->getValue("YourEmailBody"));	
		
		$activity->setUserName("Anonymous")
				  ->setUserActivity("Email Sending< Contact us:". $form->getValue("YourEmailSubject") ."> To:".$form->getValue("YourEmailAddress"). " CC:".$config->mail->admin->alias)
				  ->setModule(Application_Model_DbTable_Activities::SENDING_EMAIL)
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);		
		$this->_helper->redirector->gotoRoute(array("controller"=> "auth", "action"=> "index"));					
    }

    public function documentationAction()
    {
        $this->_helper->layout->setLayout('layout-login');
		$this->view->activeDocumentation = true;				
    }

    public function sendEmailRequestAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		
		$config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/smtp.xml', APPLICATION_ENV);			
		$this->_helper->layout->setLayout('layout-login');
		$form = new Application_Form_SendEmailRequest();
		$sendingEmail = new Application_Model_Mapper_SendingEmail();	
		$form->submit->setLabel("Send");		
		$form->YourEmailSubject->setValue($this->_getParam("emailSubjectType", 1) == 1 ? "Request for Lost Bento Login/Password": "Request for Bento Account");		
		$this->view->form = $form;
		
		if(!$this->getRequest()->isPost()){
			return;			
		}

		$formData = $this->getRequest()->getPost();
		if(!$form->isValid($formData) || !$this->isValidInputCaptcha()){			
			return $form->populate($formData);
		}
						
		$sendingEmail->sendingEmail($config->mail->server->sender, $config->mail->admin->alias, $form->getValue("YourEmailAddress"), null, $form->getValue("YourEmailSubject"), $form->getValue("YourEmailBody"));	
		
		$activity->setUserName("Anonymous")
				  ->setUserActivity("Email Sending<". $form->getValue("YourEmailSubject") ."> To:".$form->getValue("YourEmailAddress"). " CC:".$config->mail->admin->alias)
				  ->setModule(Application_Model_DbTable_Activities::SENDING_EMAIL)
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);		
		
		$this->_helper->redirector->gotoRoute(array("controller"=> "auth", "action"=> "index"));			
    }
	
	private function isValidInputCaptcha(){
		  $captcha = $this->getRequest()->getPost('captcha');  		  
		  $captchaId = $captcha['id'];  
		  $captchaInput = $captcha['input'];  		  
		  $captchaSession = new Zend_Session_Namespace('Zend_Form_Captcha_'.$captchaId);  
		  $captchaIterator = $captchaSession->getIterator();  
		  $captchaWord = $captchaIterator['word'];  
		  return ($captchaInput == $captchaWord);		  
	}	
}







