<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Game.php 989 2015-01-23 12:51:18Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Game extends Agenda_TimeSlot implements Voetbal_Game_Interface, Import_Importable_Interface, Patterns_Validatable_Interface
{
	// Voetbal_Game_Interface
	protected $m_oCompetitionSeason;	// Voetbal_CompetitonSeason
	protected $m_oHomePoulePlace;		// Voetbal_PoulePlace
	protected $m_oAwayPoulePlace;		// Voetbal_PoulePlace
	protected $m_nHomeGoals;			// int
	protected $m_nAwayGoals;			// int
	protected $m_nHomeGoalsExtraTime;	// int
	protected $m_nAwayGoalsExtraTime;	// int
	protected $m_nHomeGoalsPenalty;		// int
	protected $m_nAwayGoalsPenalty;		// int
	protected $m_nHomeNrOfCorners;		// int
	protected $m_nAwayNrOfCorners;		// int
	protected $m_nNumber;				// int
	protected $m_oLocation;				// Voetbal_Location
	protected $m_nState;				// int
	protected $m_nViewOrder;			// int

	use Import_Importable_Trait, Patterns_Validatable_Trait;

	CONST HOME = 1;
	CONST AWAY = 2;

    CONST DRAW = 3;

	CONST DETAIL_GOAL = 1;
	CONST DETAIL_GOALOWN = 2;
	CONST DETAIL_GOALPENALTY = 4;
	CONST DETAIL_SUBSTITUTE = 8;
	CONST DETAIL_YELLOWCARDONE = 16;
	CONST DETAIL_YELLOWCARDTWO = 32;
	CONST DETAIL_REDCARD = 64;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @see Voetbal_Game_Interface::getCompetitionSeason()
	 */
	public function getCompetitionSeason()
	{
		if ( is_int( $this->m_oCompetitionSeason ) )
			$this->m_oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( $this->m_oCompetitionSeason );

		return $this->m_oCompetitionSeason;
	}

	/**
	 * @see Voetbal_Game_Interface::putCompetitionSeason()
	 */
	public function putCompetitionSeason( $oCompetitionSeason )
	{
		$this->m_oCompetitionSeason = $oCompetitionSeason;
	}
	
	/**
	 * @see Voetbal_Game_Interface::getHomePoulePlace()
	 */
	public function getHomePoulePlace()
	{
		if ( is_int( $this->m_oHomePoulePlace ) )
			$this->m_oHomePoulePlace = Voetbal_PoulePlace_Factory::createObjectFromDatabase( $this->m_oHomePoulePlace );

		return $this->m_oHomePoulePlace;
	}

	/**
	 * @see Voetbal_Game_Interface::putHomePoulePlace()
	 */
	public function putHomePoulePlace( $oHomePoulePlace )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game::HomePoulePlace", $this->m_oHomePoulePlace, $oHomePoulePlace );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oHomePoulePlace = $oHomePoulePlace;
	}

	/**
	 * @see Voetbal_Game_Interface::getAwayPoulePlace()
	 */
	public function getAwayPoulePlace()
	{
		if ( is_int( $this->m_oAwayPoulePlace ) )
			$this->m_oAwayPoulePlace = Voetbal_PoulePlace_Factory::createObjectFromDatabase( $this->m_oAwayPoulePlace );

		return $this->m_oAwayPoulePlace;
	}

	/**
	 * @see Voetbal_Game_Interface::putAwayPoulePlace()
	 */
	public function putAwayPoulePlace( $oAwayPoulePlace )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game::AwayPoulePlace", $this->m_oAwayPoulePlace, $oAwayPoulePlace );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oAwayPoulePlace = $oAwayPoulePlace;
	}

	/**
	 * @see Voetbal_Game_Interface::getPoule()
	 */
	public function getPoule()
	{
		return $this->getHomePoulePlace()->getPoule();
	}

	/**
	 * @see Voetbal_Game_Interface::getHomeGoals()
	 */
	public function getHomeGoals()
	{
		return $this->m_nHomeGoals;
	}

	/**
	 * @see Voetbal_Game_Interface::putHomeGoals()
	 */
	public function putHomeGoals( $nHomeGoals )
	{
		$nHomeGoals = $nHomeGoals;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game::HomeGoals", $this->m_nHomeGoals, $nHomeGoals );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nHomeGoals = $nHomeGoals;
	}

	/**
	 * @see Voetbal_Game_Interface::getAwayGoals()
	 */
	public function getAwayGoals()
	{
		return $this->m_nAwayGoals;
	}

	/**
	 * @see Voetbal_Game_Interface::putAwayGoalsAwayGoals()
	 */
	public function putAwayGoals( $nAwayGoals )
	{
		$nAwayGoals = $nAwayGoals;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game::AwayGoals", $this->m_nAwayGoals, $nAwayGoals );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nAwayGoals = $nAwayGoals;
	}

	/**
	* @see Voetbal_Game_Interface::getHomeGoalsExtraTime()
	*/
	public function getHomeGoalsExtraTime()
	{
		return $this->m_nHomeGoalsExtraTime;
	}

	/**
	 * @see Voetbal_Game_Interface::putHomeGoalsExtraTime()
	 */
	public function putHomeGoalsExtraTime( $nHomeGoalsExtraTime )
	{
		$nHomeGoalsExtraTime = $nHomeGoalsExtraTime;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game::HomeGoalsExtraTime", $this->m_nHomeGoalsExtraTime, $nHomeGoalsExtraTime );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nHomeGoalsExtraTime = $nHomeGoalsExtraTime;
	}

	/**
	 * @see Voetbal_Game_Interface::getAwayGoalsExtraTime()
	 */
	public function getAwayGoalsExtraTime()
	{
		return $this->m_nAwayGoalsExtraTime;
	}

	/**
	 * @see Voetbal_Game_Interface::putAwayGoalsExtraTimeAwayGoalsExtraTime()
	 */
	public function putAwayGoalsExtraTime( $nAwayGoalsExtraTime )
	{
		$nAwayGoalsExtraTime = $nAwayGoalsExtraTime;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game::AwayGoalsExtraTime", $this->m_nAwayGoalsExtraTime, $nAwayGoalsExtraTime );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nAwayGoalsExtraTime = $nAwayGoalsExtraTime;
	}

	/**
	 * @see Voetbal_Game_Interface::getHomeGoalsPenalty()
	 */
	public function getHomeGoalsPenalty()
	{
		return $this->m_nHomeGoalsPenalty;
	}

	/**
	 * @see Voetbal_Game_Interface::putHomeGoalsPenalty()
	 */
	public function putHomeGoalsPenalty( $nHomeGoalsPenalty )
	{
		$nHomeGoalsPenalty = $nHomeGoalsPenalty;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game::HomeGoalsPenalty", $this->m_nHomeGoalsPenalty, $nHomeGoalsPenalty );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nHomeGoalsPenalty = $nHomeGoalsPenalty;
	}

	/**
	 * @see Voetbal_Game_Interface::getAwayGoalsPenalty()
	 */
	public function getAwayGoalsPenalty()
	{
		return $this->m_nAwayGoalsPenalty;
	}

	/**
	 * @see Voetbal_Game_Interface::putAwayGoalsPenaltyAwayGoalsPenalty()
	 */
	public function putAwayGoalsPenalty( $nAwayGoalsPenalty )
	{
		$nAwayGoalsPenalty = $nAwayGoalsPenalty;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game::AwayGoalsPenalty", $this->m_nAwayGoalsPenalty, $nAwayGoalsPenalty );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nAwayGoalsPenalty = $nAwayGoalsPenalty;
	}

	/**
	* @see Voetbal_Game_Interface::getHomeGoalsCalc()
	*/
	public function getHomeGoalsCalc( $bCountPenalties = false )
	{
		if ( $this->getHomeGoalsPenalty() >= 0 and $bCountPenalties === true )
			return $this->getHomeGoalsPenalty();
		else if ( $this->getHomeGoalsExtraTime() >= 0 )
			return $this->getHomeGoalsExtraTime();
		return $this->getHomeGoals();
	}

	/**
	* @see Voetbal_Game_Interface::getAwayGoalsCalc()
	*/
	public function getAwayGoalsCalc( $bCountPenalties = false )
	{
		if ( $this->getAwayGoalsPenalty() >= 0 and $bCountPenalties === true )
			return $this->getAwayGoalsPenalty();
		else if ( $this->getAwayGoalsExtraTime() >= 0 )
			return $this->getAwayGoalsExtraTime();
		return $this->getAwayGoals();
	}

	/**
	 * @see Voetbal_Game_Interface::getHomeNrOfCorners()
	 */
	public function getHomeNrOfCorners()
	{
		return $this->m_nHomeNrOfCorners;
	}

	/**
	 * @see Voetbal_Game_Interface::putHomeNrOfCorners()
	 */
	public function putHomeNrOfCorners( $nHomeNrOfCorners )
	{
		$nHomeNrOfCorners = $nHomeNrOfCorners;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game::HomeNrOfCorners", $this->m_nHomeNrOfCorners, $nHomeNrOfCorners );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nHomeNrOfCorners = $nHomeNrOfCorners;
	}

	/**
	 * @see Voetbal_Game_Interface::getAwayNrOfCorners()
	 */
	public function getAwayNrOfCorners()
	{
		return $this->m_nAwayNrOfCorners;
	}

	/**
	 * @see Voetbal_Game_Interface::putAwayNrOfCorners()
	 */
	public function putAwayNrOfCorners( $nAwayNrOfCorners )
	{
		$nAwayNrOfCorners = $nAwayNrOfCorners;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game::AwayNrOfCorners", $this->m_nAwayNrOfCorners, $nAwayNrOfCorners );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nAwayNrOfCorners = $nAwayNrOfCorners;
	}

    /**
     * @see Voetbal_Game_Interface::getLocation()
     */
    public function getLocation()
    {
        if ( is_int( $this->m_oLocation ) )
            $this->m_oLocation = Voetbal_Location_Factory::createObjectFromDatabase( $this->m_oLocation );

        return $this->m_oLocation;
    }

    /**
     * @see Voetbal_Game_Interface::putLocation()
     */
    public function putLocation( $oLocation )
    {
        if ( $this->m_bObserved === true )
        {
            $oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game::Location", $this->m_oLocation, $oLocation );
            $this->notifyObservers( $oObjectChange );
        }
        $this->m_oLocation = $oLocation;
    }

	/**
	 * @see Voetbal_Game_Interface::getNumber()
	 */
	public function getNumber()
	{
		return $this->m_nNumber;
	}

	/**
	 * @see Voetbal_Game_Interface::putNumber()
	 */
	public function putNumber( $nNumber )
	{
		$nNumber = $nNumber;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game::Number", $this->m_nNumber, $nNumber );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nNumber = $nNumber;
	}

	/**
	 * @see Voetbal_Game_Interface::getState()
	 */
	public function getState()
	{
		return $this->m_nState;
	}

	/**
	 * @see Voetbal_Game_Interface::putState()
	 */
	public function putState( $nState )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game::State", $this->m_nState, $nState );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nState = $nState;
	}

	/**
	 * @see Agenda_TimeSlot_Interface::getEndDateTime()
	 */
	public function getEndDateTime()
	{
		if ( $this->m_objEndDateTime === null ) {
			$this->m_objEndDateTime = Agenda_Factory::createDateTime( $this->getStartDateTime() );

			$nDuration = 0;
			{
				$oPoule = $this->getPoule();
				$oCompetitionSeason = $oPoule->getRound()->getCompetitionSeason();
				$nDuration += $oCompetitionSeason->getNrOfMinutesGame() + 15 + 5 /* break time + default extratime */;
				if ( $this->getState() === Voetbal_Factory::STATE_PLAYED )
				{
					if ( $this->getHomeGoalsExtraTime() >= 0 )
						$nDuration += $oCompetitionSeason->getNrOfMinutesExtraTime();
					if ( $this->getHomeGoalsPenalty() >= 0 )
						$nDuration += 30;
				}
				else
				{
					if ( $oPoule->needsRanking() === false  )
					{
						$nDuration += $oCompetitionSeason->getNrOfMinutesExtraTime();
						$nDuration += 30;
					}
				}
			}
			$this->m_objEndDateTime->modify( "+".$nDuration." minutes" );
		}
		return $this->m_objEndDateTime;
	}

	/**
	 * @see Voetbal_Game_Interface::getViewOrder()
	 */
	public function getViewOrder()
	{
		return $this->m_nViewOrder;
	}

	/**
	 * @see Voetbal_Game_Interface::putViewOrder()
	 */
	public function putViewOrder( $nViewOrder )
	{
		$nViewOrder = $nViewOrder;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game::ViewOrder", $this->m_nViewOrder, $nViewOrder );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nViewOrder = $nViewOrder;
	}

	/**
	* @see Voetbal_Game_Interface::getParticipations()
	*/
	public function getParticipations( Voetbal_Team $oTeam = null ): Patterns_ObservableObject_Collection_Idable
	{
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter("Voetbal_Game_Participation::Game", "EqualTo", $this );
		if ( $oTeam !== null )
			$oOptions->addFilter("Voetbal_Game_Participation::Team", "EqualTo", $oTeam );
		return Voetbal_Game_Participation_Factory::createObjectsFromDatabase( $oOptions );
	}

	/**
	* @see Voetbal_Game_Interface::getGoals()
	*/
	public function getGoals( $nHomeAway = null ): Patterns_ObservableObject_Collection
	{
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter("Voetbal_Game_Participation::Game", "EqualTo", $this );

		if ( ( $nHomeAway & Voetbal_Game::HOME ) === Voetbal_Game::HOME )
		{
			$oOptionsOr = Voetbal_Goal_Factory::createHomeAwayFilters( $this->getHomePoulePlace()->getTeam(), $this->getAwayPoulePlace()->getTeam() );
			$oOptions->add( $oOptionsOr );
		}
		if ( ( $nHomeAway & Voetbal_Game::AWAY ) === Voetbal_Game::AWAY )
		{
			$oOptionsOr = Voetbal_Goal_Factory::createHomeAwayFilters( $this->getAwayPoulePlace()->getTeam(), $this->getHomePoulePlace()->getTeam() );
			$oOptions->add( $oOptionsOr );
		}
		return Voetbal_Goal_Factory::createObjectsFromDatabase( $oOptions );
	}
}