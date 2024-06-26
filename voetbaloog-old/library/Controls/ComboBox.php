<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: ComboBox.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Controls
 */

/**
 * @package    Controls
 */
class Controls_ComboBox implements Controls_Interface, Controls_ComboBox_Interface, Patterns_Idable_Interface
{
	protected $m_objObjectsToSelect;
	protected $m_objCollection;
	protected $m_objFilterCollection;
	protected $m_objEventCollection;
	protected $m_szObjectPropertyToShow;
	protected $m_bEmptyRow;
	protected $m_nMaximumNumberOfCharacters;
	protected $m_szClassId;
	protected $m_nWidth;
	protected $m_sStyle;
	protected $m_szMultipleSelect;
	protected $m_bDisabled;
	protected $m_arrAttributes;
	protected $m_arrOptionAttributes;

	use Patterns_Idable_Trait;

	public function __construct()
	{
		$this->m_bEmptyRow = true;
		$this->m_nMaximumNumberOfCharacters = 0;
		$this->m_szMultipleSelect = "";
		$this->m_bDisabled = false;
		$this->m_arrAttributes = array();
		$this->m_arrOptionAttributes = array();
	}

	/**
	 * @see Controls_ComboBox_Interface::disable()
	 */
	public function disable()
	{
		$this->m_bDisabled = true;
	}

	/**
	 * @see Controls_ComboBox_Interface::addOptionAttribute()
	 */
	public function addOptionAttribute( $sOptionAttribute )
	{
		$this->m_arrOptionAttributes[] = $sOptionAttribute;
	}

	/**
	 * @see Controls_ComboBox_Interface::addAttribute()
	 */
	public function addAttribute( $sAttribute )
	{
		$this->m_arrAttributes[] = $sAttribute;
	}

	/**
	 * @see Controls_ComboBox_Interface::emptyObjectToSelect()
	 */
	public function emptyObjectToSelect()
	{
		$this->m_objObjectsToSelect = null;
	}

	/**
	 * Defined by Controls_ComboBox_Interface; puts the object which will be selected
	 *
	 * @see Controls_ComboBox_Interface::putObjectToSelect()
	 */
	public function putObjectToSelect( $vtObjectToSelect )
	{
		if ( $vtObjectToSelect !== null )
		{
			if ( $this->m_objObjectsToSelect === null )
				$this->m_objObjectsToSelect = Patterns_Factory::createCollection();

			if ( $vtObjectToSelect instanceof Patterns_Collection_Interface )
				$this->m_objObjectsToSelect->addCollection( $vtObjectToSelect );
			else
				$this->m_objObjectsToSelect->add( $vtObjectToSelect );
		}
	}

	/**
	 * Defined by Controls_ComboBox_Interface; puts the collection which not will be shown
	 *
	 * @see Controls_ComboBox_Interface::putFilterCollection()
	 */
	public function putFilterCollection( $objFilterCollection )
  	{
  		$this->m_objFilterCollection = $objFilterCollection;
	}

	/**
	 * Defined by Controls_ComboBox_Interface; puts the objectproperty which will be shown
	 *
	 * @see Controls_ComboBox_Interface::putObjectPropertyToShow()
	 */
	public function putObjectPropertyToShow( $szObjectProperty )
	{
		$this->m_szObjectPropertyToShow = $szObjectProperty;
	}

	/**
	 * Defined by Controls_ComboBox_Interface; removes the empty row
	 *
	 * @see Controls_ComboBox_Interface::removeEmptyRow()
	 */
	public function removeEmptyRow()
	{
		$this->m_bEmptyRow = false;
	}

	/**
	 * Defined by Controls_ComboBox_Interface; adds an event
	 *
	 * @see Controls_ComboBox_Interface::addEvent()
	 */
	public function addEvent( $szEvent )
	{
		if ( $this->m_objEventCollection === null )
		{
			$this->m_objEventCollection = Patterns_Factory::createCollection();
		}

		$objEvent = Patterns_Factory::createIdable( $szEvent );
		$this->m_objEventCollection->add( $objEvent );
	}

	/**
	 * Defined by Controls_ComboBox_Interface; puts the maximum number of characters
	 *
	 * @see Controls_ComboBox_Interface::putMaximumNumberOfCharacters()
	 */
	public function putMaximumNumberOfCharacters( $nMaximumNumberOfCharacters )
	{
		$this->m_nMaximumNumberOfCharacters = $nMaximumNumberOfCharacters;
	}

	/**
	 * Defined by Controls_ComboBox_Interface; puts the cssclass
	 *
	 * @see Controls_ComboBox_Interface::putCSSClass()
	 */
	public function putCSSClass( $szClassId )
	{
		$this->m_szClassId = $szClassId;
	}

	/**
	 * Defined by Controls_ComboBox_Interface
	 *
	 * @see Controls_ComboBox_Interface::putMultiple()
	 */
	public function putMultiple( $nSize )
	{
		//multiple="multiple"
		$this->m_szMultipleSelect = "MULTIPLE SIZE=".$nSize;
	}

	/**
	 * Defined by Controls_ComboBox_Interface; puts the cssclass
	 *
	 * @see Controls_ComboBox_Interface::putCSSClass()
	 */
	public function putStyle( $sStyle )
	{
		$this->m_sStyle = $sStyle;
	}

	/**
	 * Defined by Controls_ComboBox_Interface; puts the width
	 *
	 * @see Controls_ComboBox_Interface::putWidth()
	 */
	public function putWidth( $nWidth )
	{
		$this->m_nWidth = $nWidth;
	}

	/**
	 * Defined by Controls_Interface; gets the sourceobject
	 *
	 * @see Controls_Interface::getSource()
	 */
	public function getSource()
	{
		return $this->m_objCollection;
	}

	/**
	 * Defined by Controls_Interface; puts the sourceobject
	 *
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
	 * Defined by Controls_TimeSlotPicker_Interface; gets the hiddenhtmlcontrol
	 *
	 * @see Controls_TimeSlotPicker_Interface::toHidden()
	 */
	public function toHidden()
	{
		if ( strlen( $this->m_szMultipleSelect ) === 0 )
		{
			if ( $this->m_objObjectsToSelect !== null )
			{
				$szValue = $this->m_objObjectsToSelect->first()->getId();
				return "<input type=\"hidden\" id=\"".$this->getId()."\" name=\"".$this->getId()."\" value=\"".$szValue."\">";
			}
		}
  		else
  		{
  			return "<input type=\"hidden\" id=\"".$this->getId()."\" name=\"".$this->getId()."\" value=\"".$this->m_objObjectsToSelect."\">";
  		}
		return null;
	}

	/**
	 * Overload of a "php-magic-method"
	 *
	 * @return  string	the Id
	 */
	public function __toString()
	{
		$sStyle = null;
		if ( $this->m_nWidth !== null )
			$sStyle = "style='width:".$this->m_nWidth."px'";
		else if ( $this->m_sStyle !== null )
			$sStyle = "style='".$this->m_sStyle."'";

		$sAttributes = "";
		foreach ( $this->m_arrAttributes as $sAttribute )
			$sAttributes .= " " . $sAttribute;
		$szRet = null;
		$sDisabled = $this->m_bDisabled ? " DISABLED" : null;
		if ( strlen( $this->m_szMultipleSelect ) === 0 )
			$szRet = "<SELECT".$sDisabled." ".$sAttributes." ID=\"".$this->getId()."\" NAME=\"".$this->getId()."\" ".$sStyle." ";
		else
			$szRet = "<SELECT".$sDisabled." ".$sAttributes." ID=\"".$this->getId()."\" NAME=\"".$this->getId()."[]\" ".$this->m_szMultipleSelect." ".$sStyle." ";

		if ( $this->m_objEventCollection !== null )
		{
			foreach ( $this->m_objEventCollection as $szEvent => $objEvent )
				$szRet .= $szEvent." ";
		}

		$szCSSClass = "";
		if ( $this->m_szClassId !== null )
			$szCSSClass = "class=\"".$this->m_szClassId."\"";
		$szRet .= $szCSSClass;

		$szRet .= ' > ';

		if ( $this->m_objCollection !== null )
		{
			// Begin : add NULL value
			if ( $this->m_bEmptyRow === true )
			{
				$szSelected = '';
				if ( $this->m_objObjectsToSelect === null ) //or $this->m_objCollection[ $this->m_objObjectToSelect->getId() ] === false )
				{
					$szSelected = 'selected';
				}
				$szRet .= '<option '.$szSelected.' value=""></option>';
			}
			// End : add NULL value

			// Begin : add Collectionitems
			$szRet .= $this->toOptions( $this->m_objCollection );
			// End : add Collectionitems
		}

		$szRet .= '</SELECT>';

		if ( $this->m_bDisabled )
			$szRet .= $this->toHidden();

		return $szRet;
	}

	protected function toShowableValue( $oItem )
	{
		$szValueToShow = $oItem->getId();
		if ( $this->m_szObjectPropertyToShow !== null )
		{
			$szValueToShow = Controls_Factory::replaceObjectProperties( $oItem, $this->m_szObjectPropertyToShow );
			if ( $szValueToShow instanceof Patterns_Idable_Interface )
				$szValueToShow = $szValueToShow->getId();
		}

		if ( $this->m_nMaximumNumberOfCharacters > 0 )
		{
			$szDots = "";
			if ( strlen( $szValueToShow ) > $this->m_nMaximumNumberOfCharacters )
				$szDots = "..";
			$szValueToShow = substr( $szValueToShow, 0, $this->m_nMaximumNumberOfCharacters ).$szDots;
		}
		return htmlspecialchars( $szValueToShow );
	}

	protected function toOptions( $oItems )
	{
		$szRet = "";
		foreach ( $oItems as $oItem )
		{
			if ( $this->m_objFilterCollection !== null and $this->m_objFilterCollection[ $oItem->getId() ] !== null )
				continue;

			$szSelected = '';
			if ( $this->m_objObjectsToSelect !== null and $this->m_objObjectsToSelect[ $oItem->getId() ] !== null )
					$szSelected = 'selected';

			$szValueToShow = $this->toShowableValue( $oItem );

			$sOptionAttributes = "";
			foreach ( $this->m_arrOptionAttributes as $sOptionAttribute )
				$sOptionAttributes .= " " . $sOptionAttribute;
			$sOptionAttributes = Controls_Factory::replaceObjectProperties( $oItem, $sOptionAttributes );

			$szRet .= "<option ".$sOptionAttributes." ".$szSelected." value=\"".$oItem->getId()."\">".$szValueToShow."</option>";
		}
		return $szRet;
	}
}