<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kstowell
 * Date: 4/1/12
 * Time: 10:13 AM
 * To change this template use File | Settings | File Templates.
 */
class SR_Form_Form extends Zend_Form
{
    public  function __construct($options = null)
    {
        parent::__construct($options);
    }

    /**
     * @return SR_Form_Form
     */
    public function loadDefaultDecorators()
    {

        if ($this->loadDefaultDecoratorsIsDisabled())
        {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators))
        {
            $this
                ->addDecorator('FormElements')
                ->addDecorator('HtmlTag', array('tag' => 'ul', 'class' => 'form-elements'))
                ->addDecorator('Form');
        }
        return $this;
    }
}