<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kstowell
 * Date: 4/1/12
 * Time: 2:03 AM
 * To change this template use File | Settings | File Templates.
 */
class SR_Form_Element_Text  extends Zend_Form_Element_Text
{
    /**
     * @param $spec
     * @param null $options
     */
    public function __construct($spec, $options = null)
    {
        parent::__construct($spec,$options);

        //add Filter
        $this->addFilter(
            new Zend_Filter_HtmlEntities());
        $this->addFilter(
            new Zend_Filter_StripTags());
    }

    public function loadDefaultDecorators()
    {
        if($this->loadDefaultDecoratorsIsDisabled()){
            return $this;
        }

        $decorators = $this->getDecorators();
        if(empty($decorators)){
            $this
                ->addDecorator('ViewHelper')
                ->addDecorator('Description', array('tag' => 'span', 'class' => 'note', 'placement' => 'PREPEND'))
                ->addDecorator('Label')
                ->addDecorator('Errors', array('placement' => 'prepend'))
                ->addDecorator('HtmlTag', array('tag' => 'li', 'class' => 'input-container ' . $this->getName()));
        }
    }
}