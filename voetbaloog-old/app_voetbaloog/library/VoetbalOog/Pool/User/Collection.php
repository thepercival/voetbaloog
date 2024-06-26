<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Collection.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		VoetbalOog
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Pool_User_Collection extends Patterns_ObservableObject_Collection
											implements VoetbalOog_Pool_User_Collection_Interface
{
	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Defined by VoetbalOog_UserCompetiotion_User_Collection_Interface; gets the pools
	 *
	 * @see VoetbalOog_UserCompetiotion_User_Collection_Interface::getPools()
	 */
	public function getPools()
	{
		$oPools = VoetbalOog_Pool_Factory::createObjects();
        foreach ( $this as $sPoolUserId => $oPoolUser )
        {
            $sId = $oPoolUser->getPool()->getId();
            $oPool = $oPools[ $sId ];
            if ( $oPool === null )
                $oPools->add( $oPoolUser->getPool() );
        }
		return $oPools;
	}
}