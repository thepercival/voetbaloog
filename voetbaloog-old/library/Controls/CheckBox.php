<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: CheckBox.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 *
 * @package    Controls
 */

/**
 * @package    Controls
 */
class Controls_CheckBox implements Controls_Interface, Controls_CheckBox_Interface, Patterns_Idable_Interface
{
	protected $m_objSource;
	protected $m_vtValue;
	protected $m_objEventCollection;
	protected $m_szObjectPropertyToShow;

	use Patterns_Idable_Trait;


	public function getValue()
  	{
  		return $this->m_vtValue;
	}

	public function putValue( $vtValue )
	{
  		$this->m_vtValue = $vtValue;
	}

  	public function getSource()
  	{
  		return $this->m_objSource;
	}

	public function putSource( $objSource )
	{
  		$this->m_objSource = $objSource;
	}

	/**
	 * @see Controls_CheckBoxes_Interface::putObjectPropertyToShow()
	 */
	public function putObjectPropertyToShow( $szObjectProperty )
	{
		$this->m_szObjectPropertyToShow = $szObjectProperty;
	}

	/**
	 * Defined by Controls_CheckBoxes_Interface; adds an event
	 *
	 * @see Controls_CheckBoxes_Interface::addEvent()
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
	 * Overload of a "php-magic-method"
	 *
	 * @return  string	the Id
	 */
  	public function __toString()
  	{
  		$szRet = '';

  		$szSelected = '';
  		if ( ( (int) $this->getValue() ) === 1 )
			$szSelected = 'checked';

		$szEvents = "";
		if ( $this->m_objEventCollection !== null )
  		{
  			foreach ( $this->m_objEventCollection as $szEvent )
  				$szEvents .= $szEvent." ";
  		}

		$szValueToShow = null;
		if ( $this->m_szObjectPropertyToShow !== null and $this->m_objSource !== null )
		{
			$szValueToShow = Controls_Factory::replaceObjectProperties( $this->m_objSource, $this->m_szObjectPropertyToShow );
			if ( $szValueToShow instanceof Patterns_Idable_Interface )
				$szValueToShow = $szValueToShow->getId();
		}

		$szRet .= "<input type=\"checkbox\" id=\"".$this->getId()."\" name=\"".$this->getId()."\" ".$szSelected." ".$szEvents.">".$szValueToShow;

		return $szRet;
	}
}