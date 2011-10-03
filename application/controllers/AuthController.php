<?php

class AuthController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$authFormSession = new Zend_Session_Namespace('authFormSession');
		$this->_helper->layout->setLayout('layout-login');
		$form = new Application_Form_Login();
		$this->view->form = $form;
		$request = $this->getRequest();
		
		
		if( !$request->isPost()){
			return;			
		}
		
		if( !$this->isSupportJavascript()){
			return;
		}
		
		if( !$this->isBrowserSupport()){
			$this->view->messageForBrowserUnSupport = $this->getMessageForBrowserUnSupport();
			return;
		}
		
		$form->populate(array("username" => $request->getPost("username"), "password" => $request->getPost("password")));
		$this->countTimeLogin();
		$this->registerCaptcha();		
		
		if( !$form->isValid($request->getPost())){			
			return;
		}
		$this->registerJonckersAdmin();
						
		if(!$this->_process($form->getValues()) || !$this->isValidCapcha()){				
			$form->setDescription('Invalid credentials provided');	
			return;
		}		
		$this->resetYourLoginRequestCount();
		$activity->setUserName($request->getPost("username"))
				  ->setUserActivity("User login")
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);		  
				  
		if (isset($authFormSession->destinationUrl)){			
			return $this->_redirect($authFormSession->destinationUrl);
		}
		
		$this->_helper->redirector->gotoRoute(array("controller"=>"ho", "action"=>"index"));
		
    }
	
	private function isSupportJavascript(){
		return ( 1 != (int)$this->getRequest()->getParam('js_enabled', 0));
	}
	
	private function isBrowserSupport(){
		$bootstrap = $this->getInvokeArg('bootstrap');
		$userAgent = $bootstrap->getResource('useragent');		
		$device = $userAgent->getDevice();
				
		switch(strtolower($device->getBrowser())){
			case "internet explorer":
				switch($device->getBrowserVersion()){
					case "4.0":
					case "5.0": 
					case "5.5":
					case "6.0":
					case "7.0":
						return false;
					default:
						return true;
				}
			case "firefox":
				switch($device->getBrowserVersion()){
					case "1.0":
					case "1.5":
					case "2.0":
					case "3.0":
						return false;
					default:
						return true;
				}
			case "chrome":
				return true;
			default:
				return false;
		}
		
		return true;
	}
	
	private function getMessageForBrowserUnSupport(){
		$bootstrap = $this->getInvokeArg('bootstrap');
		$userAgent = $bootstrap->getResource('useragent');		
		$device = $userAgent->getDevice();
		$browserType = $device->getBrowser();
		$browserVersion = $device->getBrowserVersion();
		
		return "Your browser type is $browserType and browser version is $browserVersion, it is not supported by our system, please check the documentation for further details";
	}
	
	private function countTimeLogin(){
		$authFormSession = new Zend_Session_Namespace('authFormSession');
		
		if(!isset($authFormSession->yourLoginRequest)){
				$authFormSession->yourLoginRequest = 0;
		}			
			
		$authFormSession->yourLoginRequest++;
		$this->view->yourLoginRequest = $authFormSession->yourLoginRequest;
	}
	
	private function resetYourLoginRequestCount(){
		$authFormSession = new Zend_Session_Namespace('authFormSession');
		unset($authFormSession->yourLoginRequest);
	}
	
	private function registerCaptcha(){
		$authFormSession = new Zend_Session_Namespace('authFormSession');
		
		if(!isset($authFormSession->yourLoginRequest)){
				$authFormSession->yourLoginRequest = 0;
		}			
		
		if($authFormSession->yourLoginRequest > 2){
			$captcha = new Zend_Captcha_Image();
			$captcha->setImgDir(APPLICATION_PATH . '/../public/captcha/images/');
			$captcha->setImgUrl($this->view->baseUrl('/captcha/images/'));
			$captcha->setFont(APPLICATION_PATH . '/../public/captcha/fonts/arial.ttf');
			$captcha->setWidth(182);
			$captcha->setHeight(40);
			$captcha->setWordlen(5);
			$captcha->setFontSize(19);
			$captcha->setLineNoiseLevel(2);
			$captcha->generate();
			$this->view->captcha = $captcha;
		}
	}	
	
	private function isValidCapcha(){
		$authFormSession = new Zend_Session_Namespace('authFormSession');
		
		if (!isset($authFormSession->yourLoginRequest)){
			return true;
		}
		
		if ($authFormSession->yourLoginRequest > 2){
			$capId = $this->getRequest()->getPost('cid');
			$capSession = new Zend_Session_Namespace('Zend_Form_Captcha_'.$capId);
			
			return ($this->getRequest()->getPost('captcha')==$capSession->word);
		}
		
		return true;
	}
	
	private function registerJonckersAdmin(){
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		$userMapper = new Application_Model_Mapper_User();
		$user = new Application_Model_User();
		$users = $userMapper->getUserByUserLoginName(0, $config->fmsadmin->username);
		
		if(!isset($config->fmsadmin->username) && !isset($config->fmsadmin->password)){
			return;
		}
		
		$user->setUserName($config->fmsadmin->username)
				  ->setUserEmail($config->mail->admin->alias)
				  ->setUserLoginName($config->fmsadmin->username)
				  ->setUserPassword($config->fmsadmin->password)
				  ->setUserRootUploadDirectory($config->fmsadmin->username)
				  ->setUserLastActivity(date("Y-m-d : H:i:s", time()))
				  ->setUserIsClient('N')
				  ->setUserIsJonckersPM('Y')
				  ->setUserIsActive('Y');
		
		if( 0 == count($users)){
			$userMapper->save($user);
			return;
		}
	}
	

	public function logoutAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();
		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User logout")
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);		  
		
        Zend_Auth::getInstance()->clearIdentity();
		Zend_Session:: namespaceUnset('authFormSession');
		
		$this->_helper->redirector->gotoRoute(array("controller"=>"auth", "action"=>"index"));		
    }
	
    protected function _process($values)
    {
		$adapter = $this->_getAuthAdapter();
		$adapter->setIdentity($values["username"]);
		$adapter->setCredential(md5($values["password"]));
		$auth = Zend_Auth::getInstance();
		$result = $auth->authenticate($adapter);
		
		if($result->isValid()){			
			$user = $adapter->getResultRowObject(null, array("UserPassword"));
			$auth->getStorage()->write($user);
			return true;
		}
		return false;
    }

    protected function _getAuthAdapter()
    {
		$dbAdapter = Zend_Db_Table::getDefaultAdapter();
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
		
		$authAdapter->setTableName('users')
			->setIdentityColumn('UserLoginName')
			->setCredentialColumn('UserPassword');
		$authAdapter->getDbSelect()->where('UserIsActive = "Y"');
		
		return $authAdapter;
    }	
}



