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
trait Patterns_Idable_Trait
{
    protected $m_vtId;

    /**
     * @see Patterns_Idable_Interface::getId()
     */
    public function getId()
    {
        return $this->m_vtId;
    }

    /**
     * @see Patterns_Idable_Interface::putId()
     */
    public function putId( $vtId )
    {
        $this->m_vtId = $vtId;
    }

    /**
     * Overload of a "php-magic-method"
     *
     * @return  string	the Id
     */
    public function __toString()
    {
        return (string) $this->m_vtId;
    }
}