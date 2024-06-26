<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Round.php 883 2014-07-01 15:06:15Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Round extends Agenda_TimeSlot implements Voetbal_Round_Interface
{
	// Voetbal_Round_Interface
	protected $m_sName;					// string
	protected $m_oTeams;				// Collection
	protected $m_arrTeamsByPlace;		// Collection
	protected $m_oPoules;				// Collection
	protected $m_oCompetitionSeason;	// Voetbal_CompetitionSeason
	protected $m_nNumber;				// int
	protected $m_bSemiCompetition;		// bool
	protected $m_bNeedsRanking;			// bool
	protected $m_oFromQualifyRules;		// Collection
	protected $m_oToQualifyRules;		// Collection

	CONST TYPE_POULE = 1;
	CONST TYPE_KNOCKOUT = 2;
	CONST TYPE_WINNER = 4;

	CONST MAX_NAME_LENGTH = 10;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @see Voetbal_Round_Interface::getName()
	 */
	public function getName()
	{
		return $this->m_sName;
	}

	/**
	 * @see Voetbal_Round_Interface::putName()
	 */
	public function putName( $sName )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Round::Name", $this->m_sName, $sName );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sName = $sName;
	}

	/**
	 * @see Voetbal_Round_Interface::getDisplayName()
	 */
	public function getDisplayName()
	{
		$oCompetitionseason = $this->getCompetitionSeason();
		$oRounds = $oCompetitionseason->getRounds();

		$nPouleRounds = 0;
		{
			foreach( $oRounds as $oRound )
			{
				if ( $oRound->getType() == Voetbal_Round::TYPE_KNOCKOUT )
					break;
				$nPouleRounds++;
			}
		}

		$sRoundName = "";
		if ( ( $this->getNumber() + 1 ) > $nPouleRounds ) {
			$nFromWinning = $oRounds->count() - ( $this->getNumber() + 1 );
			if ( $nFromWinning == 5 ) { $sRoundName = "<span style='font-size: 80%'><sup>1</sup>&frasl;<sub>16</sub></span> finale"; }
			else if ( $nFromWinning == 4 ) { $sRoundName = "&frac18; finale"; }
			else if ( $nFromWinning == 3 ) { $sRoundName = "&frac14; finale"; }
			else if ( $nFromWinning == 2 ) { $sRoundName = "&frac12; finale"; }
			else if ( $nFromWinning == 1 ) { $sRoundName = "finale"; }
			else if ( $nFromWinning == 0 ) { $sRoundName = "winnaar"; }
		}
		else {
			$sRoundName = ( $this->getNumber() + 1 ) . '<sup>' . ( $this->getNumber() == 0 ? 'st' : 'd' ) . "e</sup> ronde";
		}
		return $sRoundName;
	}

	/**
	 * @see Voetbal_Round_Interface::getPoules()
	 */
	public function getPoules()
	{
		if ( $this->m_oPoules === null )
		{
			$oFilters = Construction_Factory::createOptions();
			$oFilters->addFilter( "Voetbal_Poule::Round", "EqualTo", $this );
            $oFilters->addOrder( "Voetbal_Poule::Number", false );
			$this->m_oPoules = Voetbal_Poule_Factory::createObjectsFromDatabase( $oFilters );
		}
		return $this->m_oPoules;
	}

	/**
	 * @see Voetbal_Round_Interface::getPoulePlaces()
	 */
	public function getPoulePlaces()
	{
		$oPoulePlaces = Voetbal_PoulePlace_Factory::createObjects();

		$oPoules = $this->getPoules();
		foreach ( $oPoules as $oPoule )
			$oPoulePlaces->addCollection( $oPoule->getPlaces() );

		return $oPoulePlaces;
	}

	/**
	 * @see Voetbal_Round_Interface::getTeams()
	 */
	public function getTeams()
	{
		if ( $this->m_oTeams === null )
		{
			$this->m_oTeams = Voetbal_Team_Factory::createObjects();

			$oPoules = $this->getPoules();
			foreach ( $oPoules as $oPoule )
				$this->m_oTeams->addCollection( $oPoule->getTeams() );
		}
		return $this->m_oTeams;
	}

	/**
	 * @see Voetbal_Round_Interface::getTeamsByPlace()
	 */
	public function getTeamsByPlace()
	{
		if ( $this->m_arrTeamsByPlace === null )
		{
			$this->m_arrTeamsByPlace = array();

			$oPoules = $this->getPoules();
			foreach ( $oPoules as $oPoule ) {
                $this->m_arrTeamsByPlace[ $oPoule->getNumber() ] = array();
				$oPlaces = $oPoule->getPlaces();
				foreach ( $oPlaces as $oPlace ) {
                    $this->m_arrTeamsByPlace[ $oPoule->getNumber() ][ $oPlace->getNumber() ] = $oPlace->getTeam();
				}
			}
		}
		return $this->m_arrTeamsByPlace;
	}

	/**
	 * @see Voetbal_Round_Interface::getGames()
	 */
	public function getGames( $bByDate = false, $oOptions = null )
	{
		$oGames = null;

		if ( $bByDate === false )
		{
			$oGames = Voetbal_Game_Factory::createObjects();

			$oPoules = $this->getPoules();
			foreach ( $oPoules as $oPoule )
				$oGames->addCollection( $oPoule->getGames() );
		}
		elseif ( $bByDate === true )
		{
			if ( $oOptions === null )
				$oOptions = Construction_Factory::createOptions();

			$oOptions->addFilter( "Voetbal_Round::Id", "EqualTo", $this );
			$oOptions->addOrder( "Voetbal_Game::StartDateTime", false );
			$oOptions->addOrder( "Voetbal_Game::ViewOrder", false );
			$oGames = Voetbal_Game_Factory::createObjectsFromDatabase( $oOptions );
		}

		return $oGames;
	}

	/**
	 * @see Voetbal_Round_Interface::getCompetitionSeason()
	 */
	public function getCompetitionSeason()
	{
		if ( is_int( $this->m_oCompetitionSeason ) )
			$this->m_oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( $this->m_oCompetitionSeason );

		return $this->m_oCompetitionSeason;
	}

	/**
	 * @see Voetbal_Round_Interface::putCompetitionSeason()
	 */
	public function putCompetitionSeason( $oCompetitionSeason )
	{
		$this->m_oCompetitionSeason = $oCompetitionSeason;
	}

	/**
	 * @see Voetbal_Round_Interface::getNumber()
	 */
	public function getNumber()
	{
		return $this->m_nNumber;
	}

	/**
	 * @see Voetbal_Round_Interface::putNumber()
	 */
	public function putNumber( $nNumber )
	{
		$this->m_nNumber = $nNumber;
	}

	/**
	 * @see Voetbal_Round_Interface::isFirstRound()
	 */
	public function isFirstRound()
	{
		return $this->getNumber() === 0;
	}

	/**
	 * @see Voetbal_Round_Interface::isFirstRound()
	 */
	public function isLastRound()
	{
		return ( $this->getCompetitionSeason()->getRounds()->count() - 1 === $this->getNumber() );
	}

	/**
	 * @see Voetbal_Round_Interface::getSemiCompetition()
	 */
	public function getSemiCompetition()
	{
		return $this->m_bSemiCompetition;
	}

	/**
	 * @see Voetbal_Round_Interface::putSemiCompetition()
	 */
	public function putSemiCompetition( $bSemiCompetition )
	{
        $bSemiCompetition = ( (int) $bSemiCompetition ) === 1;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Round::SemiCompetition", $this->m_bSemiCompetition, $bSemiCompetition );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_bSemiCompetition = $bSemiCompetition;
	}

	/**
	 * @see Voetbal_Round_Interface::getState()
	 */
	public function getState()
	{
		$oPoules = $this->getPoules();

		if ( $oPoules->count() === 0 )
			return false;

		foreach ( $oPoules as $oPoule )
		{
			if ( $oPoule->getState() !== Voetbal_Factory::STATE_PLAYED )
				return $oPoule->getState();
		}
		return Voetbal_Factory::STATE_PLAYED;
	}

	/**
	 * @see Voetbal_Round_Interface::getType()
	 */
	public function getType()
	{
		if ( $this->getPoulePlaces()->count() < 2 )
			return Voetbal_Round::TYPE_WINNER;
		return ( $this->needsRanking() ? Voetbal_Round::TYPE_POULE : Voetbal_Round::TYPE_KNOCKOUT );
	}

	/**
	* @see Voetbal_Round_Interface::needsRanking()
	*/
	public function needsRanking()
	{
		if ( $this->m_bNeedsRanking === null )
		{
			$this->m_bNeedsRanking = false;

			$oPoules = $this->getPoules();
			foreach ( $oPoules as $oPoule )
			{
				if ( $oPoule->needsRanking() === true )
				{
					$this->m_bNeedsRanking = true;
					break;
				}
			}
		}
		return $this->m_bNeedsRanking;
	}

	/**
	 * @see Voetbal_Round_Interface::getFromQualifyRules()
	 */
	public function getFromQualifyRules()
	{
		if ( $this->m_oFromQualifyRules == null ) {
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "Voetbal_QualifyRule::ToRound", "EqualTo", $this );
			$this->m_oFromQualifyRules = Voetbal_QualifyRule_Factory::createObjectsFromDatabase( $oOptions );
		}
		return $this->m_oFromQualifyRules;
	}

	/**
	 * @see Voetbal_Round_Interface::getToQualifyRules()
	 */
	public function getToQualifyRules()
	{
		if ( $this->m_oToQualifyRules == null ) {
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "Voetbal_QualifyRule::FromRound", "EqualTo", $this );
			$this->m_oToQualifyRules = Voetbal_QualifyRule_Factory::createObjectsFromDatabase( $oOptions );
		}
		return $this->m_oToQualifyRules;
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
			$oGames = $this->getGames( true, $oOptions );
			$oGame = $oGames->first();
			if ( $oGame !== null )
				$this->m_objStartDateTime = $oGame->getStartDateTime();
			else if ( $this->isLastRound() )
			{
				$oPreviousRound = $this->getCompetitionSeason()->getPreviousRound( $this );
				if ( $oPreviousRound !== null )
					$this->m_objStartDateTime = $oPreviousRound->getEndDateTime();
			}
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
			$oOptions->addFilter( "Voetbal_Round::Id", "EqualTo", $this );
			$oOptions->addOrder( "Voetbal_Game::StartDateTime", true );
			$oGame = Voetbal_Game_Factory::createObjectFromDatabase( $oOptions );

			if ( $oGame !== null )
				$this->m_objEndDateTime = Agenda_Factory::createDate( $oGame->getStartDateTime() );
			else if ( $this->isLastRound() )
			{
				$oPreviousRound = $this->getCompetitionSeason()->getPreviousRound( $this );
				if ( $oPreviousRound !== null )
					$this->m_objEndDateTime = $oPreviousRound->getEndDateTime();
			}
		}
		return $this->m_objEndDateTime;
	}
}