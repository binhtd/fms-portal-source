<?php

class Application_Model_Mapper_PageTranslationTracking{
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
            $this->setDbTable('Application_Model_DbTable_PageTranslationTrackings');
        }
        return $this->_dbTable;
    }
		
	public function getPaginator($page, $itemsPerPage){	
		$paginator = Zend_Paginator::factory($this->getDbTable()
												  ->select()
												  ->from( "pagetranslationtracking",
															array("PageUrl", "TranslationLanguage", "CountTranslation" => 
															'count("pagetranslationtracking")'))
												  ->group(array("PageUrl", "TranslationLanguage"))
												  ->limit($itemsPerPage, ($page-1) * $itemsPerPage));
		
		$paginator->setCurrentPageNumber($page);
		
		$paginator->setItemCountPerPage($itemsPerPage);

		$paginator->setPageRange(5);
		
		return $paginator; 
	}
	
	public function getPaginatorData($page, $itemsPerPage){
		
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()
														  ->select()
														  ->from( "pagetranslationtracking",
																	array("PageUrl", "TranslationLanguage", "CountTranslation" => 
																	'count("pagetranslationtracking")'))
														  ->group(array("PageUrl", "TranslationLanguage"))
														  ->limit($itemsPerPage, ($page-1) * $itemsPerPage));
		
		return $this->CreateResultSetEntity($resultSet);
	}
	
	public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        
		return $this->CreateResultSetEntity($resultSet);
    }

	public function save(Application_Model_PageTranslationTracking $pageTranslationTracking){			
		$data = array(
            'PageUrl'   => $pageTranslationTracking->getPageUrl(),
            'TranslationtDate' => $pageTranslationTracking->getTranslationtDate(),
            'TranslationLanguage' => $pageTranslationTracking->getTranslationLanguage(),			
        );
 
		return $this->getDbTable()->insert($data);
	}
		
	private function CreateResultSetEntity($resultSet){
		$entries   = array();
		$languages = Zend_Registry::get('languages'); 
		
		foreach ($resultSet as $row) {
			$entry = new Application_Model_PageTranslationTracking();
			
			
			$entry->setPageUrl($row->PageUrl)
				  ->setCountTranslation($row->CountTranslation)
				  ->setTranslationLanguage($languages[$row->TranslationLanguage]);
				  
			$entries[] = $entry;
		}
		
		return $entries;
	}
}