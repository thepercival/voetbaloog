<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Participation.php 926 2014-08-30 08:53:11Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Game_Participation implements Voetbal_Game_Participation_Interface, Patterns_ObservableObject_Interface, Patterns_Idable_Interface
{
	// Voetbal_Game_Participation_Interface
	protected $m_oGame;					// Voetbal_Game
	protected $m_oTeam;					// Voetbal_Team
	protected $m_oTeamMembershipPlayer;	// Voetbal_Team_Membership_Player
	protected $m_nYellowCardOne;		// int
	protected $m_nYellowCardTwo;		// int
	protected $m_nRedCard;				// int
	protected $m_nIn;					// int
	protected $m_nOut;					// int

	use Patterns_ObservableObject_Trait, Patterns_Idable_Trait;

	/**
	 * @see Voetbal_Game_Participation_Interface::getGame()
	 */
	public function getGame()
	{
		if ( is_int( $this->m_oGame ) )
			$this->m_oGame = Voetbal_Game_Factory::createObjectFromDatabase( $this->m_oGame );

		return $this->m_oGame;
	}

	/**
	 * @see Voetbal_Game_Participation_Interface:: putGame()
	 */
	public function putGame( $oGame )
	{
		$this->m_oGame = $oGame;
	}

	/**
	 * @see Voetbal_Game_Participation_Interface::getTeam()
	 */
	public function getTeam()
	{
		if ( is_int( $this->m_oTeam ) )
			$this->m_oTeam = Voetbal_Team_Factory::createObjectFromDatabase( $this->m_oTeam );

		return $this->m_oTeam;
	}

	/**
	 * @see Voetbal_Game_Participation_Interface::putTeam()
	 */
	public function putTeam( $oTeam )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game_Participation::Team", $this->m_oTeam, $oTeam );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oTeam = $oTeam;
	}

	/**
	* @see Voetbal_Game_Participation_Interface::getTeamMembershipPlayer()
	*/
	public function getTeamMembershipPlayer()
	{
		if ( is_int( $this->m_oTeamMembershipPlayer ) )
			$this->m_oTeamMembershipPlayer = Voetbal_Team_Membership_Player_Factory::createObjectFromDatabase( $this->m_oTeamMembershipPlayer );

		return $this->m_oTeamMembershipPlayer;
	}

	/**
	 * @see Voetbal_Game_Participation_Interface::putTeamMembershipPlayer()
	 */
	public function putTeamMembershipPlayer( $oTeamMembershipPlayer )
	{
        if ( $this->m_bObserved === true )
        {
            $oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game_Participation::TeamMembershipPlayer", $this->m_oTeamMembershipPlayer, $oTeamMembershipPlayer );
            $this->notifyObservers( $oObjectChange );
        }
		$this->m_oTeamMembershipPlayer = $oTeamMembershipPlayer;
	}

	/**
	 * @see Voetbal_Game_Participation_Interface::getYellowCardOne()
	 */
	public function getYellowCardOne()
	{
		return $this->m_nYellowCardOne;
	}

	/**
	 * @see Voetbal_Game_Participation_Interface::putYellowCardOne()
	 */
	public function putYellowCardOne( $nYellowCardOne )
	{
		$nYellowCardOne = (int) $nYellowCardOne;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game_Participation::YellowCardOne", $this->m_nYellowCardOne, $nYellowCardOne );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nYellowCardOne = $nYellowCardOne;
	}

	/**
	 * @see Voetbal_Game_Participation_Interface::getYellowCardTwo()
	 */
	public function getYellowCardTwo()
	{
		return $this->m_nYellowCardTwo;
	}

	/**
	 * @see Voetbal_Game_Participation_Interface::putYellowCardTwo()
	 */
	public function putYellowCardTwo( $nYellowCardTwo )
	{
		$nYellowCardTwo = (int) $nYellowCardTwo;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game_Participation::YellowCardTwo", $this->m_nYellowCardTwo, $nYellowCardTwo );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nYellowCardTwo = $nYellowCardTwo;
	}

	/**
	 * @see Voetbal_Game_Participation_Interface::getRedCard()
	 */
	public function getRedCard()
	{
		return $this->m_nRedCard;
	}

	/**
	 * @see Voetbal_Game_Participation_Interface::putRedCard()
	 */
	public function putRedCard( $nRedCard )
	{
		$nRedCard = (int) $nRedCard;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game_Participation::RedCard", $this->m_nRedCard, $nRedCard );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nRedCard = $nRedCard;
	}

	/**
	 * @see Voetbal_Game_Participation_Interface::getIn()
	 */
	public function getIn()
	{
		return $this->m_nIn;
	}

	/**
	 * @see Voetbal_Game_Participation_Interface::putIn()
	 */
	public function putIn( $nIn )
	{
		$nIn = (int) $nIn;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game_Participation::In", $this->m_nIn, $nIn );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nIn = $nIn;
	}

	/**
	 * @see Voetbal_Game_Participation_Interface::getOut()
	 */
	public function getOut()
	{
		return $this->m_nOut;
	}

	/**
	 * @see Voetbal_Game_Participation_Interface::putOut()
	 */
	public function putOut( $nOut )
	{
		$nOut = (int) $nOut;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Game_Participation::Out", $this->m_nOut, $nOut );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nOut = $nOut;
	}

	/**
	 * @see Voetbal_Game_Participation_Interface::getGoals()
	 */
	public function getGoals()
	{
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter("Voetbal_Goal::GameParticipation", "EqualTo", $this );
		return Voetbal_Goal_Factory::createObjectsFromDatabase( $oOptions );
	}
}