<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Cache.php 4557 2019-08-12 18:50:59Z thepercival $
 * @since      File available since Release 4.0
 * @package    ZendExt
 */

/**
 * @package ZendExt
 */
class ZendExt_Cache implements Patterns_Singleton_Interface
{
	private static $m_objSingleton;

	protected function __construct() {}

	/**
	 * Defined by Patterns_Singleton_Interface; Prevent users to clone the instance
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
	 *
	 * @param int $nSeconds
	 * @param string $sDir
	 * @param bool|int $bAutoClean Automatically clean expired entries, or a number for 1 in $bAutoClean probability
	 * @return Zend_Cache_Core|Zend_Cache_Frontend_Class
	 */
	public static function getCache( $nSeconds = null /* altijd */, $sDir = null, $bAutoClean = false )
	{
		$frontendOptions = array(
			"caching" => true,
			"cache_id_prefix" => APPLICATION_NAME,
			"lifetime" => $nSeconds,
			"automatic_serialization" => true,
			"automatic_cleaning_factor" => (is_bool($bAutoClean) ? ($bAutoClean ? 1 : 0) : intval($bAutoClean))
		);
		if ( $sDir === null )
			$sDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
		$backendOptions = array(
			"cache_dir" => $sDir,
			"file_name_prefix" => "zend_cache"
		);
		return Zend_Cache::factory( "Core", "File", $frontendOptions, $backendOptions );
	}

	public static function getDefaultCache()
	{
		return static::getCache( null, APPLICATION_PATH . "/cache/", 25 );
	}
}
