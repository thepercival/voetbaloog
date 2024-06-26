<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 955 2014-09-15 16:08:29Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_Formation_Line_Interface
{
	/**
	 * gets the Line
	 *
	 * @return 	Voetbal_Team_Line	the Line
	 */
	public function getLine();
	/**
	 * puts the Line
	 *
	 * @param Voetbal_Team_Line 	$oLine  the Line
	 * @return 	null
	 */
	public function putLine( $oLine );
	/**
	 * gets the nrofplayers
	 *
	 * @return 	int	the nrofplayers
	 */
	public function getNrOfPlayers();
	/**
	 * puts the nrofplayers
	 *
	 * @param 	int	$nNrOfPlayers puts the nrofplayers
	 * @return 	null
	 */
	public function putNrOfPlayers( $nNrOfPlayers );
	/**
	 * gets the Formation
	 *
	 * @return 	Voetbal_Formation	the Formation
	 */
	public function getFormation();
	/**
	 * puts the Formation
	 *
	 * @param Voetbal_Formation	    $oFormation     the Formation
	 * @return 	null
	 */
	public function putFormation( $oFormation );

}