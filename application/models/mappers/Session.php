<?php

class Application_Model_Mapper_Session{
	protected $_dbTable;
 
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_Sessions');
        }
        return $this->_dbTable;
    }
			
	public function fetchAll()
    {
       return $this->createResultSetEntity($this->getDbTable()->fetchAll());
    }

	private function createResultSetEntity($resultSet){
		$entries   = array();
		foreach ($resultSet as $row) {
			$entry = new Application_Model_Session();
			$entry->setId($row->id)
                  ->setModified($row->modified)
                  ->setLifetime($row->lifetime)
				  ->setData($row->data);
				
			$entries[] = $entry;
		}
		
		return $entries;
	}	
}