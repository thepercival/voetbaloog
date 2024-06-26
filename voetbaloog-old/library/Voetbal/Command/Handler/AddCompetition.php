<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:44
 */

class Voetbal_Command_Handler_AddCompetition
{
    public function handle( Voetbal_Command_AddCompetition $command) {

        $oDbWriter = Voetbal_Competition_Factory::createDbWriter();
        $oCompetitions = Voetbal_Competition_Factory::createObjects();
        $oCompetitions->addObserver( $oDbWriter );
        $oCompetition = Voetbal_Competition_Factory::createObject();
        $oCompetition->putId( "__NEW__" );
        $oCompetition->putName( $command->getName() );
        $oCompetition->putAbbreviation( $command->getAbbreviation() );
        $oCompetitions->add( $oCompetition );
        $oDbWriter->write();
        return $oCompetition;
    }
}

