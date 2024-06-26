<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 580 2013-11-20 15:28:51Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Factory
{
	const DEFAULT_NROFMINUTES_GAME = 90;
	const DEFAULT_NROFMINUTES_EXTRATIME = 30;
	const DEFAULT_WINPOINTS_AFTERGAME = 3;
	const DEFAULT_WINPOINTS_AFTEREXTRATIME = 3;
	const DEFAULT_PROMOTION_RULE = Voetbal_Ranking::PROMOTION_RULE_EC;
	CONST STATE_CREATED = 1;
	CONST STATE_SCHEDULED = 2;
	CONST STATE_INPROGRESS = 4;
	CONST STATE_PLAYED = 8;
    CONST STATE_POSTPONED = 16;

	public static function getExternalLib( $sName )
	{
		$oExternalLib = null;
		if ( $sName === Voetbal_Extern_System_SofaScore::NAME )
            $oExternalLib = new Voetbal_Extern_System_SofaScore();

		if ( $oExternalLib === null )
			throw new Exception( "externe module ".$sName." kon niet gevonden worden", E_ERROR );

		if ( ! ( $oExternalLib instanceof Voetbal_Extern_System_Interface ) )
			throw new Exception( "externe module ".$sName." ondersteunt niet Voetbal_Extern_System_Interface", E_ERROR );

		return $oExternalLib;
	}

	public static function getConfigValue( /* variable param list */ )
	{
		$cfgApp = new Zend_Config_Ini( __DIR__ . DIRECTORY_SEPARATOR . 'config.ini', 'application');

		$vtItem = $cfgApp;
		for ( $nI = 0 ; $nI < func_num_args() ; $nI++ )
		{
			$vtProp = func_get_arg( $nI );
			$vtItem = $vtItem->$vtProp;
		}
		return $vtItem;
	}
}