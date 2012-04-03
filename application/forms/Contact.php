<?php

/**
 *
 */
class Form_Contact extends Zend_Form
{
   public function init()
   {
       $this->setMethod('post');
       //Name Element
       $name = $this->createElement('text', 'name')
            ->setLabel('Name: ')
            ->setRequired(true)
            ->addFilter('StripTags');
       $this->addElement($name);

       //Email
       $email = $this->createElement('text', 'email')
            ->setLabel('Email: ')
            ->setRequired(true)
            ->addFilter('StripTags');
       $this->addElement($email);

       $subject = $this->createElement('select', 'subject');
       $subject->setLabel('Subject of Inquiry: ')
               ->setRequired(true);
       $subject->addMultiOption('general', 'General Inquiry')
               ->addMultiOption('service', 'Service Inquiry')
               ->addMultiOption('pricing', 'Pricing Inquiry')
               ->addMultiOption('schedule', 'Schedule a Shoot!') ;
       $this->addElement($subject);


       $mesage = $this->createElement('textarea', 'message')
            ->setLabel('Message: ')
            ->addFilter('StripTags')
            ->setAttrib('rows', '6')
            ->setAttrib('cols', '60');
       $this->addElement($mesage);



       $submit = $this->addElement('submit', 'submit', array('label' => 'Submit'));
   }
}