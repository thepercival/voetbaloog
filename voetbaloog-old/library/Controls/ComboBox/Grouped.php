<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Grouped.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 *
 * @package    Controls
 */

/**
 *
 *
 * @package    Controls
 */
class Controls_ComboBox_Grouped extends Controls_ComboBox implements Controls_ComboBox_Grouped_Interface
{
	protected $m_szPropertyShowParent;
  	protected $m_szPropertyGetParent;
	
	/**
	 * Constructs the class	
	 */
  	public function __construct(){ parent::__construct(); }

	/**
	 * @see Controls_ComboBox_Grouped_Interface::putPropertyShowParent()
	 */
	public function putPropertyGetParent( $szPropertyGetParent )
	{
		$this->m_szPropertyGetParent = $szPropertyGetParent;
	}

	/**
	 * @see Controls_ComboBox_Grouped_Interface::putParentMethod()
	 */
	public function putPropertyShowParent( $szPropertyShowParent )
	{
		$this->m_szPropertyShowParent = $szPropertyShowParent;
	}
	
	protected function toOptions( $objItems )
	{
		$szRet = "";
		
		$objParentPrevious = null;
		foreach ( $objItems as $objItem )
		{
			if ( $this->m_objFilterCollection !== null and $this->m_objFilterCollection[ $objItem->getId() ] !== null )
					continue;
			
			$objParent = MetaData_Factory::getValue( $objItem, $this->m_szPropertyGetParent );
					
			$szSelected = '';
			if ( $this->m_objObjectsToSelect !== null and $this->m_objObjectsToSelect[ $objItem->getId() ] !== null )
					$szSelected = 'selected';
			
			$szValueToShow = $this->toShowableValue( $objItem );
			
			if ( $objParent !== $objParentPrevious )
			{
				if ( $objParentPrevious !== null )
					$szRet .= "</optgroup>";
					
				$szRet .= "<optgroup label=\"".MetaData_Factory::getValue( $objParent, $this->m_szPropertyShowParent )."\">";
			}
			
			$szRet .= '<option '.$szSelected.' value="'.$objItem->getId().'">'.$szValueToShow.'</option>';

			$objParentPrevious = $objParent;
		}
		if ( $objParentPrevious !== null )
			$szRet .= "</optgroup>";		
		
		return $szRet;
	}
}