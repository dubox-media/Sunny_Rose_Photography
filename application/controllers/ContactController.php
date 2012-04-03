<?php

class ContactController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->pageTitle = 'contact';
        $this->view->headLink()->appendStylesheet('/styles/styles_contact.css');
    }

    public function indexAction()
    {
        $form = new Form_Contact();
        $form->setAction('/contact/submit');
        $form->setDecorators(array(
            array('ViewScript', array('viewScript' => 'partials/_contactForm.phtml'))
        ));
        $this->view->form = $form;

    }

    public function submitAction()
    {
        $form = new Form_Contact();

        //$params = $this->_request->getParams();
        //print_r($params);
        if($this->_request->isPost()){
            if($form->isValid($_POST)){
                //Get form values and save to db
                $contact = new Model_Contact();
                $data = $form->getValues();
                $id = $contact->saveContactRequest($data);

                //Send to Sunny Rose emil
                $mail = new Zend_Mail();

                //Set up smtp params
                $config = array(
                    'auth'       =>  'login',
                    'username'   =>  'sunnyrose@sunnyrosephotography.com',
                    'password'   =>  'kurami22',
                    'port'       =>  '587'
                );

                //Set up transport
                $transport = new Zend_Mail_Transport_Smtp('mail.sunnyrosephotography.com', $config);

                $mail->setBodyHtml('<h4 style="text-decoration:underline">From: </h4>'.$data['name'] .
                                   "\n".'<h4 style="text-decoration:underline">At: </h4>'.$data['email'].
                                    '<br /><h4 style="text-decoration:underline">Message: </h4>' ."\n". $data['message']);
                $mail->setFrom($data['email'], $data['name']);
                $mail->setSubject('sunnyrosephotography.com ' .'"'.$data['subject']. ' inquiry"')
                     ->addTo('sunnyrose@sunnyrosephotography.com', 'Sunny Rose');
                $mail->send($transport);

                //If db row saves correctly, forward to success action
                if(!empty($id)){
                    $this->_forward('success');
                } else {
                    echo 'Error - something went wrong, try again in a few minutes.';
                }
            } else {
                echo 'Your entries could no be processed.';
            }
        }
    }

    public function successAction()
    {
        // action body
    }


}





