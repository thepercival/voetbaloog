<?php

class AdminPoolController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$this->pre();
	}

	protected function pre()
	{
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addOrder( "Voetbal_Season::StartDateTime", true );
		$oOptions->addOrder( "VoetbalOog_Pool::Name", false );
		$this->view->oPools = VoetbalOog_Pool_Factory::createObjectsFromDatabase( $oOptions );
	}

	public function deleteAction()
	{
		$this->pre();

		$nPoolId = (int) $this->getParam("poolid");
		$oPool = $this->view->oPools[ $nPoolId ];

		$bRealDelete = $this->getParam("realdelete") === "true";
		if ( $bRealDelete === true )
		{
			$oDbWriter = VoetbalOog_Pool_Factory::createDbWriter();
			$this->view->oPools->addObserver( $oDbWriter );

			$bRemoved = $this->view->oPools->remove( $oPool );

			try
			{
				if ( $bRemoved === true )
				{
					$oDbWriter->write();
					$this->view->savemessage = "pool ".$oPool->getName()." verwijderd";
				}

			}
			catch ( Exception $e )
			{
				if ( $bRemoved === true )
					$this->view->oPools->add( $oPool );
				$this->view->errormessage = "pool kan niet verwijderd worden : " . $e->getMessage();
			}
		}
		else
		{
			$sLink = $this->view->url."/poolid/".$nPoolId."/realdelete/true/";
			$this->view->confirmationmessage = "klik <a href=\"".$sLink."\">hier</a> om pool ".$oPool->getName()." definitief te verwijderen";
		}
		$this->render( 'index');
	}

	public function updateAction()
	{
		$this->pre();

		$this->view->oPool = $this->view->oPools[ (int) $this->getParam("poolid") ];

		if ( $this->view->oPool === null )
			$this->render( 'index');
	}
}

?>