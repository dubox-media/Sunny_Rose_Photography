<?php
class Admin_Model_Photo extends Zend_Db_Table_Abstract
{
	/**
	 * 
	 * @var $_name
	 */
	protected $_name = 'photos';
    public $id = '';

    /**
     * @param $imgTitle
     * @param $albumName
     * @param $catName
     * @param $isThumb
     * @return string
     */
    public function addPhoto($imgTitle, $albumName, $catName, $isThumb)
    {
        $row = $this->createRow();
        $row->image_title = $imgTitle;
        $row->album_name = $albumName;
        $row->category = $catName;
        $row->isThumbnail = $isThumb;
        $row->save();

        $this->id = $row->id;
        return $row->id;
    }

    public function insertPhotoPath($path, $id, $file_name)
    {
        $row = $this->find($id)->current();

        if($row){
            $row->path = $path;
            $row->file_name = $file_name;
            $row->save();
        } else {
            throw new Zend_Exception();
        }
    }

    public function updatePhoto($data)
    {
        $row = $this->find($data['id'])->current();
        if($row){
            if($data['image_title'] !== $data['image_title_existing']){
                $row->image_title = $data['image_title'];
            }

            if($data['album_name'] !== $data['album_name_existing']){
                $row->album_name = $data['album_name'];
            }

            if($data['category_name'] !== $data['category_name_existing']){

                $row->category = $data['category_name'];
            }

            if($data['path']){
                $row->path = $data['path'];
            }

            $row->isThumbnail = $data['is_thumb'];

            $row->save();
        }
    }

    public function deletePhoto($id)
    {
        $response = array();

        $row = $this->find($id)->current();
        if($row){

            $response['path'] = $row->path;
            $row->delete();
            return $response;
        } else {
            throw new Zend_Exception('Delete Failed: could not find page');
            return false;
        }
    }
}