<?php

class Application_Model_Mapper_Upload{

	const UPLOAD_NAME = "Filedata"; 
	const MAX_FILE_SIZE_IN_BYTES = 2147483647;// 2GB in bytes 
    const EXTENSION_WHITELIST = "return array(\"rar\", \"zip\");";// Allowed file extension
    const VALID_CHARS_REGEX = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';             
	const MAX_FILENAME_LENGTH = 260; 
	
	public function __construct(){
		return eval(self::EXTENSION_WHITELIST);
	}
	
	public function saveUploadFile($savePath){		
		/*
		if (!($this->isValidFileSize() && $this->isValidFileSizeSWFAllowUpload() && $this->isValidFileName() && $this->isValidFileExtension())){
			return;
		}
		*/
		if(!isset($_FILES['Filedata']["tmp_name"])){
			return;
		}
			
		if (!@move_uploaded_file($_FILES['Filedata']["tmp_name"], $savePath. $_FILES ['Filedata'] ['name'])){ 
			throw new Exception("File could not be saved."); 
		} 
	}
	
	private function isValidFileSize(){
		$POST_MAX_SIZE = ini_get('post_max_size'); 
		$unit = strtoupper(substr($POST_MAX_SIZE, -1)); 
		$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1))); 
		
		if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) { 
			header("HTTP/1.1 500 Internal Server Error");
			return false;	
		}
		
		return true;
	}

	private function isValidFileSizeSWFAllowUpload(){
		$file_size = @filesize($_FILES[self::UPLOAD_NAME]["tmp_name"]); 
		var_dump($file_size);
		var_dump(self::MAX_FILE_SIZE_IN_BYTES); die();
		if (!$file_size || $file_size > self::MAX_FILE_SIZE_IN_BYTES) { 
			throw new Exception("File exceeds the maximum allowed size"); 
		} 
     
		if ($file_size <= 0) { 
			throw new Exception("File size outside allowed lower bound");        
		} 
		
		return true;
	}
     
	private function isValidFileName(){
		// Validate file name (for our purposes we'll just remove invalid characters) 
		$file_name = preg_replace('/[^'. self::VALID_CHARS_REGEX.']|\.+$/i', "", basename($_FILES[self::UPLOAD_NAME]['name'])); 
		if (strlen($file_name) == 0 || strlen($file_name) > self::MAX_FILENAME_LENGTH) { 
			throw new Exception("Invalid file name"); 
		} 

		return true;
	}
	
	private function isValidFileExtension(){
		// Validate file extension 
		$path_info = pathinfo($_FILES[self::UPLOAD_NAME]['name']); 
		$file_extension = $path_info["extension"]; 

		foreach (self::EXTENSION_WHITELIST as $extension) { 
			if (strcasecmp($file_extension, $extension) == 0) { 
				throw new Exception("Invalid file extension");
			} 
		}
		
		return true;	
	}
} 