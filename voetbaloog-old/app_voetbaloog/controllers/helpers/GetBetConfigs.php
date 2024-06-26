<?php

/**
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class VoetbalOog_Helper_GetBetConfigs extends Zend_Controller_Action_Helper_Abstract
{
	protected $m_sControlRoundPrefix = '_roundid';
	protected $m_sControlBetTypePrefix = '_bettypeid';

	public function direct( $oCompetitionSeason )
	{
		$arrParams = $this->getRequest()->getParams(); // getUserParams
		$arrRBCs = array();
		$oRounds = $oCompetitionSeason->getRounds();
		foreach( $oRounds as $oRound )
		{
			foreach ( $arrParams as $sId => $sValue )
			{
				$nBetType = $this->getBetTypeFromInput( 'chkbettype', $sId, $oRound );
				if ( $nBetType === null )
					continue;

				if ( $sValue !== "on" )
					continue;

				$nBetTime = $this->getOtherFromInput( 'cbxbettime', $oRound, $nBetType );
				$nPoints = $this->getOtherFromInput( 'cbxpoints', $oRound, $nBetType );

				$arrRBCs[] = array(
					"roundid" => $oRound->getId(),
					"bettype" => $nBetType,
					"bettime" => $nBetTime,
					"points" => $nPoints,
				);
			}
		}
        return $arrRBCs;
	}

	protected function getBetTypeFromInput( $sInputPrefix, $sInputId, $oRound )
	{
		$sControlBetTypePrefix = $sInputPrefix . $this->m_sControlRoundPrefix . $oRound->getId() . $this->m_sControlBetTypePrefix;

		if ( strpos( $sInputId, $sControlBetTypePrefix ) !== false )
			return (int) substr( $sInputId, strlen( $sControlBetTypePrefix ) );
		return null;
	}

	protected function getOtherFromInput( $sInputPrefix, $oRound, $nBetType )
	{
		$sInputId = $sInputPrefix . $this->m_sControlRoundPrefix . $oRound->getId() . $this->m_sControlBetTypePrefix . $nBetType;
		$arrParams = $this->getRequest()->getParams(); // getUserParams
		return $arrParams[ $sInputId ];
	}
}
?>