<?php

require_once GEN_APPLICATION_PATH . '/Bootstrap.php';

class Bootstrap extends Apps_Bootstrap
{
	protected function _initAll()
	{
		$this->initControllers();;
		$this->initAutoLoader();
		$this->initTBSConfig();
		$this->initHelpers();

		$this->initDateTime();
		$this->initDb();

		if ( PHP_SAPI !== "cli" )
			$this->initSession();

		$this->initBaseUrl();
        $cfgJS = new Zend_Config_Ini( APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'config.ini', 'js');
		$this->initJS($cfgJS);
        $this->initRoutes();
		$this->initPlugins();

		if ( PHP_SAPI !== "cli" )
			$this->initView();
	}

	protected function initAutoLoader()
	{
		parent::initAutoLoader();
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->registerNamespace( array( "VoetbalOog", "TBS", "League" ) );

		include_once __DIR__ . "/../ThirdParties/tactician_autoload.php";
	}

	protected function initTBSConfig()
	{
		Zend_Registry::set('config', $this->getOptions());
	}

	protected function initJS( $cfgJS )
	{
		Zend_Registry::set( 'jslibraryvo', Zend_Registry::get( 'baseurl') . $cfgJS->libraryvo );
		parent::initJS( $cfgJS );
	}

	protected function initRoutes()
	{
		$oRouter = $this->frontController->getRouter();
		$oRoute = new Zend_Controller_Router_Route(':name', array('controller' => 'pool', 'action' => 'index'));
		$oRouter->addRoute('poolname', $oRoute );
		// $oRoute = new Zend_Controller_Router_Route(':name/:season', array('controller' => 'pool', 'action' => 'index'));
		// $oRoute = new Zend_Controller_Router_Route()
		// $oRouter->addRoute('poolnameseason', $oRoute );

		parent::initRoutes();
	}

	protected function initHelpers()
	{
		parent::initHelpers();
		Zend_Controller_Action_HelperBroker::addPath( APPLICATION_PATH . '/controllers/helpers', 'VoetbalOog_Helper');
	}

	protected function initPlugins()
	{
		$this->frontController->registerPlugin( new VoetbalOog_ZendExt_Plugin_Image(), 1 );

		parent::initPlugins();
	}

	protected function initSession()
	{
		parent::initSession();

		$oSession = new Zend_Session_Namespace( APPLICATION_NAME );

		if ( $oSession->userid === null and array_key_exists( "rememberme", $_COOKIE ) and strlen( $_COOKIE["rememberme"] ) > 0 )
		{
			// $cfgAuth = new Zend_Config_Ini( APPLICATION_PATH . '/configs/config.ini', 'auth');

			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "VoetbalOog_User::CookieSessionToken", "EqualTo", $_COOKIE["rememberme"] );
			$oUser = VoetbalOog_User_Factory::createObjectFromDatabase( $oOptions );

			if ( $oUser !== null ) {
				$sPath = ( APPLICATION_ENV === "production" ) ? "/" : "/voetbaloog";
				setcookie('rememberme', $_COOKIE["rememberme"], time() + 6048000 /* 70 dagen */, $sPath );
				$oSession->userid = $oUser->getId();
			}
		}

	}

	protected function initView()
	{
		$oView = $this->getPluginResource('view')->getView();

		$oSession = new Zend_Session_Namespace( APPLICATION_NAME );
		if ( $oSession->userid > 0 )
			$oView->oUser = VoetbalOog_User_Factory::createObjectFromDatabase( $oSession->userid );

		parent::initView();

		$sTitle = "VoetbalOog";

		$this->initViewLayout( $oView );

		$oView->googleanalyticsid = "UA-13137855-1";
	}

	protected function initViewLayout( $oView )
	{
		$oLayout = $this->getPluginResource('layout')->getLayout();

		$oView->cssincludes = $oView->render( "cssincludes." . $oLayout->getViewSuffix() );
		$oView->jsincludes = $oView->render( "jsincludes." . $oLayout->getViewSuffix() );
	}
}

?>
