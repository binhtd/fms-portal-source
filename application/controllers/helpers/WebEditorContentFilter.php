<?php

class Zend_Controller_Action_Helper_WebEditorContentFilter extends Zend_Controller_Action_Helper_Abstract
{
	public function direct($content){		
		return strlen($content) >= 4 && substr($content, strlen($content) - 4, 4)=="<br>"? substr($content, 0, strlen($content) - 4): $content;
	}
	
}