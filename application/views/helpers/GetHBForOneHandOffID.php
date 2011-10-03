<?php

require_once 'Zend/View/Interface.php';

class Zend_View_Helper_GetHBForOneHandOffID
{
	public $view;
	
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
			
	public function getHBForOneHandOffID($handOffID){
		$hbMapper = new Application_Model_Mapper_HB();
		
		return $hbMapper->getAvailabeHandbackForHandOffID($handOffID);
	}
}