<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Membership.php 776 2014-03-05 08:37:12Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Formation_Line implements Voetbal_Formation_Line_Interface, Patterns_Idable_Interface, Patterns_ObservableObject_Interface, JsonSerializable
{
	// Voetbal_Formation_Line_Interface
	protected $m_oLine;		        // Voetbal_Team_Line
	protected $m_nNrOfPlayers;		// int
	protected $m_oFormation;		// Voetbal_Formation
	
    use Patterns_Idable_Trait, Patterns_ObservableObject_Trait;

	/**
	 * @see Voetbal_Formation_Line_Interface::getLine()
	 */
	public function getLine()
	{
		if ( is_int( $this->m_oLine ) )
			$this->m_oLine = Voetbal_Team_Factory::createLine( $this->m_oLine );
		return $this->m_oLine;
	}

	/**
	 * @see Voetbal_Formation_Line_Interface::putLine()
	 */
	public function putLine( $oLine )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Formation_Line::Line", $this->m_oLine, $oLine );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oLine = $oLine;
	}

	/**
	 * @see Voetbal_Formation_Line_Interface::getNrOfPlayers()
	 */
	public function getNrOfPlayers()
	{
		return $this->m_nNrOfPlayers;
	}

	/**
	 * @see Voetbal_Formation_Line_Interface::getNrOfPlayers()
	 */
	public function putNrOfPlayers( $nNrOfPlayers )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Formation_Line::NrOfPlayers", $this->m_nNrOfPlayers, $nNrOfPlayers );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nNrOfPlayers = $nNrOfPlayers;
	}

	/**
	 * @see Voetbal_Formation_Line_Interface::getFormation()
	 */
	public function getFormation()
	{
		if ( is_int( $this->m_oFormation ) )
			$this->m_oFormation = Voetbal_Formation_Factory::createObjectFromDatabase( $this->m_oFormation );
		return $this->m_oFormation;
	}

	/**
	 * @see Voetbal_Formation_Line_Interface::putFormation()
	 */
	public function putFormation( $oFormation )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Formation_Line::Formation", $this->m_oFormation, $oFormation );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oFormation = $oFormation;
	}

	/**
	 * @see JsonSerializable::jsonSerialize()
	 */
	public function jsonSerialize()
	{
		return array( "number" => $this->getLine()->getId(), "nrofplayers" => $this->getNrOfPlayers() );
	}

}