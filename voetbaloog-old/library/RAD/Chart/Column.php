<?php

/*
 $oChart = RAD_Chart_Factory::createBar();

$oChart->size  	= array(600, 300);  //breedte, hoogte br x hg <=300000 anders exception
$oChart->Xdata = array( "Team A", "Team B", "Team C" );
$oChart->Ydata = array(
		array( "name"=>"periode 1", "color"=>"0099FF", "values"=>array(30, 30, 70) ),
		array( "name"=>"periode 2", "color"=>"0099DD", "values"=>array(20, 40, 50) ),
		array( "name"=>"periode 3", "color"=>"0099BB", "values"=>array(25, 30, 60) ),
		array( "name"=>"periode 4", "color"=>"009999", "values"=>array(35, 35, 55) )
);
$oChart->markers =	true;
$oChart->setAxisRanges(15, 0, 10); //min, max, step
$oChart->title  = "Titel van het ding";
$oChart->alt		= "Alt Titel";

echo "<div>";

try
{
echo $oChart->writeChart();
}
catch( Exception $e )
{
echo $e->getMessage();
}

echo "</div>";
*/

/**
 * @copyright  2007 Coen Dunnink
* @license    http://www.gnu.org/licenses/gpl.txt
* @version    $Id: Bar.php 3846 2013-02-19 11:38:05Z cdunnink $
*
* @package    Chart
*/

/**
 * @package Chart
*/
class RAD_Chart_Column extends RAD_Chart_Bar
{
	public function __construct()
	{
		parent::__construct();
	}
}

?>