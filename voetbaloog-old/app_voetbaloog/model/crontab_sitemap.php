<?php

include_once 'crontab_base.php';

function executeScheduledTasks()
{
	$cfgWeb = new Zend_Config_Ini( APPLICATION_PATH.'/configs/config.ini', 'web');
	$sHeader = "VoetbalOog Sitemap(".$cfgWeb->map.")";
	$sOutput = "Hallo,<br><br>Hierbij een overzicht van de uitgevoerde taken<br><br>";

	$sOutputUpdate = update();
	if ( $sOutputUpdate === true )
		return;

	$sOutput .= $sOutputUpdate;

	$cfgAuth = new Zend_Config_Ini( APPLICATION_PATH.'/configs/config.ini', 'auth');
	if ( $cfgAuth->superadminemail !== null )
		RAD_Email::sendHtml( $cfgAuth->superadminemail, $sHeader, $sOutput );

	// echo $sOutput;
}


/**
 * walkthrough pools and fill site map
 *
 * @return bool|string
 */
function update()
{
	$cfgWeb = new Zend_Config_Ini( APPLICATION_PATH.'/configs/config.ini', 'web');

	$sContent = $cfgWeb->map . PHP_EOL;
	$sContent .= $cfgWeb->map . "user/register/" . PHP_EOL;
	$sContent .= $cfgWeb->map . "user/login/" . PHP_EOL;
	$sContent .= $cfgWeb->map . "user/forgetpassword/" . PHP_EOL;

	$oOptions = Construction_Factory::createOptions();
	$oOptions->addOrder( "VoetbalOog_Pool::Name", false );
	$oOptions->addOrder( "Voetbal_Season::StartDateTime", true );
	$oPools = VoetbalOog_Pool_Factory::createObjectsFromDatabase( $oOptions );

	$sPreviousPoolName = null;
	foreach( $oPools as $oPool )
	{
		if ( $sPreviousPoolName === null or $sPreviousPoolName !== $oPool->getName() )
		{
			$sContent .= $cfgWeb->map . str_replace( " ", "_", $oPool->getName() ) . "/" . PHP_EOL;
		}

		$sSeasonName = $oPool->getCompetitionSeason()->getSeason()->getName();
		$sContent .= $cfgWeb->map . str_replace( " ", "_", $oPool->getName() ) . "/?seizoen=" . $sSeasonName . PHP_EOL;

		$sPreviousPoolName =$oPool->getName();
	}

	try
	{
		$sPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "pub_voetbaloog" . DIRECTORY_SEPARATOR . "sitemap.txt" ;
		file_put_contents( $sPath, $sContent );
	}
	catch ( Exception $oException )
	{
		return "sitemap.txt kan niet worden weggeschreven : " . $oException->getMessage();
	}

	return true;
}

executeScheduledTasks();
?>
