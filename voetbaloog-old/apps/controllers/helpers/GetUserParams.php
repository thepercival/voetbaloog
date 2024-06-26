<?php

/**
 *
 *
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Apps_Helper_GetUserParams extends Zend_Controller_Action_Helper_Abstract
{
	public function direct( $bAsString = false )
	{
		$objActionController = $this->getActionController();

		$arrUserParams = $objActionController->getRequest()->getParams();

		$arrFilters = array(
			'module' => true,
			'controller' => true,
			'action' => true,
			'username' => true,
			'password' => true,
			'loginmessage' => true
		);

		$arrMyParams = array();
		foreach( $arrUserParams as $szId => $szValue )
		{
			if ( array_key_exists( $szId, $arrFilters ) === false )
				$arrMyParams[$szId] = $szValue;
		}

		if ( $bAsString === true )
		{
			$szMyParams = "";
			foreach( $arrMyParams as $szId => $szValue )
				$szMyParams .= $szId."/".$szValue."/";
			return $szMyParams;
		}
		return $arrMyParams;
	}
}
