function Agenda_TimeSlot( vtStartDateTime, vtEndDateTime ) 
{
	/// this.Idable();
	Agenda_TimeSlot.baseConstructor.call( this );
	
	if ( typeof vtStartDateTime == 'number' )
		vtStartDateTime = new Date( vtStartDateTime );
	if ( typeof vtEndDateTime == 'number' )
		vtEndDateTime = new Date( vtEndDateTime );
			
	var m_oStartDateTime = vtStartDateTime;
	var m_oEndDateTime = vtEndDateTime;
	
	this.getStartDateTime = function(){ return m_oStartDateTime; };
	this.putStartDateTime = function( oStartDateTime ){ m_oStartDateTime = oStartDateTime; };
	
	this.getEndDateTime = function(){ return m_oEndDateTime; };
	this.putEndDateTime = function( oEndDateTime ){ m_oEndDateTime = oEndDateTime; };
	
	this.getDuration = function( sPart )
	{	
		if ( sPart != 'D' && sPart != 'H' && sPart != 'm' )
			throw("getDuration: parameter must be D, h or m");
		
		var nDiff = m_oEndDateTime - m_oStartDateTime;
		nDiff /= 1000;
		nDiff /= 60;
		if ( sPart == 'm' )
			return Math.round( nDiff );
		nDiff /= 60;
		if ( sPart == 'H' )
			return Math.round( nDiff );
		nDiff /= 24;
		if ( sPart == 'D' )
			return Math.round( nDiff );

		return undefined;
	};
	
	this.overlapses = function( objTimeSlot, nRange /* Agenda_TimeSlot.nExcludeNone */ )
  	{
		if ( nRange == undefined )
			nRange = Agenda_TimeSlot.nExcludeNone;
		
  		if ( objTimeSlot == undefined )
  			return false;

  		var bRet = false;   		
  		
  		var bEndsAfterStart = ( objTimeSlot.getEndDateTime() == undefined || ( m_oStartDateTime < objTimeSlot.getEndDateTime() ) );  		
  		var bStartsBeforeEnd = ( m_oEndDateTime == undefined || m_oEndDateTime > objTimeSlot.getStartDateTime() );		
  		
  		if ( bEndsAfterStart == true && bStartsBeforeEnd == true ) 
  			bRet = true;
		
  		if ( ( Agenda_TimeSlot.nExcludeBeforeStart & nRange ) == Agenda_TimeSlot.nExcludeBeforeStart )	
  		{
  			if ( m_oStartDateTime > objTimeSlot.getStartDateTime() )
  				bRet = false;
  		}
  		
  		if ( ( Agenda_TimeSlot.nExcludeAfterEnd & nRange ) == Agenda_TimeSlot.nExcludeAfterEnd )	
  		{
  			if ( objTimeSlot.getEndDateTime() != undefined && m_oEndDateTime != undefined 
  				&& m_oEndDateTime < objTimeSlot.getEndDateTime() 
  			)
  				bRet = false;
  		}		
  		return bRet;
  	};
}
Inheritance_Manager.extend(Agenda_TimeSlot, Idable);

Agenda_TimeSlot.nExcludeNone = 0;
Agenda_TimeSlot.nExcludeBeforeStart = 1;
Agenda_TimeSlot.nExcludeAfterEnd = 2;
Agenda_TimeSlot.nExcludeBoth = 3;