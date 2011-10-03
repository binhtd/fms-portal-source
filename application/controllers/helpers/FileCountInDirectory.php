<?php

class Zend_Controller_Action_Helper_FileCountInDirectory extends Zend_Controller_Action_Helper_Abstract
{
	public function direct($directoryPath){		
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