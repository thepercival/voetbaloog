<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Goal.php 929 2014-08-31 18:12:20Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Goal implements Voetbal_Goal_Interface, Patterns_ObservableObject_Interface, Patterns_Idable_Interface
{
	// Voetbal_Goal_Interface
	protected $m_oGameParticipation;    	// Voetbal_Game_Participation
	protected $m_nMinute;		    		// int
	protected $m_bOwnGoal;	    			// bool
	protected $m_bPenalty;  				// bool
    protected $m_oAssistGameParticipation;	// Voetbal_Game_Participation

	use Patterns_ObservableObject_Trait, Patterns_Idable_Trait;

	/**
	 * @see Voetbal_Goal_Interface::getGameParticipation()
	 */
	public function getGameParticipation()
	{
		if ( is_int( $this->m_oGameParticipation ) )
			$this->m_oGameParticipation = Voetbal_Game_Participation_Factory::createObjectFromDatabase( $this->m_oGameParticipation );

		return $this->m_oGameParticipation;
	}

	/**
	 * @see Voetbal_Goal_Interface:: putGameParticipation()
	 */
	public function putGameParticipation( $oGameParticipation )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Goal::GameParticipation", $this->m_oGameParticipation, $oGameParticipation );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oGameParticipation = $oGameParticipation;
	}

	/**
	* @see Voetbal_Goal_Interface::getMinute()
	*/
	public function getMinute()
	{
		return $this->m_nMinute;
	}

	/**
	 * @see Voetbal_Goal_Interface::putMinuteMinute()
	 */
	public function putMinute( $nMinute )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Goal::Minute", $this->m_nMinute, $nMinute );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nMinute = $nMinute;
	}

	/**
	 * @see Voetbal_Goal_Interface::getOwnGoal()
	 */
	public function getOwnGoal()
	{
		return $this->m_bOwnGoal;
	}

	/**
	 * @see Voetbal_Goal_Interface::putOwnGoal()
	 */
	public function putOwnGoal( $bOwnGoal )
	{
		$bOwnGoal = ( ( (int) $bOwnGoal ) === 1 );
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Goal::OwnGoal", $this->m_bOwnGoal, $bOwnGoal );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_bOwnGoal = $bOwnGoal;
	}

	/**
	 * @see Voetbal_Goal_Interface::getPenalty()
	 */
	public function getPenalty()
	{
		return $this->m_bPenalty;
	}

	/**
	 * @see Voetbal_Goal_Interface::putPenalty()
	 */
	public function putPenalty( $bPenalty )
	{
		$bPenalty = ( (int) $bPenalty ) === 1;
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Goal::Penalty", $this->m_bPenalty, $bPenalty );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_bPenalty = $bPenalty;
	}

    /**
     * @see Voetbal_Goal_Interface::getAssistGameParticipation()
     */
    public function getAssistGameParticipation()
    {
        if ( is_int( $this->m_oAssistGameParticipation ) )
            $this->m_oAssistGameParticipation = Voetbal_Game_Participation_Factory::createObjectFromDatabase( $this->m_oAssistGameParticipation );

        return $this->m_oAssistGameParticipation;
    }

    /**
     * @see Voetbal_Goal_Interface:: putAssistGameParticipation()
     */
    public function putAssistGameParticipation( $oAssistGameParticipation )
    {
        if ( $this->m_bObserved === true )
        {
            $oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Goal::AssistGameParticipation", $this->m_oAssistGameParticipation, $oAssistGameParticipation );
            $this->notifyObservers( $oObjectChange );
        }
        $this->m_oAssistGameParticipation = $oAssistGameParticipation;
    }
}