<?php

class Admin_PhotoController extends Zend_Controller_Action
{
    public function init()
    {

    }

    /**
     *
     */
    public function indexAction()
    {
        $form = new Form_AddPhoto();
        $this->view->form = $form;
    }

    /**
     *
     */
	public function addPhotosAction()
	{
        //This can sometimes trip up the json response(if you don't disable that is)
        $this->_helper->layout->disableLayout();

        //$data = $_POST['data'];
        $response = array();

        //iterate through post data and set response array values
        while (list($key,$value) = each($_POST)){
            $response[$key] = $value;
        }

        //Add Photo model to memory and pass $_POST values to addPhoto method
        $photo_model = new Admin_Model_Photo();
        $test = $photo_model->addPhoto($response['image_name'], $response['album_name'], $response['category_name'], $response['is_thumb']);


         $this->lastID = $test;
        //header('Content-type: application/json');
        $json = json_encode($test);
        print $json;
    }

	/**
     *
     */
	public function updatePhotosAction()
	{
        //This can sometimes trip up the json response(if you don't disable that is)
        $this->_helper->layout->disableLayout();

        //Init $_POST array
        $data = array();

        //iterate through post data and set response array values
        while (list($key,$value) = each($_POST)){
            $data[$key] = $value;
        }

        //Move photo files accordingly
        $data['path'] = '';
        $dir_is_empty = '';

        $baseDir = APPLICATION_PATH . '/../public/images/gallery/categories/';

        //Do a quick n dirty general check to see if the updated photo already exists in that location
        if(file_exists($baseDir .$data['category_name'] . '/' .  $data['album_name'] . '/' . $data['file_name'])){
            //Set the path so the db value can be updated
            $data['path'] = $baseDir .$data['category_name'] . '/' .  $data['album_name'] . '/' . $data['file_name'];

            //Call the model and save $data to the db.
            $photo_model = new Admin_Model_Photo();
            $photo_model->updatePhoto($data);

            //Succes val
            print json_encode('GREAT SUCCESS');
        }else{
            //start the cascading directory creation
            if(!file_exists($baseDir . $data['category_name'])){
                mkdir($baseDir . $data['category_name']);
            } elseif (!file_exists($baseDir . $data['category_name'] . '/' . $data['album_name'])){
                    mkdir($baseDir . $data['category_name'] . '/' . $data['album_name']);
            }

            //Now that the needed dirs are there - copy the file to be updated
            if( copy($baseDir .$data['category_name_existing'] . '/' . $data['album_name_existing'] .'/' .$data['file_name_existing'],
                $baseDir . $data['category_name'] . '/' . $data['album_name'] . '/' . $data['file_name'])){

                //now that the file has been copied - delete the old one.
                if(unlink($baseDir .$data['category_name_existing'] . '/' . $data['album_name_existing'] .'/' .$data['file_name_existing'])){

                    //look at the _album_ directory from which we deleted the file. Set a flag based on its contents
                    if ( ($files = @scandir($baseDir .$data['category_name_existing'] . '/' . $data['album_name_existing'])) && (count($files) > 2) )
                    {
                        $dir_is_empty = FALSE;
                    } else {
                        $dir_is_empty = TRUE;
                    }

                    //if there's nothing left in the folder - delete it.
                    if($dir_is_empty){
                        //Delete Album dir
                        if(rmdir($baseDir .$data['category_name_existing'] . '/' . $data['album_name_existing'])){
                            //now Check Category dir and do the same thing
                            if ( ($files = @scandir($baseDir .$data['category_name_existing'])) && (count($files) > 2) )
                            {
                                $dir_is_empty = FALSE;
                            } else {
                                $dir_is_empty = TRUE;
                            }

                            if($dir_is_empty){
                                //delete category dir
                                rmdir($baseDir .$data['category_name_existing']);
                            }
                        }
                    }
                    //TODO: the whole process of setting the flag might be erroneous, look at another way of doing it.
                }

                //set the new path
                $data['path'] = $baseDir .$data['category_name'] . '/' .  $data['album_name'] . '/' . $data['file_name'];

                //Call the model and save $data to the db.
                $photo_model = new Admin_Model_Photo();
                $photo_model->updatePhoto($data);

                //test dump
                print json_encode('Successfully moved file(s)');
            } else {
                //return a 'you failed' string
                print json_encode('Could not update file - please contact your awesome husband to fix it :D ');
            }
        }
	}
	
	public function editPhotosAction()
	{
        $photo_model = new Admin_Model_Photo();
        $select = $photo_model->select();
        $select->order('album_name');
        $photos = $photo_model->fetchAll($select);
        $this->view->photos = $photos;
	}
	
	public function deletePhotosAction()
	{
        //This can sometimes trip up the json response(if you don't disable that is)
        $this->_helper->layout->disableLayout();

        $photo_model = new Admin_Model_Photo();
        $path = $photo_model->deletePhoto($_POST['id']);

        //If there's a return path - delete the image file as well.
        $baseDir = APPLICATION_PATH . '/../public/images/gallery/categories/';
        $dir_is_empty = '';

        if($path){
            if(unlink($path['path'])){
                //look at the directory from which we deleted the file. Set a flag based on its contents
                if ( ($files = @scandir($baseDir .$_POST['category_name'] . '/' . $_POST['album_name_existing'])) && (count($files) > 2) )
                {
                    $dir_is_empty = FALSE;
                } else {
                    $dir_is_empty = TRUE;
                }

                //if there's nothing left in the folder - delete it.
                if($dir_is_empty){
                    rmdir($baseDir .$_POST['category_name'] . '/' . $_POST['album_name']);

                    //now check Category dir and do the same thing
                    if ( ($files = @scandir($baseDir .$_POST['category_name'])) && (count($files) > 2) )
                    {
                        $dir_is_empty = FALSE;
                    } else {
                        $dir_is_empty = TRUE;
                    }

                    if($dir_is_empty){
                        //delete category dir
                        rmdir($baseDir .$_POST['category_name']);
                    }
                }
            }
        }
	}

    /**
     *
     */
    public function uploadAction()
    {
        //This can sometimes trip up the json response(if you don't disable that is)
        $this->_helper->layout->disableLayout();

        /**
         * @TODO: select/create directories based on supplied album/category names
         */
        $baseDir = APPLICATION_PATH . '/../public/images/gallery/categories/' . $_POST['category-name'] . '/';

        //Check to see if DIR exists, if not: create it and update $baseDir
        if(file_exists($baseDir . $_POST['album-name'])){
            $baseDir = $baseDir .= $_POST['album-name'];
        } else {
            mkdir($baseDir .$_POST['album-name']);
            $baseDir = $baseDir .= $_POST['album-name'];
        }

        $file = $baseDir .'/' . $_FILES['file_0']['name'];

        if (move_uploaded_file($_FILES['file_0']['tmp_name'], $file)) {
            $photo_model = new Admin_Model_Photo();
            $photo_model->insertPhotoPath($file, $_POST['id'], $_FILES['file_0']['name']);
        } else {
           //throw new Zend_Exception((string)$_FILES);
        }

        print json_encode($_FILES['file_0']['name']);
    }
}

