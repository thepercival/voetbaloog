<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:44
 */

class VoetbalOog_Command_Handler_CopyBets
{
    public function handle( VoetbalOog_Command_CopyBets $command )
    {
        $oFromPoolUser = $command->getFromPoolUser();
        $oToPoolUser = $command->getToPoolUser();

        if ( $oFromPoolUser === null )
            throw new Exception( "de van-pool(bron) kon niet gevonden worden", E_ERROR );
        else if ( $oToPoolUser === null )
            throw new Exception( "de naar-pool(doel) kon niet gevonden worden", E_ERROR );
        else if ( $oFromPoolUser === $oToPoolUser )
            throw new Exception( "de van-pool(bron) en naar-pool(doel) kunnen niet gelijk zijn aan elkaar", E_ERROR );

        $oDbWriter = VoetbalOog_Bet_Factory::createDbWriter();

        $nMaxRBCsToCopy = 0; $nRBCsCopied = 0;

        $oRounds = $oFromPoolUser->getPool()->getCompetitionSeason()->getRounds();
        foreach ( $oRounds as $oRound )
        {
            list( $nMaxRBCsToCopyTmp, $nRBCsCopiedTmp ) = $this->copyBets( $oRound, $oFromPoolUser, $oToPoolUser, $oDbWriter );
            $nMaxRBCsToCopy += $nMaxRBCsToCopyTmp;
            $nRBCsCopied += $nRBCsCopiedTmp;
        }

        $oDbWriter->write();

        $command->putSuccessMessage( $nRBCsCopied." van de ".$nMaxRBCsToCopy." voorspel-ronden zijn gekopi&euml;erd" );
    }

    protected function copyBets( Voetbal_Round $oRound, VoetbalOog_Pool_User $oFromPoolUser, VoetbalOog_Pool_User $oToPoolUser, $oDbWriter ) {
        $oNow          = Agenda_Factory::createDateTime(/*"2012-01-01 08:30"*/ );
        $oPool         = $oFromPoolUser->getPool();
        $oPoolToCopyTo = $oToPoolUser->getPool();

        $oFromRoundBetConfigs = $oPool->getBetConfigs( $oRound );
        $oToRoundBetConfigs   = $oPoolToCopyTo->getBetConfigs( $oRound );

        $nRBCsCopied = 0;

        foreach ( $oFromRoundBetConfigs as $oFromRoundBetConfig ) {
            foreach ( $oToRoundBetConfigs as $oToRoundBetConfig ) {
                if ( $oFromRoundBetConfig->getBetType() !== $oToRoundBetConfig->getBetType()
                     or $oFromRoundBetConfig->getBetTime() !== $oToRoundBetConfig->getBetTime()
                ) {
                    continue;
                }

                $nRBCsCopied++;

                $oFromBets = $oFromPoolUser->getBets( $oFromRoundBetConfig );
                $oToBets   = $oToPoolUser->getBets( $oToRoundBetConfig );
                $oToBets->addObserver( $oDbWriter );

                // loop door de frombets en update of insert
                foreach ( $oFromBets as $oFromBet ) {
                    if ( $oNow >= $oFromBet->getDeadLine() ) {
                        continue;
                    }

                    $oOptions = Construction_Factory::createOptions();
                    $oOptions->addFilter( "VoetbalOog_Bet::PoolUser", "EqualTo", $oToPoolUser );
                    $oOptions->addFilter( "VoetbalOog_Bet::RoundBetConfig", "EqualTo", $oToRoundBetConfig );

                    if ( $oFromRoundBetConfig->getBetType() === VoetbalOog_Bet_Score::$nId
                         or $oFromRoundBetConfig->getBetType() === VoetbalOog_Bet_Result::$nId
                    ) {
                        $oOptions->addFilter( "VoetbalOog_Bet_Score::Game", "EqualTo", $oFromBet->getGame() );
                        $oToBet = VoetbalOog_Bet_Factory::createObjectFromDatabase( $oOptions );
                        if ( $oToBet === null ) {
                            $oToBet = null;
                            if ( $oFromRoundBetConfig->getBetType() === VoetbalOog_Bet_Score::$nId ) {
                                $oToBet = VoetbalOog_Bet_Factory::createScore();
                            } else if ( $oFromRoundBetConfig->getBetType() === VoetbalOog_Bet_Result::$nId ) {
                                $oToBet = VoetbalOog_Bet_Factory::createResult();
                            }

                            $oToBet->putId( "__NEW__" . $oFromBet->getId() );
                            $oToBet->putPoolUser( $oToPoolUser );
                            $oToBet->putRoundBetConfig( $oToRoundBetConfig );
                            $oToBet->putGame( $oFromBet->getGame() );
                            $oToBets->add( $oToBet );
                        } else {
                            $oToBet->addObserver( $oDbWriter );
                        }

                        if ( $oFromRoundBetConfig->getBetType() === VoetbalOog_Bet_Score::$nId ) {
                            $oToBet->putHomeGoals( $oFromBet->getHomeGoals() );
                            $oToBet->putAwayGoals( $oFromBet->getAwayGoals() );
                        } else if ( $oFromRoundBetConfig->getBetType() === VoetbalOog_Bet_Result::$nId ) {
                            $oToBet->putResult( $oFromBet->getResult() );
                        }
                    } else if ( $oFromRoundBetConfig->getBetType() === VoetbalOog_Bet_Qualify::$nId ) {
                        $oOptions->addFilter( "VoetbalOog_Bet_Qualify::PoulePlace", "EqualTo", $oFromBet->getPoulePlace() );
                        $oToBet = VoetbalOog_Bet_Factory::createObjectFromDatabase( $oOptions );
                        if ( $oToBet === null ) {
                            $oToBet = VoetbalOog_Bet_Factory::createQualify();

                            $oToBet->putId( "__NEW__" . $oFromBet->getId() );
                            $oToBet->putPoolUser( $oToPoolUser );
                            $oToBet->putRoundBetConfig( $oToRoundBetConfig );
                            $oToBet->putPoulePlace( $oFromBet->getPoulePlace() );
                            $oToBets->add( $oToBet );
                        } else {
                            $oToBet->addObserver( $oDbWriter );
                        }

                        $oToBet->putTeam( $oFromBet->getTeam() );
                    }
                }

                // loop door de tobets en als niet bestaat bij from dan verwijderen
                $oBetsToRemove = VoetbalOog_Bet_Factory::createObjects();
                foreach ( $oToBets as $oToBet ) {
                    if ( $oNow >= $oToBet->getDeadLine() ) {
                        continue;
                    }

                    $oOptions = Construction_Factory::createOptions();
                    $oOptions->addFilter( "VoetbalOog_Bet::PoolUser", "EqualTo", $oFromPoolUser );
                    $oOptions->addFilter( "VoetbalOog_Bet::RoundBetConfig", "EqualTo", $oFromRoundBetConfig );

                    if ( $oFromRoundBetConfig->getBetType() === VoetbalOog_Bet_Score::$nId
                         or $oFromRoundBetConfig->getBetType() === VoetbalOog_Bet_Result::$nId
                    ) {
                        $oOptions->addFilter( "VoetbalOog_Bet_Score::Game", "EqualTo", $oToBet->getGame() );
                        $oFromBet = VoetbalOog_Bet_Factory::createObjectFromDatabase( $oOptions );
                        if ( $oFromBet === null ) {
                            $oBetsToRemove->add( $oToBet );
                        }
                    } else if ( $oFromRoundBetConfig->getBetType() === VoetbalOog_Bet_Qualify::$nId ) {
                        $oOptions->addFilter( "VoetbalOog_Bet_Qualify::PoulePlace", "EqualTo", $oToBet->getPoulePlace() );
                        $oFromBet = VoetbalOog_Bet_Factory::createObjectFromDatabase( $oOptions );
                        if ( $oFromBet === null ) {
                            $oBetsToRemove->add( $oToBet );
                        }
                    }
                }
                $oToBets->removeCollection( $oBetsToRemove );
            }
        }
        return array( $oToRoundBetConfigs->count(), $nRBCsCopied );
    }
}