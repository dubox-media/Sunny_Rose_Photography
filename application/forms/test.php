<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kstowell
 * Date: 4/1/12
 * Time: 10:19 AM
 * To change this template use File | Settings | File Templates.
 */
class Form_Test extends  SR_Form_Form
{
    public function init()
    {
        //Name
        $name = new SR_Form_Element_Text('name');
        $name->setLabel('Name: ')
             ->setRequired(TRUE);
        $this->addElement($name);

        //Email
        $email = new SR_Form_Element_Email('email');
        $email->setLabel('Email: ')
              ->setRequired(TRUE);
        $this->addElement($email);

        $submit = new SR_Form_Element_Button('submit');
        $submit->setLabel('Contact Me');
        $this->addElement($submit);
    }
}