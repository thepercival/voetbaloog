<?php

class Zend_View_Helper_ShowWorldMap
{
	public function ShowWorldMap( $oCompetitionSeason )
	{
		// $oCache = ZendExt_Cache::getDefaultCache();
		// $sCacheId = "comptetitionseason".$oCompetitionSeason->getId()."map";
		// $sHtml = $oCache->load( $sCacheId );
		// $sHtml = false;
		// if ( $sHtml === false )
		// {
			$sRegion = "world";
			$oAssocation = $oCompetitionSeason->getAssociation();
			if ( $oAssocation !== null and $oAssocation->getName() === "UEFA" )
				$sRegion = "150";

			$sCountries = "var arrCountries = new Array();";
			$oTeams = $oCompetitionSeason->getTeams();
			foreach ( $oTeams as $oTeam )
			{
				$sCountries .= "arrCountries.push( new Array( '".strtoupper( $oTeam->getImageName() )."') );";
			}

			$nWidth = 350;

			$sHtml = "
				<div style=\"float:left;\" id=\"chart".$oCompetitionSeason->getId()."\">
						<span class=\"glyphicon glyphicon-globe\" style=\"cursor:pointer;\" onclick=\"toggleGraph();\"></span>
				</div>

				<div id=\"visualisationworldmap\" style=\"float:left;\"></div>

				<script type=\"text/javascript\">
					function toggleGraph()
					{
						initWorldMapChart();

						var oDiv = document.getElementById( 'visualisationworldmap' );
						if ( oDiv.style.display == 'block' ) {
							oDiv.style.display = 'none';
							oDiv.style.border = '0px solid green';
						}
						else {
							oDiv.style.display = 'block';
							oDiv.style.border = '1px solid green';
						}
					}

					var g_oWorldMapChart = null;
					function initWorldMapChart()
					{
						if( g_oWorldMapChart == null )
						{
							".$sCountries."
							var data = google.visualization.arrayToDataTable( arrCountries );

							var options = {
								legend : { position : 'none' },
								region : '".$sRegion."',
								width : ".$nWidth.",
								keepAspectRatio : true,
							};

							g_oWorldMapChart = new google.visualization.GeoChart( document.getElementById('visualisationworldmap') );
							g_oWorldMapChart.draw(data, options);
						}
					}

				</script>
			";

			// $oCache->save( $sHtml, $sCacheId, array('comptetitionseason'.$oCompetitionSeason->getId() ) );
		// }
		return $sHtml;
	}
}