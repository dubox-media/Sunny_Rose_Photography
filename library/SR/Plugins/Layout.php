<?php


/**
 *  Used to load module spcific resources.
 */
class SR_Plugins_Layout extends Zend_Layout_Controller_Plugin_Layout
{
    public function routeShutdown($request)
    {
        $modName = $request->getModuleName();

        if($modName == 'admin'){
            $view = $this->_layout->getView();
            $view->headLink()->appendStylesheet('/styles/admin/styles_admin.css');
            $view->headScript()->appendFile('/scripts/admin/scripts_admin.js');
        }
    }
}