<?php

class Application_Model_DbTable_HOs extends Zend_Db_Table_Abstract
{
    protected $_name = 'handoffs';
	protected $_dependentTables = array('Application_Model_DbTable_HBs', 'Application_Model_DbTable_HandOffTargetLanguages');
	protected $_referenceMap = array(
        'ho_user' => array(
            'columns'           => array('UserID'),
            'refTableClass'     => 'Application_Model_DbTable_Users',
            'refColumns'        => array('UserID'),
            'onDelete'          => self::CASCADE
        ));
	
	const HO_CREATED = "HO - Created";
	const HO_UPLOADED = "HO - ReadyToloc";
	const HO_RECEIVED = "HO - Received";
    const HB_COMPLETED = "HB - Completed";
	const HO_CLOSED = "HO - Closed";
	const HO_CANCELLED = "HO - Cancelled";
			
	public static function getHOStatusOrder($hoStatus){
		switch($hoStatus){
			case self::	HO_CREATED:
				return 1;
			case self::	HO_CANCELLED:
				return 2;
			case self::	HO_UPLOADED:
				return 3;
			case self::	HO_RECEIVED:
				return 4;
			case self::	HB_COMPLETED:
				return 5;				
			case self::	HO_CLOSED:
				return 6;
			default: 
				return 0;	
		}
	}
}

