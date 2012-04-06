<?php
class Form_User extends Zend_Form
{
    public function init()
    {   
        $this->setMethod('post');
        
        //User Name element
        $username = $this->createElement('text', 'username');
        $username->setLabel('User Name:')
        		 ->setRequired(TRUE)
        		 ->addFilter('StripTags')
        		 ->addErrorMessage('User Name is required!');
        $this->addElement($username);
        
        //Password Element
        $password = $this->createElement('password', 'password');
        $password->setLabel('Password:')
        		 ->setRequired(TRUE)
        		 ->addFilter('StripTags')
        		 ->addErrorMessage('Password is required!');
        $this->addElement($password);
        
        //Role
        $role = $this->createElement('select', 'role');
        $role->setLabel('Select a role: ')
        	 ->addMultiOption('User', 'user')
        	 ->addMultiOption('Administrator', 'admin');
        $this->addElement($role);
        
        //Submit
        $submit = $this->addElement('submit', 'submit', array('label' => 'Log In'));
    }
}