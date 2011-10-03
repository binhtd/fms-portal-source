<?php

class Application_Model_DbTable_Activities extends Zend_Db_Table_Abstract
{
    protected $_name = 'activities';	
	const SENDING_EMAIL = "Sending Email";
	const EMAIL_TEMPLATES = "Email Template";
	const HANDBACKS = "Handbacks";
    const HANDOFFS = "Handoffs";
	const LANGUAGES = "Languages";
	const USERS = "Users";	
}

