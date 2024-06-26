<?php

/**
 * Action Helper
 *
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Apps_Helper_ImportCSVBeheer extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Constructor: initialize plugin loader
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function direct( $arrOptions = null )
    {
    	$oActionController = $this->getActionController();

    	$oActionController->view->objecttypes = $arrOptions["objecttypes"];

    	if ( array_key_exists( "defaultfilters", $arrOptions ) === true )
    		$oActionController->view->arrDefFilters = $arrOptions["defaultfilters"];

    	$vtRetVal = $this->handleAction();
    	if ( $vtRetVal !== true )
    		$oActionController->view->message = "<div class=\"message-error\">".$vtRetVal."</div>";

		echo $oActionController->view->render('import/csv.phtml');
    }

    protected function handleAction()
    {
    	$oActionController = $this->getActionController();

    	$oActionController->view->stepnr = (int) $this->getRequest()->getParam("stepnr");
    	if ( $oActionController->view->stepnr === 0 )
    		$oActionController->view->stepnr = 1;

    	// var_dump( $this->getRequest()->getParams() );

    	$vtRetVal = null;
    	if ( strlen( $this->getRequest()->getParam("btntostep2") ) > 0 )
    	{
    		$vtRetVal = $this->executeStepOne();
    		if ( $vtRetVal === true )
    			$oActionController->view->stepnr = 2;
    	}
    	if ( strlen( $this->getRequest()->getParam("btntostep3") ) > 0 )
    	{
    		$vtRetVal = $this->executeStepTwo();
    		if ( $vtRetVal === true )
    			$oActionController->view->stepnr = 3;
    	}
    	if ( strlen( $this->getRequest()->getParam("btntostep4") ) > 0 )
    	{
    		$vtRetVal = $this->executeStepThree();
    		if ( $vtRetVal === true )
    		{
    			$oActionController->view->stepnr = 4;
    		}
    	}

    	return $vtRetVal;
    }

    protected function executeStepOne()
    {
    	$vtRetVal = true;

    	$oActionCtrl = $this->getActionController();

    	$oActionCtrl->view->objecttype = $this->getRequest()->getParam("objecttype");
    	// if ( $_FILES['csvimportfile'] ['error'] != 0 )

    	$oActionCtrl->view->arrCSVData = Source_CSV_Reader::getCSVData( $_FILES['csvimportfile']['tmp_name'] );

    	//var_dump( $oActionCtrl->view->arrCSVData );
    	if ( $this->getRequest()->getParam("csvheader") === "on" )
			$arrFirstLine = array_shift( $oActionCtrl->view->arrCSVData );

    	$oSession = new Zend_Session_Namespace( APPLICATION_NAME );
    	$oSession->arrCSVData = $oActionCtrl->view->arrCSVData;

    	$oActionCtrl->view->sFactory = $oActionCtrl->view->objecttype . "_Factory";

    	$oActionCtrl->view->oFactory = call_user_func( $oActionCtrl->view->sFactory . "::getInstance" );

    	$oActionCtrl->view->objectproperties = $oActionCtrl->view->oFactory->createDbReader()->getObjectPropertiesToRead();

    	return $vtRetVal;
    }

	protected function executeStepTwo()
	{
		$vtRetVal = true;

		$oActionCtrl = $this->getActionController();

		$oActionCtrl->view->objecttype = $this->getRequest()->getParam("objecttype");
		$oActionCtrl->view->sFactory = $oActionCtrl->view->objecttype . "_Factory";
		$oActionCtrl->view->oFactory = call_user_func( $oActionCtrl->view->sFactory . "::getInstance" );

		$oObjects = $oActionCtrl->view->oFactory->createObjectsFromDatabase();

		$oImportChecker = Import_Factory::createImportChecker( $oObjects );

		$oDbWriter = $oActionCtrl->view->oFactory->createDbWriter();
		$oObjects->addObserver( $oDbWriter );

		$oSession = new Zend_Session_Namespace( APPLICATION_NAME );
		// $oSession->arrCSVData = $oActionCtrl->view->arrCSVData;
		$oActionCtrl->view->arrCSVData = $oSession->arrCSVData;
		// var_dump( $oActionCtrl->view->arrCSVData );
		$nNrOfColumns = (int) $this->getRequest()->getParam("nrofcolumns");

		$arrExternIdColumns = $this->getExternIdColumns();
		if ( count( $arrExternIdColumns ) === 0 )
			return "Specify at least the ExternId property to identify rows";

		$arrObjectProperties = $this->getObjectProperties();
		if ( count( $arrObjectProperties ) === 0 )
			return "No properties to update";

		foreach ( $oActionCtrl->view->arrCSVData as $arrLine )
		{
			$vtExternId = strtolower( $this->getExternIdValue( $arrExternIdColumns, $arrLine ) );
			if ( strlen( $vtExternId ) === 0 )
				continue;

			$vtId = $oImportChecker->getSystemId( $vtExternId );
			$oObject = null;
			if ( $vtId !== null )
				$oObject = $oObjects[ (int) $vtId ];

			$sExternId = Import_Factory::$m_szExternPrefix. $vtExternId;

			// if externid is twice is csv
			if ( $oObject === null and $oObjects[ $sExternId ] !== null )
				continue;

			$bNew = null;
			if ( $oObject !== null )
			{
				$oObject->addObserver( $oDbWriter );
				$bNew = false;
			}
			else
			{
				$oObject = $oActionCtrl->view->oFactory->createObject();

				$oObject->putId( $sExternId );
				$oObject->putExternId( $sExternId );

				$bNew = true;
			}

			$bObjectIsCorrect = true;
    		foreach ( $arrObjectProperties as $sObjectProperty => $arrObjectProperty )
			{
				$nColumnNr = $arrObjectProperty[0];
				$bNotNull = $arrObjectProperty[1];
				$sFormat = $arrObjectProperty[2];

				$vtValue = trim ( trim ( $arrLine[$nColumnNr] ), "\"" );
				if ( $vtValue === "null" )
					$vtValue = null;

				$vtValue = $this->getObject( $sObjectProperty, $vtValue );

				if ( ( $vtValue === null or strlen( $vtValue ) === 0 ) and $bNotNull === true )
				{
					$bObjectIsCorrect = false;
					continue;
				}

				if ( strlen( $sFormat ) > 0 and $vtValue !== null )
				{
					$vtValue = DateTime::createFromFormat( $sFormat, $vtValue );
					if ( $vtValue !== false )
					{
						$vtValue->setTime( 0, 0 );
						$vtValue = Agenda_Factory::createDateTime( $vtValue->format( Agenda_Factory::$m_szDateTimeFormat ) );
					}
				}

				if ( $bNew === true
					or MetaData_Factory::getValue( $oObject, $sObjectProperty ) != $vtValue
				)
				{
					// var_dump( $sObjectProperty . ":->" . $vtValue );
					MetaData_Factory::putValue( $oObject, $sObjectProperty, $vtValue );
				}
			}

			if ( $bNew === true and $bObjectIsCorrect === true )
			{
				$oObjects->add( $oObject );
			}
		}

		// die();

		try
		{
			$vtRetVal = $oDbWriter->write();
    	}
    	catch( Exception $e )
    	{
    		return $e->getMessage();
    	}



    	return $vtRetVal;
    }

    protected function getExternIdColumns()
    {
    	$arrExternIdColumns = array();

    	$nNrOfColumns = (int) $this->getRequest()->getParam("nrofcolumns");
    	for ( $nI = 0 ; $nI < $nNrOfColumns ; $nI++ )
    	{
    		$arrObjectProperties = $this->getRequest()->getParam("objectprop" . $nI );
    		foreach( $arrObjectProperties as $nId => $sObjectProperty )
    		{
    			if ( strlen( $sObjectProperty ) === 0 )
    				continue;

    			if ( strrpos( $sObjectProperty, "::ExternId" ) !== false )
    			{
    				$arrExternIdColumns[] = $nI;
    			}
    		}
    	}
    	return $arrExternIdColumns;
    }

    protected function getObjectProperties()
    {
    	$arrObjectProperties = array();

    	$nNrOfColumns = (int) $this->getRequest()->getParam("nrofcolumns");
    	for ( $nI = 0 ; $nI < $nNrOfColumns ; $nI++ )
    	{
    		$arrObjectPropertiesSub = $this->getRequest()->getParam("objectprop" . $nI );

    		foreach( $arrObjectPropertiesSub as $nId => $sObjectProperty )
    		{

    			if ( strlen( $sObjectProperty ) > 0
    				and strrpos( $sObjectProperty, "::ExternId" ) === false
    			)
    			{
    				$bNotNull = $this->getRequest()->getParam("notnull" . $nI ) === "on";
    				$sFormat = $this->getRequest()->getParam("format" . $nI );
    	   			$arrObjectProperties[ $sObjectProperty ] = array( $nI, $bNotNull, $sFormat );
    			}
    		}
    	}

    	return $arrObjectProperties;
    }

    protected function getExternIdValue( $arrExternIdColumns, $arrLine )
    {
    	$sExternId = "";
    	foreach( $arrExternIdColumns as $nExternIdColumn )
    	{
    		if ( strlen( $sExternId ) > 0 )
    			$sExternId .= "_";
    		$sExternId .= trim( trim ( $arrLine[ $nExternIdColumn ] ), "\"" );
    	}
    	// var_dump();
    	return $sExternId;
    }

    protected function getObject( $sObjectProperty, $vtValue )
    {
    	if ( $vtValue === null )
    		return null;

    	$oActionCtrl = $this->getActionController();

    	$oValuable = $oActionCtrl->view->objecttypes[ $oActionCtrl->view->objecttype ];
    	$arrObjectMap = $oValuable->getValue();

    	//var_dump( $arrObjectMap );
    	//var_dump( $sObjectProperty );
    	//die();

    	if ( array_key_exists( $sObjectProperty, $arrObjectMap ) === false )
    		return $vtValue;

    	$sClassName = $arrObjectMap[ $sObjectProperty ];
    	$sFactory = $sClassName . "_Factory";
    	$oFactory = call_user_func( $sFactory . "::getInstance" );

    	$oOptions = Construction_Factory::createOptions();
    	$oOptions->addFilter( $sClassName . "::ExternId", "EqualTo", Import_Factory::$m_szExternPrefix.strtolower( $vtValue ) );
    	return $oFactory->createObjectFromDatabase( $oOptions );
    }

    protected function executeStepThree()
    {
    	$vtRetVal = true;
    	// $this->getActionController()->view->setupCompleted = true;

    	return $vtRetVal;
    }
}