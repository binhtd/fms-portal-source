<?php

class FMController extends Zend_Controller_Action
{ 
    public function indexAction()
    {			
		if (0 <> strlen($this->_getParam("backbutton", ""))){					
			$this->_redirect($this->_getParam("backurl","/ho/index"));
			return;				
		}
	
		if (0 <> strlen($this->_getParam("Downloadall", ""))){			
			$this->downloadAll();
			return;				
		}
	
		$auth = Zend_Auth::getInstance();
		$this->view->backModuleName = $this->_getParam("download", "ho");
		if ($auth->getIdentity()->UserIsClient == "Y"){
			$this->view->backModuleName = "ho";
		}
		
		$fmMapper = new Application_Model_Mapper_FM();
		$files = array();
		$directoryPath = $fmMapper->getDirectoryPath($this->_getParam("handoffid", 0), $this->_getParam("download", "ho"));
		$handle = opendir($directoryPath);
						
		while ($file = readdir($handle)) { 
			if ($file == "." || $file == "..")  continue;
			
			$files[] = array( "fileinurl" => urlencode(stripslashes($file)), "handoffid" => $this->_getParam("handoffid", 0),
							"download" => $this->_getParam("download", "ho"), "filename" => 
						( strlen(stripslashes($file)) > 43) ? substr(stripslashes($file), 0, 40) . '...' : stripslashes($file),
							"filesize" => round(filesize($directoryPath. $file)/1024) . " KB",
							"createdate" => date ("Y:m:d", filemtime($directoryPath .$file)),
							"allowDelete" => $fmMapper->isAllowDelete($this->_getParam("handoffid", 0), $this->_getParam("download", "ho"))
						);
			
		}

		$this->view->files = $files;
		
		closedir($handle); 

    }
	
	public function deleteAction(){
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();
	
		$fmMapper = new Application_Model_Mapper_FM();
		if(!$fmMapper->isAllowDelete($this->_getParam("handoffid", 0), $this->_getParam("download", "ho"))){
			return $this->_helper->redirector->gotoRoute(array("controller"=> "fm", "action"=> "index", "handoffid" => $this->_getParam("handoffid", 0),
							"download" => $this->_getParam("download", "ho")));		
		}
		
		$directoryPath = $fmMapper->getDirectoryPath($this->_getParam("handoffid", 0), $this->_getParam("download", "ho"));
				
		if (!file_exists($directoryPath . urldecode($this->_getParam("filename", "")))){
			throw new Exception("Please select correct file to delete");
		}
				
		unlink($directoryPath . urldecode($this->_getParam("filename", "")));	

		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User Delete File " .  $directoryPath . urldecode($this->_getParam("filename", "")))
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);		  
				
		if ( 0 == $fmMapper->updateFileCount($this->_getParam("handoffid", 0), $this->_getParam("download", "ho"))){
			return $this->_helper->redirector->gotoRoute(array("controller"=> $this->_getParam("download", "ho"), "action"=> "index"));			
		}
		
		$this->_helper->redirector->gotoRoute(array("controller"=> "fm", "action"=> "index", "handoffid" => $this->_getParam("handoffid", 0),
							"download" => $this->_getParam("download", "ho")));
	}
	
	public function downloadAction(){	
		$activityMapper = new Application_Model_Mapper_Activity();
		$activity = new Application_Model_Activity();
		$auth = Zend_Auth::getInstance();
			
		$fmMapper = new Application_Model_Mapper_FM();
		$directoryPath = $fmMapper->getDirectoryPath($this->_getParam("handoffid", 0), $this->_getParam("download", "ho"));
		
		if (!$fmMapper->isAllowDownload($this->_getParam("handoffid", 0), $this->_getParam("download", "ho"))){
			return $this->_helper->redirector->gotoRoute(array("controller"=> "fm", "action"=> "index", "handoffid" => $this->_getParam("handoffid", 0),
							"download" => $this->_getParam("download", "ho")));		
		}
				
		if (!file_exists($directoryPath . $this->_getParam("filename", ""))){
			throw new Exception("Please select correct file to download");
		}
		
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header("Content-Transfer-Encoding: binary");		
		header("Content-Length: " . filesize($directoryPath . $this->_getParam("filename", ""))); 
		header('Content-Disposition: attachment; filename="'.$this->_getParam("filename", "").'"');
		ob_end_flush();
		readfile($directoryPath . $this->_getParam("filename", ""));		
		
		$activity->setUserName($auth->getIdentity()->UserLoginName)
				  ->setUserActivity("User download File " .  $directoryPath . $this->_getParam("filename", ""))
				  ->setUserActivityDateTime(date("Y-m-d : H:i:s", time()));
		$activityMapper->save($activity);
		die();		
	}
	
	private function downloadAll(){
		$zip = new ZipArchive();
		$fileName = sprintf("%s_ALL.zip", date("Y_m_d", time()));
		$fmMapper = new Application_Model_Mapper_FM();
		$directoryPath = $fmMapper->getDirectoryPath($this->_getParam("handoffid", 0), $this->_getParam("download", "ho"));
		$listFileNames = $fmMapper->getListFileInDirectory($directoryPath);
		if ($zip->open($directoryPath.$fileName, ZIPARCHIVE::CREATE )!==TRUE) {
			throw new Exception("cannot open zip file");
		}
		
		foreach($listFileNames as $files){
			$zip->addFile($directoryPath.$files, $files);
		}
		
		$zip->close();

		header("Content-Disposition: attachment; filename=\"" .$fileName."\"");	
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($directoryPath.$fileName));
		header("Accept-Ranges: ".filesize($directoryPath.$fileName));
		ob_end_flush();
		readfile($directoryPath.$fileName);
		unlink($directoryPath.$fileName);
		die();
	}
}

