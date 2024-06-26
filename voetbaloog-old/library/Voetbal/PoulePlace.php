<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: PoulePlace.php 919 2014-08-27 17:38:26Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_PoulePlace implements Voetbal_PoulePlace_Interface, Patterns_ObservableObject_Interface, Patterns_Idable_Interface
{
	// Voetbal_PoulePlace_Interface
	protected $m_nNumber;					// int
	protected $m_oPoule;					// Voetbal_Poule
	protected $m_oTeam;						// Voetbal_Team
	protected $m_oFromQualifyRule;			// Voetbal_QualifyRule_PoulePlace
	protected $m_oToQualifyRule;			// Voetbal_QualifyRule_PoulePlace
	protected $m_oGames;					// Collection
	protected $m_nRanking;					// int
	protected $m_nPenaltyPoints;			// int
	protected $m_nNrOfPlayedGames;			// int

	use Patterns_ObservableObject_Trait, Patterns_Idable_Trait;

    /**
     * @see Voetbal_PoulePlace_Interface::getDisplayName()
     */
    public function getDisplayName()
    {
        if ( $this->getTeam() !== null )
            return $this->getTeam()->getName();

        $oFromPoulePlaces = null;
        $oFromQualifyRule = $this->getFromQualifyRule();
        if ( $oFromQualifyRule !== null )
            $oFromPoulePlaces = $oFromQualifyRule->getQualifyRule()->getFromPoulePlaces();

        if ( $oFromPoulePlaces !== null )
        {
            $sPoulePlaceName = "";

            if ( !$oFromQualifyRule->getQualifyRule()->isSingle() )
            {
                $arrConfig = $oFromQualifyRule->getQualifyRule()->getConfig();
	            $oTooPoulePlaces = $oFromQualifyRule->getQualifyRule()->getToPoulePlaces();
	            $nIndex = 0;
	            foreach( $oTooPoulePlaces as $oTooPoulePlace ) {
		            if ( $this === $oTooPoulePlace ) { break; }
		                $nIndex++;
	            }
	            $nPouleNumbersPow = $arrConfig["display"][ $nIndex ];
                $nPouleNumber = 0;
                while( pow( 2, $nPouleNumber ) <= $nPouleNumbersPow )
                {
                    $nPouleNrPow = pow( 2, $nPouleNumber );
                    if ( ( $nPouleNrPow & $nPouleNumbersPow ) === $nPouleNrPow ) {
                        $sPoulePlaceName .= ( chr( ord( "A" ) + $nPouleNumber ) );
                    }
                    $nPouleNumber++;
                }

                /*if ( $oFromPoulePlaces->count() > 3 )
                {
                    $oFirstPoulePlace = $oFromPoulePlaces->first();
                    $oLastPoulePlace = $oFromPoulePlaces->getIteratorReversed()->current();
                    $sPoulePlaceName .= $oFirstPoulePlace->getPoule()->getDisplayName( false );
                    $sPoulePlaceName .= "..";
                    $sPoulePlaceName .= $oLastPoulePlace->getPoule()->getDisplayName( false );
                }*/
            }
            else {
                foreach( $oFromPoulePlaces as $oFromPoulePlace ) {
                    $sPoulePlaceName .= $oFromPoulePlace->getPoule()->getDisplayName( false );
                }
            }

            $sPoulePlaceName .= ( $oFromPoulePlaces->first()->getNumber() + 1 );
            return $sPoulePlaceName;
        }
        return ( $this->getNumber() + 1 );

        // 	return $this->getPoule()->getName()." - Nr. ".( $this->getNumber() + 1 );
    }

	/**
	 * @see Voetbal_PoulePlace_Interface::getNumber()
	 */
	public function getNumber()
	{
		return $this->m_nNumber;
	}

	/**
	 * @see Voetbal_PoulePlace_Interface::putNumber()
	 */
	public function putNumber( $nNumber )
	{
		$nNumber = (int) $nNumber;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_PoulePlace::Number", $this->m_nNumber, $nNumber );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nNumber = $nNumber;
	}

	/**
	 * @see Voetbal_PoulePlace_Interface::getPoule()
	 */
	public function getPoule()
	{
		if ( is_int( $this->m_oPoule ) )
			$this->m_oPoule = Voetbal_Poule_Factory::createObjectFromDatabase( $this->m_oPoule );

		return $this->m_oPoule;
	}

	/**
	 * @see Voetbal_PoulePlace_Interface:: putPoule()
	 */
	public function putPoule( $oPoule )
	{
		$this->m_oPoule = $oPoule;
	}

	/**
	 * @see Voetbal_PoulePlace_Interface::getTeam()
	 */
	public function getTeam()
	{
		if ( is_int( $this->m_oTeam ) )
			$this->m_oTeam = Voetbal_Team_Factory::createObjectFromDatabase( $this->m_oTeam );

		return $this->m_oTeam;
	}

	/**
	 * @see Voetbal_PoulePlace_Interface:: putTeam()
	 */
	public function putTeam( $oTeam )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_PoulePlace::Team", $this->m_oTeam, $oTeam );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oTeam = $oTeam;

	}

	/**
	 * @see Voetbal_PoulePlace_Interface::getFromQualifyRule()
	 */
	public function getFromQualifyRule()
	{
		if ( $this->m_oFromQualifyRule == null ) {
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "Voetbal_QualifyRule_PoulePlace::ToPoulePlace", "EqualTo", $this );
			$this->m_oFromQualifyRule = Voetbal_QualifyRule_PoulePlace_Factory::createObjectFromDatabase( $oOptions );
		}
		return $this->m_oFromQualifyRule;
	}

	/**
	 * @see Voetbal_PoulePlace_Interface::getToQualifyRule()
	 */
	public function getToQualifyRule(): ?Voetbal_QualifyRule_PoulePlace
	{
		if ( $this->m_oToQualifyRule === null ) {
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "Voetbal_QualifyRule_PoulePlace::FromPoulePlace", "EqualTo", $this );
			$this->m_oToQualifyRule = Voetbal_QualifyRule_PoulePlace_Factory::createObjectFromDatabase( $oOptions );
		}
		return $this->m_oToQualifyRule;
	}

	/**
	 * @see Voetbal_PoulePlace_Interface:: getGames()
	 */
	public function getGames(): Patterns_Collection
	{
		if ( $this->m_oGames === null )
		{
			$oFilters = Construction_Factory::createOptions();

			$oOrFilters = Construction_Factory::createOptions();
			$oOrFilters->putId( "__OR__" );
			$oOrFilters->addFilter( "Voetbal_Game::HomePoulePlace", "EqualTo", $this );
			$oOrFilters->addFilter( "Voetbal_Game::AwayPoulePlace", "EqualTo", $this );

			$oFilters->add( $oOrFilters );

			$this->m_oGames = Voetbal_Game_Factory::createObjectsFromDatabase( $oFilters );
		}
		return $this->m_oGames;
	}

	/**
	 * @see Voetbal_PoulePlace_Interface::getNrOfPlayedGames()
	 */
	public function getNrOfPlayedGames( $oGames = null, $nGameStates = Voetbal_Factory::STATE_PLAYED )
	{
		if ( $oGames === null )
		{
			if ( $this->m_nNrOfPlayedGames === null )
			{
				$oOptions = Construction_Factory::createOptions();
				$oOptions->addFilter( "Voetbal_Game::State", "BinaryIn", $nGameStates );

				$oOrOptions = Construction_Factory::createOptions();
				$oOrOptions->putId( "__OR__" );
				$oOrOptions->addFilter( "Voetbal_Game::HomePoulePlace", "EqualTo", $this );
				$oOrOptions->addFilter( "Voetbal_Game::AwayPoulePlace", "EqualTo", $this );

				$oOptions->add( $oOrOptions );

				$this->m_nNrOfPlayedGames = Voetbal_Game_Factory::getNrOfObjectsFromDatabase( $oOptions );
			}
			return $this->m_nNrOfPlayedGames;
		}

		$nNrOfPlayedGames = 0;

		foreach ( $oGames as $oGame )
		{
			 if ( ( $oGame->getState() & $nGameStates ) === $oGame->getState()
				and ( $oGame->getHomePoulePlace() === $this or $oGame->getAwayPoulePlace() === $this )
			)
				$nNrOfPlayedGames++;
		}
		return $nNrOfPlayedGames;
	}

	/**
	 * @see Voetbal_PoulePlace_Interface::getPoints()
	 */
	public function getPoints( $oGames = null, $nGameStates = Voetbal_Factory::STATE_PLAYED )
	{
		if ( $oGames === null )
			$oGames = $this->getGames();

		$oCompetitionSeason = $this->getPoule()->getRound()->getCompetitionSeason();

		$nPoints = 0;

		foreach ( $oGames as $oGame )
		{
			if ( ( $oGame->getState() & $nGameStates ) === $oGame->getState() )
			{
				if ( $oGame->getHomePoulePlace() === $this )
				{
					if ( $oGame->getHomeGoalsPenalty() > -1 )
					{
						if ( $oGame->getHomeGoalsPenalty() > $oGame->getAwayGoalsPenalty() )
							$nPoints += $oCompetitionSeason->getWinPointsAfterExtraTime(); // penalty
					}
					else if ( $oGame->getHomeGoalsExtraTime() > -1 )
					{
						if ( $oGame->getHomeGoalsExtraTime() > $oGame->getAwayGoalsExtraTime() )
							$nPoints += $oCompetitionSeason->getWinPointsAfterExtraTime();
					}
					else if ( $oGame->getHomeGoals() > $oGame->getAwayGoals() )
						$nPoints += $oCompetitionSeason->getWinPointsAfterGame();
					elseif ( $oGame->getHomeGoals() == $oGame->getAwayGoals() )
						$nPoints += 1;
				}
				else if ( $oGame->getAwayPoulePlace() === $this )
				{
					if ( $oGame->getHomeGoalsPenalty() > -1 )
					{
						if ( $oGame->getAwayGoalsPenalty() > $oGame->getHomeGoalsPenalty() )
							$nPoints += $oCompetitionSeason->getWinPointsAfterExtraTime(); // penalty
					}
					else if ( $oGame->getHomeGoalsExtraTime() > -1 )
					{
						if ( $oGame->getAwayGoalsExtraTime() > $oGame->getHomeGoalsExtraTime() )
							$nPoints += $oCompetitionSeason->getWinPointsAfterExtraTime();
					}
					else if ( $oGame->getAwayGoals() > $oGame->getHomeGoals() )
						$nPoints += $oCompetitionSeason->getWinPointsAfterGame();
					elseif ( $oGame->getHomeGoals() == $oGame->getAwayGoals() )
						$nPoints += 1;
				}
			}
		}
		return $nPoints;
	}

	/**
	 * @see Voetbal_PoulePlace_Interface::getGoalDifference()
	 */
	public function getGoalDifference( $oGames, $nGameStates = Voetbal_Factory::STATE_PLAYED )
	{
		if ( $oGames === null )
			$oGames = $this->getGames();
		return ( $this->getNrOfGoalsScored( $oGames, $nGameStates ) - $this->getNrOfGoalsReceived( $oGames, $nGameStates ) );
	}

	/**
	 * @see Voetbal_PoulePlace_Interface::getNrOfGoalsScored()
	 */
	public function getNrOfGoalsScored( $oGames, $nGameStates = Voetbal_Factory::STATE_PLAYED )
	{
		if ( $oGames === null )
			$oGames = $this->getGames();

		$nNrOfGoalsScored = 0;
		foreach ( $oGames as $oGame )
		{
			if ( ( $oGame->getState() & $nGameStates ) === $oGame->getState() )
			{
				if ( $oGame->getHomePoulePlace() === $this )
					$nNrOfGoalsScored += $oGame->getHomeGoalsCalc();
				else if ( $oGame->getAwayPoulePlace() === $this )
					$nNrOfGoalsScored += $oGame->getAwayGoalsCalc();
			}
		}
		return $nNrOfGoalsScored;
	}

	/**
	 * @see Voetbal_PoulePlace_Interface::getNrOfGoalsReceived()
	 */
	public function getNrOfGoalsReceived( $oGames, $nGameStates = Voetbal_Factory::STATE_PLAYED )
	{
		if ( $oGames === null )
			$oGames = $this->getGames();

		$nNrOfGoalsReceived = 0;
		foreach ( $oGames as $oGame )
		{
			if ( ( $oGame->getState() & $nGameStates ) === $oGame->getState() )
			{
				if ( $oGame->getHomePoulePlace() === $this )
					$nNrOfGoalsReceived += $oGame->getAwayGoalsCalc();
				else if ( $oGame->getAwayPoulePlace() === $this )
					$nNrOfGoalsReceived += $oGame->getHomeGoalsCalc();
			}
		}
		return $nNrOfGoalsReceived;
	}

	/**
	 * @see Voetbal_PoulePlace_Interface::getRanking()
	 */
	public function getRanking()
	{
		return $this->m_nRanking;
	}

	/**
	 * @see Voetbal_PoulePlace_Interface::putRanking()
	 */
	public function putRanking( $nRanking )
	{
		$this->m_nRanking = $nRanking;
	}

	/**
	* @see Voetbal_PoulePlace_Interface::getPenaltyPoints()
	*/
	public function getPenaltyPoints()
	{
		return $this->m_nPenaltyPoints;
	}

	/**
	 * @see Voetbal_PoulePlace_Interface::putPenaltyPoints()
	 */
	public function putPenaltyPoints( $nPenaltyPoints )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_PoulePlace::PenaltyPoints", $this->m_nPenaltyPoints, $nPenaltyPoints );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nPenaltyPoints = $nPenaltyPoints;
	}
}