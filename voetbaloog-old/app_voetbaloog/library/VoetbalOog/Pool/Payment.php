<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Payment.php 1050 2015-12-28 21:03:53Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */


/**
 *
 * @package VoetbalOog
 */
class VoetbalOog_Pool_Payment implements VoetbalOog_Pool_Payment_Interface, Patterns_Idable_Interface, Patterns_ObservableObject_Interface
{
	// VoetbalOog_Pool_User_Interface
	protected $m_oPool;				// VoetbalOog_Pool_Interface
	protected $m_nPlace;			// int
	protected $m_nTimesStake;		// int

	use Patterns_Idable_Trait, Patterns_ObservableObject_Trait;

	/**
	 * @see VoetbalOog_Pool_Payment_Interface::getPool()
	 */
	public function getPool()
	{
		if ( is_int( $this->m_oPool ) )
			$this->m_oPool = VoetbalOog_Pool_Factory::createObjectFromDatabase( $this->m_oPool );

		return $this->m_oPool;
	}

	/**
	 * @see VoetbalOog_Pool_Payment_Interface:: putPool()
	 */
	public function putPool( $oPool )
	{
		$this->m_oPool = $oPool;
	}

	/**
	 *
	 * @see VoetbalOog_Pool_Payment_Interface::getPlace()
	 */
	public function getPlace()
	{
		return $this->m_nPlace;
	}

	/**
	 * @see VoetbalOog_Pool_Payment_Interface:: putPlace()
	 */
	public function putPlace( $nPlace )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Pool_Payment::Place", $this->m_nPlace, $nPlace );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nPlace = $nPlace;
	}

	/**
	 * @see VoetbalOog_Pool_Payment_Interface::getTimesStake()
	 */
	public function getTimesStake()
	{
		return $this->m_nTimesStake;
	}

	/**
	 * @see VoetbalOog_Pool_Payment_Interface:: putTimesStake()
	 */
	public function putTimesStake( $nTimesStake )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Pool_Payment::TimesStake", $this->m_nTimesStake, $nTimesStake );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nTimesStake = $nTimesStake;
	}
}