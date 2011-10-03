<?php

require_once ('Zend/Controller/Plugin/Abstract.php');

class Application_Plugin_CheckACL extends  Zend_Controller_Plugin_Abstract
{
	public function postDispatch(Zend_Controller_Request_Abstract $request) {
		parent::postDispatch($request);	
	}

	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {						
		$auth = Zend_Auth::getInstance();			
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		
		if(!$auth->hasIdentity() || strtolower($request->getControllerName())=="auth"){			
			return;
		}
		
		$acl = Zend_Registry::get('acl');	
		$roleName = ($auth->getIdentity()->UserIsClient == 'Y' ? 'client' : ($auth->getIdentity()->UserIsJonckersPM == 'Y' ? 'jonckerspm' : 'client'));
		if(isset($config->fmsadmin->username)){
			$roleName = ($auth->getIdentity()->UserLoginName == $config->fmsadmin->username) ? "jonckersadmin" : $roleName;
		}
		
		if(!$acl->isAllowed($roleName, strtolower($request->getControllerName()), strtolower($request->getActionName()))){		
			$request->setControllerName('Error');
			$request->setActionName('index');
		}		
	} 	  
}