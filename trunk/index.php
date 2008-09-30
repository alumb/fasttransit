
<html>
<head>
	<title>Fast Transit</title>
	<link rel="stylesheet" href="mainStyles.css" type="text/css" />
	<script language="javascript" src="interface.js"></script>
	<script language="javascript" src="quickList.js"></script>
</head>
<body>

<div id="routeSelect">
	<div class="title">Routes</div>
	<div class="search">
		<input type="text" id="routeID" name="routeID" value="filter route #"/>
	</div>
	<script language="javascript">
		var xmlHttp = new XMLHttpRequest();
		xmlHttp.open("GET", "getLines.php", false);
		xmlHttp.send(null);
		document.write(xmlHttp.responseText);
	</script>
</div>
<div id="route">
	<div class="title larger">
		<span id="adminLinks">
			<a id="adminScheduleLink" onclick="showSchedule();">Schedule&nbsp;<b>|</b></a>
			<a id="adminUpdateAllLink" onclick="updateAll();">Update All&nbsp;<b>|</b></a>
			<a id="adminUpdateLineLink" onclick="updateLine();">Update This Line&nbsp;<b>|</b></a>
			<a id="adminMiniPrint" onclick="miniPrint();">Mini Print&nbsp;<b>|</b></a>
			<a id="adminHelp" onclick="showHelp()">Help</a>
		</span>
		<span id="validUntil"></span>
		Route: <span id="routeNumber"></span><a href="#" id="routeMap" target="_blank">(Route Map)</a>
	</div>
	<div id="stopNav">
		<div class="title">Available Stops</div>
		<div class="search">
			<input type="text" id="stopName" name="stopName" value="Filter Stops"/>
		</div>
		<ul id="stopList">
		</ul>
	</div>
	<div id="routeInfo">
		<span class="search smaller" id="dowSpan">
			<?php $currentDate = getdate(); ?>
			weekday:<input type="radio" name="dow" id="dow" value="week" onclick="changeDOW('week')" <?php if($currentDate['wday']>0 && $currentDate['wday']<6) echo "CHECKED";?> />
			Saturday:<input type="radio" name="dow" id="dow" value="sat" onclick="changeDOW('sat')"<?php if($currentDate['wday']==6) echo "CHECKED";?>/>
			Sunday / Holidays:<input type="radio" name="dow" id="dow" value="sun" onclick="changeDOW('sun')" <?php if($currentDate['wday']==0) echo "CHECKED";?>/>
		</span>
		<div class="title">
			Route Times
		</div>
		<div id="timesTableHeaderDiv"><table id="timesTableHeader" class="timesTable" border=0></table></div>
		<iframe id="timesIframe" src="splash.htm"></iframe>
	</div>
	<div id="updateFrame">
				<iframe id="updateIframe" src="about:blank"></iframe>
	</div>
	<div style="clear:both;">
</div>

<script language="javascript">
	attachQuickList(document.getElementById("routeID"),document.getElementById("routeList"), "/^0*%1/i", changeRoute);
	if(document.getElementById("routeID").value=="filter route #") document.getElementById("routeID").focus();
</script>

<script type="text/javascript">
      var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
      document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
		var pageTracker = _gat._getTracker("UA-624567-1");
		pageTracker._initData();
		pageTracker._trackPageview();
</script>

</body>
</html>
