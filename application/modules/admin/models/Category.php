<?php

/**
 * Created by JetBrains PhpStorm.
 * User: kstowell
 * Date: 4/3/12
 * Time: 3:05 PM
 * To change this template use File | Settings | File Templates.
 */
class Admin_Model_Category extends Zend_Db_Table_Abstract
{

    protected $_name = 'categories';

    /**
     * @param $data
     */
    public function saveCategories($data)
    {
        $repsonse = array();

        if($this->select()->from('categories')->where('category = ?', $data['category-name'])){
           $response['category_exists'] = true;
        } else {
            $row = $this->createRow();

            if($row){
                $row->category = $data['category-name'];
            }
            $response['id'] = $row->id;
        }

        if(empty($repsonse)){
            $repsonse['response'] = 'Nothing worked';
        }
        print json_encode($repsonse);
    }

}