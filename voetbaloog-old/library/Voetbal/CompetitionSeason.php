<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: CompetitionSeason.php 910 2014-08-22 17:52:25Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_CompetitionSeason extends Agenda_TimeSlot implements Voetbal_CompetitionSeason_Interface, Import_Importable_Interface
{
	// Voetbal_CompetitionSeason_Interface
	protected $m_oCompetition;					// Voetbal_Competition
	protected $m_oSeason;						// Voetbal_Season
	protected $m_sName;							// string
	protected $m_oRounds;						// Collection
	protected $m_bPublic;						// bool
	protected $m_bHasGames;						// bool
	protected $m_bHasGamesWithoutStartDateTime;	// bool
	protected $m_oRoundBetConfigs;				// Collection
	protected $m_arrRoundBetTypes;				// array
	protected $m_oAssociation;					// Voetbal_Association
	protected $m_nPromotionRule;				// int
	protected $m_nNrOfMinutesGame;				// int
	protected $m_nNrOfMinutesExtraTime;			// int
	protected $m_nWinPointsAfterGame;			// int
	protected $m_nWinPointsAfterExtraTime;		// int
	protected $m_oTopscorers;					// Collection
	protected $m_oTeamsInTheRace;				// Collection
    protected $m_oLocations;					// Collection

	use Import_Importable_Trait, Patterns_Validatable_Trait;
	/**
	 * @return Voetbal_CompetitionSeason
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @see Voetbal_CompetitionSeason_Interface::getCompetition()
	 */
	public function getCompetition()
	{
		if ( is_int( $this->m_oCompetition ) )
			$this->m_oCompetition = Voetbal_Competition_Factory::createObjectFromDatabase( $this->m_oCompetition );

		return $this->m_oCompetition;
	}

	/**
	 * {@inheritdoc }
	 */
	public function putCompetition( $oCompetition )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_CompetitionSeason::Competition", $this->m_oCompetition, $oCompetition );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oCompetition = $oCompetition;
	}

	/**
	 * @see Voetbal_CompetitionSeason_Interface::getSeason()
	 */
	public function getSeason()
	{
		if ( is_int( $this->m_oSeason ) )
			$this->m_oSeason = Voetbal_Season_Factory::createObjectFromDatabase( $this->m_oSeason );

		return $this->m_oSeason;
	}

	/**
	 * {@inheritdoc }
	 */
	public function putSeason( $oSeason )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_CompetitionSeason::Season", $this->m_oSeason, $oSeason );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oSeason = $oSeason;
	}

	/**
	 * @see Voetbal_CompetitionSeason_Interface::getName()
	 */
	public function getName()
	{
		return $this->getCompetition()->getName()." ".$this->getSeason()->getName();
	}

	/**
	 * @see Voetbal_CompetitionSeason_Interface::getAbbreviation()
	 */
	public function getAbbreviation()
	{
		return $this->getCompetition()->getAbbreviation()." ".$this->getSeason()->getAbbreviation();
	}

	/**
	 * @see Voetbal_CompetitionSeason_Interface::getRounds()
	 */
	public function getRounds(): Patterns_Collection
	{
		if ( $this->m_oRounds === null )
		{
			$oFilters = Construction_Factory::createOptions();
			$oFilters->addFilter( "Voetbal_Round::CompetitionSeason", "EqualTo", $this );
            $oFilters->addOrder( "Voetbal_Round::Number", false );
			$this->m_oRounds = Voetbal_Round_Factory::createObjectsFromDatabase( $oFilters );
		}
		return $this->m_oRounds;
	}

	/**
	 * @see Voetbal_CompetitionSeason_Interface::getPoules()
	 */
	public function getPoules(): Patterns_Collection
	{
		$oPoules = Voetbal_Poule_Factory::createObjects();

		$oRounds = $this->getRounds();
		foreach ( $oRounds as $oRound )
			$oPoules->addCollection( $oRound->getPoules() );
		return $oPoules;
	}

	/**
	 * {@inheritdoc }
	 */
	public function getGames( $bByDate = false, Construction_Option_Collection $oOptions = null ): Patterns_Collection
	{
		$oGames = null;

		if ( $bByDate === false )
		{
			$oGames = Voetbal_Game_Factory::createObjects();

			$oRounds = $this->getRounds();
			foreach ( $oRounds as $oRound )
				$oGames->addCollection( $oRound->getGames() );
		}
		else if ( $bByDate === true )
		{
			if ( $oOptions === null )
				$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "Voetbal_Round::CompetitionSeason", "EqualTo", $this );
			$oOptions->addOrder( "Voetbal_Game::StartDateTime", false );
			$oGames = Voetbal_Game_Factory::createObjectsFromDatabase( $oOptions );
		}

		return $oGames;
	}

	/**
	 * {@inheritdoc }
	 */
	public function hasGames( $bWithoutStartDateTime = false )
	{
		if ( $bWithoutStartDateTime === false )
		{
			if ( $this->m_bHasGames === null )
			{
				$oOptions = Construction_Factory::createOptions();
				$oOptions->addFilter( "Voetbal_Round::CompetitionSeason", "EqualTo", $this );
				$this->m_bHasGames = ( Voetbal_Game_Factory::getNrOfObjectsFromDatabase( $oOptions ) > 0 );
			}
			return $this->m_bHasGames;
		}
		else
		{
			if ( $this->m_bHasGamesWithoutStartDateTime === null )
			{
				$oOptions = Construction_Factory::createOptions();
				$oOptions->addFilter( "Voetbal_Round::CompetitionSeason", "EqualTo", $this );
				$oOptions->addFilter( "Voetbal_Game::StartDateTime", "EqualTo", null );
				$this->m_bHasGamesWithoutStartDateTime = ( Voetbal_Game_Factory::getNrOfObjectsFromDatabase( $oOptions ) > 0 );
			}
			return $this->m_bHasGamesWithoutStartDateTime;
		}
	}

	/**
	 * {@inheritdoc }
	 */
	public function getPreviousRound( Voetbal_Round $oRound ): ?Voetbal_Round
	{
		$oPreviousRound = null;

		$oRounds = $this->getRounds();
		foreach ( $oRounds as $oRoundIt )
		{
			if ( $oRound === $oRoundIt )
				return $oPreviousRound;

			$oPreviousRound = $oRoundIt;
		}
		return $oPreviousRound;
	}

	/**
	 * {@inheritdoc }
	 */
	public function getNextRound( Voetbal_Round $oRoundToFind ): ?Voetbal_Round
	{
		$oNextRound = null;

		$oRounds = $this->getRounds();
		$bFound = false;
		foreach ( $oRounds as $oRound )
		{
			if ( $bFound === true )
			{
				$oNextRound = $oRound;
				break;
			}
			if ( $oRoundToFind === $oRound )
				$bFound = true;
		}
		return $oNextRound;
	}

	/**
	 * {@inheritdoc }
	 */
	public function getTeams(): Patterns_Collection
	{
		$oTeams = Voetbal_Team_Factory::createObjects();

		$oRounds = $this->getRounds();
		foreach ( $oRounds as $oRound )
			$oTeams->addCollection( $oRound->getTeams() );

		return $oTeams;
	}

	/**
	 * {@inheritdoc }
	 */
	public function getTeamsInTheRace(): Patterns_Collection
	{
		if ( $this->m_oTeamsInTheRace === null )
		{
			$this->m_oTeamsInTheRace = Voetbal_Team_Factory::createObjects();

			$oRoundToCheck = null;
			{
				$oRounds = $this->getRounds();
				foreach( $oRounds as $oRound )
				{
					if ( $oRound->getState() !== Voetbal_Factory::STATE_PLAYED )
					{
						$oRoundToCheck = $oRound;
						break;
					}
				}
			}

			if ( $oRoundToCheck === null )
				return $this->m_oTeamsInTheRace;

			$oPoules = $oRoundToCheck->getPoules();
			foreach( $oPoules as $oPoule )
			{
				$oQualifiedPouleTeams = Voetbal_Team_Factory::createObjects();
				$oPoulePlaces = $oPoule->getPlaces();
				foreach( $oPoulePlaces as $oPoulePlace )
				{
					$oToQualifyRule = $oPoulePlace->getToQualifyRule();
					if ( $oToQualifyRule !== null ) {
                        $oToPoulePlaces = $oToQualifyRule->getQualifyRule()->getToPoulePlaces();
                        foreach( $oToPoulePlaces as $oToPoulePlace )
                        {
                            $oQualifiedPouleTeam = $oToPoulePlace->getTeam();
                            if ( $oQualifiedPouleTeam !== null )
                                $oQualifiedPouleTeams->add( $oQualifiedPouleTeam );
                        }
                    }
				}

				// zoja, voeg alleen de teams uit die poule toe die zich geplaatst hebben
				if ( $oQualifiedPouleTeams->count() > 0 )
					$this->m_oTeamsInTheRace->addCollection( $oQualifiedPouleTeams );
				// zonee, voeg alle teams uit die poule toe
				else
					$this->m_oTeamsInTheRace->addCollection( $oPoule->getTeams() );
			}
		}
		return $this->m_oTeamsInTheRace;
	}

	/**
	 * {@inheritdoc }
	 */
	public function getState()
	{
		$oRounds = $this->getRounds();
		foreach ( $oRounds as $oRound )
		{
			if ( $oRound->getState() !== Voetbal_Factory::STATE_PLAYED )
				return $oRound->getState();
		}
		return Voetbal_Factory::STATE_PLAYED;
	}

	/**
	 * {@inheritdoc }
	 */
	public function getPublic()
	{
		return $this->m_bPublic;
	}

	/**
	 * {@inheritdoc }
	 */
	public function putPublic( $bPublic )
	{
		$bPublic = ( (int) $bPublic ) === 1;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_CompetitionSeason::Public", $this->m_bPublic, $bPublic );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_bPublic = $bPublic;
	}

	/**
	 * {@inheritdoc }
	 */
	public function getAssociation()
	{
		if ( is_int( $this->m_oAssociation ) )
			$this->m_oAssociation = Voetbal_Association_Factory::createObjectFromDatabase( $this->m_oAssociation );

		return $this->m_oAssociation;
	}

	/**
	 * {@inheritdoc }
	 */
	public function putAssociation( $oAssociation )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_CompetitionSeason::Association", $this->m_oAssociation, $oAssociation );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oAssociation = $oAssociation;
	}

	/**
	 * {@inheritdoc }
	 */
	public function getPromotionRule()
	{
		return $this->m_nPromotionRule;
	}

	/**
	 * {@inheritdoc }
	 */
	public function putPromotionRule( $nPromotionRule )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_CompetitionSeason::PromotionRule", $this->m_nPromotionRule, $nPromotionRule );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nPromotionRule = $nPromotionRule;
	}

	/**
	* {@inheritdoc }
	*/
	public function getNrOfMinutesGame()
	{
		return $this->m_nNrOfMinutesGame;
	}

	/**
	 * {@inheritdoc }
	 */
	public function putNrOfMinutesGame( $nNrOfMinutesGame )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_CompetitionSeason::NrOfMinutesGame", $this->m_nNrOfMinutesGame, $nNrOfMinutesGame );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nNrOfMinutesGame = $nNrOfMinutesGame;
	}

	/**
	* {@inheritdoc }
	*/
	public function getNrOfMinutesExtraTime()
	{
		return $this->m_nNrOfMinutesExtraTime;
	}

	/**
	 * {@inheritdoc }
	 */
	public function putNrOfMinutesExtraTime( $nNrOfMinutesExtraTime )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_CompetitionSeason::NrOfMinutesExtraTime", $this->m_nNrOfMinutesExtraTime, $nNrOfMinutesExtraTime );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nNrOfMinutesExtraTime = $nNrOfMinutesExtraTime;
	}

	/**
	* {@inheritdoc }
	*/
	public function getWinPointsAfterGame()
	{
		return $this->m_nWinPointsAfterGame;
	}

	/**
	 * {@inheritdoc }
	 */
	public function putWinPointsAfterGame( $nWinPointsAfterGame )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_CompetitionSeason::WinPointsAfterGame", $this->m_nWinPointsAfterGame, $nWinPointsAfterGame );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nWinPointsAfterGame = $nWinPointsAfterGame;
	}

	/**
	* {@inheritdoc }
	*/
	public function getWinPointsAfterExtraTime()
	{
		return $this->m_nWinPointsAfterExtraTime;
	}

	/**
	 * {@inheritdoc }
	 */
	public function putWinPointsAfterExtraTime( $nWinPointsAfterExtraTime )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_CompetitionSeason::WinPointsAfterExtraTime", $this->m_nWinPointsAfterExtraTime, $nWinPointsAfterExtraTime );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nWinPointsAfterExtraTime = $nWinPointsAfterExtraTime;
	}

	/**
	 * {@inheritdoc }
	 */
	public function getImageName()
	{
		return strtolower( $this->getCompetition()->getAbbreviation() . str_replace("/", "", $this->getSeason()->getName() ) );
	}

	/**
	* {@inheritdoc }
	*/
	public function getTopscorers( $nMaxNrOfPersons = null ): Patterns_Collection
	{
		if ( $this->m_oTopscorers === null )
		{
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "Voetbal_CompetitionSeason::Id", "EqualTo", $this );
			if ( $nMaxNrOfPersons === null )
				$nMaxNrOfPersons = 10;
			$oOptions->addLimit( $nMaxNrOfPersons );
			$this->m_oTopscorers = Voetbal_Person_Factory::getTopscorers( $oOptions );
		}
		return $this->m_oTopscorers;
	}

	/**
	 * {@inheritdoc }
	 */
	public function getStartDateTime(): Agenda_DateTime
	{
		if ( $this->m_objStartDateTime === null )
		{
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addLimit( 1 );
			$oOptions->addFilter( "Voetbal_Game::StartDateTime", "NotEqualTo", null );

			$oGames = $this->getGames( true, $oOptions );
			$oGame = $oGames->first();
			if ( $oGame !== null )
				$this->m_objStartDateTime = $oGame->getStartDateTime();
		}
		return $this->m_objStartDateTime;
	}

	/**
	 * {@inheritdoc }
	 */
	public function putStartDateTime( $oDateTime )
	{
		throw new Exception( "CompetitionSeason::StartDateTime will be determined by the first game!", E_ERROR );
	}

	/**
	 * {@inheritdoc }
	 */
	public function getEndDateTime()
	{
		if ( $this->m_objEndDateTime === null )
		{
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addLimit( 1 );
			$oOptions->addFilter( "Voetbal_Round::CompetitionSeason", "EqualTo", $this );
			$oOptions->addOrder( "Voetbal_Game::StartDateTime", true );
			$oGames = Voetbal_Game_Factory::createObjectsFromDatabase( $oOptions );

			$oGame = $oGames->first();
			if ( $oGame !== null ) {
				$this->m_objEndDateTime = Agenda_Factory::createDate( $oGame->getEndDateTime() );
                $this->m_objEndDateTime->modify("+1 days");
			}
		}
		return $this->m_objEndDateTime;
	}

	/**
	 * {@inheritdoc }
	 */
	public function putEndDateTime( $oDateTime )
	{
		throw new Exception( "CompetitionSeason::EndDateTime will be determined by the last game!", E_ERROR );
	}

    /**
     * {@inheritdoc }
     */
    public function getLocations(): Patterns_Collection
    {
        if ( $this->m_oLocations === null )
        {
            $oOptions = Construction_Factory::createOptions();
            $oOptions->addFilter( "Voetbal_Location::CompetitionSeason", "EqualTo", $this );
            $this->m_oLocations = Voetbal_Location_Factory::createObjectsFromDatabase( $oOptions );
        }
        return $this->m_oLocations;
    }

    /**
     * {@inheritdoc }
     */
    public function getDefaultPoule(): Voetbal_Poule
    {
        return $this->getRounds()->first()->getPoules()->first();
    }
}