<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Qualify.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Bet_Qualify extends VoetbalOog_Bet implements VoetbalOog_Bet_Qualify_Interface
{
	// VoetbalOog_Bet_Qualify_Interface
	protected $m_oPoulePlace;	// Voetbal_PoulePlace_Interface
	protected $m_oTeam;			// Voetbal_Team_Interface
	public static $nId = 1;		// int

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @see VoetbalOog_Bet_Qualify_Interface::getPoulePlace()
	 */
	public function getPoulePlace()
	{
		if ( is_int( $this->m_oPoulePlace ) )
			$this->m_oPoulePlace = Voetbal_PoulePlace_Factory::createObjectFromDatabase( $this->m_oPoulePlace );

		return $this->m_oPoulePlace;
	}

	/**
	 * @see VoetbalOog_Bet_Qualify_Interface::putPoulePlace()
	 */
	public function putPoulePlace( $oPoulePlace )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Bet_Result::PoulePlace", $this->m_oPoulePlace, $oPoulePlace );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oPoulePlace = $oPoulePlace;
	}

	/**
	 * @see VoetbalOog_Bet_Qualify_Interface::getTeam()
	 */
	public function getTeam()
	{
		if ( is_int ( $this->m_oTeam ) )
			$this->m_oTeam = Voetbal_Team_Factory::createObjectFromDatabase( $this->m_oTeam );

		return $this->m_oTeam;
	}

	/**
	 * @see VoetbalOog_Bet_Qualify_Interface::putTeam()
	 */
	public function putTeam( $oTeam )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Bet_Qualify::Team", $this->m_oTeam, $oTeam );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oTeam = $oTeam;
	}

	/**
	 * @see VoetbalOog_Bet_Interface::isCorrect()
	 */
	protected function _isCorrect( $oQualifiedTeams )
	{
		$oTeam = $this->getTeam();
		return ( $oTeam !== null and $oQualifiedTeams[ $oTeam->getId() ] !== null );
	}

	/**
	* @see VoetbalOog_Bet_Interface::getDeadLine()
	*/
	protected function _getDeadLine()
	{
		return $this->getRoundBetConfig()->getDeadLine();
	}

    /**
     * @see VoetbalOog_Bet_Interface::getName()
     */
    public function _getName()
    {
        return VoetbalOog_BetType_Factory::getDescription( static::$nId );
    }
}