<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 14-12-15
 * Time: 16:47
 */

/**
 * @package Voetbal
 */
interface Voetbal_Extern_System_Interface
{
    /**
     * @param string $sCompetitionId
     * @param string $sSeasonId
     * @return array|Voetbal_Extern_Team[]
     */
    public function getTeams( string $sCompetitionId, string $sSeasonId ): array;

    /**
     * @param string $sCompetitionId
     * @param string $sSeasonId
     * @return string
     */
    public function getUrlForTeams( string $sCompetitionId, string $sSeasonId ): string;

    /**
     * @return int
     */
    public function getCacheTimeForTeams(): int;

    /**
     * @param string $sCompetitionId
     * @param string $sSeasonId
     * @param int|null $nGameRoundId
     * @return array|Voetbal_Extern_Game[]
     */
    public function getGames( string $sCompetitionId, string $sSeasonId, int $nGameRoundId = null ): array;

    /**
     * @param string $sCompetitionId
     * @param string $sSeasonId
     * @param int|null $nGameRoundId
     * @return string
     */
    public function getUrlForGames( string $sCompetitionId, string $sSeasonId, int $nGameRoundId = null ): string;

    /**
     * @return int
     */
    public function getCacheTimeForGames(): int;

    /**
     * @param string $sCompetitionId
     * @param string $sSeasonId
     * @param int $nGameId
     * @return Voetbal_Extern_GameExt
     */
    public function getGame( string $sCompetitionId, string $sSeasonId, int $nGameId /*, Agenda_TimeSlot $oDefaultPlayerPeriodTimeSlot */ ): Voetbal_Extern_GameExt;

    /**
     * @param string $sCompetitionId
     * @param string $sSeasonId
     * @param int $nGameId
     * @return string
     */
    public function getUrlForGame( string $sCompetitionId, string $sSeasonId, int $nGameId ): string;

    /**
     * @return int
     */
    public function getCacheTimeForGame(): int;
}
