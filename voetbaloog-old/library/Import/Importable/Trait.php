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
trait Import_Importable_Trait
{
    protected $m_sExternId;			// string

    /**
     * @see Import_Importable_Interface::getExternId()
     */
    public function getExternId()
    {
        return $this->m_sExternId;
    }

    /**
     * @see Import_Importable_Interface::putExternId()
     */
    public function putExternId( $sExternId )
    {
        if ( $this->m_bObserved === true )
        {
            $objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::ExternId", $this->m_sExternId, $sExternId );
            $this->notifyObservers( $objObjectChange );
        }
        $this->m_sExternId = $sExternId;
    }
}