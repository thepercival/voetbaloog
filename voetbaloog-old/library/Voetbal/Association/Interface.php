<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 580 2013-11-20 15:28:51Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_Association_Interface
{
	/**
	 * gets the Name
	 *
	 * @return 	string	the Name
	 */
	public function getName();
	/**
	 * puts the Name
	 *
	 * @param string $sName the Name which will be set
	 * @return 	null
	 */
	public function putName( $sName );
	/**
	 * gets the Description
	 *
	 * @return 	string	the Description
	 */
	public function getDescription();
	/**
	 * puts the Description
	 *
	 * @param string $sDescription the Description which will be set
	 * @return 	null
	 */
	public function putDescription( $sDescription );
	/**
	 * gets the Teams
	 *
	 * @return 	Patterns_Collection	the Teams
	 */
	public function getTeams(): Patterns_Collection;
}