function DivHelper()
{
	
}

DivHelper.create = function ( vtParentDiv, id, theclass, inhoud, cssString )
{
	var newdiv = document.createElement('div');
	
	if ( id != undefined )
		newdiv.id = id;			
	
	if ( theclass != undefined )
		newdiv.className = theclass;
	
	if ( cssString != undefined )
		newdiv.style.cssText = cssString;
	
	if ( inhoud != undefined )
		newdiv.innerHTML = inhoud;		
	
	if ( vtParentDiv != undefined )
	{
		if ( typeof( vtParentDiv ) == 'string' )
			vtParentDiv = document.getElementById( vtParentDiv );
		
		if ( vtParentDiv != undefined )
			vtParentDiv.appendChild( newdiv );		
	}
	
	return newdiv;
};

DivHelper.getPoint = function ( obj ) 
{
	var nLeft = nTop = 0;
	if ( obj.offsetParent ) 
	{
		do {			
			nLeft += obj.offsetLeft;
			nTop += obj.offsetTop;			
		} while (obj = obj.offsetParent);
	}
	
	return new Point( nLeft,nTop );
};

DivHelper.getViewPort = function () 
{		
	if( document.all ) 
		return document.documentElement;
	return document.body;
};

DivHelper.getTargetDiv = function ( e )
{
	var oDiv = null;
	if ( !e ) 
		e = window.event;
	
	if ( e.target ) 
		oDiv = e.target;
	else if (e.srcElement) 
		oDiv = e.srcElement;
	
	if ( oDiv.nodeType == 3) // defeat Safari bug
		oDiv = oDiv.parentNode;
	
	return oDiv;
};

DivHelper.getHorizontalScrollBarHeight = function ( oDiv )
{
	return oDiv.offsetHeight - oDiv.clientHeight;
};

DivHelper.getFloatStyle = function()
{
	if (document.all) {   // very basic browser detection
		return "styleFloat"; //ie
	} else {
		return "cssFloat"; //firefox
	}
};	
