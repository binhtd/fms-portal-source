<?php

class Application_Model_Mapper_HB{
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
            $this->setDbTable('Application_Model_DbTable_HBs');
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
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->order("HandBackID desc")
														->limit($itemsPerPage, ($page-1) * $itemsPerPage));
		
		return $this->CreateResultSetEntity($resultSet);
	}
	
	public function find($handBackID, Application_Model_HB $hb){
		$result = $this->getDbTable()->find($handBackID); 
		if (0 == count($result)){
			return;
		}		
		
		$row = $result->current();
        $hb->setHandBackID($row->HandBackID)
		   ->setHandOffID($row->HandOffID)
		   ->setHandBackTotalNumberOfUploadFiles($row->HandBackTotalNumberOfUploadFiles)
		   ->setHandBackUploadDate($row->HandBackUploadDate)
		   ->setHandBackFolderLocation($row->HandBackFolderLocation)	
		   ->setHandBackComments($row->HandBackComments);
	}
	
	public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        
		return $this->CreateResultSetEntity($resultSet);
    }
	
	public function save(Application_Model_HB $hb){			
		$data = array(
            "HandBackID" => $hb->HandBackID, "HandOffID" => $hb->HandOffID,
		    "HandBackTotalNumberOfUploadFiles" => $hb->HandBackTotalNumberOfUploadFiles, "HandBackUploadDate" => $hb->HandBackUploadDate,
		    "HandBackFolderLocation" => $hb->HandBackFolderLocation, "HandBackComments" => $hb->HandBackComments
        );

        if (null === ($handBackID = $hb->getHandBackID())) {
            unset($data['HandBackID']);
			
			if( 0 == count($this->getAvailabeHandbackForHandOffID($data["HandOffID"]))){
				return $this->getDbTable()->insert($data);	
			}
        } 
		
		return $this->getDbTable()->update($data, array('HandBackID = ?' => $handBackID));
	}	
	
	public function deleteHB($handBackID){

		$this->getDbTable()->delete("HandBackID = " . (int)$handBackID);
	}

	public function getAvailabeHandbackForHandOffID($handOffID){
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where(" HandOffID = ". $handOffID));
		
		return $this->CreateResultSetEntity($resultSet);
	}
	
	public function getHandBackPath($handOffFolderLocation){
		$handbackPath = $handOffFolderLocation . '/hb/' ;
		if(!file_exists($handbackPath)){
			mkdir($handbackPath, 0777);
		}				
		
		$handbackPath = $handbackPath . date("Y_m_d_H_i_s", time()) . "/";
		if(!file_exists($handbackPath)){
			mkdir($handbackPath, 0777);
		}	
		
		return $handbackPath;
	}
	
	public function isValidHOStatusForSaveUpload($hoStatus){
		return ((Application_Model_DbTable_HOs::getHOStatusOrder($hoStatus) >= 	Application_Model_DbTable_HOs::getHOStatusOrder(Application_Model_DbTable_HOs::HO_UPLOADED)) && (Application_Model_DbTable_HOs::getHOStatusOrder($hoStatus) < 	Application_Model_DbTable_HOs::getHOStatusOrder(Application_Model_DbTable_HOs::HB_COMPLETED)));
	}
	
	public function isAllowModifyHb($hoStatus){
		$auth = Zend_Auth::getInstance();		
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		
		if (($auth->getIdentity()->UserIsJonckersPM == "Y") && ($auth->getIdentity()->UserLoginName == $config->fmsadmin->username)) {
			return true;
		}
		
		if (($hoStatus == Application_Model_DbTable_HOs::HB_COMPLETED) || ($hoStatus == Application_Model_DbTable_HOs::HO_CLOSED) || ($hoStatus == Application_Model_DbTable_HOs::HO_CANCELLED)){
			return false;
		}
		
		return true;
	}
		
	private function CreateResultSetEntity($resultSet){
		$entries   = array();
		$hoMapper = new Application_Model_Mapper_HO();
		$ho = new Application_Model_HO();
		
		foreach ($resultSet as $row) {
			$entry = new Application_Model_HB();
			$hoMapper->find($row->HandOffID, $ho);
			$entry->setHandBackID($row->HandBackID)
				  ->setHandOffID($row->HandOffID)
				  ->setHandBackTotalNumberOfUploadFiles($row->HandBackTotalNumberOfUploadFiles)
				  ->setHandBackUploadDate($row->HandBackUploadDate)
				  ->setHandBackFolderLocation($row->HandBackFolderLocation)	
				  ->setHandBackComments($row->HandBackComments)
				  ->setAllowModifyHbRecord($this->isAllowModifyHb($ho->HandOffStatus));

			$entries[] = $entry;
		}
		
		return $entries;
	}		
}