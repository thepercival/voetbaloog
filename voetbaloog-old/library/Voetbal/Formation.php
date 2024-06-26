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
class Voetbal_Formation implements Voetbal_Formation_Interface, Patterns_Idable_Interface, JsonSerializable
{
	protected $m_oLines;

	use Patterns_Idable_Trait;

	/**
	 * @see Voetbal_Formation_Interface::getName()
	 */
	public function getName()
	{
		$oLines = $this->getLines();
		$sSeperator = "-";
		$sRetVal = "";
		foreach ( $oLines as $oLine )
			$sRetVal .= $oLine->getNrOfPlayers() . $sSeperator;
		return substr( $sRetVal, 0, strlen( $sRetVal ) - strlen( $sSeperator ) );
	}

	/**
	 * @see Voetbal_Formation_Interface::getLines()
	 */
	public function getLines()
	{
		if ( $this->m_oLines === null ) {
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "Voetbal_Formation_Line::Formation", "EqualTo", $this );
			$this->m_oLines = Voetbal_Formation_Line_Factory::createObjectsFromDatabase( $oOptions );
		}
		return $this->m_oLines;
	}

	/**
	 * @see Voetbal_Formation_Interface::getLinesByNumber()
	 */
	public function getLinesByNumber()
	{
		return Patterns_Factory::createIndex( $this->getLines(), array("Voetbal_Team_Line::Line::Id") );
	}

	/*public function getNrOfPlayers( $vtLine )
	{
		return $this[ $vtLine ];
	}*/

	/**
	 * @see JsonSerializable::jsonSerialize()
	 */
	public function jsonSerialize()
	{
		return array( "name" => $this->getName(), "lines" => $this->getLines() );
	}

}