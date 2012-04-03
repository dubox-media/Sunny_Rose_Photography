<?php
class Admin_Model_User extends Zend_Db_Table_Abstract
{	
    /**
     * 
     * @var unknown_type
     */
    protected $_name = 'users';

    /**
     * 
     */
    public function createUser($username, $password, $role)
    {
        $rowUser = $this->createRow();
        if($rowUser){
            $rowUser->name = $username;
            $rowUser->password = $password;
            $rowUser->role = $role;
            $rowUser->save();
            
            return $rowUser;
        } else{
            throw new Zend_Exception('Could not create user!');
        }
    }
}