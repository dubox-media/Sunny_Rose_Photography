<?php
class SR_Plugins_Acl extends Zend_Controller_Plugin_Abstract {
	/**
	 * The Zend_Acl we're using.
	 * @var Zend_Acl
	 */
	private $_acl = null;
	
	/**
	 * Constructor.
	 * @param Zend_Acl $acl
	 */
	
	public function __construct(Zend_Acl $acl)
	{
		$this->_acl = $acl;
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
        $role = '';
        $auth = Zend_Auth::getInstance();
		//$role = (Zend_Auth::getInstance()->hasIdentity()) ? 'Administrator' : 'guest';
        if($auth->hasIdentity())
        {
            $user = $auth->getIdentity();
            $role = $user->role;
        } else {
            $role = 'guest';
        }

		$resource = strtolower($request->getModuleName() . ':' . $request->getControllerName());
		
		if (!$this->_acl->has($resource))
			{
				$resource = strtolower($request->getModuleName()) . ':';
				if (!$this->_acl->has($resource))
				{
					$this->_acl->addResource($resource);
				}
			}
				
			$action = $request->getActionName();
	
			if (!$this->_acl->isAllowed($role, $resource, $action))
			{
				$request->setModuleName('admin')
					->setControllerName('index')
					->setActionName('login');
				$fc = Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(401);
			}
	}
}