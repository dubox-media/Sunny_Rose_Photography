<?php
class Form_AddPhoto extends Zend_Form
{
	public function init()
	{
		$this->setAttrib('enctype', 'multipart/form-data');
		
		//Initialize 
		$addPhoto = $this->createElement('button', 'Add Another Photo');
		$addPhoto->setLabel('Add Another Photo');
		
		//ID placeholder
		$id = $this->createElement('hidden', 'id');
		$id->setDecorators(array('ViewHelper'));
		//$this->addElement($id);
		
		//image Name
		$imgName = $this->createElement('text', 'image_title');
		$imgName->setLabel('Image Title')
		->setRequired(TRUE)
		->addFilter('StripTags');
		//$this->addElement($imgName);
		
		//Album Name
		$albmName = $this->createElement('text', 'album_name');
		$albmName->setLabel('Album Name')
				 ->setRequired(TRUE)
                 ->addFilter('StripTags');
		//$this->addElement($albmName);
		
		//Category Name
		$catName = $this->createElement('select', 'category_name');
		$catName->setLabel('Category Name')
				->setRequired(TRUE)
                ->addMultiOption('beauty', 'Beauty')
                ->addMultiOption('children', 'Children')
                ->addMultiOption('family', 'Family')
                ->addMultiOption('maternity', 'Maternity')
                ->addMultiOption('newborn', 'Newborn')
                ->addMultiOption('pet', 'Pet')
                ->addMultiOption('weddings', 'Weddings');

		//$this->addElement($catName);
		
		//Image File
		$img = $this->createElement('file', 'image');
		$img->setLabel('Image: ')
			->setRequired(FALSE)
			//Don't forget to set this.
			->setDestination(APPLICATION_PATH . '/../public/images/gallery')
			->addValidator('Count', false, 1);
			//->addValidator('Size', false, 102400);
		//$this->addElement($img);

        $thumb = $this->createElement('checkbox', 'thumbnail');
        $thumb->setLabel('Is Thumbnail?')
              ->setRequired(FALSE);

		//Add Another Photo
		//$this->addElement($addPhoto);



        $order = array($id, $imgName, $albmName, $catName,$thumb, $addPhoto);
        $this->addElements($order);

        $this->addDisplayGroup($order, 'Add-Photos');

        //Submit
        $submit = $this->addElement('button', 'submit', array('label' => 'Submit'));
	}
}