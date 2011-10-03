<?php

class Zend_Controller_Action_Helper_Acl extends Zend_Controller_Action_Helper_Abstract
{
	public $acl;
	
	private function setRoles()
	{
		$this->acl->addRole(new Zend_Acl_Role('anonymous'));
		$this->acl->addRole(new Zend_Acl_Role('client', 'anonymous'));
		$this->acl->addRole(new Zend_Acl_Role('jonckerspm'), 'client');
		$this->acl->addRole(new Zend_Acl_Role('jonckersadmin'), 'jonckerspm');
	}

	private function setResources()
	{
		$this->acl->add(new Zend_Acl_Resource('index'));
		$this->acl->add(new Zend_Acl_Resource('user'));
		$this->acl->add(new Zend_Acl_Resource('email'));
		$this->acl->add(new Zend_Acl_Resource('language'));
		$this->acl->add(new Zend_Acl_Resource('ho'));		
		$this->acl->add(new Zend_Acl_Resource('myaccount'));
		$this->acl->add(new Zend_Acl_Resource('hb'));
		$this->acl->add(new Zend_Acl_Resource('fm'));
		$this->acl->add(new Zend_Acl_Resource('upload'));
		$this->acl->add(new Zend_Acl_Resource('activity'));
		$this->acl->add(new Zend_Acl_Resource('pagetranslationtracking'));
		
	}

	private function setPrivilages()
	{
		$this->acl->allow('anonymous', 'index');
		$this->acl->allow('client', 'index');
		$this->acl->allow('client', 'ho', array('index', 'viewdetail', 'add', 'edit', 'delete'));		
		$this->acl->allow('client', 'fm');
		$this->acl->allow('client', 'upload');
		$this->acl->allow('client', 'myaccount');
		$this->acl->allow('jonckerspm');
		$this->acl->allow('jonckersadmin', 'ho', 'add');
		$this->acl->allow('jonckersadmin', 'activity');
		$this->acl->allow('jonckersadmin', 'pagetranslationtracking');
		$this->acl->deny('jonckerspm','ho','add');
		$this->acl->deny('jonckerspm','myaccount');		
		$this->acl->deny('jonckersadmin','myaccount');
	}	
	
	private function setAcl()
	{
		Zend_Registry::set('acl', $this->acl);
	}
	
	public function direct(){		
		$this->acl = new Zend_Acl();
		
		$this->setRoles();
		$this->setResources();
		$this->setPrivilages();
		$this->setAcl();
	}
	
}