<?php

class VoetbalOog_Command_CopyBets extends Voetbal_Command
{
	private $m_oFromPoolUser;
	private $m_oToPoolUser;
	private $m_sSuccessMessage;

	private $m_oBus;

	public function __construct( $oFromPoolUser, $oToPoolUser )
	{
		$this->m_oFromPoolUser = $oFromPoolUser;
		$this->m_oToPoolUser = $oToPoolUser;
	}

	public function getFromPoolUser(){ return $this->m_oFromPoolUser; }
	public function getToPoolUser(){ return $this->m_oToPoolUser; }

	public function getSuccessMessage(){ return $this->m_sSuccessMessage; }
	public function putSuccessMessage( $sSuccessMessage ){ $this->m_sSuccessMessage = $sSuccessMessage; }

	public function getBus(){ return $this->m_oBus; }
	public function putBus( $oBus ){ $this->m_oBus = $oBus; }
}
