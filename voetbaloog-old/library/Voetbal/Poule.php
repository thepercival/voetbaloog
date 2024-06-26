<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Poule.php 776 2014-03-05 08:37:12Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Poule extends Agenda_TimeSlot implements Voetbal_Poule_Interface
{
	// Voetbal_Poule_Interface
    protected $m_nNumber;			    // int
	protected $m_sName;				    // string
	protected $m_oPlaces;			    // Collection
	protected $m_oTeams;			    // Collection
	protected $m_oRankedPlaces;		    // Collection
	protected $m_oRound;			    // Voetbal_Round
    protected $m_arrGameRoundStates;	// array

	public function __construct()
	{
		parent::__construct();
	}

    /**
     * @see Voetbal_Poule_Interface::getNumber()
     */
    public function getNumber()
    {
        return $this->m_nNumber;
    }

    /**
     * @see Voetbal_Poule_Interface::putNumber()
     */
    public function putNumber( $nNumber )
    {
        $nNumber = (int) $nNumber;
        if ( $this->m_bObserved === true )
        {
            $oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Poule::Number", $this->m_nNumber, $nNumber );
            $this->notifyObservers( $oObjectChange );
        }
        $this->m_nNumber = $nNumber;
    }

	/**
	 * @see Voetbal_Poule_Interface::getName()
	 */
	public function getName()
	{
		return $this->m_sName;
	}

	/**
	 * @see Voetbal_Poule_Interface::putName()
	 */
	public function putName( $sName )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Poule::Name", $this->m_sName, $sName );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sName = $sName;
	}

    /**
     * @see Voetbal_Poule_Interface::getDisplayName()
     */
    public function getDisplayName( $bWithPrefix )
    {
        $nPreviousNrOfPoules = 0;
        {
            $oCompetitionseason = $this->getRound()->getCompetitionSeason();
            $oRounds = $oCompetitionseason->getRounds();
            foreach( $oRounds as $oRound )
            {
                if ( $oRound === $this->getRound() )
                    break;
                $nPreviousNrOfPoules += $oRound->getPoules()->count();
            }
        }
        $sPouleName = "";
        if ( $bWithPrefix == true )
            $sPouleName = $this->getRound()->getType() == Voetbal_Round::TYPE_KNOCKOUT ? "wed." : "poule";
        $sPouleName .= " " . ( chr( ord( "A" ) + $nPreviousNrOfPoules + $this->getNumber() ) );
        return $sPouleName;
    }

	/**
	 * @see Voetbal_Poule_Interface::getPlaces()
	 */
	public function getPlaces( $nGameStates = Voetbal_Factory::STATE_PLAYED ): Patterns_Collection
	{
		if ( $this->m_oPlaces === null )
		{
			$oFilters = Construction_Factory::createOptions();
			$oFilters->addFilter( "Voetbal_PoulePlace::Poule", "EqualTo", $this );
			$this->m_oPlaces = Voetbal_PoulePlace_Factory::createObjectsFromDatabase( $oFilters );
		}
		return $this->m_oPlaces;
	}

	/**
	 * @see Voetbal_Poule_Interface::getPlaces()
	 */
	public function getPlacesByRank( $nGameStates = Voetbal_Factory::STATE_PLAYED ): Patterns_Collection
	{
		if ( $this->m_oRankedPlaces === null )
		{
			$oGames = $this->getGames();
			Voetbal_Ranking::putPromotionRule( $this->getRound()->getCompetitionSeason()->getPromotionRule() );
			Voetbal_Ranking::putGameStates( $nGameStates );
			Voetbal_Ranking::updatePoulePlaceRankings( $oGames, null );
			$this->m_oRankedPlaces = Voetbal_Ranking::getPoulePlacesByRanking( $oGames, null );
		}
		return $this->m_oRankedPlaces;
	}

	/**
	 * @see Voetbal_Poule_Interface::getTeams()
	 */
	public function getTeams(): Patterns_Collection
	{
		if ( $this->m_oTeams === null )
		{
			$this->m_oTeams = Voetbal_Team_Factory::createObjects();

			$oPlaces = $this->getPlaces();
			foreach ( $oPlaces as $oPlace )
			{
				$oTeam = $oPlace->getTeam();
				if ( $oTeam !== null )
					$this->m_oTeams->add( $oTeam );
			}
		}
		return $this->m_oTeams;
	}

	/**
	 * @see Voetbal_Poule_Interface::getGames()
	 */
	public function getGames(): Patterns_Collection
	{
        $oGames = Voetbal_Game_Factory::createObjects();
        $oPlaces = $this->getPlaces();
        foreach ( $oPlaces as $oPlace ) {
            $oGames->addCollection( $oPlace->getGames() );
        }
		return $oGames;
	}

    /**
     * @see Voetbal_Poule_Interface::getGamesByDate()
     */
    public function getGamesByDate( Construction_Option_Collection $oOptions = null ): Patterns_Collection
    {
        if( $oOptions === null ) {
            $oOptions = Construction_Factory::createOptions();
        }
        $oOptions->addFilter( "Voetbal_Poule::Id", "EqualTo", $this );
        $oOptions->addOrder( "Voetbal_Game::StartDateTime", false );
        $oOptions->addOrder( "Voetbal_Game::ViewOrder", false );
        return Voetbal_Game_Factory::createObjectsFromDatabase( $oOptions );
    }

	/**
	 * @see Voetbal_Poule_Interface::getRound()
	 */
	public function getRound()
	{
		if ( is_int( $this->m_oRound ) )
			$this->m_oRound = Voetbal_Round_Factory::createObjectFromDatabase( $this->m_oRound );

		return $this->m_oRound;
	}

	/**
	 * @see Voetbal_Poule_Interface:: putRound()
	 */
	public function putRound( $oRound )
	{
		$this->m_oRound = $oRound;
	}

	/**
	 * @see Voetbal_Poule_Interface::getState()
	 */
	public function getState()
	{
		$oGames = $this->getGames();

		if ( $oGames->count() === 0 )
		{
			$oPoulePlaces = $this->getPlaces();
			if ( $oPoulePlaces->count() === 1 )
			{
				$oTeam = $oPoulePlaces->first()->getTeam();
				if ( $oTeam !== null )
					return Voetbal_Factory::STATE_PLAYED;
			}
			return Voetbal_Factory::STATE_SCHEDULED;
		}
		else
		{
			foreach ( $oGames as $oGame )
			{
				if ( $oGame->getState() !== Voetbal_Factory::STATE_PLAYED )
					return $oGame->getState();
			}
		}
		return Voetbal_Factory::STATE_PLAYED;
	}

    /**
     * @see Voetbal_Poule_Interface::hasGameRoundState()
     */
    public function hasGameRoundState( int $nGameNumber, int $nState )
    {
        if( $this->m_arrGameRoundStates ===  null ) {
            $this->m_arrGameRoundStates = Voetbal_Game_Factory::getStateGameRounds( $this );
        }
        return array_key_exists( $nGameNumber, $this->m_arrGameRoundStates) && $this->m_arrGameRoundStates[$nGameNumber] === $nState;
    }

	/**
	* @see Voetbal_Poule_Interface::needsRanking()
	*/
	public function needsRanking()
	{
		return ( $this->getPlaces()->count() > 2 );
	}

	/**
	 * @see Agenda_TimeSlot_Interface::getStartDateTime()
	 */
	public function getStartDateTime(): Agenda_DateTime
	{
		if ( $this->m_objStartDateTime === null )
		{
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addLimit( 1 );
			$oGames = $this->getGamesByDate( $oOptions );
			$oGame = $oGames->first();
			if ( $oGame !== null )
				$this->m_objStartDateTime = $oGame->getStartDateTime();
		}
		return $this->m_objStartDateTime;
	}

	/**
	 * @see Agenda_TimeSlot_Interface::getEndDateTime()
	 */
	public function getEndDateTime()
	{
		if ( $this->m_objEndDateTime === null )
		{
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addLimit( 1 );
			$oOptions->addFilter( "Voetbal_Poule::Id", "EqualTo", $this );
			$oOptions->addOrder( "Voetbal_Game::StartDateTime", true );
			$oGame = Voetbal_Game_Factory::createObjectFromDatabase( $oOptions );

			if ( $oGame !== null )
				$this->m_objEndDateTime = Agenda_Factory::createDate( $oGame->getStartDateTime() );
		}
		return $this->m_objEndDateTime;
	}
}