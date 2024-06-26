<?php

class Voetbal_SeizoenController extends Zend_Controller_Action
{
	public function init()
	{
		$cfgApp = new Zend_Config_Ini( APPLICATION_PATH . '/configs/config.ini');
		$this->view->bHasImport = ( $cfgApp->get( "import" ) !== null );
	}

	public function indexAction()
	{
        $cfgJs = new Zend_Config_Ini( APPLICATION_PATH . '/configs/config.ini', 'js');
        $lazyLoad = filter_var($cfgJs->lazyload, FILTER_VALIDATE_BOOLEAN);
		$this->getResponse()->insert("extrajsincludes", $this->_helper->GetDateIncludes("js", $lazyLoad) );
		$this->getResponse()->insert("extracssincludes", $this->_helper->GetDateIncludes("css", false) );

		$this->view->oSeasons = Voetbal_Season_Factory::createObjectsFromDatabase();

		$sBtnSave = $this->getParam('btnsave');
		if ( strlen ( $sBtnSave ) > 0 ) {
			if ( $sBtnSave === "add" )
				$this->add();
			else if ( $sBtnSave === "edit" )
				$this->update();
			else if ( $sBtnSave === "remove" )
				$this->remove();
		}

		$this->view->oSeasons->uasort(
			function( $oSeasonA, $oSeasonB )
			{
				if ( $oSeasonA->getStartDateTime() < $oSeasonB->getStartDateTime() )
					return 1;
				if ( $oSeasonA->getStartDateTime() > $oSeasonB->getStartDateTime() )
					return -1;
				return ( $oSeasonA->getName() < $oSeasonB->getName() ? -1 : 1 );
			}
		);
	}

	protected function add()
	{
		$sName = $this->getParam('name');
		if (strlen($sName) == 0)
			$this->view->errormessage = "naam is niet ingevuld";
		if (mb_strlen($sName, "UTF-8") > Voetbal_Season::MAX_NAME_LENGTH)
			$this->view->errormessage = "naam mag maximaal " . Voetbal_Season::MAX_NAME_LENGTH . " karakters bevatten";
		$oStartDateTime = $this->_helper->getDateTime('startdatetime', false);
		if ($oStartDateTime === null)
			$this->view->errormessage = "er is geen startdatum ingevuld";
		$oEndDateTime = $this->_helper->getDateTime('enddatetime', false);
		if ($oEndDateTime === null)
			$this->view->errormessage = "er is geen einddatum ingevuld";

		if ($this->view->errormessage !== null)
			return;

		$oDbWriter = Voetbal_Season_Factory::createDbWriter();
		$this->view->oSeasons->addObserver($oDbWriter);

		$oSeason = Voetbal_Season_Factory::createObject();
		$oSeason->putId("__new__");
		$oSeason->putName($sName);
		$oSeason->putStartDateTime($oStartDateTime);
		$oSeason->putEndDateTime($oEndDateTime);
		if ($this->view->bHasImport === true) {
			$sExternId = $this->getParam('externid');
			$sExternId = ( strlen ( $sExternId ) > 0 ) ? Import_Factory::$m_szExternPrefix . $sExternId : null;
			$oSeason->putExternId( $sExternId );
		}

		$this->view->oSeasons->add( $oSeason );

		try
		{
			$oDbWriter->write();
			$this->view->savemessage = "het seizoen is toegevoegd";
		}
		catch ( Exception $e)
		{
			$this->view->oSeasons->remove( $oSeason );
			$this->view->errormessage = "seizoen ".$sName." kon niet worden toegevoegd: " . $e->getMessage();
		}
	}

	protected function update()
	{
		$sName = $this->getParam('name');
		if ( strlen ( $sName ) == 0 )
			$this->view->errormessage = "naam is niet ingevuld";
		if ( mb_strlen( $sName, "UTF-8" ) > Voetbal_Season::MAX_NAME_LENGTH )
			$this->view->errormessage = "naam mag maximaal ".Voetbal_Season::MAX_NAME_LENGTH." karakters bevatten";
		$oStartDateTime = $this->_helper->getDateTime( 'startdatetime', false );
		if ( $oStartDateTime === null )
			$this->view->errormessage = "er is geen startdatum ingevuld";
		$oEndDateTime = $this->_helper->getDateTime( 'enddatetime', false );
		if ( $oEndDateTime === null )
			$this->view->errormessage = "er is geen einddatum ingevuld";
		$oSeason = null;
		{
			$nId = (int) $this->getParam("seasonid");
			if ( $nId > 0 )
				$oSeason = $this->view->oSeasons[ $nId ];
		}
		if ( $oSeason === null )
			$this->view->errormessage = "seizoen kon niet gevonden worden";

		if ( $this->view->errormessage !== null )
			return;

		$oDbWriter = Voetbal_Season_Factory::createDbWriter();
		$oSeason->addObserver( $oDbWriter );

		$oSeason->putName( $sName );
		$oSeason->putStartDateTime( $oStartDateTime );
		$oSeason->putEndDateTime( $oEndDateTime );
		if ( $this->view->bHasImport === true ){
			$sExternId = $this->getParam('externid');
			$sExternId = ( strlen ( $sExternId ) > 0 ) ? Import_Factory::$m_szExternPrefix . $sExternId : null;
			$oSeason->putExternId( $sExternId );
		}


		try
		{
			$oDbWriter->write();
			$this->view->savemessage = "het seizoen is gewijzigd";
		}
		catch ( Exception $e)
		{
			$this->view->errormessage = "seizoen ".$oSeason->getName()." kon niet worden gewijzigd: " . $e->getMessage();
		}
	}

	protected function remove()
	{
		$oDbWriter = Voetbal_Season_Factory::createDbWriter();
		$this->view->oSeasons->addObserver( $oDbWriter );

		$oSeason = null;
		{
			$nId = (int) $this->getParam("seasonid");
			if ( $nId > 0 )
				$oSeason = $this->view->oSeasons[ $nId ];
		}
		if ( $oSeason === null )
			$this->view->errormessage = "seizoen kon niet gevonden worden";

		if ( $this->view->errormessage !== null )
			return;

		$this->view->oSeasons->remove( $oSeason );

		try {
			$oDbWriter->write();
			$this->view->savemessage = "seizoen ".$oSeason->getName()." is verwijderd";
		}
		catch ( Exception $e ){
			$this->view->errormessage = "seizoen ".$oSeason->getName()." kon niet worden verwijderd: " . $e->getMessage();
		}
	}

	public function ajaxAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		if ( $this->getParam('method') === "editform" )
		{
			$nId = (int) $this->getParam('id');
			if ( $nId > 0 )
				$this->view->oSeason = Voetbal_Season_Factory::createObjectFromDatabase( $nId );
			echo $this->render( "edit" );
		}
		else{
			echo "no input-param 'method'";
		}
	}
}

?>
