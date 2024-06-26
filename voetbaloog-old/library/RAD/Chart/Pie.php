<?php
/**
 * Pie.php
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Pie.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 *
 * @package    Chart
 */


/**
 *
 * @package Chart
 */
class RAD_Chart_Pie extends RAD_Chart
{
	protected $m_arrPieData;
	protected $m_arrPieColors;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	protected function getType()  //Dit is de implementatie van de abstracte class die in de parent aangeroepen wordt (protected !!)
	{   
	  return "p3";
	}
	
	// "color" geeft kleur van dat segment. Mag weggelaten worden. Bij ook geen pieColors pakt hij standaard kleuren bruin-geel
	// 
	// Wanneer aantal pieColors>0 dan overruled hij de kleuren uit pieData. Mag weggelaten worden
	// Bij 1 kleur, geeft hij verschillende kleurschakeringen van die tint //$objChart->pieColors = array("0000FF");
	// Bij 2 kleuren, geeft hij een kleurverloop tussen die 2 //$objChart->pieColors = array("FFFF00","FF0000");
	public function __set($p_szName, $p_vValue)
	{
		parent::__set($p_szName, $p_vValue);
		
		if($p_szName == "pieData")			{ $this->m_arrPieData   = $p_vValue; 	}
		elseif($p_szName == "pieColors")	{ $this->m_arrPieColors = $p_vValue; 	}
	}
		
	public function getPieChart()
	{
		//echo "<hr>this->m_arrPieData=<pre>"; echo var_dump($this->m_arrPieData); echo "</pre><hr>"; 	
		$arrValue = array();	
		$arrLabel = array();
		$arrColor = array();
		
		$nRange = 0;
		foreach( $this->m_arrPieData as $arrData )
		{
			if ( array_key_exists( "value", $arrData ) )
				$arrValue[] = $arrData["value"];
			if ( array_key_exists( "label", $arrData ) )
				$arrLabel[] = $arrData["label"];
			if ( array_key_exists( "color", $arrData ) )
				$arrColor[] = $arrData["color"];
		}
		
		//echo "<hr>this->m_arrPieData=<pre>"; echo var_dump($arrValue); echo "</pre><hr>"; 	
		if(count($this->m_arrPieColors) > 0 )
			$arrColor = $this->m_arrPieColors;
		
		$szAmp = "&amp;";	
		$szHTML = 	$szAmp."chd=t:".implode(",", $arrValue );	
		$szHTML .= 	$szAmp."chl="  .implode("|", $arrLabel );		
		$szHTML .= 	$szAmp."chco=" .implode(",", $arrColor );
		 			
		//$szHTML = "&chd=t:10,30,50,70&chl=January|February|March|April&chco=FFFF00,FF0000";
		return $szHTML;
	}
}
?>