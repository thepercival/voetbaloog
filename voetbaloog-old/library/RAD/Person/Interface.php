<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4563 2019-09-01 12:32:37Z thepercival $
 * @package    RAD
 */

/**
 * @package RAD
 */
interface RAD_Person_Interface
{
	/**
	 * gets the FirstName
	 *
	 * @return 	string	the FirstName
	 */
	public function getFirstName();
	/**
	 * puts the FirstName
	 *
	 * @param string $szFirstName the FirstName which will be set
	 * @return 	null
	 */
	public function putFirstName( $szFirstName );
	/**
	 * gets the LastName
	 *
	 * @return 	string	the LastName
	 */
	public function getLastName();
	/**
	 * puts the LastName
	 *
	 * @param string $szLastName the LastName which will be set
	 * @return 	null
	 */
	public function putLastName( $szLastName );
	/**
	* gets the LastNamePartner
	*
	* @return 	string	the LastNamePartner
	*/
	public function getLastNamePartner();
	/**
	 * puts the LastNamePartner
	 *
	 * @param string $szLastNamePartner the LastNamePartner which will be set
	 * @return 	null
	 */
	public function putLastNamePartner( $szLastNamePartner );
	/**
	 * gets the NameInsertions
	 *
	 * @return 	string	the NameInsertions
	 */
	public function getNameInsertions();
	/**
	 * puts the NameInsertions
	 *
	 * @param string $szNameInsertions the NameInsertions which will be set
	 * @return 	null
	 */
	public function putNameInsertions( $szNameInsertions );
	/**
	* gets the NameInsertionsPartner
	*
	* @return 	string	the NameInsertionsPartner
	*/
	public function getNameInsertionsPartner();
	/**
	 * puts the NameInsertionsPartner
	 *
	 * @param string $szNameInsertionsPartner the NameInsertionsPartner which will be set
	 * @return 	null
	 */
	public function putNameInsertionsPartner( $szNameInsertionsPartner );
	/**
	* gets how the person is called
	*
	* @return 	int	how the person is called
	*/
	public function getCallType();
	/**
	 * puts how the person is called
	 *
	 * @param 	int $nCallType 	how the person is called
	 * @return 	null
	 */
	public function putCallType( $nCallType );
	/**
	* gets the LastNameCalled
	*
	* @param bool $bForOrdering if it should easy ordered
	* @return 	string the last name called
	*/
	public function getLastNameCalled( $bForOrdering = false );
	/**
	 * gets the FullName
	 *
	 * @param int $nCallType
     * @param int $nMaxLength
	 * @return 	string the full name
	 */
	public function getFullName( $nCallType = 0, $nMaxLength = null );
	/**
	* gets the dateofbirth
	*
	* @return 	DateTime	The date of birth
	*/
	public function getDateOfBirth();
	/**
	 * puts the DateOfBirth
	 *
	 * @param 	DateTime	$oDateOfBirth	The DateOfBirth
	 * @return 	null
	 */
	public function putDateOfBirth( $oDateOfBirth );
}