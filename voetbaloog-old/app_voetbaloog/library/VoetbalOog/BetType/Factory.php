<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 1116 2016-05-19 18:44:12Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_BetType_Factory implements Patterns_Singleton_Interface
{
	private static $m_objSingleton;

	/**
	 * A protected constructor; prevents direct creation of object
	 */
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
			self::$m_objSingleton = new self();
		}
		return self::$m_objSingleton;
	}

	public static function getDescription ( $nBetType )
	{
		if ( $nBetType === VoetbalOog_Bet_Qualify::$nId )
			return 'gekwalificeerden';
		else if ( $nBetType === VoetbalOog_Bet_Result::$nId )
			return 'resultaat';
		else if ( $nBetType === VoetbalOog_Bet_Score::$nId )
			return 'score';
		return null;
	}
}