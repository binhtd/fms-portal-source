<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initSwfuploadSession(){
		if (isset($_POST['PHPSESSID']) && strpos($_SERVER['REQUEST_URI'], "/upload")!==false) {
			session_id($_POST['PHPSESSID']);
		}
	}
	
	protected function _initSessionDataHandler(){
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		$db = Zend_Db::factory($config->resources->db->adapter, array(
			'host'        => $config->resources->db->params->host,
			'username'    => $config->resources->db->params->username,
			'password'    => $config->resources->db->params->password,
			'dbname'    => $config->resources->db->params->dbname
		));
				
		Zend_Db_Table_Abstract::setDefaultAdapter($db);
		$config = array(
			'name'           => 'sessions',
			'primary'        => 'id',
			'modifiedColumn' => 'modified',
			'dataColumn'     => 'data',
			'lifetimeColumn' => 'lifetime'
		);
		
		Zend_Session::setSaveHandler(new Zend_Session_SaveHandler_DbTable($config));
		Zend_Session::start();
	}
	
	protected function _initMicrosoftMachineTranlation(){											
		$languages = array('en'	=>	'English', 'ar'	=>	'Arabic' , 'cs'	=>	'Czech', 'da'	=>	'Danish', 'de'	=>	'German'	,
						'et'	=>	'Estonian', 'fi'	=>	'Finnish',  'fr'	=>	'French', 'nl'	=>	'Dutch',
						'el'	=>	'Greek', 'he'	=>	'Hebrew', 'ht'	=>	'Haitian Creole'	, 'hu'	=>	'Hungarian'	, 'id'	=>	'Indonesian', 
						'it'	=>	'Italian', 'ja'	=>	'Japanese', 'ko'	=>	'Korean'	, 'lt'	=>	'Lithuanian'	,
						'lv'	=>	'Latvian', 'no'	=>	'Norwegian'	, 'pl'	=>	'Polish'	,
						'pt'	=>	'Portuguese'	, 'ro'	=>	'Romanian', 'es'	=>	'Spanish'	,
						'ru'	=>	'Russian'	, 'sk'	=>	'Slovak', 'sl'	=>	'Slovene', 'sv'	=>	'Swedish'	, 'th'	=>	'Thai'	,
						'tr'	=>	'Turkish'	, 'uk'	=>	'Ukrainian', 'vi'	=>	'Vietnamese', 'zh-CHS'	=>	'Simplified Chinese', 'zh-CHT'	=>	'Traditional Chinese'	
						);	
						
		$registry = Zend_Registry::getInstance();
		$registry['languages'] = $languages;		
	}
}

