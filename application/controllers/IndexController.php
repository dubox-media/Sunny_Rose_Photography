<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->pageTitle = 'home';
    }

    public function indexAction()
    {
        
    }

    public function logoutAction()
    {

        //Clear identity
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $this->_redirect('/index');

    }


}

