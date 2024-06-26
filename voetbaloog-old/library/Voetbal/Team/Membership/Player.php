<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Player.php 993 2015-02-13 20:46:13Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Team_Membership_Player extends Voetbal_Team_Membership implements Voetbal_Team_Membership_Player_Interface
{
	// Voetbal_Team_Membership_Player_Interface
	protected $m_oLine;					// Voetbal_Team_Line
	protected $m_nBackNumber;			// int
	protected $m_oGameDetailsTotals;	// Patterns_Collection

	const MAX_BACKNUMBER = 50;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @see Voetbal_Team_Membership_Player_Interface::getLine()
	 */
	public function getLine()
	{
		if ( is_int( $this->m_oLine ) )
			$this->m_oLine = Voetbal_Team_Factory::createLine( $this->m_oLine );
		return $this->m_oLine;
	}

	/**
	 * @see Voetbal_Team_Membership_Player_Interface::putLine()
	 */
	public function putLine( $oLine )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Team_Membership_Player::Line", $this->m_oLine, $oLine );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oLine = $oLine;
	}

	/**
	* @see Voetbal_Team_Membership_Interface::getBackNumber()
	*/
	public function getBackNumber()
	{
		return $this->m_nBackNumber;
	}

	/**
	 * @see Voetbal_Team_Membership_Interface::putBackNumber()
	 */
	public function putBackNumber( $nBackNumber )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Team_Membership_Player::BackNumber", $this->m_nBackNumber, $nBackNumber );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nBackNumber = $nBackNumber;
	}

	/**
	 * @see Voetbal_Team_Membership_Interface::getGames()
	 */
	public function getGames( $oCompetitionSeason = null )
	{
		$oOptions = Construction_Factory::createOptions();
		if ( $oCompetitionSeason !== null )
			$oOptions->addFilter( "Voetbal_CompetitionSeason::Id", "EqualTo", $oCompetitionSeason );
		$oOptions->addOrder( "Voetbal_Game::StartDateTime", true );
		return Voetbal_Game_Factory::createObjectsFromDatabaseExt( $this, $oOptions, "Voetbal_Team_Membership" );
	}

	/**
	 * @see Voetbal_Team_Membership_Player_Interface::getGameDetails()
	 */
	public function getGameDetails( $oPoule, $vtGameNumberRange = null )
	{
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "Voetbal_PoulePlace::Poule", "EqualTo", $oPoule );

		if( $this->m_oGameDetailsTotals === null ) {
			$this->m_oGameDetailsTotals = Voetbal_Game_Participation_Factory::getDetails( $oOptions, $this, true );
		}

		if ( is_int( $vtGameNumberRange ) ) {
			return $this->m_oGameDetailsTotals[ $vtGameNumberRange ];
		}
		else if ( is_object( $vtGameNumberRange ) ) {
            $arrGameDetailsTotal = [];
		    for( $nGameNumber = $vtGameNumberRange->getStart() ; $nGameNumber <= $vtGameNumberRange->getEnd() ; $nGameNumber++ ) {
                $oGameDetailsTotal = $this->m_oGameDetailsTotals[$nGameNumber];
		        if( $oGameDetailsTotal === null ) {
		            continue;
                }
                $arrGameDetailsTotal[$nGameNumber] = $oGameDetailsTotal;
            }
            return $arrGameDetailsTotal;
		}
		return $this->m_oGameDetailsTotals;
	}

	/**
	 * @see Voetbal_Team_Membership_Interface::getGoals()
	 */
	public function getGoals()
	{
		/*
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "Voetbal_Team_Membership_Player::Client", "EqualTo", $this );
		return Voetbal_Team_Membership_Player_Factory::createObjectsFromDatabase( $oOptions );
		*/
	}
}