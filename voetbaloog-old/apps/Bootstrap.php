<?php

class Apps_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function initControllers()
	{
		$this->bootstrap('FrontController');
		$this->frontController->setControllerDirectory(APPLICATION_PATH . '/controllers');
		// $this->frontController->addControllerDirectory(GEN_APPLICATION_PATH . '/controllers', 'apps');
		$this->frontController->addControllerDirectory(GEN_APPLICATION_PATH . '/controllers/admin', 'admin');
		$this->frontController->addControllerDirectory(GEN_APPLICATION_PATH . '/controllers/voetbal', 'voetbal');
        $this->frontController->addControllerDirectory(APPLICATION_PATH . '/controllers/api', 'api');
        $this->frontController->addControllerDirectory(APPLICATION_PATH . '/controllers/apihtml', 'apihtml');
		$this->frontController->throwExceptions(true);

        $response = new Zend_Controller_Response_Http();
		$response->setHeader('Content-Type', 'text/html; charset=UTF-8', true);
		$this->frontController->setResponse($response);
	}

	protected function initHelpers()
	{
        Zend_Controller_Action_HelperBroker::addPath( GEN_APPLICATION_PATH . '/controllers/helpers', 'Apps_Helper');
		$objResourceview = $this->getPluginResource('view');
		$view = $objResourceview->getView();
		$view->addHelperPath( GEN_APPLICATION_PATH . '/views/helpers' );
		$view->addScriptPath( GEN_APPLICATION_PATH . '/views/scripts' );
        $view->addScriptPath( APPLICATION_PATH . '/views/scripts/apihtml' );
        $view->addScriptPath( GEN_APPLICATION_PATH . '/views/scripts/voetbal' );
		$view->addScriptPath( GEN_APPLICATION_PATH . '/views/scripts/admin' );
    }

	protected function initPlugins()
	{
		$bInstallMode = false;

		$this->frontController->registerPlugin( new ZendExt_Plugin_Acl( $bInstallMode ), 10 );

		$oResourceview = $this->getPluginResource('view');
		$view = $oResourceview->getView();

		$this->frontController->registerPlugin( new ZendExt_Plugin_Url( $view ), 11 );
	}

    protected function initRoutes()
    {
        $oRouter = $this->frontController->getRouter();
        $oRoute = new Zend_Controller_Router_Route('voetbal/api/competitionseason/:id', array('module' => 'voetbal', 'controller' => 'api', 'action' => 'competitionseason'));
        $oRouter->addRoute('apics', $oRoute );
    }

	protected function initSession()
	{
		Zend_Session::start();

		// begin : anti-hack-hack
		$ra = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
		$ua = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');
		$hash = hash('sha1', "ANTI_HACK=$ra=$ua=1");
		if ( !empty($_SESSION['_ANTI_HACK_HASH_']) && ($_SESSION['_ANTI_HACK_HASH_'] !== $hash) ) {
			Zend_Session::destroy(true, true);
			header('Location: /?h');
			exit;
		}
		$_SESSION['_ANTI_HACK_HASH_'] = $hash;
		// end : anti-hack-hack
	}

	protected function initDb()
	{
		$cfgDb = new Zend_Config_Ini( APPLICATION_PATH.'/configs/config.ini', 'database');
		$oDatabase = Source_Db::initDb( $cfgDb->db );
		Zend_Registry::set('db', $oDatabase );

		if ( APPLICATION_ENV === 'development' )
			Zend_Registry::set('debug', new RAD_Tools_Debug( $oDatabase ) );
	}

	protected function initBaseUrl()
	{
		$web = new Zend_Config_Ini( APPLICATION_PATH.'/configs/config.ini', 'web');
		Zend_Registry::set( 'baseurl', $web->map );
	}

	protected function initJS( $cfgJS )
	{
		$sBaseUrl = Zend_Registry::get( 'baseurl');
		Zend_Registry::set( 'jsthirdparties', $sBaseUrl . $cfgJS->thirdparties );
		Zend_Registry::set( 'jslibrary', $sBaseUrl . $cfgJS->library );
		Zend_Registry::set( 'jsmap', $sBaseUrl . $cfgJS->map );
	}

	protected function initDateTime()
	{
		date_default_timezone_set('Europe/Amsterdam');
	}

	protected function initAutoLoader()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->registerNamespace( array(
			"Agenda",
			"Construction",
			"Controls",
			"Import",
			"JSON",
			"MemberShip",
			"Message",
			"MetaData",
			"Object",
			"Patterns",
			"RAD",
			"Source",
			"Voetbal",
			"XML",
			"ZendExt",
		) );
	}

	protected function initView()
	{
		$objResourceview = $this->getPluginResource('view');
		$view = $objResourceview->getView();
		$oSession = new Zend_Session_Namespace( APPLICATION_NAME );
		if ( $oSession->userid > 0 )
		{
			if ( $view->oUser === null )
				$view->oUser = RAD_Auth_User_Factory::createObjectFromDatabase( $oSession->userid );

			if ( $oSession->simulation === true )
				$view->oUser->getRoles()->add( RAD_Auth_Role_Factory::createObjectFromDatabase( APPLICATION_NAME . "_simulator" ) );
		}
	}
}

