<?php

class Admin_UserController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function createAction()
    {
        $user_form = new Form_User();
        if($this->_request->isPost()){
            if($user_form->isValid($_POST)){
            $userModel = new Admin_Model_User();
        	$userModel->createUser(
        		$user_form->getValue('username'),
        		$user_form->getValue('password'),
        	    $user_form->getValue('role')
        	);
        	return $this->_forward('success');
            }    
        }
        $user_form->setAction('/admin/user/create');
        $this->view->form = $user_form;
    }
    
    public function successAction()
    {
        
    }
}



