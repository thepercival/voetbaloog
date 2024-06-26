<?php

class BeheerCompetitieSeizoenController extends Zend_Controller_Action
{
	protected function updateOrder()
	{
		$sUpdateOrder = $this->getParam('updateorder');
		if ( strlen ( $sUpdateOrder ) > 0 )
		{
			// verwissel de ViewOrder van de game met de game met vieworder -1 of + 1
			$nGameId = (int) $this->getParam('gameid');
			$oGame = Voetbal_Game_Factory::createObjectFromDatabase( $nGameId );

			if ( $oGame !== null )
			{
				$oGameDbWriter = Voetbal_Game_Factory::createDbWriter();
				$nViewOrder = $oGame->getViewOrder();

				$nSwitchViewOrder = null;
				if ( $sUpdateOrder === "up" )
				{
					$nSwitchViewOrder = $nViewOrder - 1;
				}
				elseif ( $sUpdateOrder === "down" )
				{
					$nSwitchViewOrder = $nViewOrder + 1;
				}

				$oFilters = Construction_Factory::createOptions();
				$oFilters->addFilter( "Voetbal_Game::ViewOrder", "EqualTo", $nSwitchViewOrder );
				$oFilters->addFilter( "Voetbal_Poule::Id", "EqualTo", $oGame->getHomePoulePlace()->getPoule() );
				$oSwitchGame = Voetbal_Game_Factory::createObjectFromDatabase( $oFilters );

				if ( $oSwitchGame !== null )
				{
					$oGame->addObserver( $oGameDbWriter );
					$oSwitchGame->addObserver( $oGameDbWriter );

					$oGame->putViewOrder( $nSwitchViewOrder );
					$oSwitchGame->putViewOrder( $nViewOrder );

					try
					{
						if ( $oGameDbWriter->write() === true )
						{
							$this->view->competitionseasonupdatemessage = "<div class=\"alert alert-success\">Order aangepast!</div>";
						}
					}
					catch ( Exception $oException )
					{
						$this->view->competitionseasonupdatemessage = "<div class=\"alert alert-danger\">Unknown databaseerror:".$oException->getMessage()."</div>";
					}
				}
			}
		}
	}
}

?>
