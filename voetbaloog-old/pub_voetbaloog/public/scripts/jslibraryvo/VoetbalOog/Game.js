function VoetbalOog_Game()
{
	VoetbalOog_Game.baseConstructor.call( this );

	var m_oHomePoulePlace = null;
	var m_oAwayPoulePlace = null;
	var m_nHomeGoals = null;
	var m_nAwayGoals = null;
	var m_nHomeGoalsExtraTime = null;
	var m_nAwayGoalsExtraTime = null;
	var m_nHomeGoalsPenalty = null;
	var m_nAwayGoalsPenalty = null;
	var m_sCity = null;
	var m_nState = null;
	var m_nViewOrder = null;
	var m_oParticipations = null;
	var m_oGoals = null;

	this.getHomePoulePlace = function()
	{
		if ( typeof m_oHomePoulePlace == 'number' )
			m_oHomePoulePlace = VoetbalOog_PoulePlace_Factory().createObjectFromDatabase( m_oHomePoulePlace );
		return m_oHomePoulePlace;
	};
	this.putHomePoulePlace = function( oHomePoulePlace ){ m_oHomePoulePlace = oHomePoulePlace; };

	this.getAwayPoulePlace = function()
	{
		if ( typeof m_oAwayPoulePlace == 'number' )
			m_oAwayPoulePlace = VoetbalOog_PoulePlace_Factory().createObjectFromDatabase( m_oAwayPoulePlace );
		return m_oAwayPoulePlace;
	};
	this.putAwayPoulePlace = function( oAwayPoulePlace ){ m_oAwayPoulePlace = oAwayPoulePlace; };

	this.getHomeGoals = function(){ return m_nHomeGoals; };
	this.putHomeGoals = function( nHomeGoals ){ m_nHomeGoals = nHomeGoals; };

	this.getAwayGoals = function(){ return m_nAwayGoals; };
	this.putAwayGoals = function( nAwayGoals ){ m_nAwayGoals = nAwayGoals; };

	this.getHomeGoalsExtraTime = function(){ return m_nHomeGoalsExtraTime; };
	this.putHomeGoalsExtraTime = function( nHomeGoalsExtraTime ){ m_nHomeGoalsExtraTime = nHomeGoalsExtraTime; };

	this.getAwayGoalsExtraTime = function(){ return m_nAwayGoalsExtraTime; };
	this.putAwayGoalsExtraTime = function( nAwayGoalsExtraTime ){ m_nAwayGoalsExtraTime = nAwayGoalsExtraTime; };

	this.getHomeGoalsPenalty = function(){ return m_nHomeGoalsPenalty; };
	this.putHomeGoalsPenalty = function( nHomeGoalsPenalty ){ m_nHomeGoalsPenalty = nHomeGoalsPenalty; };

	this.getAwayGoalsPenalty = function(){ return m_nAwayGoalsPenalty; };
	this.putAwayGoalsPenalty = function( nAwayGoalsPenalty ){ m_nAwayGoalsPenalty = nAwayGoalsPenalty; };

	this.getHomeGoalsCalc = function( bCountPenalties )
	{
		if ( this.getHomeGoalsPenalty() >= 0 && bCountPenalties == true )
			return this.getHomeGoalsPenalty();
		else if ( this.getHomeGoalsExtraTime() >= 0 )
			return this.getHomeGoalsExtraTime();
		return this.getHomeGoals();
	};
	this.getAwayGoalsCalc = function( bCountPenalties )
	{
		if ( this.getAwayGoalsPenalty() >= 0 && bCountPenalties == true )
			return this.getAwayGoalsPenalty();
		else if ( this.getAwayGoalsExtraTime() >= 0 )
			return this.getAwayGoalsExtraTime();
		return this.getAwayGoals();
	};

	this.getCity = function(){ return m_sCity; };
	this.putCity = function( sCity ){ m_sCity = sCity; };

	this.getState = function(){ return m_nState; };
	this.putState = function( nState ){ m_nState = nState; };

	this.getViewOrder = function(){ return m_nViewOrder; };
	this.putViewOrder = function( nViewOrder ){ m_nViewOrder = nViewOrder; };

	this.getParticipations = function( oProvider )
	{
		if ( oProvider != null )
		{
			var oParticipations = new Object();
			for ( var nI in m_oParticipations )
			{
				if ( !( m_oParticipations.hasOwnProperty( nI ) ) )
					continue;

				var oParticipation = m_oParticipations[nI];
				if ( oParticipation.getTeamMembershipPlayer().getProvider() == oProvider )
					oParticipations[nI] = oParticipation;
			}
			return oParticipations;
		}
		return m_oParticipations;
	};
	this.putParticipations = function( oParticipations ){ m_oParticipations = oParticipations; };

	this.getGoals = function(){ return m_oGoals; };
	this.putGoals = function( oGoals ){ m_oGoals = oGoals; };

	this.getResult = function ()
	{
		if ( this.getState() == g_jsonVoetbal.nState_Played )
		{
			if ( this.getHomeGoals() == this.getAwayGoals() )
				return 0;
			else if ( this.getHomeGoals() > this.getAwayGoals() )
				return 1;
			else // if ( this.getHomeGoals() < this.getAwayGoals() )
				return -1;
		}
		return null;
	};
}
Inheritance_Manager.extend(VoetbalOog_Game, Agenda_TimeSlot);