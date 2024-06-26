<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Chart.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Chart
 */

/**
 * @package Chart
 */
abstract class RAD_Chart  //met abstract geef je aan dat hij niet geinstantieerd kan worden en alleen via een child ge-extend kan worden
{
	protected $m_nHeight;
	protected $m_nWidth;

	protected $m_szTitle;
    protected $m_bMarkers;
	protected $m_szAltTitle;
	protected $m_nTextTitleSize;
	protected $m_szTextTitleColor;
	
	public function __construct()
	{
		$this->m_nTextTitleSize = 20;
		$this->m_szTextTitleColor = "000099";
	}
	
	// Houd voor goed zichtbare labels verhouding br : hg = 2,5 : 1 aan (bij 2D pieCharts br : hg = 2 : 1)
	// Of lengte, of breedte invullen => berekend de rest met bovenstaande verhoudingen. Niets invullen: 375 x 150
	// breedte, hoogte br x hg <=300000 anders exception
	public function __set($p_szName, $p_vValue)
	{
		if($p_szName == "height")	            { $this->m_nHeight	= $p_vValue;}
		elseif($p_szName == "width")			{ $this->m_nWidth	= $p_vValue; 		}
		elseif($p_szName == "title")	        { $this->m_szTitle	  = str_replace(" ", "+", $p_vValue) ;}
		elseif($p_szName == "alt")		        { $this->m_szAltTitle	= $p_vValue; 	}
		elseif($p_szName == "markers")          { $this->m_bMarkers	  = $p_vValue;  }
		elseif($p_szName == "nTextTitleSize")   { $this->m_nTextTitleSize     = $p_vValue; }
		elseif($p_szName == "szTextTitleColor") { $this->m_szTextTitleColor   = $p_vValue; }
	}
	
	public function __get($p_szName)
	{
														
	}	
	
	abstract protected function getType(); //met abstract dwing je af dat deze functie in een child geimplementeerd (gedefinieerd) is
	//Je hoeft hier geen { ...} achter de method te zetten (=de implementatie) omdat dit in de childklasse gebeurd

	protected function checkMaxGraphArea()
	{
		$nTotGraphArea = $this->m_nWidth * $this->m_nHeight;
		if( $nTotGraphArea > 300000 )
		{
			$szMessage = "Fout: hoogte x breedte moet kleiner dan 300.000 zijn<br> ";
			$szMessage .= "Ingevoerd: ".$this->m_nWidth." x ".$this->m_nHeight." =  ".$nTotGraphArea."<br>";
			//$szMessage .= "De hoogte x breedte is terugezet naar 800 x 375.<br>";
			throw new Exception( $szMessage, E_ERROR );
		}
	}
	
	public function correctHeightWidth()
	{
		$factorWidth = 2;
		if( $this->getType() == "p3"  ) { $factorWidth = 2.5; } //3D pie
		if( $this->getType() == "p"   ) { $factorWidth = 2;   } //platte, 2D pie
		if( $this->getType() == "pc"  ) { $factorWidth = 2;   } //meerdere ringen om pie
		if( $this->getType() == "bvg" ) { $factorWidth = 2.5; } //barchart
		
		if( $this->m_nHeight == 0 and $this->m_nWidth == 0)
			$this->m_nHeight = 150;
			
		if( $this->m_nWidth > 0 and $this->m_nHeight == 0)
			$this->m_nHeight = round( $this->m_nWidth / $factorWidth );
			
		if( $this->m_nHeight > 0 and $this->m_nWidth == 0)
			$this->m_nWidth = round( $this->m_nHeight * $factorWidth );	
				
	}

	public function writeChart()
	{
		$this->checkMaxGraphArea();
		
		$szAmp = "&amp;";
		$szHTML  = "";
		$szHTML .= "<img ";
		$szHTML .= "  height=\"".$this->m_nHeight."\" "; //hoogte van de image
		$szHTML .= "  width =\"".$this->m_nWidth."\" ";	//breedte van de image
		if( $this->m_szAltTitle === null ) { $this->m_szAltTitle = "-afbeelding grafiek-"; }
		$szHTML .= "  alt = \"". $this->m_szAltTitle."\" ";			//alt-titel van de image
		$szHTML .= "  src=\"http://chart.apis.google.com/chart?";
		
		// -------- type en grootte ------------------------------------------------- //
		$szHTML .= 		"cht=".$this->getType();  			//type grafiek is in abstracte class in Childclass geimplementeerd
		
		$this->correctHeightWidth();
		$szHTML .= 		$szAmp;
		$szHTML .= 		"chs=".$this->m_nWidth;						//breedte van de chart
		$szHTML .= 		   "x".$this->m_nHeight;					//hoogte van de chart
		// -------- type en grootte ------------------------------------------------- //
		
		// -------- titel met opmaak ------------------------------------------------ //
		if( $this->m_szTitle !== null )
		{
			$szHTML .= 		$szAmp;
			$szHTML .= 		"chtt=".$this->m_szTitle;  		//tekst boven de grafiek spaties moeten +tekens zijn
			
			$szHTML .= 		$szAmp;
			$szHTML .= 		"chts=";											//tekstkleur en groote van kop 
			$szHTML .= 		$this->m_szTextTitleColor.",".$this->m_nTextTitleSize;		//tekstkleur zonder #, tekstgrootte
		}
		// -------- titel met opmaak ------------------------------------------------ //

//		if($this->getType() == "p3")
//		{
//			$szHTML .= 	$this->getPieChart();
//		}
//
//		if($this->getType() == "bvg")
//		{
//			$szHTML .= 	$this->getBarChart();
//		}
		
		$szHTML .= "\">";
		return $szHTML;
	}
}
?>