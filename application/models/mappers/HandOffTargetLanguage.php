<?php

class Application_Model_Mapper_HandOffTargetLanguage{
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
            $this->setDbTable('Application_Model_DbTable_HandOffTargetLanguages');
        }
        return $this->_dbTable;
    }
		
	
	public function getTargetLanguages($handOffID){
		$handofftargetlanguages = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where('HandOffID = '. (int)$handOffID));
		if (0 == count($handofftargetlanguages)){
			return;
		}		
		
		return $this->CreateArrayResult($handofftargetlanguages);
	}
	
	public function getTargetLanguageNames($handOffID){
		$languageMapper = new Application_Model_Mapper_Language();
		$language = new Application_Model_Language();
		$listLanguage = array();
		$handofftargetlanguages = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where('HandOffID = '. (int)$handOffID));
		if (0 == count($handofftargetlanguages)){
			return;
		}
		
		foreach($handofftargetlanguages as $handofftargetlanguage){
			$languageMapper->find($handofftargetlanguage->LanguageID, $language);
			$listLanguage[] = $language->LanguageName;
		}
		return implode(", ", $listLanguage);
	}
	
	public function save($handOffID, $languageID){			
		$data = array(
            'HandOffID'   => $handOffID,
            'LanguageID' => $languageID
        );
 

		$this->getDbTable()->insert($data);
	}
	
	
	public function delete($handOffID){		
		$languages = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where('HandOffID = '. (int)$handOffID));
		
		foreach($languages as $language){
			$language->delete();
		}
	}
		
	private function CreateArrayResult($resultSet){
		$entries   = array();
		
		foreach ($resultSet as $row) {
			$entries[] = $row->LanguageID;
		}
		
		return $entries;
	}
}