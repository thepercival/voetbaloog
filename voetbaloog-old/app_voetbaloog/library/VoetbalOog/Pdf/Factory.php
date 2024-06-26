<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Pdf_Factory implements Patterns_Singleton_Interface, VoetbalOog_Pdf_Factory_Interface
{
	private static $m_objSingleton;

	protected function __construct(){}

	/**
	 * @see Patterns_Singleton_Interface::__clone()
	 */
	public function __clone()
	{
		trigger_error('Cloning is not allowed.', E_USER_ERROR);
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

	public static function getFont( $bBold = false, $bItalic = false )
	{
		$sFontDir = APPLICATION_PATH . "/library/VoetbalOog/Pdf/Font/";
		if ( $bBold === false and $bItalic === false )
			return Zend_Pdf_Font::fontWithPath( $sFontDir . "times.ttf" );
		if ( $bBold === true and $bItalic === false )
			return Zend_Pdf_Font::fontWithPath( $sFontDir . "timesbd.ttf" );
		else if ( $bBold === false and $bItalic === true )
			return Zend_Pdf_Font::fontWithPath( $sFontDir . "timesi.ttf" );
		else if ( $bBold === true and $bItalic === true )
			return Zend_Pdf_Font::fontWithPath( $sFontDir . "timesbi.ttf" );
	}

	/**
	 * @see VoetbalOog_Pdf_Factory_Interface::createCompetitionSeason()
	 */
	public static function createCompetitionSeason( $oCompetitionSeason )
	{
		return new VoetbalOog_Pdf_Document_CompetitionSeason( $oCompetitionSeason );
	}

	/**
	 * @see VoetbalOog_Pdf_Factory_Interface::createPoolInput()
	 */
	public static function createPoolForm( $oPoolUser )
	{
		return new VoetbalOog_Pdf_Document_PoolForm( $oPoolUser );
	}

	/**
	 * @see VoetbalOog_Pdf_Factory_Interface::createPoolTotal()
	 */
	public static function createPoolTotal( $oPool, $oPoolUser )
	{
		return new VoetbalOog_Pdf_Document_PoolTotal( $oPool, $oPoolUser );
	}
}