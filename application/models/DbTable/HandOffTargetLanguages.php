<?php

class Application_Model_DbTable_HandOffTargetLanguages extends Zend_Db_Table_Abstract
{
    protected $_name = 'handofftargetlanguages';
	protected $_referenceMap = array(
        'ho_handofftargetlanguages' => array(
            'columns'           => array('HandOffID'),
            'refTableClass'     => 'Application_Model_DbTable_HOs',
            'refColumns'        => array('HandOffID'),
            'onDelete'          => self::CASCADE
        ),'ho_languages' => array(
            'columns'           => array('LanguageID'),
            'refTableClass'     => 'Application_Model_DbTable_Languages',
            'refColumns'        => array('LanguageID'),
            'onDelete'          => self::CASCADE
        ));
}	