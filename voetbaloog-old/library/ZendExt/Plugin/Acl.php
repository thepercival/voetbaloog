<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Acl.php 4557 2019-08-12 18:50:59Z thepercival $
 * @since      File available since Release 4.0
 * @package    ZendExt
 */

/**
 * @package ZendExt
 */
class ZendExt_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	protected $m_bInstallMode;	// bool

	/**
	 * Constructs the class
	 */
	public function __construct( $bInstallMode = false )
	{
		$this->m_bInstallMode = $bInstallMode;
	}

	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$sAction = $request->getControllerName()."/".$request->getActionName()."/";
		$sModuleName = $request->getModuleName();
		if ( $sModuleName !== null and $sModuleName !== "default" )
			$sAction = $sModuleName . "/" . $sAction;

		$oSession = new Zend_Session_Namespace( APPLICATION_NAME );

		if ( $this->m_bInstallMode === true )
			return;

		if( !RAD_Auth::isAllowed( $sAction, $oSession->__get("userid"), APPLICATION_NAME, APPLICATION_PATH  . "/cache" )	)
		{
			$arrParams = $this->getMyParams( $request );
			$arrParams["previousmodule"] = $request->getModuleName();
			$arrParams["previouscontroller"] = $request->getControllerName();
			$arrParams["previousaction"] = $request->getActionName();

			$request->setModuleName("default");
			$request->setControllerName("user");
			if ( $oSession->__get("userid") === null ) {
				$request->setActionName("login");
				if ( array_key_exists( "loginmessage", $arrParams ) === false )
					$arrParams["loginmessage"] = "je moet eerst inloggen om de opgevraagde pagina te bekijken";
			}
			else {
				$request->setActionName("auth");
				if ( array_key_exists( "errormessage", $arrParams ) === false )
					$arrParams["errormessage"] = urlencode( "je hebt geen rechten om de opgevraagde pagina te bekijken" );
			}
			$request->setParams( $arrParams );
		}
	}

	protected function getMyParams( $request )
	{
		$arrParams = $request->getParams();

		$arrFilters = array( "module", "controller", "action", "username", "password", "loginmessage" );

		$arrMyParams = array();
		foreach( $arrParams as $szId => $szValue )
		{
			if ( array_key_exists( $szId, $arrFilters ) === false )
				$arrMyParams[$szId] = $szValue;
		}

		return $arrMyParams;
	}
}
?>