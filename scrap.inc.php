<?php

include_once 'db.inc.php';

//print nl2br(htmlspecialchars(var_export(getSchedual('66671','3799'), true)));

function getRouteAbbr() {
	$result="";
		echo "<li>Getting Route Abbriviations - getting page "; ob_flush(); flush();
	while(strlen($result)<=1) {
		echo "."; ob_flush(); flush();
		$result = file_get_contents('http://tripplanning.translink.bc.ca/hiwire?.a=iScheduleLookup&.s={$SID}', false);
	}
   echo " Done</li>"; ob_flush(); flush();
	preg_match_all("#<option value=\"(\w\w+?)\">#si", $result, $matches);
	return $matches[1];
}

function getLineID($lineAbbr) {
	
	$Day = 86400;
	$currentDate = getdate();

	$postdata = http_build_query(
	   array(
	       '.a' => 'iScheduleLookupSearch',
		   'LineAbbr' => $lineAbbr,
		   'FormState' => 'ScheduleLookup',
		   'Date' => date("m-d-Y", $currentDate[0] + (($currentDate['wday']==0) ? 1 : (($currentDate['wday']==6) ? 2 : 0))*$Day)
	   )
	);
	$opts = array('http' =>
	   array(
	       'method'  => 'POST',
	       'header'  => 'Content-type: application/x-www-form-urlencoded',
	       'content' => $postdata
	   )
	);
	$context  = stream_context_create($opts);
	$result="";
	echo "<li>Getting line ID - getting page "; ob_flush(); flush();
	while(strlen($result)<=1) {
		echo "."; ob_flush(); flush();
		$result = file_get_contents('http://tripplanning.translink.bc.ca/hiwire', false, $context);
	}
   echo " Done</li>"; ob_flush(); flush();

	preg_match_all("#<input[^>]*LineDirId[^\d]*(\d*)[^>]*>(\w*)#si", $result, $matches);
	$IDs = array();
	foreach ($matches[2] as $key => $value) 
		$IDs[] = array($value, $matches[1][$key]);
	
	return $IDs;
}

function getStopsAndTimes($lineDirId, $period) {
	
	$actualDate = date("m-d-Y");
	
	$Day = 86400;
	$currentDate = getdate();
	switch($period) {
		case "sat":
			echo "<b>Saturday</b>";
			$actualDate = date("m-d-Y", $currentDate[0] + (6-$currentDate['wday'])*$Day);
			break;
		case "sun":
			echo "<b>sunday</b>";
			$actualDate = date("m-d-Y", $currentDate[0] + ((7-$currentDate['wday'])%7)*$Day);
			break;
		default: //weekday
			echo "<b>weekday</b>";
			$actualDate = date("m-d-Y", $currentDate[0] + (($currentDate['wday']==0) ? 1 : (($currentDate['wday']==6) ? 2 : 0))*$Day);
	}
	
	$postdata = http_build_query(
	   array( '.a' => 'iHeadwaySheet', 'ServiceGroupId' => '', 'StopDisplay' => 'Select', 'Date' =>  $actualDate, 'PLayout' => 'letter', 
		   'FromTime' => '5:00', 'FromMeridiem' => 'a', 'ToTime' => '4:30', 'ToMeridiem' => 'a', 'LineDirId' => $lineDirId)
	);

	$opts = array('http' =>
	   array(
	       'method'  => 'POST', 
	       'header'  => 'Content-type: application/x-www-form-urlencoded',
	       'content' => $postdata
	   )
	);

	$context  = stream_context_create($opts);

	$result="";
	echo "<li>Updating Schedule - getting page for ".$period; ob_flush(); flush();
	while(strlen($result)<=1) {
		echo "."; ob_flush(); flush();
		$result = file_get_contents('http://tripplanning.translink.bc.ca/hiwire', false, $context);
	}
   echo " Done</li>"; ob_flush(); flush();
	 
	preg_match_all('#<td[^>]+?title="([^\"]+?) as [^>]*>([^<]+)(<br/><span class="smtype">(Un)?load Only</span>)?</td>#si', $result, $matches);
	preg_match_all('@<span[^>]*>Stop #\s+(\d+)[^<]*</span>@si', $result, $stopNums);

	//set up list of stops
	$numStops=1;
	$stops = array(array($stopNums[1][0],$matches[1][0])); 
	for(; $matches[1][0]!=$matches[1][$numStops] && $numStops<count($matches[1]); $numStops++) {
		if($matches[1][$numStops]!=$matches[1][$numStops-1])
			$stops[] = Array($stopNums[1][$numStops],$matches[1][$numStops]);
	}

	//check for duplicates:
	for($i=0; $i<count($stops); $i++) {
		for($j=$i+1; $j<count($stops); $j++) {
			if($i!=$j && $stops[$i][0]==$stops[$j][0]) $stops[$j][0].='n';
		}
	}

	//set up run times // Array(time, arrivalTime, load / unload)
	$runs = array();
	$numRuns = (count($matches[2]))/$numStops;
	for($i=0; $i<$numRuns; $i++) {
		for($j=0; $j<$numStops; $j++) {
			if($j>1 && $matches[1][$j]==$matches[1][$j-2]) {
				$runs[$i][count($runs[$i])-1][0]=$matches[2][$i*$numStops+$j];
			}
			else if($j>0 && $matches[1][$j]==$matches[1][$j-1]) {
				$runs[$i][count($runs[$i])-1][1]=$runs[$i][count($runs[$i])-1][0];
				$runs[$i][count($runs[$i])-1][0]=$matches[2][$i*$numStops+$j];
				$runs[$i][count($runs[$i])-1][2]='both';
			}
			else {
				$runs[$i][count($runs[$i])]=Array($matches[2][$i*$numStops+$j], null, (($matches[3][$i*$numStops+$j]=='')?'both':(($matches[4][$i*$numStops+$j]=='')?'load':'unload')));
			}
				
		}
	}

	preg_match_all('@http://www.translink.bc.ca/bus/(\d+)/routemap/r@si', $result, $routeMap);
	preg_match_all('@service in effect from:[^t]*to[^\d]*([^<]*)@si', $result, $validThrough);
	$split = explode("-",$validThrough[1][0]);

	return array($stops,$runs,$routeMap[1][0],$split[2]."-".$split[0]."-".$split[1]);
}

function testPage() {

	$result = getLineID('143');
	print nl2br(htmlspecialchars(var_export($result, true)));

}



?>
