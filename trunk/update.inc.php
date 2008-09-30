<?php

include_once 'db.inc.php';
include_once 'scrap.inc.php';
include_once 'geocode.php';

//	print nl2br(htmlspecialchars(var_export($results, true))); return;	
function updateStopsAndTimes($LineDirID) {
	$exists = querySingleRecord("select 1 from `lines` where active = 0 and `LineDirID`='".$LineDirID."'"); 
	if($exists) {
		echo "Line is not active. It is either no longer runing or being updated elsewhere. Please try again in a few minutes";
		return;
	}
	queryNR("update `lines` set `active`=0 where `LineDirID`=".$LineDirID);
	updateStopsAndTimesForaPeriod($LineDirID, true, 'week');
	updateStopsAndTimesForaPeriod($LineDirID, false, 'sat');
	updateStopsAndTimesForaPeriod($LineDirID, false, 'sun');
	queryNR("update `lines` set `active`=1 where `LineDirID`=".$LineDirID);	
}

function updateStopsAndTimesForaPeriod($LineDirID, $updateStops = true, $period='week') {
	
	$results = getStopsAndTimes($LineDirID, $period);
	
	//update stops
	if($updateStops == true) {
		echo "<li>Updating stops (".$period.") ";ob_flush(); flush();
		$editedStops = queryRBI('Select * from `stops` where lineDirID = '.$LineDirID.' and manualEdit = 1', 'StopID');
		queryNR("update `lines` set `routeMap`='".$results[2]."', validUntil='".$results[3]."' where `LineDirID`=".$LineDirID);	
		queryNR("delete from `stops` where `LineDirID`=".$LineDirID);
		queryNR("delete from `times` where `LineDirID`=".$LineDirID);

		foreach($results[0] as $stopNum => $stop) {
			if($editedStops[$stop[0]]!=null) $stopName = $editedStops[$stop[0]]["Name"];
			else {
				$stopInfo = getLocationOf($stop[1]);
				if(count($stopInfo)<=1) $stopName = $stop[1];
				else $stopName = $stopInfo[0]; 
				//$loc = split(',',$stopInfo[1]); // latatude = '".$loc[1]."', longitude = '".$loc[0]."' 
			}
			queryNR("INSERT into `stops` ( `LineDirID` , `StopID` , `Name`, `sequence`, `manualEdit` )  VALUES (" . 
				$LineDirID . ", '" . $stop[0] . "', '" . $stopName . "', ".$stopNum.", ".(($editedStops[$stop[0]]!=null) ? "1" : "0").");");
			echo ".";ob_flush(); flush();
		}
		echo " Done</li>";
	}
	
	echo "<li>Updating times (".$period.")."; ob_flush(); flush();
	foreach($results[1] as $runNum => $run) {
		foreach($run as $stopNum => $time) {
			switch(substr($time[0],strlen($time[0])-1)) {
				case 'a':
				case 'p':
					$time[0] = strftime("%H%M00",strtotime($time[0].'m'));
					break;
				case 'x':
					$time[0] =  strftime("%H%M00",strtotime(substr($time[0],0,strlen($time[0])-1).'pm'))+120000;
					break;
			}
			if(strlen($time[1]) > 0) {
				switch(substr($time[1],strlen($time[1])-1)) {
					case 'a':
					case 'p':
						$time[1] = strftime("%H%M00",strtotime($time[1].'m'));
						break;
					case 'x':
						$time[1] =  strftime("%H%M00",strtotime(substr($time[1],0,strlen($time[1])-1).'pm'))+120000;
						break;
				}	
			}

			queryNR("INSERT into `times` ( `LineDirID` , `runNum`, `stopID`, `time`, `arrivalTime`, `loadOnly`, `dow` )  VALUES ('" . 
				$LineDirID . "', '" . $runNum. "', '" . $results[0][$stopNum][0] . "', '". $time[0] ."', '" . $time[1] . "', '". $time[2] ."', '".$period."');");

		}
		echo ".";ob_flush(); flush();
	}
	echo " Done</li>"; ob_flush(); flush();
		
}

function updateLine($LineAbbr) {
	echo "<b>Updating ".$LineAbbr."</b><ul>";
	$routeInfo = getLineID($LineAbbr);
	foreach ($routeInfo as $line) {
		echo "Updating ".$LineAbbr." (". $line[0] . ", " . $line[1]. "):<br><ul>"; ob_flush(); flush();
		$exists = querySingleRecord("select 1 from `lines` where LineAbbr = '" . $LineAbbr . "' and Cardinal = '" . $line[0] . "'"); 
		if(!$exists) queryNR("INSERT into `lines` ( `LineAbbr` , `Cardinal` , `LineDirID` )  VALUES ('" . $LineAbbr . "', '" . $line[0] . "', " . $line[1] . ")");
		else queryNR("update `lines` set LineDirID = ".$line[1]." where LineAbbr = '" . $LineAbbr . "' and Cardinal = '" . $line[0] . "'");
		updateStopsAndTimes($line[1]);
		echo "</ul>";
	}
	echo "</ul>";
}

	$action = $_GET['action'];
	set_time_limit(259200);
	ignore_user_abort(true);
	if($_GET['action']== "UpdateAll") {
			echo "<b>Updateing All</b><ul>";
			$routeAbbrs = getRouteAbbr();
			foreach ($routeAbbrs as $route) {
				updateLine($route);
				if(connection_aborted()) return;
			}
			echo "</ul>All Done.";		
			return;
	}
	if($_GET['action']== "Outdated") {
		echo "<b>Updateing Outdated</b><ul>";
		$results = query("SELECT DISTINCT LineAbbr FROM `lines` where validUntil < CURDATE()");
		foreach($results as $lineRecord) {
			updateLine($lineRecord["LineAbbr"]);
			if(connection_aborted()) return;
		}		
		echo "</ul>Updating Outdated Done.";	
	}
	$LineAbbr = $_GET['LineAbbr'];
	if($LineAbbr != null && $LineAbbr!="") {		
			echo "<b>Updating This Line</b> (LineAbbr: ".$LineAbbr.")<br/><ul>";
			updateLine($LineAbbr);
			echo "</ul>Line Updated";
	}

?>








