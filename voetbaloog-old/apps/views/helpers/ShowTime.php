<?php

class Zend_View_Helper_ShowTime
{
	protected $m_nStartHour;
	protected $m_nEveryXMinutes = 5;
	protected $m_bShowEmptyHourMinuteRow;

	public function showTime( $sId, $oDateTime, $arrOptions = array() )
	{
		$oHourNumbers = Patterns_Factory::createNumbers( $this->getStartHour(), 23 );
		$oMinuteNumbers = Patterns_Factory::createNumbers( 0, 59, $this->m_nEveryXMinutes );

		$oHour = null;
		$oMinute = null;
		if ( $oDateTime !== null )
		{
			$nHour = $oDateTime->toValue( Zend_Date::HOUR_SHORT );
			$oHour = $oHourNumbers	[ $nHour ];
			$nMinute = $oDateTime->toValue( Zend_Date::MINUTE_SHORT );
			while ( $nMinute % $this->m_nEveryXMinutes !== 0 )
				$nMinute++;
			$oMinute = $oMinuteNumbers [ $nMinute ];
		}

		$oHourValues = Patterns_Factory::createCollection();
		$oHourValue = null;
		foreach ( $oHourNumbers as $oHourNumber )
		{
			$vtValue = $oHourNumber->getId();
			if ( $vtValue < 10 )
				$vtValue = "0".$vtValue;
			$oHourValueTmp = Patterns_Factory::createValuable( $oHourNumber->getId(), $vtValue );
			$oHourValues->add( $oHourValueTmp );
			if ( $oHour === $oHourNumber )
				$oHourValue = $oHourValueTmp;
		}

		$oHourComboBox = Controls_Factory::createComboBox( $sId."hour" );
		$oHourComboBox->putSource( $oHourValues );
		$oHourComboBox->putObjectToSelect( $oHourValue );
		$oHourComboBox->putCSSClass( "form-control" );
		$oHourComboBox->putStyle( "width:65px; display:inline-block;" );
		$oHourComboBox->putObjectPropertyToShow( "**::Value**");
		if ( $this->m_bShowEmptyHourMinuteRow !== true )
			$oHourComboBox->removeEmptyRow();

		$sHtml = $oHourComboBox;

		$oMinuteValues = Patterns_Factory::createCollection();
		$oMinuteValue = null;
		foreach ( $oMinuteNumbers as $oMinuteNumber )
		{
			$vtValue = $oMinuteNumber->getId();
			if ( $vtValue < 10 )
				$vtValue = "0".$vtValue;
			$oMinuteValueTmp = Patterns_Factory::createValuable( $oMinuteNumber->getId(), $vtValue );
			$oMinuteValues->add( $oMinuteValueTmp );
			if ( $oMinute === $oMinuteNumber )
				$oMinuteValue = $oMinuteValueTmp;
		}

		$oMinuteComboBox = Controls_Factory::createComboBox( $sId."minute" );
		$oMinuteComboBox->putSource( $oMinuteValues );
		$oMinuteComboBox->putObjectToSelect( $oMinuteValue );
		$oMinuteComboBox->putObjectPropertyToShow( "**::Value**");
		$oMinuteComboBox->putCSSClass( "form-control" );
		$oMinuteComboBox->putStyle( "margin-left: 5px; width:65px; display:inline-block;" );
		if ( $this->m_bShowEmptyHourMinuteRow !== true )
			$oMinuteComboBox->removeEmptyRow();

		$sHtml .= $oMinuteComboBox;

		return $sHtml;
	}

	protected function getStartHour()
	{
		if ( $this->m_nStartHour === null )
			return 7;
		return $this->m_nStartHour;
	}

	protected function putStartHour( $nStartHour )
	{
		$this->m_nStartHour = $nStartHour;
	}

	protected function putEveryXMinutes( $nEveryXMinutes )
	{
		if ( $nEveryXMinutes < 60 and $nEveryXMinutes > 0 )
			$this->m_nEveryXMinutes = $nEveryXMinutes;
	}

	protected function showEmptyHourMinuteRow()
	{
		$this->m_bShowEmptyHourMinuteRow = true;
	}
}