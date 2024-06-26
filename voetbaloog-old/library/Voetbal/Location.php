<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Location.php 883 2014-07-01 15:06:15Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Location implements Voetbal_Location_Interface, Patterns_ObservableObject_Interface, Patterns_Idable_Interface
{
    // Voetbal_Location_Interface
    protected $m_sName;					// string
    protected $m_oCompetitionSeason;	// Voetbal_CompetitionSeason

    use Patterns_ObservableObject_Trait, Patterns_Idable_Trait;

    CONST MAX_NAME_LENGTH = 10;

    /**
     * @see Voetbal_Location_Interface::getName()
     */
    public function getName()
    {
        return $this->m_sName;
    }

    /**
     * @see Voetbal_Location_Interface::putName()
     */
    public function putName( $sName )
    {
        if ( $this->m_bObserved === true )
        {
            $oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Location::Name", $this->m_sName, $sName );
            $this->notifyObservers( $oObjectChange );
        }
        $this->m_sName = $sName;
    }

    /**
     * @see Voetbal_Location_Interface::getCompetitionSeason()
     */
    public function getCompetitionSeason()
    {
        if ( is_int( $this->m_oCompetitionSeason ) )
            $this->m_oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( $this->m_oCompetitionSeason );

        return $this->m_oCompetitionSeason;
    }

    /**
     * @see Voetbal_Location_Interface::putCompetitionSeason()
     */
    public function putCompetitionSeason( $oCompetitionSeason )
    {
        $this->m_oCompetitionSeason = $oCompetitionSeason;
    }
}