<?php
require_once 'Zend/View/Interface.php';

class Zend_View_Helper_ShowMenuDependingRole
{
	public $view;
	
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
	
	public function showMenuDependingRole()
	{	
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		$auth = Zend_Auth::getInstance();
		
		if( $auth->hasIdentity()){
			$this->view->UserIsClient = $auth->getIdentity()->UserIsClient;
			$this->view->UserIsJonckersPM = $auth->getIdentity()->UserIsJonckersPM;
			$this->view->UserIsAdmin = ($auth->getIdentity()->UserLoginName == $config->fmsadmin->username) ? "Y" : "N";
		}
		
		return $this->view->render('index/_show-menu-depending-role.phtml');
	}
}