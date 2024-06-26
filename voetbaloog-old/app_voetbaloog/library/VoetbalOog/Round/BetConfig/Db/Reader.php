<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Reader.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Round_BetConfig_Db_Reader extends Source_Db_Reader implements Source_Reader_Ext_Interface
{
	public function __construct( $oFactory )
	{
		parent::__construct( $oFactory );

		$this->addPersistance( Voetbal_Round_Factory::createDbPersistance() );
	}

	protected function getSelectFrom( $bCount = false )
	{
		$oSelect = parent::getSelectFrom( $bCount );
		$sTableRounds = Voetbal_Round_Db_Persistance::getTable()->getName();
		$oSelect->join( array( $sTableRounds => $sTableRounds ), $this->getTableName().".RoundId = ".$sTableRounds.".Id", array() );
		return $oSelect;
	}


	/**
	* @see Source_Reader_Ext_Interface::createObjectsExt()
	*/
    public function createObjectsExt( $oObject, Construction_Option_Collection $oOptions = null, $sClassName = null ): Patterns_Collection
	{
		if ( $oOptions === null )
			$oOptions = Construction_Factory::createOptions();

		if ( $oObject instanceof VoetbalOog_Pool )
		{
			$oOptions->addFilter("Voetbal_Round::CompetitionSeason", "EqualTo", $oObject->getCompetitionSeason() );
			$oOptions->addFilter("VoetbalOog_Round_BetConfig::Pool", "EqualTo", $oObject );
		}
		else if ( $oObject instanceof Voetbal_CompetitionSeason )
		{
			$oOptions->addFilter("Voetbal_Round::CompetitionSeason", "EqualTo", $oObject );
			$oOptions->addFilter("VoetbalOog_Round_BetConfig::Pool", "EqualTo", null );
		}

		$oOptions->addOrder( "Voetbal_Round::Number", false );

		$oSelect = $this->getQuery( $oOptions );

		return $this->createObjectsHelper( $oSelect, $this->getCustomReadProperties( $oOptions ) );
	}

	/**
	 * @see Source_Db_Reader::createObjectsHelper()
	 */
    protected function createObjectsHelper( Zend_Db_Select $oSelect, Patterns_Collection $oReadProperties = null ): Patterns_Collection
	{
		$oObjects = $this->m_objFactory->createObjects();

		$oPool = $this->m_objFactory->getPool();

		$oObjectProperties = $this->getObjectPropertiesToRead();
		if ( $oReadProperties !== null )
			$oObjectProperties = $oReadProperties;

		try
		{
			$stmt = $this->m_objDatabase->prepare( $oSelect );
			$stmt->execute( $this->m_arrBindVars );
			$this->m_arrBindVars = array();

			$sObjectPropertyId = $this->m_objFactory->getIdProperty();

			$nPreviousRoundId = null;
			$oRoundBetConfigs = null;
			while ( $row = $stmt->fetch() )
			{
				$nRoundId = $row[ "RoundId" ];
				if ( $nRoundId !== $nPreviousRoundId )
				{
					$oRoundBetConfigs = new Patterns_ObservableObject_Collection_Idable();
					$oRoundBetConfigs->putId( $nRoundId );
					$oObjects->add( $oRoundBetConfigs );
				}

				$sVtId = $this->getValue( $row, $sObjectPropertyId );

				$oObject = $oPool[ $sVtId ];
				if ( $oObject === null )
				{
					$oObject = $this->createObjectFromRow( $row, $oObjectProperties );
					$oPool->add( $oObject );
				}
				$oRoundBetConfigs->add( $oObject );

				$nPreviousRoundId = $nRoundId;
			}
		}
		catch ( Exception $e )
		{
			throw new Exception( $e->getMessage().", For Query: ".(string) $oSelect, E_ERROR );
		}

		return $oObjects;
	}

	/*
	public function createBetTypes( $oOptions = null )
	{
		$nBetTypes = 0;

		$oSelect = $this->getQuery( $oOptions );
		$oSelect->reset( Zend_Db_Select::COLUMNS );
		$oSelect->columns( array( "SUM(BetType) as BetTypeNumber" ) );
		$oSelect->group( array( "RoundId", "PoolId") );

		try
		{
			$stmt = $this->m_objDatabase->prepare( $oSelect );
			$stmt->execute( $this->m_arrBindVars );
			$this->m_arrBindVars = array();

			while ( $row = $stmt->fetch() )
			{
				$nBetTypes += $row["BetTypeNumber"];
		 	}
		}
		catch ( Exception $e)
		{
			throw new Exception( $e->getMessage().", For Query: ".(string) $rr, E_ERROR );
		}

		return $nBetTypes;
	}
	*/
	// vull hier
}