<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 4-12-15
 * Time: 16:07
 */

class Voetbal_Command_AddUpdatePlayerPeriod extends Voetbal_Command
{
    private $m_oPlayerPeriod;
    private $m_oClient;
    private $m_oProvider;
    private $m_oTimeSlot;
    private $m_nBackNumber;
    private $m_nLine;

    private $m_oBus;

    public function __construct( $oPlayerPeriod, $oClient = null, $oProvider = null, $oTimeSlot = null )
    {
        $this->m_oPlayerPeriod = $oPlayerPeriod;
        $this->m_oClient = $oClient;
        $this->m_oProvider = $oProvider;
        $this->m_oTimeSlot = $oTimeSlot;
    }

    /*
        {
            client : { id : 1112, externid : 12 }
            provider : { id : 555, externid : 114 }
            datetimeslot: { startdatetime : '2015-12-25', enddatetime : null },
            backnumber : 12,
            line : 0/1/2/4/8
        }
    */
    public function getPlayerPeriod(){ return $this->m_oPlayerPeriod; }

    public function getClient(){ return $this->m_oClient; }
    public function getProvider(){ return $this->m_oProvider; }
    public function getTimeSlot(){ return $this->m_oTimeSlot; }

    public function getBackNumber(){ return $this->m_nBackNumber; }
    public function putBackNumber( $nBackNumber ){ $this->m_nBackNumber = $nBackNumber; }

    public function getLine(){ return $this->m_nLine; }
    public function putLine( $nLine ){ $this->m_nLine = $nLine; }

    public function getBus(){ return $this->m_oBus; }
    public function putBus( $oBus ){ $this->m_oBus = $oBus; }

    public function getRealProvider( $oProvider )
    {
        if ( $oProvider === null )
            return null;

        if ( $oProvider instanceof Voetbal_Team )
            return $oProvider;

        $oRealProvider = null;
        if ( property_exists( $oProvider, "id" ) and $oProvider->id !== null ) {
            $oRealProvider = Voetbal_Team_Factory::createObjectFromDatabase( $oProvider->id );
        }
        if ( $oRealProvider === null and $oProvider->externid !== null ) {
            $oOptions = Construction_Factory::createOptions();
            $oOptions->addFilter("Voetbal_Team::ExternId", "EqualTo", Import_Factory::$m_szExternPrefix . $oProvider->externid );
            $oRealProvider = Voetbal_Team_Factory::createObjectFromDatabase($oOptions);
        }
        return $oRealProvider;
    }

    public function getRealClient( $oClient )
    {
        if ( $oClient === null )
            return null;

        if ( $oClient instanceof Voetbal_Person )
            return $oClient;

        $oRealClient = null;
        if ( property_exists( $oClient, "id" ) and $oClient->id !== null ) {
            $oRealClient = Voetbal_Person_Factory::createObjectFromDatabase( $oClient->id );
        }
        if ( $oRealClient === null and $oClient->externid !== null ) {
            $oOptions = Construction_Factory::createOptions();
            $oOptions->addFilter("Voetbal_Person::ExternId", "EqualTo", Import_Factory::$m_szExternPrefix . $oClient->externid );
            $oRealClient = Voetbal_Person_Factory::createObjectFromDatabase($oOptions);
        }
        return $oRealClient;
    }

    public function getRealTimeSlot( $oTimeSlot )
    {
       if ( $oTimeSlot instanceof Agenda_TimeSlot )
            return $oTimeSlot;

        if ( $oTimeSlot === null )
            return Agenda_Factory::createTimeSlotNew( null, null );

        return Agenda_Factory::createTimeSlotNew( $oTimeSlot->startdatetime, $oTimeSlot->enddatetime );
    }
}

