<?php
include_once 'db.inc.php';
?>
<html>
	<head>
		<link rel="stylesheet" href="mainStyles.css" type="text/css" />
		<link rel="stylesheet" href="pda.css" type="text/css" />
		<script language="javascript" src="pda.js"></script>	
	</head>
	<body style="	margin:0px;">
		<?php
			$LineDirID = $_GET['line'];
			$Stops = $_GET['stops'];
			$dow = $_GET['dow'];
			if($dow == null || strlen($dow)<=0) $dow='week';

			if($LineDirID == null || $LineDirID == "") echo "<error>No Line selected</error>";
			if($Stops == null || $Stops == "") echo "<error>No Stop selected</error>";
	
			$Stops = "'".implode("','",explode(',',$Stops))."'";
			
			$stopInfo = query("select LineDirID, StopID, Name from `stops` where stops.lineDirID = '".$LineDirID."' and stops.stopID in (".$Stops.")");
			$times = query("SELECT stops.LineDirID, stops.StopID, times.time FROM `stops` join `times` on stops.LineDirId = times.LineDirId and stops.stopID = times.stopID " .
							"where stops.lineDirID = '".$LineDirID."' and stops.stopID in (".$Stops.") and dow = '".$dow."' order by times.runNum, stops.sequence ");
		
			echo var_export($stopInfo);
			echo var_export($times);
		
		?>

	
		<div id="notAvailable" >This bus is not available</div>
		<table id="timesTable" class="PDA" border="0"/>
	</body>
</html>
