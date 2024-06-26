<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Debug.php 4181 2015-05-18 21:39:48Z thepercival $
 * @package    Tools
 */

/**
 * @package    Tools
 */
class RAD_Tools_Debug
{
	protected $m_objStopWatch;
	protected $m_objProfiler;

    public function __construct( $db = null )
    {
    	$this->m_objStopWatch =new RAD_Tools_Stopwatch();
    	$this->m_objStopWatch->Start();
    	if ( $db !== null )
    	{
    		$this->m_objProfiler = $db->getProfiler();
    		$db->getProfiler()->setEnabled(true);
    	}
    }

    public function __toString()
    {
    	$szTitleHtml = "";
    	$szTimeHtml = "";
    	$szMemHtml = "";
    	$szDbHtml = "";

    	{
			$szTitleHtml .= "<div style=\"background-color:blue; text-align:center; color:white; font-size:200%; margin-top:15px;\">DEBUG INFO</div>";
		}

		{
			$szTimeHtml .= "<div style=\"background-color:yellow; color:black; width:100%;\">";
			$szTimeHtml .= "<div style=\"float:left; width:40%;\">Totale tijd</div>";
			$szTimeHtml .= "<div style=\"float:left;\">".$this->m_objStopWatch->Display()."</div>";
			$szTimeHtml .= "<div style=\"clear:both;\"></div>";
			$szTimeHtml .= "</div>";
			$this->m_objStopWatch->Stop();
		}

		{
			$szMemHtml .= "<div style=\"background-color:orange; color:black; width:100%;\">";
			$szMemHtml .= "<div style=\"float:left; width:40%;\">Max geheugenverbruik</div>";
			$szMemHtml .= "<div style=\"float:left;\">".number_format(memory_get_peak_usage(), 0, ",", ".")."</div>";
			$szMemHtml .= "<div style=\"clear:both;\"></div>";
			$szMemHtml .= "</div>";
		}

		if ( $this->m_objProfiler !== null )
		{
			$objQueries = $this->m_objProfiler->getQueryProfiles();
			if ( $objQueries !== false )
			{
				$totalTime    = $this->m_objProfiler->getTotalElapsedSecs();
				$queryCount   = $this->m_objProfiler->getTotalNumQueries();
				$longestTime  = 0;
				$longestQuery = null;

				foreach ( $objQueries as $query) {
				    if ($query->getElapsedSecs() > $longestTime) {
				        $longestTime  = $query->getElapsedSecs();
				        $longestQuery = $query->getQuery();
				    }
				}



				$szDbHtml .= "<div style=\"background-color:lime; color:black; width:100%;\">";

				$szDbHtml .= "<div style=\"float:left; width:40%;\">Executed</div>";
				$szDbHtml .= "<div style=\"float:left;\">". $queryCount . " queries in " . $totalTime . " seconds</div>";
				$szDbHtml .= "<div style=\"clear:both;\"></div>";

				$szDbHtml .= "<div style=\"float:left; width:40%;\">Average query length</div>";
				$szDbHtml .= "<div style=\"float:left;\">" . $totalTime / $queryCount . " seconds</div>";
				$szDbHtml .= "<div style=\"clear:both;\"></div>";

				$sQueriesPerSecond = $totalTime;
				if ( $totalTime > 0 )
					$sQueriesPerSecond = $queryCount / $totalTime;
				$szDbHtml .= "<div style=\"float:left; width:40%;\">Queries per second</div>";
				$szDbHtml .= "<div style=\"float:left;\">" . $sQueriesPerSecond . "</div>";
				$szDbHtml .= "<div style=\"clear:both;\"></div>";

				$szDbHtml .= "<div style=\"float:left; width:40%;\">Longest query time</div>";
				$szDbHtml .= "<div style=\"float:left;\">" . $longestTime . "</div>";
				$szDbHtml .= "<div style=\"clear:both;\"></div>";

				$szDbHtml .= "<div style=\"float:left; width:40%;\">Longest query</div>";
				$szDbHtml .= "<div style=\"float:left;\">" . $longestQuery . "</div>";
				$szDbHtml .= "<div style=\"clear:both;\"></div>";

				$szDbHtml .= "<div>&nbsp;</div>";
				$szDbHtml .= "<div>Queries:</div>";
				$szDbHtml .= "<table style=\"background-color:white; color:black;\">";
				foreach ( $objQueries as $query)
				{
					$szConvertedQuery = $this->convertQuery( $query->getQuery() );
					$nQueryType = $query->getQueryType();
					$szTableName = "";
					if ( $nQueryType === 32 )
					{
						$nPos = strpos( $szConvertedQuery, "FROM " );
						$nStartPos = strpos( $szConvertedQuery, " ", $nPos + 1 );
						$nEndPos = strpos( $szConvertedQuery, " ", $nStartPos + 1 );
						if ( $nEndPos === false )
							$nEndPos = strlen( $szConvertedQuery );

							$szTableName = substr( $szConvertedQuery, $nStartPos, $nEndPos - $nStartPos);
					}
					$szDbHtml .= "
						<tr>
							<td style=\"border:1px solid black; text-align:right;\">".round( $query->getElapsedSecs(), 6 )."</td>
							<td style=\"border:1px solid black; text-align:right;\">".$nQueryType."</td>
							<td style=\"border:1px solid black;\">".$szTableName."</td>
							<td style=\"border:1px solid black;\">".$szConvertedQuery."</td>
						</tr>
					";
				}
				$szDbHtml .= "</table>";
				$szDbHtml .= "</div>";
			}
		}
		return "<div class=\"hidden-xs\">".$szTitleHtml.$szTimeHtml.$szMemHtml.$szDbHtml."</div>";
    }

    protected function convertQuery( $szQuery )
    {
    	$szConvertedQuery = $szQuery;

    	$szConvertedQuery = str_replace( "FROM", "<br>FROM", $szConvertedQuery );
    	$szConvertedQuery = str_replace( "WHERE", "<br>WHERE", $szConvertedQuery );
    	$szConvertedQuery = str_replace( "ORDER BY", "<br>ORDER BY", $szConvertedQuery );

    	return $szConvertedQuery;
    }
}

?>