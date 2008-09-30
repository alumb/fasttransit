<?php
include_once 'db.inc.php';

function outputTimesTable() {

	$LineDirID = $_GET['LineDirID'];
	$StopIDs = $_GET['StopIDs'];
	$numStops = count(explode(',',$StopIDs));
	$dow = $_GET['dow'];
	if($dow == null || strlen($dow)<=0) $dow='week';

	if($LineDirID == null || $LineDirID == "") { echo "<div>No Line selected</div>"; return; }
	if($StopIDs == null || $StopIDs == "") { echo "<div>No Stops selected (".$StopIDs.")</div>"; return ; }

  $records = query("SELECT * from `times` left outer join `stops` on times.StopID = stops.StopID and times.LineDirID = stops.LineDirID where times.LineDirID = '".$LineDirID."' and times.StopID in (".$StopIDs.") and dow='".$dow."' order by runNum, sequence");

	if(!$records) { echo "<div id=\"notAvailable\" >This bus does not run on ". (($dow=="week")? "week days." : (($dow=="sat") ? "saturdays." : "sundays.")) ."</div>"; return; }
	
	$sequence = query("select stopID, Name, sequence, latatude, longitude from stops where LineDirID = '".$LineDirID."' and StopID in (".$StopIDs.") order by sequence");

	$meridiems = array('am','pm','x');
	//
	echo "\n<tr>";
	//header row
	foreach($sequence as $stop) {
		echo "<td class=\"titleRow\" style=\"width:". 100/$numStops ."%\"";
		echo "stopID=\"".$stop['stopID']."\" latatude=\"".$stop['latatude']."\" longitude=\"".$stop['longitude']."\">";
		echo $stop['Name']. "<img src=\"close.gif\" onclick=\"removeStop(".$stop['stopID'].")\"/>";
		echo "</td>";
	}

	foreach($records as $key=>$time) {
		if($key % $numStops == 0) { //first Column
			echo "</tr>\n<tr>";				
		}
		
		if($time['time']=="00:00:00") {
			echo "<td>";
			if($time['arrivalTime']!="00:00:00") {
				$timeArrArrival = explode(':',$time['arrivalTime']);			
				echo ((($timeArrArrival[0]-1) % 12) + 1).":".$timeArrArrival[1]." ".$meridiems[floor($timeArrArrival[0]/12)];
			}	
			else 
				echo "-";
			echo "</td>";

		}
		else
		{
			$timeArr = explode(':',$time['time']);			
			echo "<td style=\"width:". 100/$numStops ."%\">";
			echo ((($timeArr[0]-1) % 12) + 1).":".$timeArr[1]." ".$meridiems[floor($timeArr[0]/12)];
			if($time['arrivalTime']!="00:00:00") {
				$timeArrArrival = explode(':',$time['arrivalTime']);			
				echo " (Arrives: " . ((($timeArrArrival[0]-1) % 12) + 1).":".$timeArrArrival[1]." ".$meridiems[floor($timeArrArrival[0]/12)] . ")";
			}
			echo "</td>";
		}
	
	}
	echo "</tr>";
}
?>


<?php if($_GET['tableOnly'] != "true") { ?>
<html>
	<head>
		<title>Time Table</title>
		<link rel="stylesheet" href="mainStyles.css" type="text/css" />
		<script language="javascript" src="interface.js"></script>		
		<?php if($_GET['pda'] == "true") {
			echo "<script language=\"javascript\" src=\"pda.js\"></script>"; 
			echo "<link rel=\"stylesheet\" href=\"pda.css\" type=\"text/css\" />"; 
		} ?>
		
		<script language="javascript">
			function loader() {
				if(parent.document.getElementById("timesIframe")) { drawTableHeader();offsetTable(); }
				<?php if($_GET['pda'] == "true") { ?>
					var table = document.getElementById("timesTable");
					table.className="PDA";
					<?php $records = query("SELECT * from `lines` where LineDirID = '".$_GET['LineDirID']."' limit 0,1");
					mysql_error();
					echo "condence(\"".$records[0]['LineAbbr']." ".$records[0]['Cardinal']."\",table);";
				 } ?>
			}
		</script>
	</head>
	<body style="	margin:0px;" onload="loader();">
		<table id="timesTable" class="timesTable" border=0>
			<?php outputTimesTable(); ?>		
		</table>
	</body>
</html>
<?php } else { 
	outputTimesTable();
	}
?>
