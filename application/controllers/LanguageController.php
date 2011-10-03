<?php

class LanguageController extends Zend_Controller_Action
{

    public function init()
    {		
		$this->view->activeLanguage = true;
    }

    public function indexAction()
    {
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);			
		$languageMapper = new Application_Model_Mapper_Language();

		$this->view->paginator = $languageMapper->getPaginator($this->getRequest()->getParam('page', 1), $this->getRequest()->getParam("limit", (int) $config->paginator->itemCountPerPage));
		$this->view->resultSet = $languageMapper->getPaginatorData($this->getRequest()->getParam('page', 1), $this->getRequest()->getParam("limit", (int) $config->paginator->itemCountPerPage));
    }

    public function deleteAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();	
			
		if(!$this->getRequest()->isPost()){
			$languageID = $this->_getParam("languageID", 0);
			$language = new Application_Model_Language();
			$languageMapper = new Application_Model_Mapper_Language();
			
			$languageMapper->find($languageID, $language);
			$this->view->language = $language;
			return;
		}

		if($this->getRequest()->getPost("del") == "Yes"){
			$languageID = $this->getRequest()->getPost("languageID");
			$language = new Application_Model_Mapper_Language();
			
			$language->deleteLanguage($languageID);
			
			$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User Delete LanguageID " .  $languageID)
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
			$activityMapper->save($activity);
		}		
		
		$this->_helper->redirector("index");
    }

    public function addAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();	
		
        $form = new Application_Form_Language();
		$languageMapper = new Application_Model_Mapper_Language();
		$language = new Application_Model_Language();	
		$form->submit->setLabel("Save");
		$this->view->form = $form;
		
		if(!$this->getRequest()->isPost()){
			return;			
		}			

		$formData = $this->getRequest()->getPost();
		if(!$form->isValid($formData)){			
			return $form->populate($formData);
		}
				
		if (0 <> count($languageMapper->getLanguageByLanguageName($form->getValue("LanguageName"), 0))){
			$this->view->insertErrorMessage = "can not add duplicate language name";
			return $form->populate($this->getRequest()->getPost());
		}
		
		$language->LanguageName = $form->getValue("LanguageName");
		$language->LanguageIsActive = $form->getValue("LanguageIsActive") == "Y" ? "Y" : "N";
		$language->LanguageIsShowInSourceList = $form->getValue("LanguageIsActive") == "N" ? "N" : ($form->getValue("LanguageIsShowInSourceList") == "Y" ? "Y" : "N");
		$language->LanguageIsShowInTargetList = $form->getValue("LanguageIsActive") == "N" ? "N" : ($form->getValue("LanguageIsShowInTargetList") == "Y" ? "Y" : "N");
		$languageID = $languageMapper->save($language);	

		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User Add LanguageID " .  $languageID)
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);	
		
		$this->_helper->redirector("index");	
    }

    public function editAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();
		
		$form = new Application_Form_Language();
		$form->submit->setLabel("Save");
		$this->view->form = $form;
		$languageMapper = new Application_Model_Mapper_Language();					
		$language = new Application_Model_Language();
		$languageMapper->find($this->_getParam("languageID", 0), $language);		
		if($language->LanguageID == null){
			throw new Exception("please choose correct languageid");
		}
		
		if(!$this->getRequest()->isPost()){
			$form->populate(array("LanguageID" => $language->LanguageID, "LanguageName" => $language->LanguageName, "LanguageIsActive" => $language->LanguageIsActive,
			"LanguageIsShowInSourceList" =>$language->LanguageIsShowInSourceList, "LanguageIsShowInTargetList" => $language->LanguageIsShowInTargetList));
			return;
		}
		
	
		if (!$form->isValid($this->getRequest()->getPost())){
			return $form->populate($this->getRequest()->getPost());	
		}
		
		if (0 <> count($languageMapper->getLanguageByLanguageName($form->getValue("LanguageName"), $this->_getParam("languageID", 0)))){
			$this->view->updateErrorMessage = "can not update duplicate language name";
			$form->populate($formData = $this->getRequest()->getPost());
			return;
		}
		
		$language->LanguageID = (int)$form->getValue("LanguageID");
		$language->LanguageName = $form->getValue("LanguageName");
		$language->LanguageIsActive = $form->getValue("LanguageIsActive") == "Y" ? "Y" : "N";
		$language->LanguageIsShowInSourceList = $form->getValue("LanguageIsActive") == "N" ? "N" : ($form->getValue("LanguageIsShowInSourceList") == "Y" ? "Y" : "N");
		$language->LanguageIsShowInTargetList = $form->getValue("LanguageIsActive") == "N" ? "N" : ($form->getValue("LanguageIsShowInTargetList") == "Y" ? "Y" : "N");		
		$languageMapper->save($language);
		
		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User Edit LanguageID " .  (int)$form->getValue("LanguageID"))
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);	
		
		$this->_helper->redirector("index");	
    }
}







