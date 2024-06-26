<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Auth.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package Auth
 */
class RAD_Auth
{
	/**
	 * Constructs the class
	 */
	public function __construct() {	}

	public static function isAllowed( $szAction, $nUserId, $sAppName, $szCacheDir )
	{
		$oAcl = static::getAcl( $sAppName, $szCacheDir );

		$sRoleName = null;
		if ( $nUserId > 0 )
		{
			$sRoleName = (string) $nUserId;
			$oUser = RAD_Auth_User_Factory::createObjectFromDatabase( $nUserId );
			$oAcl->addRole( new Zend_Acl_Role( $sRoleName ), static::toCleanArray( $oUser->getRoles() ) );
		}
		else
			$sRoleName = RAD_Auth_Role_Factory::getGuest()->getId();

		if ( $oAcl->has( $szAction ) === false or $oAcl->hasRole( $sRoleName ) === false
			or $oAcl->isAllowed( $sRoleName, $szAction ) === false )
			return false;

		return true;
	}

	protected static function getAcl( $sAppName, $szCacheDir )
	{
		$cache = ZendExt_Cache::getCache( null, $szCacheDir );

		// $cache->remove( "acl" );
		$oAcl = $cache->load( $sAppName . "acl" );

		if( $oAcl === false )
		{
			$oAcl = new Zend_Acl();

			$oAcl->add( new Zend_Acl_Resource( "user/login/" ) );
			$oAcl->add( new Zend_Acl_Resource( "user/logout/" ) );

			// First DB CALL
			$oRoles = RAD_Auth_Role_Factory::createObjectsFromDatabase();

			foreach( $oRoles as $sRoleId => $oRole )
			{
				$oAcl->addRole( new Zend_Acl_Role( $sRoleId ) );
				$objActions = $oRole->getActions( $sAppName );

				foreach( $objActions as $szActionId => $objAction )
				{
					$szActionName = $objAction->getName();
					if ( strlen( $szActionName ) > 0 )
					{
						if ( $oAcl->has( $szActionName ) === false )
							$oAcl->add( new Zend_Acl_Resource( $szActionName ) );

						static::allow( $oAcl, $oRole, $szActionName );
					}
				}

				static::allow( $oAcl, $oRole, "user/login/" );
				static::allow( $oAcl, $oRole, "user/logout/" );
			}

			// added guest role
			{
				$oRoleGuest = RAD_Auth_Role_Factory::getGuest();
				$sRoleGuestId = $oRoleGuest->getId();
				if ( $oAcl->hasRole( $sRoleGuestId ) === false )
					$oAcl->addRole( new Zend_Acl_Role( $sRoleGuestId ) );

				if ( $oAcl->has( "index/index/" ) === true )
					static::allow( $oAcl, $oRoleGuest, "index/index/" );

				static::allow( $oAcl, $oRoleGuest, "user/login/" );
				static::allow( $oAcl, $oRoleGuest, "user/logout/" );
			}

			$cache->save( $oAcl, "acl" );
		}

		return $oAcl;
	}

	/**
	 * Converts array
	 */
	protected static function toCleanArray( $objArrayObject )
	{
		$arrStraight = array();

  		foreach ( $objArrayObject as $szItemId => $objItem )
			$arrStraight[] = $szItemId;

  		return $arrStraight;
	}

	protected static function allow( $oAcl, $oRole, $sResourceName )
	{
		$oAcl->allow( $oRole->getId(), $sResourceName );
	}
}
