<?php

class ActivityController extends Zend_Controller_Action
{

    public function init()
    {
		$userMapper = new Application_Model_Mapper_User();
        $this->view->activeActivity = true;
		$this->view->users = $userMapper->getAllUserActive();
    }

    public function indexAction()
    {
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);			
		$activityMapper = new Application_Model_Mapper_Activity();

		$this->view->paginator = $activityMapper->getPaginator($this->getRequest()->getParam('page', 1), 
		$this->getRequest()->getParam("limit", (int) $config->paginator->itemCountPerPage), null, null, null);
		$this->view->resultSet = $activityMapper->getPaginatorData($this->getRequest()->getParam('page', 1), $this->getRequest()->getParam("limit", (int) $config->paginator->itemCountPerPage),  null, null, null);
    }
	
	public function searchAction(){
		$this->_helper->viewRenderer('index');
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);			
		$activityMapper = new Application_Model_Mapper_Activity();
		$this->view->userid = $this->getRequest()->getParam('userid', null);
		$this->view->activityStartDate = $this->getRequest()->getParam("activityStartDate", null);
		$this->view->activityEndDate = $this->getRequest()->getParam("activityEndDate", null);
		
		$this->view->paginator = $activityMapper->getPaginator($this->getRequest()->getParam('page', 1), 
		$this->getRequest()->getParam("limit", (int) $config->paginator->itemCountPerPage), $this->getRequest()->getParam('userid', null),
		$this->getRequest()->getParam('activityStartDate', null), $this->getRequest()->getParam('activityEndDate', null));
		$this->view->resultSet = $activityMapper->getPaginatorData($this->getRequest()->getParam('page', 1), $this->getRequest()->getParam("limit", (int) $config->paginator->itemCountPerPage),  $this->getRequest()->getParam('userid', null),
		$this->getRequest()->getParam('activityStartDate', null), $this->getRequest()->getParam('activityEndDate', null));
	}
}

