<?php

class Application_Model_Mapper_Language{
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
            $this->setDbTable('Application_Model_DbTable_Languages');
        }
        return $this->_dbTable;
    }
		
	public function getPaginator($page, $itemsPerPage){	
		$paginator = Zend_Paginator::factory($this->getDbTable()->select());
		
		$paginator->setCurrentPageNumber($page);
		
		$paginator->setItemCountPerPage($itemsPerPage);

		$paginator->setPageRange(5);
		
		return $paginator; 
	}
	
	public function getPaginatorData($page, $itemsPerPage){
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->order("LanguageID desc")->limit($itemsPerPage, ($page-1) * $itemsPerPage));
		
		return $this->CreateResultSetEntity($resultSet);
	}
	
	public function find($languageID, Application_Model_Language $language){
		$result = $this->getDbTable()->find($languageID); 
		if (0 == count($result)){
			return;
		}		
		
		$row = $result->current();
        $language->setLanguageID($row->LanguageID)
                  ->setLanguageName($row->LanguageName)
                  ->setLanguageIsActive($row->LanguageIsActive)
				  ->setLanguageIsShowInSourceList($row->LanguageIsShowInSourceList)
				  ->setLanguageIsShowInTargetList($row->LanguageIsShowInTargetList);
	}
	
	public function getAllActiveLanguage(){		
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where("LanguageIsActive = 'Y'")->order("LanguageName asc"));
    
		return $this->CreateResultSetEntity($resultSet);
	}
	
	public function getSourceLanguageActive(){
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where(" (LanguageIsActive = 'Y') and (LanguageIsShowInSourceList='Y')")->order("LanguageName asc"));
    
		return $this->CreateResultSetEntity($resultSet);
	}
	
	public function getTargetLanguageActive(){
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where("(LanguageIsActive = 'Y') and (LanguageIsShowInTargetList='Y')")->order("LanguageName asc"));
    
		return $this->CreateResultSetEntity($resultSet);
	}
	
	public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        
		return $this->CreateResultSetEntity($resultSet);
    }

	public function save(Application_Model_Language $language){			
		$data = array(
            'LanguageID'   => $language->getLanguageID(),
            'LanguageName' => $language->getLanguageName(),
            'LanguageIsActive' => $language->getLanguageIsActive(),
			'LanguageIsShowInSourceList' => $language->getLanguageIsShowInSourceList(),
			'LanguageIsShowInTargetList' => $language->getLanguageIsShowInTargetList()
        );
 
        if (null === ($languageID = $language->getLanguageID())) {
            unset($data['LanguageID']);
            return $this->getDbTable()->insert($data);
        }
		
        return   $this->getDbTable()->update($data, array('LanguageID = ?' => $languageID));
	}
	
	
	public function deleteLanguage($languageID){		
		$languages = $this->getDbTable()->find((int)$languageID);
		$languages[0]->delete();
	}
	
	public function getLanguageByLanguageName($languageName, $languageId){
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where("(LanguageName ='". $languageName . "') and (LanguageID <> " . $languageId . " ) ")->order("LanguageID desc"));
    
		return $this->CreateResultSetEntity($resultSet);
	}
	
	private function CreateResultSetEntity($resultSet){
		$entries   = array();
		
		foreach ($resultSet as $row) {
			$entry = new Application_Model_Language();
			$entry->setLanguageID($row->LanguageID)
				  ->setLanguageName($row->LanguageName)
				  ->setLanguageIsActive($row->LanguageIsActive)
				  ->setLanguageIsShowInSourceList($row->LanguageIsShowInSourceList)
				  ->setLanguageIsShowInTargetList($row->LanguageIsShowInTargetList);
			$entries[] = $entry;
		}
		
		return $entries;
	}
}