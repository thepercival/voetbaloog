<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Reader.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package		VoetbalOog
 */
class VoetbalOog_User_Db_Reader extends RAD_Auth_User_Db_Reader implements Source_Reader_Ext_Interface
{
	/**
	 * @see Source_Reader_Ext_Interface::createObjectsExt()
	 */
    public function createObjectsExt( $oObject, Construction_Option_Collection $oOptions = null, $sClassName = null ): Patterns_Collection
	{
		if ( $oOptions === null )
			$oOptions = Construction_Factory::createOptions();

		$oSelect = $this->m_objDatabase->select();

		if ( $oObject instanceof VoetbalOog_Pool or $sClassName === "VoetbalOog_Pool" )
		{
			$this->addPersistance( VoetbalOog_Pool_User_Factory::createDbPersistance() );

			if ( $oObject !== null )
				$oOptions->addFilter( "VoetbalOog_Pool_User::Pool", "EqualTo", $oObject );

			$oSelect = $this->m_objDatabase->select();

			$sTablePoolUsers = VoetbalOog_Pool_User_Db_Persistance::getTable()->getName();

			$oSelect
				->from(array( $sTablePoolUsers ), array() )
				->join(array( $this->getTableName() => $this->getTableName()), $sTablePoolUsers . ".UserId = ".$this->getTableName().".Id" )
			;
		}
		else
			throw new Exception( "No classname set!", E_ERROR );

		$this->addWhereOrderBy( $oSelect, $oOptions );

		return $this->createObjectsHelper( $oSelect, $this->getCustomReadProperties( $oOptions ) );
	}
/*
	protected function fillObject( $oObject, $row )
	{
		//$objObject->putId( $row["LoginName"] );
		//$objObject->putPassword( $row["Password"] );
		//$objObject->putLatestLoginDateTime( $row["LatestLoginDateTime"] );
		//$objObject->putLatestLoginIpAddress( $row["LatestLoginIpAddress"] );
		//$objObject->putSystem( $row["System"] );
		//$objObject->putPreferences( $row["Preferences"] );

		parent::fillObject( $oObject, $row );
		$oObject->putEmailAddress( $row["EmailAddress"] );
		$oObject->putGender( $row["Gender"] );
		if ( strlen( $row["DateOfBirth"] ) > 0 )
			$oObject->putDateOfBirth( Agenda_Factory::createDate( $row["DateOfBirth"] ) );
		$oObject->putHashType( $row["HashType"] );
		$oObject->putSalted( $row["Salted"] );
		$oObject->putActivationKey( $row["ActivationKey"] );
		$oObject->putFacebookId( $row["FacebookId"] );
		$oObject->putCookieSessionToken( $row["CookieSessionToken"] );
	}*/
}