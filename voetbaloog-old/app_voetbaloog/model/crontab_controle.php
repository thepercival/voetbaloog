<?php

include_once 'crontab_base.php';

function executeScheduledTasks()
{
	$cfgWeb = new Zend_Config_Ini( APPLICATION_PATH.'/configs/config.ini', 'web');
	$sHeader = "VoetbalOog Users Report(".$cfgWeb->map.")";
	$sOutput = "Hallo,<br><br>Hierbij een overzicht van de uitgevoerde taken<br><br>";

	$sControleOutput = controle();
	if ( $sControleOutput === true )
		return;

	$sOutput .= $sControleOutput;

	$cfgAuth = new Zend_Config_Ini( APPLICATION_PATH.'/configs/config.ini', 'auth');

	if ( $cfgAuth->superadminemail !== null )
		RAD_Email::sendHtml( $cfgAuth->superadminemail, $sHeader, $sOutput );

	// echo $sOutput;
}

function controle()
{
	$szStyle = "style='background-color:white;'";
	$szHeaderStyle = "style='background-color:orange; font-weight:bold;'";

	$sOutput = "<table style='background-color:orange;'>";
	$sOutput .= "<tr><th ".$szHeaderStyle." colspan=4>Controle</th></tr>";
	$sOutput .= "<tr><th ".$szHeaderStyle.">Startdatumtijd</th><th ".$szHeaderStyle.">Taak</th><th ".$szHeaderStyle.">Resultaat</th><th ".$szHeaderStyle.">Einddatumtijd</th></tr>";

	$bSendEmail = false;

    $sOutput .= "<tr>";
	try
	{
		$sOutput .= "<td ".$szStyle.">".Agenda_Factory::createDateTime()->toString("l d F Y G:i:s")."</td>";
		$sOutput .= "<td ".$szStyle.">ongeactiveerde-gebruikers-controle(> 30 dagen)</td>";
		$vtRetVal = checkUnactivatedUsers();
		if ( $vtRetVal !== true )
			$bSendEmail = true;
		else
			$vtRetVal = "geen gebruikers gevonden die langer als 30 dagen zijn of haar account niet heeft geactiveerd.";

		$sOutput .= "<td ".$szStyle.">".$vtRetVal."</td>";
		$sOutput .= "<td ".$szStyle.">".Agenda_Factory::createDateTime()->toString("l d F Y G:i:s")."</td>";
	}
	catch ( Exception $oException )
	{
		$sOutput .= "<td colspan=2 ".$szStyle."><div style='font-weight:bold; color:red;'>".$oException->getMessage()."</div></td>";
	}
    $sOutput .= "</tr>";

    $sOutput .= "<tr>";
	try
	{
		$sOutput .= "<tr><td ".$szStyle.">".Agenda_Factory::createDateTime()->toString("l d F Y G:i:s")."</td>";
		$sOutput .= "<td ".$szStyle.">pools-met-enkele-deelnemer-controle</td>";
		$vtRetVal = checkEndedPoolsTooSmall();
		if ( $vtRetVal !== true )
			$bSendEmail = true;
		else
			$vtRetVal = "geen pools gevonden die afgelopen zijn en maar een enkele deelnemer bevatten.";

		$sOutput .= "<td ".$szStyle.">".$vtRetVal."</td>";
		$sOutput .= "<td ".$szStyle.">".Agenda_Factory::createDateTime()->toString("l d F Y G:i:s")."</td>";
	}
	catch ( Exception $oException )
	{
		$sOutput .= "<td colspan=2 ".$szStyle."><div style='font-weight:bold; color:red;'>".$oException->getMessage()."</div></td>";
	}
    $sOutput .= "</tr>";

	$sOutput .= "<tr>";
	try
	{
		$sOutput .= "<tr><td ".$szStyle.">".Agenda_Factory::createDateTime()->toString("l d F Y G:i:s")."</td>";
		$sOutput .= "<td ".$szStyle.">deelnemers-zonder-voorspellingen-controle</td>";
		$vtRetVal = checkPoolsUsersWithoutBets();
		if ( $vtRetVal !== true )
			$bSendEmail = true;
		else
			$vtRetVal = "geen deelnemers gevonden zonder voorspellingen in pools die afgelopen zijn.";

		$sOutput .= "<td ".$szStyle.">".$vtRetVal."</td>";
		$sOutput .= "<td ".$szStyle.">".Agenda_Factory::createDateTime()->toString("l d F Y G:i:s")."</td>";
	}
	catch ( Exception $oException )
	{
		$sOutput .= "<td colspan=2 ".$szStyle."><div style='font-weight:bold; color:red;'>".$oException->getMessage()."</div></td>";
	}
	$sOutput .= "</tr>";

	$sOutput .= "</table>";

	if ( $bSendEmail === false )
		return true;

	return $sOutput;
}

function checkUnactivatedUsers()
{
	$oThirtyDaysAgo = Agenda_Factory::createDateTime();
	$oThirtyDaysAgo->modify("-30 days");

	$oOptions = Construction_Factory::createOptions();
	$oOptions->addFilter( "VoetbalOog_User::ActivationKey", "NotEqualTo", null );
	$oOptions->addFilter( "VoetbalOog_User::LatestLoginDateTime", "SmallerThan", $oThirtyDaysAgo );
	$oUsers = VoetbalOog_User_Factory::createObjectsFromDatabase( $oOptions );

	if ( $oUsers->count() === 0 )
		return true;

	$sOutput = "<table border=\"1\">";
	$sOutput .= "<tr><th>verwijderde gebruiker</th><th>datumtijd geregistreerd</th></tr>";

	foreach( $oUsers as $oUser )
	{
		$sOutput .= "
			<tr>
				<td>".$oUser->getName()."</td>
				<td>".$oUser->getLatestLoginDateTime()."</td>
			</tr>
		";
	}
	$sOutput .= "</table>";

	$oDbWriter = RAD_Auth_User_Factory::createDbWriter();
	$oUsers->addObserver( $oDbWriter );
	$oUsers->flush();
	$oDbWriter->write();

	return $sOutput;
}

function checkEndedPoolsTooSmall()
{
	$oNow = Agenda_Factory::createDateTime();

	$oEndedPoolsTooSmall = VoetbalOog_Pool_Factory::createObjects();
	{
		$oPools = VoetbalOog_Pool_Factory::createObjectsFromDatabase();
		foreach( $oPools as $oPool )
		{
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "VoetbalOog_Round_BetConfig::Pool", "EqualTo", $oPool );
			if ( $oNow > $oPool->getStartDateTime() and ( $oPool->getUsers()->count() <= 1 or VoetbalOog_Bet_Factory::getNrOfObjectsFromDatabase( $oOptions ) === 0 ) )
			{
				$oEndedPoolsTooSmall->add( $oPool );
			}
		}
	}

	if ( $oEndedPoolsTooSmall->count() === 0 )
		return true;

	$arrEmails = array();
	$sOutput = "<table border=\"1\">";
	{
		$sOutput .= "<tr><th>verwijderde pool</th><th>competitieseizoen</th></tr>";

		foreach( $oEndedPoolsTooSmall as $oEndedPoolTooSmall )
		{
			$sOutput .= "
				<tr>
					<td>".$oEndedPoolTooSmall->getName()."</td>
					<td>".$oEndedPoolTooSmall->getCompetitionSeason()->getName()."</td>
				</tr>
			";
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "VoetbalOog_Pool_User::Admin", "EqualTo", true );
			$oOptions->addFilter( "VoetbalOog_Pool_User::Pool", "EqualTo", $oEndedPoolTooSmall );
			$oPoolUsers = VoetbalOog_Pool_User_Factory::createObjectsFromDatabase( $oOptions );
			$arrEmails[] = $oPoolUsers;
		}
		$sOutput .= "</table>";
	}

	$oDbWriter = VoetbalOog_Pool_Factory::createDbWriter();
	$oEndedPoolsTooSmall->addObserver( $oDbWriter );
	$oEndedPoolsTooSmall->flush();
	$oDbWriter->write();

	foreach( $arrEmails as $oPoolAdmins ) {
		foreach( $oPoolAdmins as $oPoolAdmin )
		{
			try{
				RAD_Email::sendHtml( $oPoolAdmin->getUser()->getEmailAddress(), "pool verwijderd",
					"Beste " . $oPoolAdmin->getUser()->getName(). ", " . PHP_EOL . PHP_EOL .
			        "Om vervuiling te voorkomen is je pool ". $oPoolAdmin->getPool()->getName() ." verwijderd." . PHP_EOL .
					"Dit heeft 1 van de volgende reden : " . PHP_EOL . PHP_EOL .
					"Er zijn geen voorspellingen gedaan" . PHP_EOL .
					"Je bent de enige deelnemer aan de pool" . PHP_EOL . PHP_EOL .
					"Bedankt voor het gebruik van VoetbalOog." . PHP_EOL . PHP_EOL .
					"groet VoetbalOog"
				);
			}
			catch( Exception $e )
			{
				$sOutput .= "<div style='color: red'>".$e->getMessage()."</div>";
			}
		}
	}

	return $sOutput;
}

function checkPoolsUsersWithoutBets()
{
	$oPoolUsersWithoutBets = VoetbalOog_Pool_User_Factory::createObjects();
	{
		$oNow        = Agenda_Factory::createDateTime();
		$oOneDayBack = Agenda_Factory::createDateTime();
		$oOneDayBack->modify( "-1 days" );
		$oTimeSlot = Agenda_Factory::createTimeSlot( $oOneDayBack, $oNow );

		$oOptions = Construction_Factory::createFiltersForTimeSlots( "Voetbal_Season", $oTimeSlot );
		$oOptions->addFilter( "Voetbal_CompetitionSeason::Public", "EqualTo", true );
		$oOptions->addOrder( "Voetbal_Season::StartDateTime", true );
		$bStarted            = true;
		$bEnded              = false;
		$oCompetitionSeasons = Voetbal_CompetitionSeason_Factory::createObjectsFromDatabaseCustom( $bStarted, $bEnded, $oOptions );
		{
			foreach ( $oCompetitionSeasons as $oCompetitionSeason ) {
				$oOptions = Construction_Factory::createOptions();
				$oOptions->addFilter( "VoetbalOog_Pool::CompetitionSeason", "EqualTo", $oCompetitionSeason );
				$oPools = VoetbalOog_Pool_Factory::createObjectsFromDatabase( $oOptions );
				foreach ( $oPools as $oPool ) {
					if ( !$oTimeSlot->overlapses( $oPool, Agenda_TimeSlot::EXCLUDE_AFTEREND ) )
						continue;

					$oPoolUsers = $oPool->getUsers();
					foreach ( $oPoolUsers as $oPoolUser ) {
						if ( $oPoolUser->getAdmin() !== true and $oPoolUser->getNrOfBets() === 0 ) {
							$oPoolUsersWithoutBets->add( $oPoolUser );
						}
					}
				}
			}
		}
	}

	if ( $oPoolUsersWithoutBets->count() === 0 )
		return true;

	$sOutput = "<table border=\"1\">";
	{
		$sOutput .= "<tr><th>verwijderde poolusers</th><th>pool</th></tr>";

		foreach( $oPoolUsersWithoutBets as $oPoolUserWithoutBets )
		{
			$sOutput .= "
				<tr>
					<td>".$oPoolUserWithoutBets->getUser()->getName()."</td>
					<td>".$oPoolUserWithoutBets->getPool()->getName()."</td>
				</tr>
			";
		}
		$sOutput .= "</table>";
	}

	$oDbWriter = VoetbalOog_Pool_User_Factory::createDbWriter();
	$oPoolUsersWithoutBets->addObserver( $oDbWriter );
	$oPoolUsersWithoutBets->flush();
	$oDbWriter->write();

	return $sOutput;
}


executeScheduledTasks();
?>
