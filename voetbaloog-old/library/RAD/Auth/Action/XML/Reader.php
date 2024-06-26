<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Reader.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package    Auth
 */
class RAD_Auth_Action_XML_Reader extends Source_XML_Reader
{
	public function __construct( $objFactory )
  	{
  		parent::__construct();
  		
  		$this->m_objFactory = $objFactory;

    	$objObjectProperties = parent::getObjectPropertiesToRead();
  		$objObjectPropertiesToAdd = MetaData_Factory::createObjectPropertiesExt( "RAD_Auth_Action", "Id" );
  		$objObjectProperties->addCollection( $objObjectPropertiesToAdd );  		
  	}

	/**
	 * @see Reader_Interface::createObjects()
	 */
	public function createObjects( Construction_Option_Collection $oOptions = null): Patterns_Collection
	{
		$objXML = $this->getSource();
		
		$szModule = $this->getModuleName( $oOptions );
		
		$objActions = $this->m_objFactory->createObjects();	
		
		foreach ( $objXML->children() as $xmlAction )
		{
			$objAction = $this->m_objFactory->createObject();
			
			$szName = (string) $xmlAction->Name;
			if ( strlen( $szName ) > 0 )
			{
				$objAction->putId( $szName );
				$objAction->putName( $szName );
				$objAction->putModule( $szModule );
			
				$objActions->add( $objAction );
			}
		}
		
		return $objActions;
	}
	
	protected function getModuleName( $oOptions )
	{
		if ( $oOptions !== null and $oOptions->count() === 1 
			and $oOptions->first()->getObjectProperty() === "RAD_Auth_Action::Module"
		)
			return $oOptions->first()->getValue();		
		return null;
	}
}