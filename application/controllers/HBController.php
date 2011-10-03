<?php

class HBController extends Zend_Controller_Action
{
	const SAVE_UPLOAD = 'Save & Upload';
	const SAVE_UPLOAD_LATER = "Save";
	const CANCEL = "Cancel";
    
	public function init()
    {
		$this->view->SAVE_UPLOAD = self::SAVE_UPLOAD;
		$this->view->SAVE_UPLOAD_LATER = self::SAVE_UPLOAD_LATER;
		$this->view->CANCEL = self::CANCEL;
		$this->view->activeHb = true;
    }

    public function indexAction()
    {
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);			
		$hbMapper= new Application_Model_Mapper_HB();
        $this->view->paginator = $hbMapper->getPaginator($this->getRequest()->getParam('page', 1), $this->getRequest()->getParam("limit", (int) $config->paginator->itemCountPerPage));
		$this->view->resultSet = $hbMapper->getPaginatorData($this->getRequest()->getParam('page', 1), $this->getRequest()->getParam("limit", (int) $config->paginator->itemCountPerPage));
	}
	
	public function getAvailabeHoForOneUserAction(){
		$hoMapper = new Application_Model_Mapper_HO();			
		echo $this->_helper->json($hoMapper->getAvailabeHOForOneUser($this->_getParam("userid", 0)));
	}
	
    public function addAction()
    {									
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();
		
		$userMapper = new Application_Model_Mapper_User();
		$hoMapper = new Application_Model_Mapper_HO();
		$ho = new Application_Model_HO();
		$hbMapper = new Application_Model_Mapper_HB();
		$hb = new Application_Model_HB();
		
		$this->view->users = $userMapper->getAllClientActive(); 	
				
		if(!$this->getRequest()->isPost()){
			return;			
		}			
				
		if($this->getRequest()->getPost("action") == self::CANCEL){
			$this->_helper->redirector("index");
			return;
		}

		if(!$this->isValidSubmitData()){
			return;
		}	
		
		$hoMapper->find( (int)$this->getRequest()->getPost("HandOff"), $ho);		
		if($ho->HandOffID == null){
			throw new Exception("please choose correct handoffid to add this hb record");
		}		
				
		$hb->HandOffID = (int)$this->getRequest()->getPost("HandOff");
		$hb->HandBackTotalNumberOfUploadFiles = 0;
		$hb->HandBackUploadDate = date("Y-m-d : H:i:s", time());
		$hb->HandBackFolderLocation = $hbMapper->getHandBackPath($ho->HandOffFolderLocation);
		$hb->HandBackComments = htmlspecialchars($this->getRequest()->getPost("content"));		
		$handbackID = $hbMapper->save($hb);
		
		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User Add HandBackID " .  $handbackID)
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);		  
		
		if($this->getRequest()->getPost("action") == self::SAVE_UPLOAD){
			$this->_helper->redirector->gotoRoute(array("controller"=> "upload", "action"=> "hb", "handbackid"=> $handbackID));			
			return;
		}
				
		$this->_helper->redirector("index");	
    }


    public function editAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();
		
        $userMapper = new Application_Model_Mapper_User();
		$hoMapper = new Application_Model_Mapper_HO();
		$ho = new Application_Model_HO();
		$hbMapper = new Application_Model_Mapper_HB();
		$hb = new Application_Model_HB();
					
		$hbMapper->find( (int)$this->_getParam("handbackid", "0"), $hb);
		if($hb->HandBackID == null){
			throw new Exception("Please choose correct handbackid to edit this hb record");
		}	
		
		$hoMapper->find($hb->HandOffID, $ho);	
		if(!$hbMapper->isAllowModifyHb($ho->HandOffStatus)){
			throw new Exception("you do not have a permission to edit this hb record");
		}
		
		$this->view->validHOStatusForSaveUpload = $hbMapper->isValidHOStatusForSaveUpload($ho->HandOffStatus);		
		
		if(!$this->getRequest()->isPost()){						
			$this->view->handOffTitle = $ho->HandOffTitle;
			$this->view->content = $hb->HandBackComments;			
			return;
		}
					
		if($this->getRequest()->getPost("action") == self::CANCEL){
			$this->_helper->redirector("index");
			return;
		}

		if ("" == $this->_helper->WebEditorContentFilter($this->_getParam("content", ""))){
			$this->view->ContentMessage = "Please input comment";
			$this->view->content = $this->_helper->WebEditorContentFilter($this->_getParam("content", ""));
			$this->view->handOffTitle = $ho->HandOffTitle;
			return;
		}	
				
		$hb->HandBackComments = htmlspecialchars($this->getRequest()->getPost("content"));		
		$hbMapper->save($hb);
		
		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User Edit HandBackID " .  (int)$this->_getParam("handbackid", "0"))
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);		  
		
		if($this->getRequest()->getPost("action") == self::SAVE_UPLOAD && $hbMapper->isValidHOStatusForSaveUpload($ho->HandOffStatus)){
			$this->_helper->redirector->gotoRoute(array("controller"=> "upload", "action"=> "hb", "handbackid"=> $hb->HandBackID));			
			return;
		}
				
		$this->_helper->redirector("index");	
    }

    public function deleteAction()
    {
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();
		
		$fmMapper = new Application_Model_Mapper_FM();
        $hbMapper = new Application_Model_Mapper_HB();
		$hb = new Application_Model_HB();
		$hoMapper = new Application_Model_Mapper_HO();
		$ho = new Application_Model_HO();
		$hbMapper->find($this->_getParam("handbackid", 0), $hb);		
		if($hb->HandBackID == null){
			throw new Exception("Please choose correct handbackid to delete this hb record");
		}	
		$hoMapper->find($hb->HandOffID, $ho);
		
		if(!$hbMapper->isAllowModifyHb($ho->HandOffStatus)){
			throw new Exception("you do not have a permission to delete this hb record");
		}
		
        if(!$this->getRequest()->isPost()){			
			$this->view->hb = $hb;
			return;
		}

		if($this->getRequest()->getPost("del") == "Yes"){
			$fmMapper->unlinkRecursive( $hb->HandBackFolderLocation , true);
			$hbMapper->deleteHB($this->getRequest()->getPost("handbackid"));
		}		
		
		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User Delete HandBackID " . $this->getRequest()->getPost("handbackid"))
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);	
		
		$this->_helper->redirector("index");
    }	
	
	function isValidSubmitData(){
		$valid = true;
		if(0 == (int)$this->_getParam("UserName", 0)){
			$this->view->UserNameMessage = "Please select one user";
			$valid = false;
		}
		
		if(0 == (int)$this->_getParam("HandOff", 0)){		
			$this->view->HandOffMessage = "Please select one handoff";
			$valid = false;

		}
		
		if("" == $this->_helper->WebEditorContentFilter($this->_getParam("content", ""))){			
			$this->view->ContentMessage = "Please input comment";
			$valid = false;
		}
		
		$this->view->UserID = $this->_getParam("UserName", 0);			
		$this->view->HandOffID = $this->_getParam("HandOff", 0);
		$this->view->content = $this->_helper->WebEditorContentFilter($this->_getParam("content", ""));
		return $valid;
	}
}