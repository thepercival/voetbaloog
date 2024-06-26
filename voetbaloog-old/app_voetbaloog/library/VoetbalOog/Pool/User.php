<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: User.php 1182 2016-07-07 08:44:39Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Pool_User implements VoetbalOog_Pool_User_Interface, Patterns_Idable_Interface, Patterns_ObservableObject_Interface
{
	// VoetbalOog_Pool_User_Interface
	protected $m_oPool;				// VoetbalOog_Pool
	protected $m_oUser;				// VoetbalOog_User
	protected $m_nNrOfBets;			// int
	protected $m_nRanking;			// int
	protected $m_arrPoints;			// array
	protected $m_bAdmin;  			// bool
	protected $m_bPaid;  			// bool
	protected $m_oBets;  			// Collection

	use Patterns_Idable_Trait, Patterns_ObservableObject_Trait;

	/**
	 * @see VoetbalOog_Pool_User_Interface::getPool()
	 */
	public function getPool()
	{
		if ( is_int( $this->m_oPool ) )
			$this->m_oPool = VoetbalOog_Pool_Factory::createObjectFromDatabase( $this->m_oPool );

		return $this->m_oPool;
	}

	/**
	 * @see VoetbalOog_Pool_User_Interface:: putPool()
	 */
	public function putPool( $oPool )
	{
		$this->m_oPool = $oPool;
	}

	/**
	 * @see VoetbalOog_Pool_User_Interface::getUser()
	 */
	public function getUser()
	{
		if ( is_int( $this->m_oUser ) )
			$this->m_oUser = VoetbalOog_User_Factory::createObjectFromDatabase( $this->m_oUser );

		return $this->m_oUser;
	}

	/**
	 * @see VoetbalOog_Pool_User_Interface:: putUser()
	 */
	public function putUser( $oUser )
	{
		$this->m_oUser = $oUser;
	}

	/**
	 * @see VoetbalOog_Pool_User_Interface::getAdmin()
	 */
	public function getAdmin()
	{
		return $this->m_bAdmin;
	}

	/**
	 * @see VoetbalOog_Pool_User_Interface::putAdmin()
	 */
	public function putAdmin( $bAdmin )
	{
		$bAdmin = ( ( (int) $bAdmin ) === 1 );
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Pool_User::Admin", $this->m_bAdmin, $bAdmin );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_bAdmin = $bAdmin;
	}

	/**
	 * @see VoetbalOog_Pool_User_Interface::getPaid()
	 */
	public function getPaid()
	{
		return $this->m_bPaid;
	}

	/**
	 * @see VoetbalOog_Pool_User_Interface::putPaid()
	 */
	public function putPaid( $bPaid )
	{
		$bPaid = ( ( (int) $bPaid ) === 1 );
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Pool_User::Paid", $this->m_bPaid, $bPaid );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_bPaid = $bPaid;
	}

	/**
	 * @see VoetbalOog_Pool_User_Interface::getPoints()
	 */
	public function getPoints( $oRound = null )
	{
		if ( $this->m_arrPoints === null )
			$this->m_arrPoints = VoetbalOog_Bet_Factory::getPoints( $this );
		if ( $oRound === null )
			return $this->m_arrPoints[0];
		if( array_key_exists( $oRound->getId(), $this->m_arrPoints ) === false )
			return 0;
		return $this->m_arrPoints[$oRound->getId()];
	}

	/**
	 * @see VoetbalOog_Pool_User_Interface::getMaxPoints()
	 */
	public function getMaxPoints()
	{
		$nMaxPoints = $this->getPoints();

		$oPool = $this->getPool();
		$oCompetitionSeason = $oPool->getCompetitionSeason();

		$oTeamsInRace = $oCompetitionSeason->getTeamsInTheRace();

		$oRounds = $oCompetitionSeason->getRounds();
		foreach( $oRounds as $oRound )
		{
			$oRoundBetConfigs = $oPool->getBetConfigs( $oRound );
			foreach( $oRoundBetConfigs as $oRoundBetConfig )
			{
				if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Qualify::$nId )
				{
					$oPreviousRound = $oRound->getCompetitionSeason()->getPreviousRound( $oRound );
					if ( $oPreviousRound !== null and $oPreviousRound->getState() === Voetbal_Factory::STATE_PLAYED )
						continue;
				}
				else
				{
					if ( $oRound->getState() === Voetbal_Factory::STATE_PLAYED )
						continue;
				}

				$oBets = $this->getBets( $oRoundBetConfig );
				foreach( $oBets as $oBet )
				{
                    if ( $oBet->getCorrect() ) { continue; }
					if ( $oBet instanceof VoetbalOog_Bet_Qualify ) {
						if ( $oBet->getTeam() !== null and $oTeamsInRace[ $oBet->getTeam()->getId() ] === null ) {
							continue;
						}
					}
					else {
						if ( $oBet->getGame()->getState() === Voetbal_JSON::$nState_Played )
							continue;
					}
					$nMaxPoints += $oRoundBetConfig->getPoints();
				}
			}
		}
		return $nMaxPoints;
	}

	/**
	 * @see VoetbalOog_Pool_User_Interface::getRanking()
	 */
	public function getRanking()
	{
		if ( $this->m_nRanking === null )
		{
			$oUsersByRanking = $this->getPool()->getUsers( true );
			$nRanking = 0;
			$nPreviousPoints = null;
			foreach ( $oUsersByRanking as $oUser )
			{
				$nPoints = $oUser->getPoints();
				if ( $nPreviousPoints === null or $nPoints < $nPreviousPoints )
					$nRanking++;
				$oUser->putRanking( $nRanking );
				$nPreviousPoints = $nPoints;
			}
		}
		return $this->m_nRanking;
	}

	/**
	 * @see VoetbalOog_Pool_User_Interface:: putRanking()
	 */
	public function putRanking( $nRanking )
	{
		$this->m_nRanking = $nRanking;
	}

	/**
	 * @see VoetbalOog_Pool_User_Interface::getNrOfBets()
	 */
	public function getNrOfBets()
	{
		if ( $this->m_nNrOfBets === null )
			$this->m_nNrOfBets = VoetbalOog_Bet_Factory::getNrOfObjectsFromDatabaseExt( $this );
		return $this->m_nNrOfBets;
	}

	/**
	 * @see VoetbalOog_Pool_User_Interface::getNrOfWins()
	 */
	public function getNrOfWins( $bOnlyPrevious = true )
	{
		return VoetbalOog_Pool_User_Factory::getNrOfWins( $this, $bOnlyPrevious );
	}

	/**
	 * @see VoetbalOog_Pool_User_Interface::getBets()
	 */
	public function getBets( $oRoundBetConfig = null )
	{
		if ( $this->m_oBets === null )
			$this->m_oBets = VoetbalOog_Bet_Factory::createObjectsForPoolUserFromDatabase( $this );

		if ( $oRoundBetConfig === null )
			return $this->m_oBets;

		$oBets = $this->m_oBets[ $oRoundBetConfig->getId() ];
		if ( $oBets === null )
		{
			$oRoundBetConfigBets = new Patterns_ObservableObject_Collection_Idable();
			$oRoundBetConfigBets->putId( $oRoundBetConfig->getId() );
			$this->m_oBets->add( $oRoundBetConfigBets );
		}

		return $this->m_oBets[ $oRoundBetConfig->getId() ];
	}
}