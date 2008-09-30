// ------------------ StopNav-----------------
function changeRoute(routeString,routeID,updateControl) {
	var split = routeID.split(',');
	document.getElementById("routeNumber").innerHTML = routeString;
	document.getElementById("routeMap").href = "http://www.translink.bc.ca/bus/"+split[1]+"/routemap/r"+routeString.split(' ')[0]+".pdf";
	document.getElementById("routeMap").style.display = "inline";
	document.getElementById("route").LineDirID = split[0];
	document.getElementById("route").routeMap = split[1];
	document.getElementById("route").validUntil = split[2];
	document.getElementById("validUntil").innerHTML = "valid until: "+split[2]+"&nbsp;<b>|</b>&nbsp;";

	if(updateControl !=null && updateControl.style.color == "gray") {
		updateLine();
		updateControl.style.color = "black";
		return;
	}
	else
		toggleAdminMode(false);
						
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.open("GET", "getStops.php?" + split[0] , false);
	xmlHttp.send(null);
	document.getElementById("stopList").innerHTML = xmlHttp.responseText;
	attachQuickList(document.getElementById("stopName"),document.getElementById("stopList"), "/( %1)|(^%1.*)/i",addStopTimes);

	removeAllStops();
	document.getElementById("stopName").focus();	

}


// ------------------ Times-----------------
function addStopTimes(routeString,stopID) {
  var xmlHttp = new XMLHttpRequest();
	var stopIDArr = stopID.split(",");
	
	var route = document.getElementById("route");
	if(!route.LineDirID || route.LineDirID != stopIDArr[0]) {
		route.LineDirID = stopIDArr[0];
		route.stopIDs = new Array();
		route.stopIDs[0] = stopIDArr[1];
		route.DOW = 'week';
		for(var i=0; i<document.getElementsByName("dow").length; i++) { 
			if(document.getElementsByName("dow")[i].checked == true) { route.DOW = document.getElementsByName("dow")[i].value; 	break; }
		}	
	}
	else {
		for(var i=0; i< route.stopIDs.length; i++) { if(route.stopIDs[i]== stopIDArr[1]) return; } // if the stop ID is already listed we are done.
		route.stopIDs[route.stopIDs.length] = stopIDArr[1];
	}
		
	drawIframe();

	//document.getElementById("stopName").value="";
	//document.getElementById("stopName").onkeyup();

}

function drawIframe() { drawIframe(false); }

function drawIframe(redraw) {
	var iframe = document.getElementById("timesIframe");
	if(route.LineDirID && route.LineDirID > 0) {
		if(route.stopIDs.length<=1 || redraw) {
			iframe.contentDocument.location="getGrid.php?LineDirID="+route.LineDirID+"&StopIDs="+route.stopIDs.join(",")+"&dow="+route.DOW
		}
		else {
			var table = iframe.contentDocument.getElementById("timesTable");	
			xmlHttp.open("GET", "getGrid.php?tableOnly=true&LineDirID="+route.LineDirID+"&StopIDs="+route.stopIDs.join(",")+"&dow="+route.DOW, false);
			xmlHttp.send(null);
			table.innerHTML = xmlHttp.responseText;
			drawTableHeader();
			offsetTable();
		}
	}
	else {
		iframe.contentDocument.location="splash.htm";
		drawTableHeader();
	}
}

function offsetTable() {
	var iframe = document.getElementById("timesIframe");
	if(iframe==null) iframe = parent.document.getElementById("timesIframe");
	var table = iframe.contentDocument.getElementById("timesTable");
	if (table.rows.length <=0) return;
	var offset = 0;
	for(var index =1; index < table.rows.length; index++) {
		var split = table.rows[index].cells[0].innerHTML.replace(/:/,' ').split(' ');
		if(split=="-") continue;
		if(split[2]=="pm" && split[0]!="12") split[0] = parseInt(split[0])+12;
		var currDate = new Date();
		if(split[0]>currDate.getHours() || (split[0]==currDate.getHours() && split[1]>currDate.getMinutes())) {
			offset = table.rows[index].cells[0].offsetTop- (2 * table.rows[index].cells[0].clientHeight);
			break;
		}
	}
	table.rows[index].className = "currentRow";
	if(offset!=0) iframe.contentWindow.scrollTo(0,offset);
}

function drawTableHeader() {
	var iframe = document.getElementById("timesIframe");
	var tableHeader = document.getElementById("timesTableHeader");
	if(iframe == null) {
		iframe = parent.document.getElementById("timesIframe");
		tableHeader = parent.document.getElementById("timesTableHeader");
	}
	if(iframe != null) {
		var table = iframe.contentDocument.getElementById("timesTable");
		if(table != null && table.rows.length>0) {
			if(tableHeader.rows.length<=0) tableHeader.insertRow(-1);
			tableHeader.rows[0].innerHTML = table.rows[0].innerHTML;
			table.rows[0].style.display = "none";
		}
		else {
			if(tableHeader.rows.length>0) tableHeader.deleteRow(0);
		}
	}
}

function removeAllStops() {
	var route = document.getElementById("route");
	route.LineDirID = 0;
	route.stopIDs = new Array();
	drawIframe(true);
	if(document.getElementById("timesTableHeader").rows.length>0) document.getElementById("timesTableHeader").deleteRow(0);
}

function removeStop(stopID) {
	
	var iframe = document.getElementById("timesIframe");
	var route = document.getElementById("route");
	if(iframe == null) {
		iframe = parent.document.getElementById("timesIframe");
		route = parent.document.getElementById("route");
	}
	
	if(route == null) return
	
	var stopIDIndex;
	for(stopIDIndex = 0; stopIDIndex < route.stopIDs.length; stopIDIndex++) {
		if(route.stopIDs[stopIDIndex] == stopID) {
			route.stopIDs.splice(stopIDIndex,1);
			break;
		}
	}	
	
	var table = iframe.contentDocument.getElementById("timesTable");
	for(var i=0; i<table.rows[0].cells.length;i++) {
		if(parseInt(table.rows[0].cells[i].getAttribute("stopID"))==parseInt(stopID)) {
			colIndex = i;
			break;
		}
	}
	
	for(var i=table.rows.length-1; i>=0; i--) {
		table.rows[i].deleteCell(colIndex);
		if(table.rows[i].cells.length==0) table.deleteRow(i);
	}
	drawTableHeader();
}
   
function changeDOW(dow) {
	var route = document.getElementById("route");
	route.DOW = dow;
	drawIframe(true);
}
 
function miniPrint() {
	window.open("getGrid.php?pda=true&LineDirID="+route.LineDirID+"&StopIDs="+route.stopIDs.join(",")+"&dow="+route.DOW);

}
	 
//------------- admin functions -----------------
//adminScheduleLink adminUpdateAllLink adminUpdateLineLink
function toggleAdminMode(adminMode) {
	document.getElementById("stopNav").style.display = (adminMode) ? "none" : "block";
	document.getElementById("routeInfo").style.display = (adminMode) ? "none" : "block";
	document.getElementById("adminUpdateLineLink").style.display = (document.getElementById("route").LineDirID == null) ? "none" : "inline";
	document.getElementById("adminScheduleLink").style.display = (adminMode) ? "inline" : "none";
	document.getElementById("updateFrame").style.display = (adminMode) ? "block"  : "none";
}

function showSchedule() {
	toggleAdminMode(false);
	var iframe = document.getElementById("timesIframe");
	iframe.contentDocument.location="splash.htm";
	route = document.getElementById("route");
	if(route.LineDirID != undefined) {
		routeID = route.LineDirID + ","+route.routeMap+","+route.validUntil;
		changeRoute(document.getElementById("routeNumber").innerHTML,routeID,null);
	}
}

function updateAll() {
	toggleAdminMode(true);
	document.getElementById("updateIframe").src = "update.inc.php?action=updateAll";
}

function updateLine() {
	toggleAdminMode(true);
	document.getElementById("updateIframe").src = "update.inc.php?action=updateLine&LineAbbr="+document.getElementById("routeNumber").innerHTML.split(' ')[0];
}

function showHelp() {
	toggleAdminMode(true)
	document.getElementById("updateIframe").src = "help.htm";
}