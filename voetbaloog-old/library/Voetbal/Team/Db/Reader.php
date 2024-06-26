<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Reader.php 627 2013-12-15 20:18:35Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Team_Db_Reader extends Source_Db_Reader implements Source_Reader_Ext_Nr_Interface
{
	public function __construct( $oFactory )
	{
		parent::__construct( $oFactory );
	}

    /**
     * @see Source_Reader_Ext_Nr_Interface::getNrOfObjectsExt()
     */
    public function getNrOfObjectsExt( $oObject, Construction_Option_Collection $oOptions = null, $sClassName = null ): int
    {
        if ( $oObject === null )
            throw new Exception( "Object can not be null", E_ERROR );

        if ( $oOptions === null )
            $oOptions = Construction_Factory::createOptions();

        $oSelect = $this->getSelectFrom( false );

        if ( $oObject instanceof Voetbal_CompetitionSeason )
        {
            $oOptions->addFilter( "Voetbal_Round::CompetitionSeason", "EqualTo", $oObject );
            $this->addPersistance( Voetbal_Round_Factory::createDbPersistance() );

            $sTablePoulePlaces = Voetbal_PoulePlace_Db_Persistance::getTable()->getName();
            $sTablePoules = Voetbal_Poule_Db_Persistance::getTable()->getName();
            $sTableRounds = Voetbal_Round_Db_Persistance::getTable()->getName();

            $oSelect
                ->distinct()
                ->join(array($sTablePoulePlaces), $this->getTableName().".Id = ".$sTablePoulePlaces.".TeamId", array() )
                ->joinLeft(array( $sTablePoules ), $sTablePoulePlaces.".PouleId = Poules.Id", array() )
                ->joinLeft(array( $sTableRounds ), $sTablePoules.".RoundId = ".$sTableRounds.".Id", array() )
            ;
        }
        else
        {
            $sDescription = ( $oObject !== null ? "Object of instance " . get_class( $oObject ) . " is not correct" : "Object is empty" );
            throw new Exception( $sDescription . " in " . __METHOD__, E_ERROR );
        }

        $this->addWhereOrderBy( $oSelect, $oOptions );

        $oCountSelect = $this->m_objDatabase->select();
        $oCountSelect->from( $oSelect, array( static::COUNT_COLUMN => new Zend_Db_Expr("COUNT(*)") ) );
        // var_dump( $this->m_arrBindVars ); echo $oCountSelect; die();
        return $this->getNrOfObjectsHelper( $oCountSelect );
    }
}