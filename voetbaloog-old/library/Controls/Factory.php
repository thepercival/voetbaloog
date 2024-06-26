<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Factory.php 4554 2019-08-12 14:37:34Z thepercival $
 *
 * @package    Controls
 */

/**
 * @package    Controls
 */
class Controls_Factory implements Controls_Factory_Interface, Patterns_Singleton_Interface
{
	private static $m_objSingleton;

	protected function __construct()
	{
	}

	/**
	 * Defined by Patterns_Singleton_Interface; Prevent to clone the instance
	 *
	 * @see Patterns_Singleton_Interface::__clone()
	 */
    public function __clone()
    {
        trigger_error("Cloning is not allowed.", E_USER_ERROR);
    }

     /**
	 * @see Patterns_Singleton_Interface::getInstance()
	 */
    public static function getInstance()
    {
    	if ( self::$m_objSingleton === null )
        {
        	$MySelf = __CLASS__;
        	self::$m_objSingleton = new $MySelf();
        }
        return self::$m_objSingleton;
    }

    /**
	 * @see Controls_Factory_Interface::createComboBox()
	 */
    public static function createComboBox( $szId )
	{
		$objComboBox = new Controls_ComboBox();
		$objComboBox->putId( $szId );
		return $objComboBox;
	}

	/**
	 *
	 * @see Controls_Factory_Interface::createGroupedComboBox()
	 */
    public static function createGroupedComboBox( $szId )
	{
		$objComboBox = new Controls_ComboBox_Grouped();
		$objComboBox->putId( $szId );
		return $objComboBox;
	}

	/**
	 * @see Controls_Factory_Interface::createCheckBox()
	 */
    public static function createCheckBox( $szId )
	{
		$objCheckBox = new Controls_CheckBox();
		$objCheckBox->putId( $szId );
		return $objCheckBox;
	}

	/**
	 * @see Controls_Factory_Interface::createCheckBoxes()
	 */
    public static function createCheckBoxes( $szId )
	{
		$objCheckBoxes = new Controls_CheckBoxes();
		$objCheckBoxes->putId( $szId );
		return $objCheckBoxes;
	}

	/**
	 * @see Controls_Factory_Interface::createDaysOfWeek()
	 */
	public static function createDaysOfWeek( $szId )
	{
		$objDaysOfWeek = new Controls_DaysOfWeek();
		$objDaysOfWeek->putId( $szId );
  		return $objDaysOfWeek;
	}

	/**
	 * @see Controls_Factory_Interface::replaceObjectProperties()
	 */
	public static function replaceObjectProperties( $objItem, $szValueWithProperty )
	{
		$szReturnValue = $szValueWithProperty;
		$szSpecialValue = "**";

		$nFirstPos = strpos( $szReturnValue, $szSpecialValue );
		if ( $nFirstPos === false )
			return $szReturnValue;
		$nSecondPos = strpos( $szReturnValue, $szSpecialValue, $nFirstPos + 1);

		while ( $nFirstPos !== false and $nSecondPos !== false )
		{
			$szStringToReplace = substr( $szReturnValue, $nFirstPos, ( $nSecondPos - $nFirstPos ) + strlen( $szSpecialValue ) );
			$szObjectProperty = substr( $szStringToReplace, strlen( $szSpecialValue ), strlen( $szStringToReplace ) - ( 2 * strlen( $szSpecialValue ) ) );

			$vtValue = MetaData_Factory::getValue( $objItem, $szObjectProperty );
			if ( $vtValue === null )
				$vtValue = "";

			$szReturnValue = str_replace( $szStringToReplace, $vtValue, $szReturnValue );

			$nFirstPos = strpos( $szReturnValue, $szSpecialValue );
			if ( $nFirstPos === false )
				break;
			$nSecondPos = strpos( $szReturnValue, $szSpecialValue, $nFirstPos + 1 );
		}
		return $szReturnValue;
	}

	public static function toJS( $szValue )
	{
		return str_replace(array('"',"'", "\r", "\n", "\0"), array('\"','\\\'','\r', '\n', '\0'), $szValue );
	}
}