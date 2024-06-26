<?php

class Voetbal_BondController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$this->view->oAssociations = Voetbal_Association_Factory::createObjectsFromDatabase();

		$sBtnSave = $this->getParam('btnsave');
		if ( strlen ( $sBtnSave ) > 0 ) {
			if ( $sBtnSave === "add" )
				$this->add();
			else if ( $sBtnSave === "edit" )
				$this->update();
			else if ( $sBtnSave === "remove" )
				$this->remove();
		}

		$this->view->oAssociations->uasort(
			function( $oAssociationA, $oAssociationB )
			{
				return ( $oAssociationA->getName() < $oAssociationB->getName() ? -1 : 1 );
			}
		);
	}

	protected function add()
	{
		$sName = $this->getParam('name');
		if (strlen($sName) == 0)
			$this->view->errormessage = "naam is niet ingevuld";
		if (mb_strlen($sName, "UTF-8") > Voetbal_Association::MAX_NAME_LENGTH)
			$this->view->errormessage = "naam mag maximaal " . Voetbal_Association::MAX_NAME_LENGTH . " karakters bevatten";

		if ($this->view->errormessage !== null)
			return;

		$oDbWriter = Voetbal_Association_Factory::createDbWriter();
		$this->view->oAssociations->addObserver($oDbWriter);

		$oAssociation = Voetbal_Association_Factory::createObject();
		$oAssociation->putId("__new__");
		$oAssociation->putName($sName);
		$oAssociation->putDescription( $this->getParam('description') );

		$this->view->oAssociations->add( $oAssociation );

		try
		{
			$oDbWriter->write();
			$this->view->savemessage = "het bond is toegevoegd";
		}
		catch ( Exception $e)
		{
			$this->view->oAssociations->remove( $oAssociation );
			$this->view->errormessage = "bond ".$sName." kon niet worden toegevoegd: " . $e->getMessage();
		}
	}

	protected function update()
	{
		$sName = $this->getParam('name');
		if ( strlen ( $sName ) == 0 )
			$this->view->errormessage = "naam is niet ingevuld";
		if ( mb_strlen( $sName, "UTF-8" ) > Voetbal_Association::MAX_NAME_LENGTH )
			$this->view->errormessage = "naam mag maximaal ".Voetbal_Association::MAX_NAME_LENGTH." karakters bevatten";

		$oAssociation = null;
		{
			$nId = (int) $this->getParam("associationid");
			if ( $nId > 0 )
				$oAssociation = $this->view->oAssociations[ $nId ];
		}
		if ( $oAssociation === null )
			$this->view->errormessage = "bond kon niet gevonden worden";

		if ( $this->view->errormessage !== null )
			return;

		$oDbWriter = Voetbal_Association_Factory::createDbWriter();
		$oAssociation->addObserver( $oDbWriter );

		$oAssociation->putName( $sName );
		$oAssociation->putDescription( $this->getParam('description') );

		try
		{
			$oDbWriter->write();
			$this->view->savemessage = "het bond is gewijzigd";
		}
		catch ( Exception $e)
		{
			$this->view->errormessage = "bond ".$oAssociation->getName()." kon niet worden gewijzigd: " . $e->getMessage();
		}
	}

	protected function remove()
	{
		$oDbWriter = Voetbal_Association_Factory::createDbWriter();
		$this->view->oAssociations->addObserver( $oDbWriter );

		$oAssociation = null;
		{
			$nId = (int) $this->getParam("associationid");
			if ( $nId > 0 )
				$oAssociation = $this->view->oAssociations[ $nId ];
		}
		if ( $oAssociation === null )
			$this->view->errormessage = "bond kon niet gevonden worden";

		if ( $this->view->errormessage !== null )
			return;

		$this->view->oAssociations->remove( $oAssociation );

		try {
			$oDbWriter->write();
			$this->view->savemessage = "bond ".$oAssociation->getName()." is verwijderd";
		}
		catch ( Exception $e ){
			$this->view->errormessage = "bond ".$oAssociation->getName()." kon niet worden verwijderd: " . $e->getMessage();
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
				$this->view->oAssociation = Voetbal_Association_Factory::createObjectFromDatabase( $nId );
			echo $this->render( "edit" );
		}
		else{
			echo "no input-param 'method'";
		}
	}
}

?>
