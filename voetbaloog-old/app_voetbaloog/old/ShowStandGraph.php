<?php

class Zend_View_Helper_ShowStandGraph
{
	protected $m_sGraphDivId;
	protected $m_oNow;

	public function ShowStandGraph( $oPool, $sGraphId )
	{
		$this->m_sGraphDivId = $sGraphId;
		$this->m_oNow = Agenda_Factory::createDateTime();

		try
		{
			$oCache = ZendExt_Cache::getDefaultCache();
			$sCacheId = "pool" . $oPool->getId() . "standgrafiek";
			$sJS = $oCache->load( $sCacheId );
			if( $sJS === false or APPLICATION_ENV !== "production" )
			{
				$sJS = $this->getGrafiek( $oPool );

				$oCache->save( $sJS, $sCacheId, array( 'pool'.$oPool->getId() ) );
			}

			return $sJS;
		}
		catch ( Exception $e )
		{
			return "alert( ".$e->getMessage()." )";
		}
		return null;
	}

	protected function getGrafiek( $oPool )
	{
		$bByRanking = true;
		$oPoolUsers = $oPool->getUsers( $bByRanking );

		$oCompetitionSeason = $oPool->getCompetitionSeason();
		$oRounds = $oCompetitionSeason->getRounds();

		$arrPointsCum = array();
		$sValues = "var arrValues = new Array();";
		// add first row
		{
			$sPoolUsers = "";
			foreach ( $oPoolUsers as $oPoolUser )
			{
				$arrPointsCum[ $oPoolUser->getId() ] = 0;

				if ( strlen( $sPoolUsers ) > 0 )
					$sPoolUsers .= ", ";
				$sPoolUsers .= "'" . $oPoolUser->getUser()->getName() . "'";
			}
			$sValues .= "arrValues.push( new Array( 'Ronden', ".$sPoolUsers." ) );";
		}

		$nHighestValue = 0;

		// add rounds
		foreach ( $oRounds as $oRound )
		{
			if ( $this->m_oNow < $oRound->getStartDateTime() )
				continue;

			$sRoundValues = "'" . $oRound->getName() . "'";
			foreach ( $oPoolUsers as $oPoolUser )
			{
				$arrPointsCum[ $oPoolUser->getId() ] += $oPoolUser->getPoints( $oRound );
				$nPointsCum = $arrPointsCum[ $oPoolUser->getId() ];

				if ( $nPointsCum > $nHighestValue )
					$nHighestValue = $nPointsCum;

				$sRoundValues .= ", " .$nPointsCum;
			}
			$sValues .= "arrValues.push( new Array( ".$sRoundValues." ) );";
		}

		while ( $nHighestValue % 10 !== 0 )
			$nHighestValue++;

		// maximum nr of pixels is 300000 (bv 600x500)
		$nHeight = $nHighestValue * 2;
		if ( ( $oPoolUsers->count() * 25 ) > $nHeight )
			$nHeight = $oPoolUsers->count() * 25;
		$nHeight += 50;

		$nMinHeight = 120;
		$nMaxHeight = 425;
		if ( $nHeight < $nMinHeight )
			$nHeight = $nMinHeight;
		else if ( $nHeight > $nMaxHeight )
			$nHeight = $nMaxHeight;

		$nWidth = (int) ( 300000 / $nHeight );
		$nMinWidth = 400;
		$nMaxWidth = 700;
		if ( $nWidth < $nMinWidth )
			$nWidth = $nMinWidth;
		else if ( $nWidth > $nMaxWidth )
			$nWidth = $nMaxWidth;

		return "
			var g_oStandChart = null;

			function initStandChart()
			{
				if( g_oStandChart == null )
				{
					".$sValues."
					var data = google.visualization.arrayToDataTable( arrValues );
					var options = {
							title: 'Deelnemers-score',
							height : ".$nHeight.",
							width : ".$nWidth.",
							pointSize : 3,
							hAxis : {slantedText: true}
					};

					g_oStandChart = new google.visualization.LineChart(document.getElementById('".$this->m_sGraphDivId."'));
					g_oStandChart.draw(data, options);
				}
			}
		";
	}

}