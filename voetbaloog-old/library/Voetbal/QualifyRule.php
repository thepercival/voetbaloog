<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Round.php 919 2014-08-27 17:38:26Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_QualifyRule implements Voetbal_QualifyRule_Interface, Patterns_ObservableObject_Interface, Patterns_Idable_Interface
{
    // Voetbal_QualifyRule_Interface
    protected $m_oFromRound;		// Voetbal_Round
    protected $m_oToRound;			// Voetbal_Round
    protected $m_nConfigNr;			// int
    protected $m_oPoulePlaceRules;  // Patterns_Collection
    protected $m_oFromPoulePlaces;  // Patterns_Collection
    protected $m_oToPoulePlaces;    // Patterns_Collection

    use Patterns_ObservableObject_Trait, Patterns_Idable_Trait;

    /**
     * @see Voetbal_QualifyRule_Interface::getFromRound()
     */
    public function getFromRound()
    {
        if ( is_int( $this->m_oFromRound ) )
            $this->m_oFromRound = Voetbal_Round_Factory::createObjectFromDatabase( $this->m_oFromRound );

        return $this->m_oFromRound;
    }

    /**
     * @see Voetbal_QualifyRule_Interface:: putFromRound()
     */
    public function putFromRound( $oFromRound )
    {
        if ( $this->m_bObserved === true )
        {
            $oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_QualifyRule::FromRound", $this->m_oFromRound, $oFromRound );
            $this->notifyObservers( $oObjectChange );
        }
        $this->m_oFromRound = $oFromRound;
    }

    /**
     * @see Voetbal_QualifyRule_Interface::getToRound()
     */
    public function getToRound()
    {
        if ( is_int( $this->m_oToRound ) )
            $this->m_oToRound = Voetbal_Round_Factory::createObjectFromDatabase( $this->m_oToRound );

        return $this->m_oToRound;
    }

    /**
     * @see Voetbal_QualifyRule_Interface:: putToRound()
     */
    public function putToRound( $oToRound )
    {
        if ( $this->m_bObserved === true )
        {
            $oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_QualifyRule::ToRound", $this->m_oToRound, $oToRound );
            $this->notifyObservers( $oObjectChange );
        }
        $this->m_oToRound = $oToRound;
    }

    /**
     * @see Voetbal_QualifyRule_Interface::getConfigNr()
     */
    public function getConfigNr()
    {
        return $this->m_nConfigNr;
    }

    /**
     * @see Voetbal_QualifyRule_Interface::putConfigNr()
     */
    public function putConfigNr( $nConfigNr )
    {
        $nConfigNr = (int) $nConfigNr;
        if ( $this->m_bObserved === true )
        {
            $oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_QualifyRule::ConfigNr", $this->m_nConfigNr, $nConfigNr );
            $this->notifyObservers( $oObjectChange );
        }
        $this->m_nConfigNr = $nConfigNr;
    }

    /**
     * @see Voetbal_QualifyRule_Interface::getFromPoulePlaces()
     */
    public function getFromPoulePlaces()
    {
        if ( $this->m_oFromPoulePlaces == null ) {
            $this->putFromToPoulePlaces();
        }
        return $this->m_oFromPoulePlaces;
    }

    /**
     * @see Voetbal_QualifyRule_Interface::getFromPoules()
     */
    public function getFromPoules()
    {
        $oFromPoules = Voetbal_Poule_Factory::createObjects();
        $oFromPoulePlaces = $this->getFromPoulePlaces();
        foreach( $oFromPoulePlaces as $oFromPoulePlace )
            $oFromPoules->add( $oFromPoulePlace->getPoule() );
        return $oFromPoules;
    }

    /**
     * @see Voetbal_QualifyRule_Interface::getToPoulePlaces()
     */
    public function getToPoulePlaces()
    {
        if ( $this->m_oToPoulePlaces === null ) {
            $this->putFromToPoulePlaces();
        }
        return $this->m_oToPoulePlaces;
    }

    protected function putFromToPoulePlaces()
    {
        $this->m_oFromPoulePlaces = Voetbal_PoulePlace_Factory::createObjects();
        $this->m_oToPoulePlaces = Voetbal_PoulePlace_Factory::createObjects();

        $oChildren = $this->getPoulePlaceRules();

        foreach( $oChildren as $oChild ) {
            $this->m_oFromPoulePlaces->add( $oChild->getFromPoulePlace() );
            $this->m_oToPoulePlaces->add( $oChild->getToPoulePlace() );
        }
    }

    /**
     * @see Voetbal_QualifyRule_Interface::getPoulePlaceRules()
     */
    public function getPoulePlaceRules()
    {
        if ( $this->m_oPoulePlaceRules === null )
        {
            $oOptions = Construction_Factory::createOptions();
            $oOptions->addFilter( "Voetbal_QualifyRule_PoulePlace::QualifyRule", "EqualTo", $this );
            $this->m_oPoulePlaceRules = Voetbal_QualifyRule_PoulePlace_Factory::createObjectsFromDatabase( $oOptions );
        }
        return $this->m_oPoulePlaceRules;
    }

    /**
     * @see Voetbal_QualifyRule_Interface::getConfig()
     */
    public function getConfig()
    {
        return Voetbal_QualifyRule_Factory::getConfig(
            $this->getFromPoulePlaces()->count(),
            $this->getToPoulePlaces()->count(),
            $this->getConfigNr()
        );
    }

    /**
     * @see Voetbal_QualifyRule_Interface::isSingle()
     */
    public function isSingle()
    {
        return ( $this->getFromPoulePlaces()->count() === 1 and $this->getToPoulePlaces()->count() === 1 );
    }
}