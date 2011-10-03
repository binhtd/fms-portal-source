<?php

class Application_Model_DbTable_HBs extends Zend_Db_Table_Abstract
{
    protected $_name = 'handbacks';

	protected $_referenceMap = array(
        'hb_ho' => array(
            'columns'           => array('HandOffID'),
            'refTableClass'     => 'Application_Model_DbTable_HOs',
            'refColumns'        => array('HandOffID'),
            'onDelete'          => self::CASCADE
        ));
}

