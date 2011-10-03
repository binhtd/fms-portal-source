<?php

class UploadController extends Zend_Controller_Action
{
    public function hoAction()
    { 	
		$upload = new Application_Model_Mapper_Upload();
		$hoMapper = new Application_Model_Mapper_HO();
		$ho = new Application_Model_HO();
		$hoMapper->find($this->_getParam("handoffid", 0), $ho);
		$this->view->activeHo = true;
		
		if($ho->HandOffID == null){
			throw new Exception("please input correct handoffid to upload file for ho record");
		}	
		
		$this->view->showSetHoStatusButton = ($ho->HandOffTotalNumberOfUploadFiles > 0);		
		if(!$hoMapper->isValidHOStatusForSaveUpload($ho->HandOffStatus)){
			return $this->_helper->redirector->gotoRoute(array("controller"=> "ho", "action"=> "index", "handoffid"=> (int)$this->_getParam("handoffid", 0)));
		}
		
		if (!$this->getRequest()->isPost()){					
			return;
		}
			
		if (0 <> strlen($this->_getParam("seefilesbutton", ""))){
			return $this->_helper->redirector->gotoRoute(array("controller"=> "fm", "action"=> "index", "handoffid" => $ho->HandOffID, "download" => "ho"));
		}
		
		if (0 <> strlen($this->_getParam("backbutton", ""))){
			$this->_helper->redirector->gotoRoute(array("controller"=> "ho", "action"=> "index"));
			return;				
		}
		
		if ((0 <> strlen($this->_getParam("setHoStatusButton", ""))) && (!$this->view->showSetHoStatusButton)){
			throw new Exception("please upload file for this ho");
		}
		
		if (0 <> strlen($this->_getParam("setHoStatusButton", ""))){												
			$this->updateHoStatus($this->_getParam("handoffid", 0), Application_Model_DbTable_HOs::HO_UPLOADED);
			$this->_helper->redirector->gotoRoute(array("controller"=> "ho", "action"=> "index"));
			return;				
		}							
		
		$handOffPath = $ho->HandOffFolderLocation . "/ho/";
		if(!file_exists($handOffPath)){
			mkdir($handOffPath, 0777);
		}

		$upload->saveUploadFile($handOffPath);		
		$ho->HandOffTotalNumberOfUploadFiles = $this->_helper->FileCountInDirectory($handOffPath);
		$ho->HandOffUploadDate = date("Y-m-d : H:i:s", time());
		$hoMapper->save($ho);		
	}

	public function hbAction()
    {
		$upload = new Application_Model_Mapper_Upload();
		$hbMapper = new Application_Model_Mapper_HB();
		$hb = new Application_Model_HB();
		$hoMapper = new Application_Model_Mapper_HO();		
		$ho = new Application_Model_HO();
		$this->view->activeHb = true;		
		$hbMapper->find($this->_getParam("handbackid", 0), $hb);
		$hoMapper->find($hb->HandOffID, $ho);
				
		if($hb->HandBackID == null){
			throw new Exception("please input correct handbackid to upload file for hb record");
		}		
		
		$this->view->showSetHoStatusButton = ($hb->HandBackTotalNumberOfUploadFiles > 0);
				
		if(!$hbMapper->isValidHOStatusForSaveUpload($ho->HandOffStatus)){
			return $this->_helper->redirector->gotoRoute(array("controller"=> "hb", "action"=> "index", "handoffid"=> (int)$this->_getParam("handoffid", 0)));
		}
			
		if (!$this->getRequest()->isPost()) {
			return;
		}
		
		if (0 <> strlen($this->_getParam("backbutton", ""))){
			return $this->_helper->redirector->gotoRoute(array("controller"=> "hb", "action"=> "index"));
		}
		
		if ((0 <> strlen($this->_getParam("setHoStatusButton", ""))) && (!$this->view->showSetHoStatusButton)){
			throw new Exception("please upload file for this hb");
		}
		
		if (0 <> strlen($this->_getParam("setHoStatusButton", ""))){								
			$this->updateHoStatus($hb->HandOffID,  Application_Model_DbTable_HOs::HB_COMPLETED);
			return $this->_helper->redirector->gotoRoute(array("controller"=> "hb", "action"=> "index"));
		}							
		if (0 <> strlen($this->_getParam("seefilesbutton", ""))){
			return $this->_helper->redirector->gotoRoute(array("controller"=> "fm", "action"=> "index", "handoffid" => $hb->HandOffID, "download" => "hb"));
		}
				
		
		$upload->saveUploadFile($hb->HandBackFolderLocation);
		$hb->HandBackTotalNumberOfUploadFiles = $this->_helper->FileCountInDirectory($hb->HandBackFolderLocation);
		$hb->HandBackUploadDate = date("Y-m-d : H:i:s", time());
		$hbMapper->save($hb);
	}
	
	private function updateHoStatus($handOffId, $handOffStatus){
		$hoMapper = new Application_Model_Mapper_HO();
		$ho = new Application_Model_HO();
		$hoMapper->find($handOffId, $ho);
		$ho->HandOffStatus = $handOffStatus;
		$hoMapper->save($ho);
		
		$hoMapper->sendEmailNotifyHo($handOffId, $handOffStatus, false);
	}
}