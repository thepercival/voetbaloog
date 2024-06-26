<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: BetConfig.php 1202 2020-05-02 09:37:15Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Round_BetConfig extends Agenda_TimeSlot implements VoetbalOog_Round_BetConfig_Interface
{
	// VoetbalOog_Round_BetConfig
	protected $m_oRound;		// VoetbalOog_Round
	protected $m_nBetType;		// int
	protected $m_nBetTime;		// int
	protected $m_nPoints;		// int
	protected $m_oPool;			// VoetbalOog_Pool

	/**
	 * Constructs the class
	 */
	public function __construct() { parent::__construct(); }

	/**
	 * @see VoetbalOog_Round_BetConfig_Interface::getRound()
	 */
	public function getRound()
	{
		if ( is_int( $this->m_oRound ) )
			$this->m_oRound = Voetbal_Round_Factory::createObjectFromDatabase( $this->m_oRound );

		return $this->m_oRound;
	}

	/**
	 * @see VoetbalOog_Round_BetConfig_Interface::putRound()
	 */
	public function putRound( $oRound )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Round_BetConfig::Round", $this->m_oRound, $oRound );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oRound = $oRound;
	}

	/**
	 * @see VoetbalOog_Round_BetConfig_Interface::getBetType()
	 */
	public function getBetType()
	{
		return $this->m_nBetType;
	}

	/**
	 * @see VoetbalOog_Round_BetConfig_Interface::putBetType()
	 */
	public function putBetType( $nBetType )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Round_BetConfig::BetType", $this->m_nBetType, $nBetType );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nBetType = $nBetType;
	}

	/**
	 * @see VoetbalOog_Round_BetConfig_Interface::getBetTime()
	 */
	public function getBetTime()
	{
		return $this->m_nBetTime;
	}

	/**
	 * @see VoetbalOog_Round_BetConfig_Interface::putBetTime()
	 */
	public function putBetTime( $nBetTime )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Round_BetConfig::BetTime", $this->m_nBetTime, $nBetTime );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nBetTime = $nBetTime;
	}

	/**
	 * @see VoetbalOog_Round_BetConfig_Interface::getPoints()
	 */
	public function getPoints()
	{
		return $this->m_nPoints;
	}

	/**
	 * @see VoetbalOog_Round_BetConfig_Interface::putPoints()
	 */
	public function putPoints( $nPoints )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Round_BetConfig::Points", $this->m_nPoints, $nPoints );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nPoints = $nPoints;
	}

	/**
	* @see VoetbalOog_Round_BetConfig_Interface::getDeadLine()
	*/
	public function getDeadLine( $oGame = null )
	{
		// maak hier een query van, die via deze functie is op te vragen!!!!!!!!!!!
		$oDeadLine = null;

		$nBetTime = $this->getBetTime();
		if ( $nBetTime === VoetbalOog_BetTime::$nBeforeStartGame )
			$oDeadLine = $oGame->getStartDateTime();
		else if ( $nBetTime === VoetbalOog_BetTime::$nBeforeStartRound )
			$oDeadLine = $this->getRound()->getStartDateTime();
		else if ( $nBetTime === VoetbalOog_BetTime::$nBeforeStartPreviousRound )
		{
			$oPreviousRound = $this->getRound()->getCompetitionSeason()->getPreviousRound( $this->getRound() );
			if ( $oPreviousRound !== null )
				$oDeadLine = $oPreviousRound->getStartDateTime();
			else
				$oDeadLine = $this->getRound()->getCompetitionSeason()->getStartDateTime();
		}
		else if ( $nBetTime === VoetbalOog_BetTime::$nBeforeCompetitionSeason )
			$oDeadLine = $this->getRound()->getCompetitionSeason()->getStartDateTime();

		return $oDeadLine;
	}

	/**
	 * @see VoetbalOog_Round_BetConfig_Interface::getPool()
	 */
	public function getPool()
	{
		if ( is_int( $this->m_oPool ) )
			$this->m_oPool = VoetbalOog_Pool_Factory::createObjectFromDatabase( $this->m_oPool );

		return $this->m_oPool;
	}

	/**
	 * @see VoetbalOog_Round_BetConfig_Interface::putPool()
	 */
	public function putPool( $oPool )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Round_BetConfig::Pool", $this->m_oPool, $oPool );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oPool = $oPool;
	}

	/**
	 * @see Agenda_TimeSlot_Interface::getStartDateTime()
	 */
	public function getStartDateTime(): Agenda_DateTime
	{
		if ( $this->m_objStartDateTime === null )
		{
			$nBetTime = $this->getBetTime();
			if ( $nBetTime === VoetbalOog_BetTime::$nBeforeStartGame
				or $nBetTime === VoetbalOog_BetTime::$nBeforeStartRound
			)
			{
				$this->m_objStartDateTime = $this->getRound()->getStartDateTime();
			}
			else if ( $nBetTime === VoetbalOog_BetTime::$nBeforeStartPreviousRound )
			{
				$oPreviousRound = $this->getRound()->getCompetitionSeason()->getPreviousRound( $this->getRound() );
				if ( $oPreviousRound != null )
					$this->m_objStartDateTime = $oPreviousRound->getStartDateTime();
				else
					$this->m_objStartDateTime = $this->getRound()->getCompetitionSeason()->getStartDateTime();
			}
			else if ( $nBetTime === VoetbalOog_BetTime::$nBeforeCompetitionSeason )
			{
				$this->m_objStartDateTime = $this->getRound()->getCompetitionSeason()->getStartDateTime();
			}
		}
		return $this->m_objStartDateTime;
	}

	/**
	 * @see Agenda_TimeSlot_Interface::putStartDateTime()
	 */
	public function putStartDateTime( $oDateTime )
	{
		throw new Exception( "Round_BetConfig::StartDateTime will be determined by the bettime!", E_ERROR );
	}

	/**
	 * @see Agenda_TimeSlot_Interface::getEndDateTime()
	 */
	public function getEndDateTime()
	{
		if ( $this->m_objEndDateTime === null )
		{
			$nBetTime = $this->getBetTime();
			if ( $nBetTime === VoetbalOog_BetTime::$nBeforeStartGame
				or $nBetTime === VoetbalOog_BetTime::$nBeforeStartRound
			)
			{
				$this->m_objEndDateTime = $this->getRound()->getEndDateTime();
			}
			else if ( $nBetTime === VoetbalOog_BetTime::$nBeforeStartPreviousRound )
			{
				$oPreviousRound = $this->getRound()->getCompetitionSeason()->getPreviousRound( $this->getRound() );
				if ( $oPreviousRound != null )
					$this->m_objEndDateTime = $oPreviousRound->getEndDateTime();
				else
					$this->m_objEndDateTime = $this->getRound()->getCompetitionSeason()->getEndDateTime();
			}
			else if ( $nBetTime === VoetbalOog_BetTime::$nBeforeCompetitionSeason )
			{
				$this->m_objEndDateTime = $this->getRound()->getCompetitionSeason()->getEndDateTime();
			}
		}
		return $this->m_objEndDateTime;
	}

	/**
	 * @see Agenda_TimeSlot_Interface::putEndDateTime()
	 */
	public function putEndDateTime( $oDateTime )
	{
		throw new Exception( "Round_BetConfig::EndDateTime will be determined by the bettime!", E_ERROR );
	}
}