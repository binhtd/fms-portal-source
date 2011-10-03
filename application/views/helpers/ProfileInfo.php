<?php

require_once 'Zend/View/Interface.php';

class Zend_View_Helper_ProfileInfo
{
	public $view;
	
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
	
	public function profileInfo()
	{
		$auth = Zend_Auth::getInstance();
	
		if(!$auth->hasIdentity() ){		
			return "";			
		}
		
		return strlen($auth->getIdentity()->UserLoginName) > 16 ? sprintf('Welcome <span title="%s">%s...</span>', $auth->getIdentity()->UserLoginName, substr($auth->getIdentity()->UserLoginName, 0, 13)) : sprintf("Welcome %s", $auth->getIdentity()->UserLoginName); 
	}
}