<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Bet.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
abstract class VoetbalOog_Bet implements VoetbalOog_Bet_Interface, Patterns_Idable_Interface, Patterns_ObservableObject_Interface
{
	// VoetbalOog_Bet_Interface
	protected $m_oPoolUser;			// VoetbalOog_Pool_User_Interface
	protected $m_oRoundBetConfig;	// VoetbalOog_Round_BetConfig_Interface
	protected $m_bCorrect;			// bool

	use Patterns_Idable_Trait, Patterns_ObservableObject_Trait;

	public function __construct() {}

	/**
	 * @see VoetbalOog_Bet_Interface::getPoolUser()
	 */
	public function getPoolUser()
	{
		if ( is_int( $this->m_oPoolUser ) )
			$this->m_oPoolUser = VoetbalOog_Pool_User_Factory::createObjectFromDatabase( $this->m_oPoolUser );

		return $this->m_oPoolUser;
	}

	/**
	 * @see VoetbalOog_Bet_Interface::putPoolUser()
	 */
	public function putPoolUser( $oPoolUser )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Bet::PoolUser", $this->m_oPoolUser, $oPoolUser );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oPoolUser = $oPoolUser;
	}

	/**
	 * @see VoetbalOog_Bet_Interface::getRoundBetConfig()
	 */
	public function getRoundBetConfig()
	{
		if ( is_int( $this->m_oRoundBetConfig ) )
			$this->m_oRoundBetConfig = VoetbalOog_Round_BetConfig_Factory::createObjectFromDatabase( $this->m_oRoundBetConfig );

		return $this->m_oRoundBetConfig;
	}

	/**
	 * @see VoetbalOog_Bet_Interface::putRoundBetConfig()
	 */
	public function putRoundBetConfig( $oRoundBetConfig )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Bet::RoundBetConfig", $this->m_oRoundBetConfig, $oRoundBetConfig );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oRoundBetConfig = $oRoundBetConfig;
	}

	/**
	 * @see VoetbalOog_Bet_Interface::getCorrect()
	 */
	public function getCorrect()
	{
		return $this->m_bCorrect;
	}

	/**
	 * @see VoetbalOog_Bet_Interface::putCorrect()
	 */
	public function putCorrect( $bCorrect )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Bet::Correct", $this->m_bCorrect, $bCorrect );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_bCorrect = $bCorrect;
	}

	/**
	* @see VoetbalOog_Bet_Interface::isCorrect()
	*/
	public function isCorrect( $oObject )
	{
		return $this->_isCorrect( $oObject );
	}

	/**
     * @see VoetbalOog_Bet_Interface::getDeadLine()
     */
    public function getName()
    {
        return $this->_getName();
    }

	/**
	 * @see VoetbalOog_Bet_Interface::getPoints()
	 */
	public function getPoints()
	{
		if ( $this->getCorrect() === true )
			return $this->getRoundBetConfig()->getPoints();
		return 0;
	}

	/**
	* @see VoetbalOog_Bet_Interface::getDeadLine()
	*/
	public function getDeadLine()
	{
		return $this->_getDeadLine();
	}

	/**
	* @see VoetbalOog_Bet_Interface::_isCorrect()
	*/
	abstract protected function _isCorrect( $oObject );
	/**
	* @see VoetbalOog_Bet_Interface::isCorrect()
	*/
	abstract protected function _getDeadLine();
    /**
     * @see VoetbalOog_Bet_Interface::isCorrect()
     */
    abstract protected function _getName();
}