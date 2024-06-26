<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: ObservableObject.php 3853 2013-03-14 15:35:55Z cdunnink $
 *
 * @package    Patterns
 */

/**
 * @package Patterns
 */
trait Patterns_Validatable_Trait
{
    protected $m_oValidatedDateTime;

    /**
     * @see Voetbal_Person_Interface::getValidated()
     */
    public function isValid()
    {
        return $this->getValidatedDateTime() !== null;
    }

    /**
     * @see Patterns_Validatable_Interface::getValidatedDateTime()
     */
    public function getValidatedDateTime()
    {
        return $this->m_oValidatedDateTime;
    }

    /**
     * @see Patterns_Validatable_Interface::putValidatedDateTime()
     */
    public function putValidatedDateTime( $oValidatedDateTime )
    {
        if ( $oValidatedDateTime !== null and is_string( $oValidatedDateTime ) )
            $oValidatedDateTime = Agenda_Factory::createDateTime( $oValidatedDateTime );

        if ( $this->m_bObserved === true )
        {
            $oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::ValidatedDateTime", $this->m_oValidatedDateTime, $oValidatedDateTime );
            $this->notifyObservers( $oObjectChange );
        }
        $this->m_oValidatedDateTime = $oValidatedDateTime;
    }
}