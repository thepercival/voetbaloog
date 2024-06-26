<?php
/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Bar.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Chart
 */

/**
 * @package Chart
 */
class RAD_Chart_Bar extends RAD_Chart
{
	protected $m_arrYdata;
	protected $m_arrXdata;
	protected $m_bMarkers;
	
	protected $m_nTextAxisSize;
	protected $m_szTextAxisColor;
	protected $m_nTextMarkersSize;
	protected $m_szTextMarkersColor;
	
	protected $m_nBarWidth; 
	protected $m_nBarDistance;
	protected $m_nBarGroupDistance;
	 
	protected $m_nMinValueChart;
	protected $m_nMaxValueChart;
	protected $m_nYaxisStepSize;

	protected function getType()  //Dit is de implementatie van de abstracte class die in de parent aangeroepen wordt (protected !!)
	{   
		return "bvg";
	}
	 
	public function __construct()
	{	
		parent::__construct();
		
		// $this->$m_boolMultipleRanges = true;
				
    	$this->m_nTextAxisSize = 10;
		$this->m_szTextAxisColor = "000000";
		
		$this->m_bMarkers = true;
		$this->m_nTextMarkersSize = 9;
		$this->m_szTextMarkersColor = "999999";
		
		$this->m_nBarWidth = "a"; //a = automatisch
		$this->m_nBarDistance = 5;
		$this->m_nBarGroupDistance = 20;
		
		$this->m_nMinValueChart = 0;
		$this->m_nMaxValueChart = 0;
		$this->m_nYaxisStepSize = 20;
		
		$this->m_szAltTitle = $this->m_szTitle;
	}

	public function __set($p_szName, $p_vValue)
	{
		parent::__set($p_szName, $p_vValue);

		if($p_szName == "nTextAxisSize")      { $this->m_nTextAxisSize      = $p_vValue; 	}
		elseif($p_szName == "Xdata")              { $this->m_arrXdata	          = $p_vValue; 	  }
		elseif($p_szName == "Ydata")              { $this->m_arrYdata	          = $p_vValue; 	  }
		
		elseif($p_szName == "markers")            { $this->m_bMarkers	          = $p_vValue;  }
		elseif($p_szName == "nTextMarkersSize")   { $this->m_nTextMarkersSize   = $p_vValue;  }
		elseif($p_szName == "szTextMarkersColor") { $this->m_szTextMarkersColor = $p_vValue;  }
			
		elseif($p_szName == "szTextAxisColor")    { $this->m_szTextAxisColor    = $p_vValue; 	}
		elseif($p_szName == "nBarWidth")          { $this->m_nBarWidth          = $p_vValue; 	}
		elseif($p_szName == "nBarDistance")       { $this->m_nBarDistance       = $p_vValue; 	}
		elseif($p_szName == "nBarGroupDistance")  { $this->m_nBarGroupDistance  = $p_vValue;  }
		elseif($p_szName == "nMinValueChart")     { $this->m_nMinValueChart     = $p_vValue; 	}
		elseif($p_szName == "nMaxValueChart")     { $this->m_nMaxValueChart     = $p_vValue; 	}
		elseif($p_szName == "nYaxisStepSize")     { $this->m_nYaxisStepSize     = $p_vValue; 	}
	}
	
	protected function checkNrOfRanges()
	{
		$nNrOfRanges = -1;
		foreach($this->m_arrYdata as $arrRange)
		{
			if($nNrOfRanges == -1) 
				$nNrOfRanges = count($arrRange["values"]);
			else
				if($nNrOfRanges != count($arrRange["values"]) )
				{
					$szMessage = "Fout: Het aantal waarden in de verschillende ranges is ongelijk (eerste range heeft ".$nNrOfRanges." waarden).";
					throw new Exception( $szMessage, E_ERROR );
				}
		}
		
		if($nNrOfRanges != count($this->m_arrXdata) )
		{
			$szMessage = "Fout: Het aantal labels (".count($this->m_arrXdata).") is niet gelijk aan het aantal ranges (".$nNrOfRanges.").<br>";
			$szMessage .= "Er zijn ".count($this->m_arrXdata)." labels en ".$nNrOfRanges." ranges";
			throw new Exception( $szMessage, E_ERROR );
		}
	}
	
	protected function getMaxRangeGraph()
	{
		$nMax = 0;
		foreach($this->m_arrYdata as $arrRange)
			foreach($arrRange["values"] as $value)
				if($nMax < $value) 
					$nMax = $value; 
		return $nMax;
	}
	
	protected function setMaxRangeGraph()
	{
		if($this->m_nMaxValueChart == 0)
			 $this->m_nMaxValueChart = round( 1.1 * $this->getMaxRangeGraph() );
	}
	
	public function setAxisRanges($p_nMin, $p_nMax, $p_nStep)
	{
		$this->m_nMinValueChart = $p_nMin;
		$this->m_nMaxValueChart = $p_nMax;
		$this->m_nYaxisStepSize = $p_nStep;
	}
	
	public function getBarChart()
	{
		$this->checkNrOfRanges();
		$this->setMaxRangeGraph();
		$szAmp = "&amp;";
		
		//echo "<hr>m_arrXdata=<pre>"; echo var_dump($this->m_arrXdata); echo "</pre><hr>"; 	
		
		// -------- labels X-as ----------------------------------------------------- //		
		$szHTML  = ""	;

		$szHTML .= 	$szAmp;
		$szHTML .= 	"chl=";	
		$szHTML .= 	implode("|", $this->m_arrXdata);
		// -------- labels X-as ----------------------------------------------------- //
		
		// -------- data en labels Y-as --------------------------------------------- //
		$arrDataYranges = array();	
		$arrDataYlabels = array();
		$arrColorsYranges = array();
		$arrMarkersYRanges = array();
		
		$nRange = 0;
		foreach($this->m_arrYdata as $arrRange)
		{
			$arrDataYranges[] = implode(",", $arrRange["values"]);
			$arrDataYlabels[] = $arrRange["name"];
			$arrColorsYranges[]= $arrRange["color"];
			if($this->m_bMarkers === true)
				$arrMarkersYRanges[] = "N,".$this->m_szTextMarkersColor.",".($nRange++).",-1,".$this->m_nTextMarkersSize;
		}
		$szHTML .= 	$szAmp."chd=t:".implode("|", $arrDataYranges    );	
		$szHTML .= 	$szAmp."chdl=" .implode("|", $arrDataYlabels    );		
		
		$szHTML .= 	$szAmp."chco=" .implode(",", $arrColorsYranges  );
		$szHTML .= 	$szAmp."chm="  .implode("|", $arrMarkersYRanges );			
		// -------- data en labels Y-as --------------------------------------------- //
		
							
		// -------- opmaak dataranges ----------------------------------------------- //
		$szHTML .= 			$szAmp;
		$szHTML .= 		"chbh=";												//breedte en afstand van staven en groepen:  ("a" : automatisch!!)
		$szHTML .= 			$this->m_nBarWidth;							//breedte staven
		$szHTML .= 			",";														//datarange-breedte scheidingsteken
		$szHTML .= 			$this->m_nBarDistance;					//afstand staven
		$szHTML .= 			",";														//datarange-breedte scheidingsteken
		$szHTML .= 			$this->m_nBarGroupDistance;			//afstand groepen
		// -------- opmaak dataranges ----------------------------------------------- //
		
		// -------- opmaak grafiek -------------------------------------------------- //
		$szHTML .= 			$szAmp; 					
		$szHTML .= 		"chxt=";												//positie labels (t = x-as bovenaan    r = rechter y-as)
		$szHTML .= 			"x";														//x = x-as onderaan
		$szHTML .= 			",";														//positie labels scheidingsteken
		$szHTML .= 			"y";														//y = linker y-as
		/*
		$szHTML .= 			",";														//positie labels scheidingsteken
		$szHTML .= 			"x";														//x = 2de x-as onderaan
		$szHTML .= 			",";														//positie labels scheidingsteken
		$szHTML .= 			"r";
		*/
		
		$szHTML .= 		$szAmp;
		$szHTML .= 		"chdlp=";											//positionering legenda
		$szHTML .= 			"r";													//letter waar hij wordt uitgelijnd: b = bottom (hor) bv = bottom (ver) 
																									// t = top  tv = top (ver) r = right l = left
		$szHTML .= 		$szAmp;
		$szHTML .= 		"chg=";												//gridlijnen
		$szHTML .= 			"0";													//nr as
		$szHTML .= 			",";													//scheidingsteken
		$szHTML .= 			"10";													//om de 10%
		$szHTML .= 			",";													//scheidingsteken
		$szHTML .= 			"2";													//lijnlengte 2
		$szHTML .= 			",";													//scheidingsteken
		$szHTML .= 			"5";													//spatielengte 5
		// -------- opmaak grafiek -------------------------------------------------- //
		
		// -------- opmaak en positie assen------------------------------------------ //
		$szHTML .= 			$szAmp;
		$szHTML .= 		"chxs=";												//opmaak assen
		$szHTML .= 			"0,".$this->m_szTextAxisColor.",".$this->m_nTextAxisSize;		//as-nummer, kleur label, grootte label
		$szHTML .= 			"|";														//scheidingsteken
		$szHTML .= 			"1,".$this->m_szTextAxisColor.",".$this->m_nTextAxisSize;		//as-nummer, kleur label, grootte label
		/*
		$szHTML .= 			"|";														//scheidingsteken
		$szHTML .= 			"2,009900,14";									//as-nummer, kleur label, grootte label
		
		
		$szHTML .= 		$szAmp;
		$szHTML .= 		"chxp=";												//positionering assen
		$szHTML .= 			"2";													//as nummer
		$szHTML .= 			",";													//scheidingsteken
		$szHTML .= 			"50";													//positie as op 50% (staat onder tekst dus middenonder)
		
		*/
		$szHTML .= 	$szAmp;
		$szHTML .= 	"chds=";												//Schaal grafiek
		$szHTML .= 	$this->m_nMinValueChart.",".$this->m_nMaxValueChart;	//minimum- en maximumwaarde van de grafiek	
		//$szHTML .= 	"0,350";												
		
		$szHTML .= 	$szAmp;
		$szHTML .= 	"chxr=";												//bereik assen
		$szHTML .= 	"1,".$this->m_nMinValueChart.",".$this->m_nMaxValueChart.",".$this->m_nYaxisStepSize;	//y-as, min, maximumwaarde, stapgrootte
		//$szHTML .= 	"1,0,350,20";	
		// -------- opmaak en positie assen------------------------------------------ //
		return $szHTML;
	}
}

?>