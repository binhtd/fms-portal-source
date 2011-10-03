<?php

class Application_Model_Mapper_FM{	
	public function unlinkRecursive($dir, $deleteRootToo) { 
		if(!$dh = @opendir($dir)) { 
			return; 
		} 
		
		while (false !== ($obj = readdir($dh))){ 
			if($obj == '.' || $obj == '..') { 
				continue; 
			} 

			if (!@unlink($dir . '/' . $obj)) 
			{ 
				$this->unlinkRecursive($dir.'/'.$obj, true); 
			} 
		} 
		closedir($dh); 
		
		if ($deleteRootToo){ 
			@rmdir($dir); 
		} 
		
		return; 
	} 
	
	public function getDirectoryPath($handoffid, $download){	
		$hoMapper = new Application_Model_Mapper_HO();
	    $auth = Zend_Auth::getInstance();
		$ho = new Application_Model_HO();
		$hoMapper->find($handoffid, $ho);
				
		if($ho->HandOffID == null){
			throw new Exception("please input correct handoffid data to get directory path");
		}
		
		if ($auth->getIdentity()->UserIsClient == 'Y' && $auth->getIdentity()->UserID <> $ho->UserID){
			throw new Exception("you do not have a permission to get directory path");
		}
				
		switch($download){
			case "ho": 
					if(!file_exists($ho->HandOffFolderLocation . '/ho/')){
						mkdir($ho->HandOffFolderLocation . '/ho/', 0777);
					}

					return realpath($ho->HandOffFolderLocation ) . '/ho/';
			case "hb":
					$hbMapper = new Application_Model_Mapper_HB();
					$result = $hbMapper->getAvailabeHandbackForHandOffID($handoffid);
					return realpath ($result[0]->HandBackFolderLocation) . "/";

			default:
				throw new Exception("please input correct download data type");
				return "";
		}
	}
	
	public function isAllowDownload($handoffid, $download){		
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		$auth = Zend_Auth::getInstance();
		$hoMapper = new Application_Model_Mapper_HO();	    
		$hbMapper = new Application_Model_Mapper_HB();
		$ho = new Application_Model_HO();
		$hoMapper->find($handoffid, $ho);
		
		if($ho->HandOffID == null){
			throw new Exception("please input correct handoffid data");
		}
		
		switch($download){
			case "ho": 					
			case "hb":
					return ($auth->getIdentity()->UserIsJonckersPM == 'Y') ||($auth->getIdentity()->UserID == $ho->UserID) || 
					($auth->getIdentity()->UserLoginName == $config->fmsadmin->username);
			default:
				throw new Exception("please input correct download data type");
				return "";
		}
	}
	
	public function isAllowDelete($handoffid, $download){
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		$auth = Zend_Auth::getInstance();
		$hoMapper = new Application_Model_Mapper_HO();	    
		$hbMapper = new Application_Model_Mapper_HB();
		$ho = new Application_Model_HO();
		$hoMapper->find($handoffid, $ho);
		
		if($ho->HandOffID == null){
			throw new Exception("please input correct handoffid data");
		}
		
		switch($download){
			case "ho": 					
					return $hoMapper->isValidHOStatusForSaveUpload($ho->HandOffStatus) && (($auth->getIdentity()->UserID == $ho->UserID) || 
					($auth->getIdentity()->UserLoginName == $config->fmsadmin->username));
			case "hb":					
					return $hbMapper->isValidHOStatusForSaveUpload($ho->HandOffStatus) && (($auth->getIdentity()->UserIsJonckersPM == 'Y') ||
					($auth->getIdentity()->UserLoginName == $config->fmsadmin->username));
			default:
				throw new Exception("please input correct download data type");
				return "";
		}
	}
	
	public function updateFileCount($handoffid, $download){
		$auth = Zend_Auth::getInstance();
		$hoMapper = new Application_Model_Mapper_HO();	    
		$ho = new Application_Model_HO();
		$hbMapper = new Application_Model_Mapper_HB();
		$hb = new Application_Model_HB();		
		$hoMapper->find($handoffid, $ho);
		
		if($ho->HandOffID == null){
			throw new Exception("please input correct handoffid data");
		}
		
		switch($download){
			case "ho": 					
					$ho->HandOffTotalNumberOfUploadFiles = $this->fileCountInDirectory($ho->HandOffFolderLocation . "/ho" );					
					$hoMapper->save($ho);
					return $ho->HandOffTotalNumberOfUploadFiles;
			case "hb":					
					$result = $hbMapper->getAvailabeHandbackForHandOffID($handoffid);
					$result[0]->HandBackTotalNumberOfUploadFiles = $this->fileCountInDirectory($result[0]->HandBackFolderLocation . "/hb");
					$hbMapper->save($result[0]);				
					return 	$result[0]->HandBackTotalNumberOfUploadFiles;
			default:
				throw new Exception("please input correct download data type");
				return "";
		}
	}
	
	public function getListFileInDirectory($directoryPath){
		$listFileNames = array();
		$handle = opendir($directoryPath);
		
		while ($entry = readdir($handle)) 
		{ 
			if (($entry == "..") || ($entry == ".")) continue;
			$listFileNames[] = $entry;		
		}
		
		return $listFileNames;
	}
	
	private function fileCountInDirectory($directoryPath){		
		$fileCount = 0; 
		$handle = opendir($directoryPath);
						
		while ($entry = readdir($handle)) 
		{ 
			if (($entry == "..") || ($entry == ".")) continue;
			$fileCount++; 
		} 

		return $fileCount;		
	}	
} 