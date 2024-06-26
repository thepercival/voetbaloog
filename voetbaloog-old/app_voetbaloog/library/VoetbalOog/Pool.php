<?php

/**
 * @copyright2007 Coen Dunnink
 * @license	http://www.gnu.org/licenses/gpl.txt
 * @version	$Id: Pool.php 1202 2020-05-02 09:37:15Z thepercival $
 * @link	 http://www.voetbaloog.nl/
 * @since	File available since Release 1.0
 * @package	VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Pool extends Agenda_TimeSlot implements VoetbalOog_Pool_Interface
{
	// VoetbalOog_Pool_Interface
	protected $m_sName;					// string
	protected $m_oCompetitionSeason;	// Voetbal_CompetitionSeason
	protected $m_oUsers;				// Collection
	protected $m_oUsersByRanking;		// Collection
	protected $m_oPayments;				// Collection
	protected $m_vtPicture;				// variant
	protected $m_nStake;				// int
	protected $m_oRoundBetConfigs;		// Collection
	protected $m_arrRoundBetTypes;		// array
	protected $m_arrNrOfBets;			// array

	/**
	 * Constructs the class
	 */
	public function __construct(){ parent::__construct(); }

	/**
	 * @see VoetbalOog_Pool_Interface::getName()
	 */
	public function getName()
	{
		return $this->m_sName;
	}

	/**
	 * @see VoetbalOog_Pool_Interface::putName()
	 */
	public function putName( $sName )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Pool::Name", $this->m_sName, $sName );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sName = $sName;
	}

	/**
	 * @see VoetbalOog_Pool_Interface::getCompetitionSeason()
	 */
	public function getCompetitionSeason()
	{
		if ( is_int( $this->m_oCompetitionSeason ) )
			$this->m_oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( $this->m_oCompetitionSeason );

		return $this->m_oCompetitionSeason;
	}

	/**
	 * @see VoetbalOog_Pool_Interface::putCompetitionSeason()
	 */
	public function putCompetitionSeason( $oCompetitionSeason )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Pool::CompetitionSeason", $this->m_oCompetitionSeason, $oCompetitionSeason );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oCompetitionSeason = $oCompetitionSeason;
	}

	/**
	 * @see VoetbalOog_Pool_Interface::getUsers()
	 */
	public function getUsers( $bByRanking = false )
	{
		if ( $bByRanking === false )
		{
			if ( $this->m_oUsers === null )
			{
				$oFilters = Construction_Factory::createOptions();
				$oFilters->addFilter( "VoetbalOog_Pool_User::Pool", "EqualTo", $this );
				$this->m_oUsers = VoetbalOog_Pool_User_Factory::createObjectsFromDatabase( $oFilters );
			}
			return $this->m_oUsers;
		}

		if ( $this->m_oUsersByRanking === null )
		{
			$this->m_oUsersByRanking = VoetbalOog_Pool_User_Factory::createObjects();
			$this->m_oUsersByRanking->addCollection( $this->getUsers() );

			$this->m_oUsersByRanking->uasort(
				function( $oPoolUserA, $oPoolUserB )
				{
					return ( $oPoolUserA->getPoints() > $oPoolUserB->getPoints() ? -1 : 1 );
				}
			);
		}
		return $this->m_oUsersByRanking;
	}

	/**
	 * @see VoetbalOog_Pool_Interface::getPayments()
	 */
	public function getPayments()
	{
		if ( $this->m_oPayments === null )
		{
			$oFilters = Construction_Factory::createOptions();
			$oFilters->addFilter( "VoetbalOog_Pool_Payment::Pool", "EqualTo", $this );
			$oFilters->addOrder( "VoetbalOog_Pool_Payment::Place", false );
			$this->m_oPayments = VoetbalOog_Pool_Payment_Factory::createObjectsFromDatabase( $oFilters );
		}
		return $this->m_oPayments;
	}

	/**
	 * @see VoetbalOog_Pool_Interface::getPicture()
	 */
	public function getPicture()
	{
		return $this->m_vtPicture;
	}

	/**
	 * @see VoetbalOog_Pool_Interface::putPicture()
	 */
	public function putPicture( $vtPicture )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Pool_User::Picture", $this->m_vtPicture, $vtPicture );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_vtPicture = $vtPicture;
	}

	/**
	 * @see VoetbalOog_Pool_Interface::getNrOfAvailableBets()
	 */
	public function getNrOfAvailableBets( $oRoundParam = null, $bAsArray = false, $oNow = null )
	{
		if ( $this->m_arrNrOfBets === null )
		{
			$this->m_arrNrOfBets = array();
            if ( $oNow === null )
                $oNow = Agenda_Factory::createDateTime();
			$oRounds = $this->getCompetitionSeason()->getRounds();
			foreach ( $oRounds as $oRound )
			{
                $nNrOfBets = 0;
                $oBetConfigs = $this->getBetConfigs( $oRound );
                $bResultScoreCounted = false;
                foreach( $oBetConfigs as $oBetConfig ) {
                    $nNrOfObjects = null;
                    if (($oBetConfig->getBetType() & VoetbalOog_Bet_Qualify::$nId) === VoetbalOog_Bet_Qualify::$nId) {
                        if ( $oNow <= $oBetConfig->getDeadLine() ) {
                            $nNrOfBets += $oRound->getPoulePlaces()->count();
                        }
                    }
                    else if ( $bResultScoreCounted === false ) {
                        $oGames = $oRound->getGames();
                        if ( $oBetConfig->getBetTime() === VoetbalOog_BetTime::$nBeforeStartGame ){
                            foreach( $oGames as $oGame )
                            {
                                if ( $oNow <= $oBetConfig->getDeadLine( $oGame ) )
                                    $nNrOfBets++;
                            }
                        }
                        else if ( $oNow <= $oBetConfig->getDeadLine() ) {
                            $nNrOfBets += $oGames->count();
                        }

                        $bResultScoreCounted = true;
                    }
                    $this->m_arrNrOfBets[$oRound->getNumber()] = $nNrOfBets;
                }
			}
		}
		if ( $oRoundParam === null ) {
			if ( $bAsArray ) {
				return $this->m_arrNrOfBets;
			}
			return array_sum ( $this->m_arrNrOfBets );
		}
		return $this->m_arrNrOfBets[ $oRoundParam->getNumber() ];
	}

	/**
	 * @see VoetbalOog_Pool_Interface::getAvailablePoints()
	 */
	public function getAvailablePoints()
	{
		$nAvailablePoints = 0;
		$oRounds = $this->getCompetitionSeason()->getRounds();
		foreach ( $oRounds as $oRound )
		{
			$oBetConfigs = $this->getBetConfigs( $oRound );
			foreach( $oBetConfigs as $oBetConfig )
			{
				$nNrOfObjects = null;
				if ( ( $oBetConfig->getBetType() & VoetbalOog_Bet_Qualify::$nId ) === VoetbalOog_Bet_Qualify::$nId )
					$nNrOfObjects = $oRound->getPoulePlaces()->count();
				else
					$nNrOfObjects = $oRound->getGames()->count();

				$nAvailablePoints += $nNrOfObjects * $oBetConfig->getPoints();
			}
		}
		return $nAvailablePoints;
	}

	/**
	 * @see VoetbalOog_Pool_Interface::getStake()
	 */
	public function getStake()
	{
		return $this->m_nStake;
	}

	/**
	 * @see VoetbalOog_Pool_Interface::putStake()
	 */
	public function putStake( $nStake )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Pool::Stake", $this->m_nStake, $nStake );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nStake = $nStake;
	}

	/**
	 * @see VoetbalOog_Pool_Interface::getBetConfigs()
	 */
	public function getBetConfigs( $oRound = null )
	{
		if ( $this->m_oRoundBetConfigs === null )
			$this->m_oRoundBetConfigs = VoetbalOog_Round_BetConfig_Factory::createObjectsFromDatabaseExt( $this );

		if ( $oRound === null )
			return $this->m_oRoundBetConfigs;

		$oRoundBetConfigs = $this->m_oRoundBetConfigs[ $oRound->getId() ];
		if ( $oRoundBetConfigs === null )
			return VoetbalOog_Round_BetConfig_Factory::createObjects();

		return $oRoundBetConfigs;
	}

	/**
	 * @see VoetbalOog_Pool_Interface::getBetTypes()
	 */
	public function getBetTypes( $oRound )
	{
		if ( $this->m_arrRoundBetTypes === null )
			$this->m_arrRoundBetTypes = array();

		if ( array_key_exists( $oRound->getId(), $this->m_arrRoundBetTypes ) === false )
		{
			$nBetTypes = 0;
			$oBetConfigs = $this->getBetConfigs( $oRound );
			foreach( $oBetConfigs as $oBetConfig )
				$nBetTypes += $oBetConfig->getBetType();
			$this->m_arrRoundBetTypes[ $oRound->getId() ] = $nBetTypes;
		}

		return $this->m_arrRoundBetTypes[ $oRound->getId() ];
	}

	/**
	 * Wanneer er geen eerste ronde is, moet je door alle ronden om te kijken wat de startdatum is
	 *
	 * @see Agenda_TimeSlot_Interface::getStartDateTime()
	 */
	public function getStartDateTime(): Agenda_DateTime
	{
		if ( $this->m_objStartDateTime === null )
		{
			$oCompetitionSeason = $this->getCompetitionSeason();
			$oRounds = $oCompetitionSeason->getRounds();

			foreach ( $oRounds as $oRound )
			{
				$oBetConfigs = $this->getBetConfigs( $oRound );

				foreach( $oBetConfigs as $oBetConfig )
				{
					if ( $oBetConfig->getBetTime() === VoetbalOog_BetTime::$nBeforeCompetitionSeason )
					{
						$this->m_objStartDateTime = $oCompetitionSeason->getStartDateTime();
						return $this->m_objStartDateTime;
					}
					elseif ( $oBetConfig->getBetTime() === VoetbalOog_BetTime::$nBeforeStartPreviousRound )
					{
						if ( $this->m_objStartDateTime === null )
							$this->m_objStartDateTime = $oCompetitionSeason->getPreviousRound( $oRound )->getStartDateTime();
					}
					elseif ( $oBetConfig->getBetTime() === VoetbalOog_BetTime::$nBeforeStartRound
						or $oBetConfig->getBetTime() === VoetbalOog_BetTime::$nBeforeStartGame
					)
					{
						if ( $this->m_objStartDateTime === null )
							$this->m_objStartDateTime = $oRound->getStartDateTime();
					}
				}
			}
		}
		return $this->m_objStartDateTime;
	}

	/**
	 * @see Agenda_TimeSlot_Interface::putStartDateTime()
	 */
	public function putStartDateTime( $oDateTime )
	{
		throw new Exception( "Pool::StartDateTime will be determined by the roundbetconfigs of the first round!", E_ERROR );
	}

	/**
	 * @see Agenda_TimeSlot_Interface::getEndDateTime()
	 */
	public function getEndDateTime()
	{
		if ( $this->m_objEndDateTime === null )
		{
			$oRounds = $this->getCompetitionSeason()->getRounds();

			$bHasBetTimeBeforeStartRound = false;

			$arrRounds = $oRounds->getArrayCopy();
			$oRound = array_pop( $arrRounds );
			while( $oRound !== null )
			{
				$oBetConfigs = $this->getBetConfigs( $oRound );

				$bHasBetTimeBeforeStartPreviousRound = false;

				foreach( $oBetConfigs as $oBetConfig )
				{
					if ( $oBetConfig->getBetTime() === VoetbalOog_BetTime::$nBeforeStartGame )
					{
						$this->m_objEndDateTime = $oRound->getEndDateTime();
						return $this->m_objEndDateTime;
					}
					elseif ( $oBetConfig->getBetTime() === VoetbalOog_BetTime::$nBeforeStartRound )
					{
						$bHasBetTimeBeforeStartPreviousRound = true;
					}
					elseif ( $oBetConfig->getBetTime() === VoetbalOog_BetTime::$nBeforeStartPreviousRound )
					{
						$bHasBetTimeBeforeStartPreviousRound = true;
					}
					elseif ( $oBetConfig->getBetTime() === VoetbalOog_BetTime::$nBeforeCompetitionSeason )
					{
						if ( $oRound->isFirstRound() === true ) {
							$this->m_objEndDateTime = $oRound->getStartDateTime();
							return $this->m_objEndDateTime;
						}
					}
				}
				if ( $bHasBetTimeBeforeStartRound === true ) {
					$this->m_objEndDateTime = $oRound->getStartDateTime();
					return $this->m_objEndDateTime;
				}
				else if ( $bHasBetTimeBeforeStartPreviousRound === true ){
					$bHasBetTimeBeforeStartRound = true;
				}
                $oRound = array_pop( $arrRounds );
			}
			throw new Exception( "function getLastDeadLine should not be called now", E_ERROR );
		}
		return $this->m_objEndDateTime;
	}

	/**
	 * @see Agenda_TimeSlot_Interface::putEndDateTime()
	 */
	public function putEndDateTime( $oDateTime )
	{
		throw new Exception( "Pool::EndDateTime will be determined by the roundbetconfigs of the last round!", E_ERROR );
	}
}
