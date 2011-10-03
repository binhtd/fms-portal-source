<?php

class PageTranslationTrackingController extends Zend_Controller_Action
{

    public function init()
    {		
		$this->view->activePageTranslationTracking= true;
    }

    public function indexAction()
    {
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);			
		$pageTranslationTrackingMapper = new Application_Model_Mapper_PageTranslationTracking();

		$this->view->paginator = $pageTranslationTrackingMapper->getPaginator($this->getRequest()->getParam('page', 1), $this->getRequest()->getParam("limit", (int) $config->paginator->itemCountPerPage));
		
		$this->view->resultSet = $pageTranslationTrackingMapper->getPaginatorData($this->getRequest()->getParam('page', 1), $this->getRequest()->getParam("limit", (int) $config->paginator->itemCountPerPage));
    }

	public function exportAction(){
		$exportPageTranslationTrackingMapper = new Application_Model_Mapper_ExportPageTranslationTracking();
		$exportPageTranslationTrackingMapper->ExportToExcel();
	}
}







