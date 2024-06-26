<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: PoulePlace.php 919 2014-08-27 17:38:26Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_QualifyRule_PoulePlace implements Voetbal_QualifyRule_PoulePlace_Interface, Patterns_ObservableObject_Interface, Patterns_Idable_Interface
{
    // Voetbal_QualifyRule_PoulePlace_Interface
    protected $m_oFromPoulePlace;		// Voetbal_PoulePlace
    protected $m_oToPoulePlace;			// Voetbal_PoulePlace
    protected $m_oQualifyRule;			// Voetbal_QualifyRule

    use Patterns_ObservableObject_Trait, Patterns_Idable_Trait;

    /**
     * @see Voetbal_QualifyRule_PoulePlace_Interface::getFromPoulePlace()
     */
    public function getFromPoulePlace()
    {
        if ( is_int( $this->m_oFromPoulePlace ) )
            $this->m_oFromPoulePlace = Voetbal_PoulePlace_Factory::createObjectFromDatabase( $this->m_oFromPoulePlace );

        return $this->m_oFromPoulePlace;
    }

    /**
     * @see Voetbal_QualifyRule_PoulePlace_Interface:: putFromPoulePlace()
     */
    public function putFromPoulePlace( $oFromPoulePlace )
    {
        if ( $this->m_bObserved === true )
        {
            $oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_QualifyRule_PoulePlace::FromPoulePlace", $this->m_oFromPoulePlace, $oFromPoulePlace );
            $this->notifyObservers( $oObjectChange );
        }
        $this->m_oFromPoulePlace = $oFromPoulePlace;
    }

    /**
     * @see Voetbal_QualifyRule_PoulePlace_Interface::getToPoulePlace()
     */
    public function getToPoulePlace()
    {
        if ( is_int( $this->m_oToPoulePlace ) )
            $this->m_oToPoulePlace = Voetbal_PoulePlace_Factory::createObjectFromDatabase( $this->m_oToPoulePlace );

        return $this->m_oToPoulePlace;
    }

    /**
     * @see Voetbal_QualifyRule_PoulePlace_Interface:: putToPoulePlace()
     */
    public function putToPoulePlace( $oToPoulePlace )
    {
        if ( $this->m_bObserved === true )
        {
            $oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_QualifyRule_PoulePlace::ToPoulePlace", $this->m_oToPoulePlace, $oToPoulePlace );
            $this->notifyObservers( $oObjectChange );
        }
        $this->m_oToPoulePlace = $oToPoulePlace;
    }

    /**
     * @see Voetbal_QualifyRule_PoulePlace_Interface::getQualifyRule()
     */
    public function getQualifyRule()
    {
        if ( is_int( $this->m_oQualifyRule ) )
            $this->m_oQualifyRule = Voetbal_QualifyRule_Factory::createObjectFromDatabase( $this->m_oQualifyRule );

        return $this->m_oQualifyRule;
    }

    /**
     * @see Voetbal_QualifyRule_PoulePlace_Interface:: putQualifyRule()
     */
    public function putQualifyRule( $oQualifyRule )
    {
        if ( $this->m_bObserved === true )
        {
            $oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_QualifyRule_PoulePlace::QualifyRule", $this->m_oQualifyRule, $oQualifyRule );
            $this->notifyObservers( $oObjectChange );
        }
        $this->m_oQualifyRule = $oQualifyRule;
    }
}