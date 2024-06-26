<?php

/**
 * @copyright2007 Coen Dunnink
 * @licensehttp://www.gnu.org/licenses/gpl.txt
 * @version$Id: CheckBoxes.php 4557 2019-08-12 18:50:59Z thepercival $
 * @sinceFile available since Release 4.0
 * @packageControls
 */

/**
 * @packageControls
 */
class Controls_CheckBoxes implements Controls_Interface, Controls_CheckBoxes_Interface, Patterns_Idable_Interface
{
	protected $m_objObjectsToSelect;
	protected $m_objCollection;
	protected $m_objFilterCollection;
	protected $m_objEventCollection;
	protected $m_szObjectPropertyToShow;
	protected $m_nMaxNrOfItemsPerColumn;
	protected $m_nMaxNrOfColumns;
	protected $m_nAuth;

	use Patterns_Idable_Trait;

	public function __construct()
	{
		$this->m_nMaxNrOfItemsPerColumn = 1;
		$this->m_nMaxNrOfColumns = 1;
		$this->m_nAuth = Source_Db::ACTION_INSERT + Source_Db::ACTION_DELETE;
	}

	/**
	 * @see Controls_CheckBoxes_Interface::getObjecstToSelect()
	 */
	public function getObjectsToSelect()
	{
		return $this->m_objObjectsToSelect;
	}

	/**
	 * @see Controls_CheckBoxes_Interface::putObjectsToSelect()
	 */
	public function putObjectsToSelect( $objObjectsToSelect )
	{
		$this->m_objObjectsToSelect = $objObjectsToSelect;
	}

	/**
	 * @see Controls_CheckBoxes_Interface::getMaxNrOfRows()
	 */
	public function getMaxNrOfColumns()
	{
		return $this->m_nMaxNrOfColumns;
	}

	/**
	 * @see Controls_CheckBoxes_Interface::putMaxNrOfColumns()
	 */
	public function putMaxNrOfColumns( $nMaxNrOfColumns )
	{
		$this->m_nMaxNrOfColumns = $nMaxNrOfColumns;
	}

	/**
	 * @see Controls_CheckBoxes_Interface::getMaxNrOfItemsPerColumn()
	 */
	public function getMaxNrOfItemsPerColumn()
	{
		return $this->m_nMaxNrOfItemsPerColumn;
	}

	/**
	 * @see Controls_CheckBoxes_Interface::putMaxNrOfItemsPerColumn()
	 */
	public function putMaxNrOfItemsPerColumn( $nMaxNrOfItemsPerColumn )
	{
		$this->m_nMaxNrOfItemsPerColumn = $nMaxNrOfItemsPerColumn;
	}

	/**
	 * @see Controls_CheckBoxes_Interface::putFilterCollection()
	 */
	public function putFilterCollection( $objFilterCollection )
	{
		$this->m_objFilterCollection = $objFilterCollection;
	}

	/**
	 * @see Controls_CheckBoxes_Interface::putObjectPropertyToShow()
	 */
	public function putObjectPropertyToShow( $szObjectProperty )
	{
		$this->m_szObjectPropertyToShow = $szObjectProperty;
	}

	/**
	 * @see Controls_CheckBoxes_Interface::addEvent()
	 */
	public function addEvent( $szEvent )
	{
		if ( $this->m_objEventCollection === null )
			$this->m_objEventCollection = Patterns_Factory::createCollection();

		$objEvent = Patterns_Factory::createIdable( $szEvent );
		$this->m_objEventCollection->add( $objEvent );
	}

	/**
	 * @see Controls_CheckBoxes_Interface::getAuth()
	 */
	public function getAuth()
	{
		return $this->m_nAuth;
	}

	/**
	 * @see Controls_CheckBoxes_Interface::putAuth()
	 */
	public function putAuth( $nAuth )
	{
		$this->m_nAuth = $nAuth;
	}

	/**
	 * @see Controls_Interface::getSource()
	 */
	public function getSource()
	{
		return $this->m_objCollection;
	}

	/**
	 * @see Controls_Interface::putSource()
	 */
	public function putSource( $objCollection )
	{
		if ( $objCollection !== null and $objCollection instanceof Patterns_Collection_Interface )
		{
			$this->m_objCollection = $objCollection;
		}
	}

	/**
	 * Overload of a "php-magic-method"
	 *
	 * @returnstring	the Id
	 */
	public function __toString()
	{
		$szRet = '';

		if ( $this->m_objCollection !== null )
		{
			$szRet .= "<div>";

			$nCounter = 0;
			$nMaxNrOfColumns = $this->getMaxNrOfColumns();
			$nMaxNrOfItemsPerColumn = $this->getMaxNrOfItemsPerColumn();
			$nNrOfItems = $this->m_objCollection->count();
			{
				$nNrOfItemsPerColumn = (int) ceil( $nNrOfItems / $nMaxNrOfColumns );
				if ( $nNrOfItemsPerColumn > $nMaxNrOfItemsPerColumn )
					$nMaxNrOfItemsPerColumn = $nNrOfItemsPerColumn;
			}

			// Begin : add Collectionitems
			foreach ( $this->m_objCollection as $szItemId => $objItem )
			{
				if ( $this->m_objFilterCollection !== null and $this->m_objFilterCollection[ $objItem->getId() ] !== null )
					continue;

				$sSelected = '';
				$sReadOnly = null;
				if ( $this->m_objObjectsToSelect !== null )
				{
					foreach ( $this->m_objObjectsToSelect as $objObjectToSelect )
					{
						if ( $objItem === $objObjectToSelect )
						{
							$sSelected = 'checked';
							if ( ( $this->m_nAuth & Source_Db::ACTION_DELETE ) !== Source_Db::ACTION_DELETE )
								$sReadOnly = "onclick=\"return false\"";
							break;
						}
					}
				}

				$szEvents = "";
				if ( $this->m_objEventCollection !== null )
				{
					foreach ( $this->m_objEventCollection as $szEvent )
						$szEvents .= $szEvent." ";
				}

				$szValueToShow = $objItem->getId();
				if ( $this->m_szObjectPropertyToShow !== null )
				{
					$szValueToShow = Controls_Factory::replaceObjectProperties( $objItem, $this->m_szObjectPropertyToShow );
					if ( $szValueToShow instanceof Patterns_Idable_Interface )
						$szValueToShow = $szValueToShow->getId();
				}

				if ( ( $nCounter % $nMaxNrOfItemsPerColumn ) === 0 )
					$szRet .= "<div style=\"float:left; margin-left:10px;\">";

				$szRet .= "<div><input type=\"checkbox\" name=\"".$this->getId()."-".$objItem->getId()."\" value=\"".$objItem->getId()."\" ".$sSelected." ".$sReadOnly." ".$szEvents.">".$szValueToShow."</div>";

				$nCounter++;

				if ( ( $nCounter % $nMaxNrOfItemsPerColumn ) === 0 or $nNrOfItems == $nCounter )
					$szRet .= "</div>";
			}
			// End : add Collectionitems

			$szRet .= "<div style=\"clear:both\"></div></div>";
		}

		return $szRet;
	}
}