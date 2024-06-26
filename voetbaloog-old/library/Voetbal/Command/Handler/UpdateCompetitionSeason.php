<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 20-12-15
 * Time: 10:04
 */

class Voetbal_Command_Handler_UpdateCompetitionSeason
{
    public function handle(Voetbal_Command_UpdateCompetitionSeason $command)
    {
        $oDbWriter = Voetbal_CompetitionSeason_Factory::createDbWriter();
        $oCompetitionSeason = $command->getCompetitionSeason();
        $oCompetitionSeason->addObserver($oDbWriter);
        if ( $command->getPublic() !== $oCompetitionSeason->getPublic() );
        $oCompetitionSeason->putPublic( $command->getPublic());
        $oDbWriter->write();

        return $oCompetitionSeason;
    }
}