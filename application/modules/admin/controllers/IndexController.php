<?php
class Admin_IndexController extends Zend_Controller_Action
{

	/**
	 * (non-PHPdoc)
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		/* Initialize action controller here */
	}

	/**
	 * 
	 */
	public function indexAction()
	{
	    $auth = Zend_Auth::getInstance();
	    
	    if($auth->hasIdentity()){
	    	$this->view->identity = $auth->getIdentity();
	    }
	}
	
	/**
	 * 
	 */
	public function loginAction(){
		//Load user creation form - remove unecessary elements
		$user_form = new Form_User();
		$user_form->setAction('/admin/index/login');
		$user_form->removeElement('role');
		
		if($this->_request->isPost() && $user_form->isValid($_POST)){
			//get values
			$data = $user_form->getValues();

			//set up auth adapter
			$db = Zend_Db_Table::getDefaultAdapter();
			$authAdapter = new Zend_Auth_Adapter_DbTable($db, 'users', 'name', 'password');
			$authAdapter->setIdentity($data['username']);
			$authAdapter->setCredential($data['password']);
			
			//authenticate
			$result = $authAdapter->authenticate();
			if($result->isValid()){
				$auth = Zend_Auth::getInstance();
				$storage = $auth->getStorage();
				$storage->write($authAdapter->getResultRowObject(
						array('name', 'role')));

                $user = $auth->getStorage()->read();
                //If the requesting url in the admin/login section
                if($user->role === 'Administrator'){
                    return $this->_redirect('/admin');
                } else {
                    return $this->_redirect(Zend_Controller_Front::getInstance()->getBaseUrl());
                }
			} else {
				$this->view->loginError = "Sorry, your username or password was incorrect";
			}
		
		}
		$this->view->form = $user_form;
	}
	
	public function logoutAction(){
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
		$this->_forward('index');
	}


}