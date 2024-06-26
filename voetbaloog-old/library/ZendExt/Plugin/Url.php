<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Url.php 4268 2015-12-24 17:54:18Z thepercival $
 * @since      File available since Release 4.0
 * @package    ZendExt
 */

/**
 * @package ZendExt
 */
class ZendExt_Plugin_Url extends Zend_Controller_Plugin_Abstract
{
	protected $m_objView;
	
	public function __construct( Zend_View_Abstract $objView )
	{
		$this->m_objView = $objView;
	}
	
	public function preDispatch( Zend_Controller_Request_Abstract $request )
	{
		$sModule = $request->getModuleName();
		if ( strlen( $sModule ) > 0 )
			$sModule .= "/";
		$this->m_objView->urlcontroller = Zend_Registry::get("baseurl"). $sModule . $request->getControllerName();
		$this->m_objView->url = $this->m_objView->urlcontroller."/".$request->getActionName();		
	}
}
?>