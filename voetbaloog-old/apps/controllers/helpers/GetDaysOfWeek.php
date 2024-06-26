<?php 

/**
 * 
 * 
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Apps_Helper_GetDaysOfWeek extends Zend_Controller_Action_Helper_Abstract
{
   public function direct( $szLabel )
    {
    	$nDaysOfWeek = 0;
    	
    	$objDays = Agenda_Week_Factory::createObjectExt()->getDays();
    	foreach( $objDays as $objDay )
	  	{
	  		$nDayOfWeek = $objDay->getStartDateTime()->toValue( Zend_Date::WEEKDAY_8601 ) - 1;
	  		$szValue = $this->getRequest()->getParam( $szLabel.$nDayOfWeek );
    		if ( $szValue === "on")		
				$nDaysOfWeek += pow( 2, $nDayOfWeek );
	  	}	
    	
    	return $nDaysOfWeek;
    }    
}
?>