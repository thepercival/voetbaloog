<?php

/**
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class VoetbalOog_Helper_AddIncludes extends Zend_Controller_Action_Helper_Abstract
{
	public function direct()
	{
		$sIncludes = null;

		if ( APPLICATION_ENV === "production" )
		{
			$sIncludes = "
				<script src= \"".Zend_Registry::get("jslibraryvo")."voetbaloog-min-1.0.0.js\" ></script>
			";
		}
		else
		{
			$sIncludes = "
				<script src=\"".Zend_Registry::get("jslibrary")."Idable.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibrary")."Object/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibrary")."Agenda/DateTime.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibrary")."Agenda/TimeSlot.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibrary")."AssociativeArray.js\" ></script>

				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Bet.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Bet/Qualify.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Bet/Score.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Bet/Result.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Bet/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/BetType/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/BetTime/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Pool.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Pool/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/User.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/User/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Pool/User.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Pool/User/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Pool/Payment.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Pool/Payment/Factory.js\" ></script>
			 	<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/CompetitionSeason.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/CompetitionSeason/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Round.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Round/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Round/BetConfig.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Round/BetConfig/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Poule.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Poule/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Ranking.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/QualifyRule.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/QualifyRule/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/PoulePlace.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/PoulePlace/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Team.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Team/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Game.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Game/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Game/Participation.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Game/Participation/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Goal.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Goal/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Person.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Person/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Control/Factory.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Control/RankView.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Control/GameView.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Control/CompetitionSeasonView.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Control/Payments.js\" ></script>
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Control/BetConfig.js\" );
			";

			/*
			 * 				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Control/BetView.js\" );
				<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Control/BetEdit.js\"></script>
    			<script src=\"".Zend_Registry::get("jslibraryvo")."VoetbalOog/Control/BetHelper.js\"></script>

			 *
			 */
		}

		return $sIncludes;
	}
}
?>