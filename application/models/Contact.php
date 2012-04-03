<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kstowell
 * Date: 4/1/12
 * Time: 12:34 PM
 * To change this template use File | Settings | File Templates.
 */
class Model_Contact extends Zend_Db_Table_Abstract
{
    //Table name
    protected $_name = 'contact_history';

    public function saveContactRequest($data)
    {
        $row = $this->createRow();

        if($row){
            $row->name = $data['name'];
            $row->email = $data['email'];
            $row->subject = $data['subject'];
            $row->message = $data['message'];
            $row->date = Zend_Date::now()->toString('yyyyMMddHHmmss');
            $row->save();
        }

        $id = $row->id;
        return $id;
    }
}