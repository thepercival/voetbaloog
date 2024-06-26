<?php

class Admin_AutorisatieController extends Zend_Controller_Action
{
    public function indexAction()
    {
        if ( strlen( $this->getRequest()->getParam("btnexecsetupxml") ) > 0 )
            $this->executeSetupXML();
    }

    protected function executeSetupXML()
    {
        Source_Db_Installer::setup( APPLICATION_NAME );
        $this->refresh();
        $this->view->setupCompleted = true;
    }

    protected function refresh()
    {
        ZendExt_Cache::getCache( null, APPLICATION_PATH  . "/cache" )->remove( 'acl' );

        $oSession = new Zend_Session_Namespace( APPLICATION_NAME );
        if ( $oSession !== null )
            $oSession->oRootMenuItem = null;
    }
}