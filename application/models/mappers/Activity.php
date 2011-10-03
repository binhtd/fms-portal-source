<?php

class Application_Model_Mapper_Activity{
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
            $this->setDbTable('Application_Model_DbTable_Activities');
        }
        return $this->_dbTable;
    }
		
	public function getPaginator($page, $itemsPerPage, $userid, $startDate, $endDate){	
		$paginator = Zend_Paginator::factory($this->getDbTable()->select()->where( $this->createWhereCondition($userid, $startDate, $endDate)));
		
		$paginator->setCurrentPageNumber($page);
		
		$paginator->setItemCountPerPage($itemsPerPage);

		$paginator->setPageRange(5);
		
		return $paginator; 
	}
	
	public function getPaginatorData($page, $itemsPerPage, $userid, $startDate, $endDate){		
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where( $this->createWhereCondition($userid, $startDate, $endDate))->order("UserActivityDateTime desc")->order("UserName desc")->order(" ActivityID desc")->limit($itemsPerPage, ($page-1) * $itemsPerPage));
				
		return $this->createResultSetEntity($resultSet);
	}
	
	public function save(Application_Model_Activity $activity){			
		$data = array(
            'ActivityID'   => $activity->getActivityID(),
            'UserName' => $activity->getUserName(),
			'UserActivity' => $activity->UserActivity,
            'UserActivityDateTime' => $activity->getUserActivityDateTime(),
			'Module' => $activity->Module
        );
 
        if (null === ($activityID = $activity->getActivityID())) {
            unset($data['ActivityID']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('ActivityID = ?' => $activityID));
        }	
	}
	
	private function createResultSetEntity($resultSet){
		$entries   = array();

		foreach ($resultSet as $row) {
			$entry = new Application_Model_Activity();
			$entry->setActivityID($row->ActivityID)
				  ->setUserName($row->UserName)
				  ->setUserActivity($row->UserActivity)
				  ->setUserActivityDateTime($row->UserActivityDateTime)
				  ->setModule($row->Module);
				  
			$entries[] = $entry;
		}
		
		return $entries;
	}
	
	private function createWhereCondition($userid, $startDate, $endDate){
		$where = " (1 = 1)";
		if (($userid != null) && ($userid != "all")){
			$where .= " and (UserName = '$userid')";
		}
		
		if($startDate != null){
			$where .= " and (UserActivityDateTime >= '$startDate')";
		}
		
		if($endDate != null){
			$where .= " and (UserActivityDateTime <= '$endDate')";
		}
		
		return $where;
	}
	
	public function getDailyReport(){
		$startDate = date("Y-m-d : H:i:s", (time()-(24*60*60)));
		$endDate = date("Y-m-d : H:i:s", time());
		$module = Application_Model_DbTable_Activities::SENDING_EMAIL;
		$resultSet = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where("(UserActivityDateTime >= '$startDate') and (UserActivityDateTime <= '$endDate') and (Module = '$module')")->order("UserActivityDateTime desc")->order("UserName desc"));
		
		return $this->createResultSetEntity($resultSet);	
	}
}