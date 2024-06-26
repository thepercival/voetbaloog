<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package    Auth
 */
interface RAD_Auth_User_Interface
{
	/**
	 * gets the Name
	 *
	 * @return string	The Name
	 */
	public function getName();
	/**
	 * puts the Name
	 *
	 * @param  string	$sName	The Name of the User
	 * @return null
	*/
	public function putName( $sName );
	/**
	 * gets the Password
	 *
	 * @return string	The Password
	 */
	public function getPassword();
	/**
	 * puts the Password
	 *
	 * @param  string	$sPassword	The Password of the User
	 * @return null
	 */
	public function putPassword( $sPassword );
	/**
	 * gets the EmailAddress
	 *
	 * @return string	The EmailAddress
	 */
	public function getEmailAddress();
	/**
	 * puts the EmailAddress
	 *
	 * @param  string	$sEmailAddress	The EmailAddress of the User
	 * @return null
	 */
	public function putEmailAddress( $sEmailAddress );
	/**
	 * gets the LatestLogindatetime
	 *
	 * @return 	DateTime	The LatestLogindatetime
	 */
	public function getLatestLoginDateTime();
	/**
	 * gets the LatestLogindatetime
	 *
	 * @param 	DateTime	$oLatestLoginDateTime	The LatestLogindatetime
	 * @return 	null
	 */
	public function putLatestLoginDateTime( $oLatestLoginDateTime );
	/**
	 * gets the LatestLoginIpAddress
	 *
	 * @return string	The LatestLoginIpAddress
	 */
	public function getLatestLoginIpAddress();
	/**
	 * puts the LatestLoginIpAddress
	 *
	 * @param  string	$sLatestLoginIpAddress	The LatestLoginIpAddress of the User
	 * @return null
	 */
	public function putLatestLoginIpAddress( $sLatestLoginIpAddress );
	/**
	 * gets the root menuitem for this user. Each user can have roles.
	 * Dependant on which roles a user has, he gets to see some menuitems.
	 *
	 * @param 	string	$sModuleName	The modulename
	 * @param 	string	$sMenuItemName	The menuitemname
	 * @return  RAD_Auth_MenuItem_Interface	The root menuitem
	 */
	public function getMenu( $sModuleName, $sMenuItemName = "root" );
	/**
	 * gets the roles to which the user belongs.
	 *
	 * @param string $sModuleName 	the modulename
	 * @return Patterns_Collection
	 */
	public function getRoles( $sModuleName = null );
	/**
	 * true if user has role
	 *
	 * @param string $sRoleName the rolename
	 * @return bool true | false
	 */
	public function hasRole( $sRoleName );
	/**
	 * gets the Preferences
	 *
	 * @return string	The Preferences
	 */
	public function getPreferences();
	/**
	 * puts the Preferences
	 *
	 * @param  string	$sPreferences	The Preferences of the User
	 * @return null
	 */
	public function putPreferences( $sPreferences );
	/**
	 * gets the value of the Preference
	 *
	 * @param  string	$sKey	The key of the preference
	 * @return string	The value of the Preference
	 */
	public function getPreference( $sKey );
    /**
     * puts the Preferences
     *
     * @return mixed
     */
	public function putPreference( /* variable param list */ );
}