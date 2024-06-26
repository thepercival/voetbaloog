<?php

/**
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Apps_Helper_GetDateTime extends Zend_Controller_Action_Helper_Abstract
{
	public function direct( $sLabel, $bWithTime = true )
	{
		$sDateTimeValue = $this->getRequest()->getParam( $sLabel );
		if ( strlen( $sDateTimeValue ) === 0 )
			return null;
		
		$sDateTimeValue = Agenda_Factory::translate( strtolower( $sDateTimeValue ), false );
		$oDateTime = DateTime::createFromFormat ( Agenda_DateTime::STR_NICEDATE, $sDateTimeValue );
		if ( $oDateTime !== null )
			$sDateTimeValue = $oDateTime->format( Agenda_DateTime::STR_SQLDATE );

		if ( $bWithTime === true )
		{
			$sHour = $this->getRequest()->getParam( $sLabel."hour" );
			$sMinute = $this->getRequest()->getParam( $sLabel."minute" );

			$sDateTimeValue .= " ".$sHour.":".$sMinute.":00";
		}

		return Agenda_Factory::createDateTime( $sDateTimeValue );
	}
}
?>