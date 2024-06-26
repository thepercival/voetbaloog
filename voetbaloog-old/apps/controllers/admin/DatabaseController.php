<?php

class Admin_DatabaseController extends Zend_Controller_Action
{
	public function indexAction()
	{
        set_time_limit ( 60 * 5 );
		if ( strlen( $this->getRequest()->getParam("btnexecupdatexml") ) > 0 )
			$this->executeUpdateXML();
		else if ( strlen( $this->getRequest()->getParam("btnexecinstallxml") ) > 0 )
			$this->executeInstallXML();
	}

	protected function executeUpdateXML()
	{
		Source_Db_Installer::update( APPLICATION_NAME );

		$this->refresh();
	}

	protected function executeInstallXML()
	{
		$sModuleName = null;
		{
			$cfgApp = new Zend_Config_Ini( APPLICATION_PATH.'/configs/config.ini', 'application');
			if ( substr( $cfgApp->version, 1 ) < 0.99 )
			{
				$sModuleName = "apps";
			}
			else // if ( substr( $cfgApp->version, 1 ) < 1.00 )
				$sModuleName = APPLICATION_NAME;
		}
		Source_Db_Installer::deInstallTables( $sModuleName );

		Source_Db_Installer::installTables( $sModuleName );

		$this->refresh();
	}

	protected function refresh()
	{
		ZendExt_Cache::getCache( null, APPLICATION_PATH  . "/cache" )->remove( 'acl' );

		$oSession = new Zend_Session_Namespace( APPLICATION_NAME );
		if ( $oSession !== null )
			$oSession->oRootMenuItem = null;
	}
}

?>