<?php
require_once ('Zend/Controller/Plugin/Abstract.php');

class Application_Plugin_Translate extends Zend_Controller_Plugin_Abstract {

	public function routeShutdown(Zend_Controller_Request_Abstract $request) {
		$front = Zend_Controller_Front::getInstance();   
		$msmtlanguage = new Zend_Session_Namespace('msmtlanguage');
		
		if(!isset($msmtlanguage->language)){
				$msmtlanguage->language = 'en';
				$msmtlanguage->showWarningMessage = true;
		}
		
		if($front->getRequest()->getParam('msmtlanguage', "") !=""){
				$msmtlanguage->language = $front->getRequest()->getParam('msmtlanguage', "");
				$msmtlanguage->showWarningMessage = false;
		}			
	} 	  

    public function postDispatch(Zend_Controller_Request_Abstract $request) {
		$front = Zend_Controller_Front::getInstance();   
		$layout = $front->getPlugin('Zend_Layout_Controller_Plugin_Layout');		
		$msmtlanguage = new Zend_Session_Namespace('msmtlanguage');
		
		$this->insertTranslationTracking($msmtlanguage->language);	
		
    	if( !$front->hasPlugin('Zend_Layout_Controller_Plugin_Layout')) {
    		return;
    	}
    			
		if( $msmtlanguage->language != "en"){			
			$htmlBeforeTranslate = $layout->getResponse()->getBody();									
			$layout->getResponse()->setBody($this->processTranslation($htmlBeforeTranslate ));		
		}  
		
    }
	
	private function insertTranslationTracking($languageCode){
		$front = Zend_Controller_Front::getInstance();   
		$request = $front->getRequest();		        
		$url = $request->getScheme() . '://' . $request->getHttpHost() . "/" . $request->getModuleName() . "/" . $request->getControllerName() . "/" . $request->getActionName();
		
		$pageTranslationTrackingMapper  = new Application_Model_Mapper_PageTranslationTracking();		
		$pageTranslationTracking =  new Application_Model_PageTranslationTracking();		
		$pageTranslationTracking->setPageUrl($url )
													->setTranslationtDate(date("Y-m-d : H:i:s", time()))
													->setTranslationLanguage($languageCode);		
		$pageTranslationTrackingMapper->save($pageTranslationTracking);			
	}
    
	private function processTranslation($value){					
		$msmtlanguage = new Zend_Session_Namespace('msmtlanguage');		
		$str = $this->removeScriptTags($value);
		$sourceTextArray = $this->getArraySourceText($str);				
		$targetTextArray = $this->translationByBing($sourceTextArray, "en", $msmtlanguage->language);
		$scriptText = $this->getScriptTagsText($value);
		$totalItem = count($sourceTextArray); 
			
		if(count($sourceTextArray) != count($targetTextArray)){
			return $value;
		}
		
		for($i=0; $i<$totalItem; $i++){
			if($targetTextArray[$i] !=""){
				$regex = "/(?<=>)(\s*\r*\n*)(" .  preg_quote ($sourceTextArray[$i], "/") . ")(\s*\r*\n*)(?=<)/imsu";
				$str = preg_replace($regex, ' '. $targetTextArray[$i] .' ', $str) ;				
			}
		}
		
		return  str_ireplace("</body>", $scriptText . "</body>" , $str);
	}
	
	private function getArraySourceText($value){		
		$str = $this->removeScriptTags($value);
		$sourceText = array();
		
		$pattern = "/((<.*?>\s*)*|(<\/.*?>\s*)*)(.+?[\s\S]*?)\s*((<.*?>\s*)+|(<\/.*?>\s*)+)/imsu";
		preg_match_all($pattern, $str, $matches, PREG_SET_ORDER);
		$totalItem = 	count($matches);
		
		for( $i=0; $i <$totalItem; $i++){			
			if( (preg_replace("/&nbsp;|&#160;/imsu", "", $matches[$i][4]) !="") && !in_array($matches[$i][4], $sourceText)){											
				$sourceText[]  = $matches[$i][4];			
			}
		}
				
		return $sourceText;
	}
	
	private function removeScriptTags($value){
		$pattern = "/<script.*?>.*?<\/script>/imsu";
		
		return preg_replace($pattern, "", $value);
	}
	
	private function getScriptTagsText($value){
		$pattern = "/<script.*?>.*?<\/script>/imsu";		
		preg_match_all($pattern, $value, $matches, PREG_SET_ORDER);
		
		$str = "";
		foreach($matches as $match){
			$str .= join("",$match);
		}
		
		return $str;
	}
	
	private function translationByBing($value, $sourceLanguageCode, $targetLanguageCode){
		$params = array();
		$client = new SoapClient("http://api.microsofttranslator.com/V2/SOAP.svc", array('encoding'=>'utf-8'));		
		$params['appId'] = "409D1363ECF1D3B9D9A16E5DDB980940EFAAF4C1";
		$params['texts'] = $value;
		$params['from'] = $sourceLanguageCode;
		$params['to'] = $targetLanguageCode;
		$params["options"] = "text/html";		
		
		$result = $client->TranslateArray($params);						
		
		return $this->convertTranslationResultToPhpArray(
			$result->TranslateArrayResult->TranslateArrayResponse);		
		
	}
	
	private function convertTranslationResultToPhpArray($array){	
		$result = array();
		
		foreach($array as $item) {
			$result[] =  $item->TranslatedText;
		}	

		return $result;
	}
}