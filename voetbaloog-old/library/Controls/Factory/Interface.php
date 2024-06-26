<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 *
 * @package    Controls
 */

/**
 *
 *
 * @package    Controls
 */
interface Controls_Factory_Interface
{
	/**
	 * creates a combobox
	 *
	 * @param string $szId The name of the combobox
	 * @return Controls_ComboBox_Interface	A list
	 */
	public static function createComboBox( $szId );
	/**
	 * creates a groupedcombobox
	 *
	 * @param string $szId The name of the combobox
	 * @return Controls_ComboBox_Interface	A list
	 */
	public static function createGroupedComboBox( $szId );
	/**
	 * creates checkbox
	 *
	 * @param string $szId The name of the checkbox
	 * @return Controls_CheckBox_Interface	A checkbox
	 */
	public static function createCheckBox( $szId );
	/**
	 * creates checkboxes
	 *
	 * @param string $szId The name of the checkbox
	 * @return Controls_CheckBoxes_Interface	A checkbox
	 */
	public static function createCheckBoxes( $szId );	
	/**
	 * creates a days of week control
	 *
	 * @param  string	$szId		The id of the panel
	 * @return  Controls_DaysOfWeek_Interface	A Panel
	 */
	public static function createDaysOfWeek( $szId );	
	/**
	 *	Objectproperties staan tussen **, per rij wil je namelijk andere waarden meegeven.
	 *
	 * 	Volgende acties worden uitgevoerd:
	 *	Zoek ** en volgende ** gevonden
	 *	gebruik substring(**RAD_Auth_User::Name**) en $objItem om waarde(Coen Dunnink) op te halen
	 *	vervang substring(**RAD_Auth_User::Name**) door de waarde(Coen Dunnink)
	 *
	 * @param Patterns_Idable_Interface	$objItem				The object to get the value from
	 * @param string			$szValueWithProperty	The property to get
	 * @return string
	 */
	public static function replaceObjectProperties( $objItem, $szValueWithProperty );
	/**
	 * converts chars
	 *
	 * @param  string	$szValue	The value to convert
	 * @return  string	The converted value
	 */
	public static function toJS( $szValue );
}