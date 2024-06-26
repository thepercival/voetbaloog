<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Result.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */


/**
 * @package VoetbalOog
 */
class VoetbalOog_Bet_Result extends VoetbalOog_Bet implements VoetbalOog_Bet_Result_Interface
{
	// VoetbalOog_Bet_Result_Interface
	protected $m_oGame;		// Voetbal_Game_Interface
	protected $m_nResult;	// int
	public static $nId = 2;	// int

	/**
	 * Constructs the class
	 */
	public function __construct() {	parent::__construct(); }

	/**
	 * @see VoetbalOog_Bet_Result_Interface::getGame()
	 */
	public function getGame()
	{
		if ( is_int( $this->m_oGame ) )
			$this->m_oGame = Voetbal_Game_Factory::createObjectFromDatabase( $this->m_oGame );

		return $this->m_oGame;
	}

	/**
	 * @see VoetbalOog_Bet_Result_Interface::putGame()
	 */
	public function putGame( $oGame )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Bet_Result::Game", $this->m_oGame, $oGame );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oGame = $oGame;
	}

	/**
	 * @see VoetbalOog_Bet_Result_Interface::getResult()
	 */
	public function getResult()
	{
		return $this->m_nResult;
	}

	/**
	 * @see VoetbalOog_Bet_Result_Interface::putResult()
	 */
	public function putResult( $nResult )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Bet_Result::Result", $this->m_nResult, $nResult );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nResult = $nResult;
	}

	/**
	* @see VoetbalOog_Bet_Interface::isCorrect()
	*/
	protected function _isCorrect( $oObject )
	{
		$nHomeGoals = $oObject->getHomeGoals();
		$nAwayGoals = $oObject->getAwayGoals();
		$nResult = VoetbalOog_Bet_Factory::getResult( $nHomeGoals, $nAwayGoals );
		return ( $this->getResult() === $nResult );
	}

	/**
	 * @see VoetbalOog_Bet_Interface::getDeadLine()
	 */
	protected function _getDeadLine()
	{
		return $this->getRoundBetConfig()->getDeadLine( $this->getGame() );
	}

    /**
     * @see VoetbalOog_Bet_Interface::getName()
     */
    public function _getName()
    {
        return VoetbalOog_BetType_Factory::getDescription( static::$nId );
    }
}
