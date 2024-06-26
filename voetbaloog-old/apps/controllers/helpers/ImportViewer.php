<?php

/**
 * Action Helper for loading forms
 *
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Apps_Helper_ImportViewer extends Zend_Controller_Action_Helper_Abstract
{
	/**
	 * Constructor: initialize plugin loader
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	public function direct()
	{
		$this->getActionController()->view->url =
			Zend_Registry::get('baseurl').
			$this->getActionController()->getRequest()->getControllerName().
			'/'.
			$this->getActionController()->getRequest()->getActionName()
		;

		$objActionController = $this->getActionController();

		$objActionController->view->pageTitle = "Import";

		if ( $objActionController->view->objImportRun === null )
			$this->setImportViewRuns( $objActionController );

		echo $objActionController->view->render('import/index.phtml');
	}

	protected function setImportViewRuns( $objActionController )
	{
		$oOptions = Construction_Factory::createOptions();

		$szStartDateTime = $this->getRequest()->getPost('dtpstartdatetime');
		$szEndDateTime = $this->getRequest()->getPost('dtpenddatetime');
		if ( strlen ( $szStartDateTime ) > 0 or strlen ( $szEndDateTime ) > 0 )
		{
			$objStartDateTime = Agenda_Factory::createDateTime( $szStartDateTime );
			$objEndDateTime = Agenda_Factory::createDateTime( $szEndDateTime );
			if ( $objEndDateTime !== null )
				$objEndDateTime->modify( "+1 day" );
			$objTimeSlot = Agenda_Factory::createTimeSlot( $objStartDateTime, $objEndDateTime );

			$objFilters = Construction_Factory::createFiltersForTimeSlots("Import_Run_FromXML", $objTimeSlot );

			$oOptions->addCollection( $objFilters );
			$objActionController->view->startdatetime = $objStartDateTime;
			$objActionController->view->enddatetime = Agenda_Factory::createDateTime( $szEndDateTime );
		}

		$szInitiator = $this->getRequest()->getPost('initiator');
		if ( strlen ( $szInitiator ) > 0 )
		{
			$oOptions->addFilter( "Import_Run_FromXML::Initiator", "Like", $szInitiator );
			$objActionController->view->initiator = $szInitiator;
		}

		$szEntityName = $this->getRequest()->getPost('entityname');
		if ( strlen ( $szEntityName ) > 0 )
		{
			$oOptions->addFilter( "Import_Run_FromXML::EntityName", "EqualTo", $szEntityName );
			$objActionController->view->entityname = $szEntityName;
		}

		$oOptions->addOrder( "Import_Run_FromXML::StartDateTime", TRUE );

		$objActionController->view->objImportRuns = Import_Run_FromXML_Factory::createObjectsFromDatabase( $oOptions );
	}


	/*
	public function viewObjectChangesAction()
	{
		// Check filters
		$oOptions = Construction_Factory::createConstructionOptions();

		$szEntityName = $this->getRequest()->getParam('entityname');
		if ( strlen ( $szEntityName ) > 0 )
		{
			$oOptions->add( "MetaData_ObjectChange::EntityName", "EqualTo", $szEntityName );
			$this->view->entityname = $szEntityName;
		}

		$szImportRunId = $this->getRequest()->getParam('importrunid');
		if ( strlen ( $szImportRunId ) > 0 )
		{
			$oOptions->add( "MetaData_ObjectChange::ImportRun", "EqualTo", $szImportRunId );
			$this->view->importrunid = $szImportRunId;
		}

		$dtStartDateTime = $this->getRequest()->getParam('startdatetime');
		if ( strlen ( $dtStartDateTime ) > 0 )
		{
			$oOptions->add( "MetaData_ObjectChange::TimeStamp", "GreaterThan", $dtStartDateTime );
			$this->view->startdatetime = $dtStartDateTime;
		}

		$dtEndDateTime = $this->getRequest()->getParam('enddatetime');
		if ( strlen ( $dtEndDateTime ) > 0 )
		{
			$oOptions->add( "MetaData_ObjectChange::TimeStamp", "GreaterThan", $dtEndDateTime );
			$this->view->enddatetime = $dtEndDateTime;
		}

		$oOptions->addOrder( "MetaData_ObjectChange::TimeStamp", FALSE );

		$this->view->objObjectChanges = MetaData_ObjectChange_Factory::createObjectsFromDatabase( $oOptions );

		$this->view->render('index');
	}
	 */
}
