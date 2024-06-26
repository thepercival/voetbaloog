<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Reader.php 4554 2019-08-12 14:37:34Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package    Auth
 */
class RAD_Auth_MenuItem_XML_Reader extends Source_XML_Reader
{
	public function __construct( $objFactory )
  	{
  		parent::__construct();
  		
  		$this->m_objFactory = $objFactory;

    	$objObjectProperties = parent::getObjectPropertiesToRead();
  		$objObjectPropertiesToAdd = MetaData_Factory::createObjectPropertiesExt( "RAD_Auth_MenuItem", "Id" );
  		$objObjectProperties->addCollection( $objObjectPropertiesToAdd );  		
  	}

	/**
	 * @see Source_Reader_Interface::createObject()
	 */
	public function createObject( $oOptions = null )
	{
		$objXML = $this->getSource();
		
		// $szModule = $this->getModuleName( $oOptions );		
		
		$objRootMenuItem = $this->m_objFactory->createObject();
	
		$this->createObjectHelper( $objXML, $objRootMenuItem );
		
		return $objRootMenuItem;
	}
	
	protected function createObjectHelper( $objXMLMenuItems, $objParentMenuItem )
	{		
		$objMenuItems = $objParentMenuItem->getChildren();
	
		foreach ( $objXMLMenuItems->children() as $objXMLMenuItem )
		{
			$szName = (string) $objXMLMenuItem->Name;
			if ( strlen( $szName ) > 0 )
			{
				$objMenuItem = $this->m_objFactory->createObject();
			
				$szParentId = $objParentMenuItem->getId();
				if ( strlen( $szParentId ) > 0 )
					$szParentId .= "_";
				$objMenuItem->putId( $szParentId . $szName );
				$objMenuItem->putDescription( (string) $objXMLMenuItem->Description );
				$szAction = (string) $objXMLMenuItem->Action->Name;
				if ( strlen( $szAction ) > 0 )
					$objMenuItem->putAction( $szAction );
				
				foreach ( $objXMLMenuItem->children() as $objXMLMenuItemProperty )
				{
					if ( $objXMLMenuItemProperty->getName() === "MenuItems" )
						$this->createObjectHelper( $objXMLMenuItemProperty, $objMenuItem );
				}
				
				$objMenuItems->add( $objMenuItem );	
			}					
		}
		return $objMenuItems;
	}
	
	protected function getModuleName( $oOptions )
	{
		if ( $oOptions !== null and $oOptions->count() === 1 
			and $oOptions->first()->getObjectProperty() === "RAD_Auth_MenuItem::Module"
		)
			return $oOptions->first()->getValue();		
		return null;
	}
}