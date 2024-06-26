<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: DaysOfWeek.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 *
 * @package    Controls
 */

/**
 * @package    Controls
 */
class Controls_DaysOfWeek implements Controls_Interface, Controls_DaysOfWeek_Interface, Patterns_Idable_Interface
{
	protected $m_nDaysOfWeek;
	protected $m_nAllDaysOfWeek = 127;
	protected $m_objEvents;
	protected $m_objWeek;

	use Patterns_Idable_Trait;

    public function __construct()
  	{
  		$this->m_objWeek = Agenda_Week_Factory::createObjectExt();
  	}

	/**
	 * Defined by Controls_CheckBoxes_Interface; adds an event
	 *
	 * @see Controls_CheckBoxes_Interface::addEvent()
	 */
	public function addEvent( $szEvent )
	{
		if ( $this->m_objEvents === null )
			$this->m_objEvents = Patterns_Factory::createCollection();
		
		$objEvent = Patterns_Factory::createIdable( $szEvent );
		$this->m_objEvents->add( $objEvent );
	}

	/**
	 * @see Controls_Interface::getSource()
	 */
	public function getSource()
	{
		return $this->m_nAllDaysOfWeek;
	}

	/**
	 * @see Controls_Interface::putSource()
	 */
	public function putSource( $objCollection )
	{
		if ( $objCollection === null or is_int( $objCollection ) )
			throw new Exception( "Should be integer!", E_ERROR );
			
		$this->m_nAllDaysOfWeek = $objCollection;
	}
	
	public function putObjectToSelect( $nDaysOfWeek )
	{
		$this->m_nDaysOfWeek = $nDaysOfWeek;
	} 

	/**
	 * Overload of a "php-magic-method"
	 *
	 * @return  string	the Id
	 */
  	public function __toString()
  	{	
  		$szRet = "";
  		
  		try
  		{
  			$szRet .= "<div>";
  			$nWidth = 18;
	  		$objDays = $this->m_objWeek->getDays();
	  		$szMargin = "margin-left:5px;";
	  		foreach( $objDays as $objDay )
	  		{
	  			$szRet .= "<div style=\"float:left; ".$szMargin." width:".$nWidth."px;\">".substr( $objDay->getStartDateTime()->toString("l"), 0, 1 )."</div>";
	  			if ( $szMargin !== null )
	  				$szMargin = null;
	  		}
			$szRet .= "</div>";
			
			$szRet .= "<div style=\"clear:both\"></div>";
			
			$szRet .= "<div>";
	  		foreach( $objDays as $objDay )
	  		{
	  			$szId = $this->getId().$objDay->getStartDateTime()->toString("l");
	  			$szRet .= "<div style=\"float:left; width:".$nWidth."px;\"><input type=\"checkbox\" id=\"".$szId."\" name=\"".$szId."\"></div>";
	  		}	 
			$szRet .= "</div>";
	
			$szRet .= "<div style=\"clear:both\"></div>";
			
			$szRet .= "
				<script type=\"text/javascript\">
					g_arrDaysOfWeekNames = new Array();
			";		
	  		foreach( $objDays as $objDay )
	  		{
	  			$szId = $this->getId().$objDay->getStartDateTime()->toString("l");
	  			$szRet .= "g_arrDaysOfWeekNames[".( $objDay->getStartDateTime()->toValue( Zend_Date::WEEKDAY_8601 ) - 1 )."] = '".$objDay->getStartDateTime()->toString("l")."';";
	  		}		 
			$szRet .= "
					function makeWritableDaysOfWeek( nDaysOfWeek )
					{
						for ( nI = 0 ; nI <= 6 ; nI++ )
						{
							var nCheck = Math.pow( 2, nI );
							if ( ( nDaysOfWeek & nCheck ) == nCheck )
								document.getElementById( '".$this->getId()."' + g_arrDaysOfWeekNames[nI] ).disabled = false;
							else
								document.getElementById( '".$this->getId()."' + g_arrDaysOfWeekNames[nI] ).disabled = true;
						}
					}
	
					function setDaysOfWeek( nDaysOfWeek, bCheck )
					{
						for ( nI = 0 ; nI <= 6 ; nI++ )
						{	
							var nCheck = Math.pow( 2, nI );
							if ( ( nDaysOfWeek & nCheck ) == nCheck && ( bCheck == undefined || bCheck == true ) )
							{
								document.getElementById( '".$this->getId()."' + g_arrDaysOfWeekNames[nI] ).checked = true;
							}
							else if ( ( nDaysOfWeek & nCheck ) != nCheck && ( bCheck == undefined || bCheck == false ) )
							{
								document.getElementById( '".$this->getId()."' + g_arrDaysOfWeekNames[nI] ).checked = false;
							}
						}				   
					}
					
					setDaysOfWeek( ".$this->m_nDaysOfWeek.", undefined );
					//makeWritableDaysOfWeek( ".$this->m_nDaysOfWeek." ); 
				</script>
			";
  		}
		catch( Exception $e )
		{
			$szRet = "DaysOfWeek control geeft exceptie:".$e->getMessage();
		}		
		
		// return htmlspecialchars( $szRet );
		return $szRet;
	}
}