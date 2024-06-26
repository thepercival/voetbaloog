<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 1202 2020-05-02 09:37:15Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Pool_User_Factory extends Object_Factory_Db_JSON implements VoetbalOog_Pool_User_Factory_Interface
{
	protected static $m_objSingleton;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see VoetbalOog_Pool_User_Factory_Interface::getNrOfWins()
	 */
	public static function getNrOfWins( $oPoolUser, $bOnlyPrevious )
	{
		$oPool = $oPoolUser->getPool();

		$arrNrOfWinsPerUser = array();
		if ( $bOnlyPrevious === true )
		{
			$oPreviousPool = static::getPreviousPool( $oPool );
			if ( $oPreviousPool !== null ) {
				static::getNrOfWinsHelper( $oPreviousPool, $arrNrOfWinsPerUser );
			}
		}
		else
		{
			if ( $oPool->getEndDateTime() > Agenda_Factory::createDateTime() )
			{
				$oPreviousPool = static::getPreviousPool( $oPool );
				if ( $oPreviousPool !== null )
					static::getNrOfWinsHelper( $oPreviousPool, $arrNrOfWinsPerUser );
			}
			else
				static::getNrOfWinsHelper( $oPool, $arrNrOfWinsPerUser );
		}

		if ( array_key_exists( $oPoolUser->getUser()->getId(), $arrNrOfWinsPerUser ) )
			return $arrNrOfWinsPerUser[ $oPoolUser->getUser()->getId() ];

		return array();
	}

	protected static function getNrOfWinsHelper( $oPool, &$arrNrOfWinsPerUser )
	{
		$oPreviousPool = static::getPreviousPool( $oPool );
		if ( $oPreviousPool !== null )
			static::getNrOfWinsHelper( $oPreviousPool, $arrNrOfWinsPerUser );

		static::getNrOfWinsPerUser( $oPool, $arrNrOfWinsPerUser );
	}

	protected static function getPreviousPool( $oPool )
	{
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "Voetbal_Season::StartDateTime", "SmallerThan", $oPool->getCompetitionSeason()->getSeason()->getStartDateTime() );
		$oOptions->addFilter( "VoetbalOog_Pool::Name", "EqualTo", $oPool->getName() );
		$oOptions->addOrder( "Voetbal_Season::StartDateTime", true );
		$oOptions->addLimit( 1 );
		return VoetbalOog_Pool_Factory::createObjectFromDatabase( $oOptions );
	}

	protected static function getNrOfWinsPerUser( VoetbalOog_Pool $oPool, &$arrWinsPerUser )
	{
		$oPoolUsers = $oPool->getUsers( true );

		$nMostPoints = null;
		foreach( $oPoolUsers as $oPoolUser )
		{
			if ( $nMostPoints !== null and $oPoolUser->getPoints() < $nMostPoints )
				break;

			if ( array_key_exists( $oPoolUser->getUser()->getId(), $arrWinsPerUser ) === false )
				$arrWinsPerUser[ $oPoolUser->getUser()->getId() ] = array();

			$arrWinsPerUser[ $oPoolUser->getUser()->getId() ][] = $oPool->getCompetitionSeason()->getAbbreviation();

			$nMostPoints = $oPoolUser->getPoints();
		}

		return $arrWinsPerUser;
	}

	/**
	 * @see JSON_Factory_Interface::convertObjectToJSON()
	 */
	public static function convertObjectToJSON( $oObject, $nDataFlag = null )
	{
		if ( $oObject === null )
			return "null";

		if ( static::isInPoolJSON( $oObject ) )
			return $oObject->getId();
		static::addToPoolJSON( $oObject );

		$sJSON =
		"{".
			"\"Id\":".$oObject->getId().",".
			"\"Admin\":".( $oObject->getAdmin() ? "true" : "false" ).",".
			"\"Paid\":".( $oObject->getPaid() ? "true" : "false" ).",".
			"\"Points\":".$oObject->getPoints().",".
			"\"Ranking\":".$oObject->getRanking().",".
			"\"Pool\":".VoetbalOog_Pool_Factory::convertObjectToJSON( $oObject->getPool(), $nDataFlag ).",".
			"\"User\":".VoetbalOog_User_Factory::convertObjectToJSON( $oObject->getUser(), $nDataFlag )
		;

		if ( ( VoetbalOog_JSON::$nPoolUser_Bets & $nDataFlag ) === VoetbalOog_JSON::$nPoolUser_Bets )
			$sJSON .= ",\"Bets\":".VoetbalOog_Bet_Factory::convertObjectsToJSON( $oObject->getBets(), $nDataFlag );

		return $sJSON."}";
	}
}