<?php

require_once ('Zend/Controller/Plugin/Abstract.php');

class Application_Plugin_CheckLogin extends  Zend_Controller_Plugin_Abstract
{
	public function postDispatch(Zend_Controller_Request_Abstract $request) {
		parent::postDispatch($request);	
	}

	public function routeShutdown(Zend_Controller_Request_Abstract $request) {

		$controllerName = strtolower($request->getControllerName());
		$actionName = strtolower($request->getActionName());
		$auth = Zend_Auth::getInstance();
		Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH .'/controllers/helpers');
		
		if($controllerName == 'auth' && $actionName == 'index') {			
			return;
		}
		
		if($controllerName == 'auth' && $actionName == 'logout') {
			return;
		}
		
		if($controllerName == 'index' && !$auth->hasIdentity()) {
			return;
		}
		
		if(!$auth->hasIdentity()){ 
			$authFormSession = new Zend_Session_Namespace('authFormSession');			
			$authFormSession->destinationUrl = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(); 
			$redirect = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
			$redirect->gotoSimple("index", "auth", "default");	
			return;
		}		
		$Acl = Zend_Controller_Action_HelperBroker::getStaticHelper('Acl');
		$Acl->direct();
	} 	  
}