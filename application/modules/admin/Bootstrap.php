<?php
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap 
{
	protected function _initResources()
	{
		$acl = Zend_Registry::get('acl');
		// generic "fallback" resource name for this module
		$acl->add(new Zend_Acl_Resource(strtolower($this->getModuleName()) . ':'));
		// members is usually a secure area. Any allowances need to be made explicitly
		$acl->deny('guest','admin:');
		
		// Allow public access to the login page
		$acl->add(new Zend_Acl_Resource('admin:index'));
		$acl->allow('guest', 'admin:index');
		
		// and members are allowed in to ... /members
		$acl->allow('Administrator','admin:');

	}
}