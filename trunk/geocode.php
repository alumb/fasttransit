<?php

include_once 'db.inc.php';

function getLocationOf($StopName) {

	$direction = "";
	$directionReplacements = array('EASTBOUND' => 'East Bound', 'WESTBOUND' => 'West Bound', 'SOUTHBOUND' => 'South Bound', 'NORTHBOUND' => 'North Bound', 
		'SB' => 'South Bound', 'WB' => 'West Bound', 'NB'=> 'North Bound', 'EB' => 'East Bound', 'East Bound' => 'East Bound', 'West Bound' => 'West Bound', 
		'South Bound' => 'South Bound', 'North Bound' => 'North Bound');
	preg_match_all("#EASTBOUND|WESTBOUND|SOUTHBOUND|NORTHBOUND|SB\s|WB\s|NB\s|EB\s|North\sBound\s|East\sBound\s|West\sBound\s|South\sBound\s#si", $StopName, $matches);
	if(count($matches[0])>0) {
		$StopName = str_replace($matches[0][0],'',$StopName);
		$direction = $directionReplacements[trim($matches[0][0])];
	}


	
	$bay = "";
	preg_match_all("#\(BAY \d+\)#si", $StopName, $matches);
	if(count($matches[0])>0) {
		$bay = $matches[0][0];
		$StopName = str_replace($bay,'',$StopName);
	}
	
	$StopName = str_replace(" FS "," at ",$StopName);
	$StopName = str_replace(" NS "," at ",$StopName);

	$number = "";
	preg_match_all("/(.*?)AT (\d+) BLOCK/si",$StopName,$matches);
	if(count($matches[0])>0) {
		$number = $matches[2][0];
		$StopName = $matches[2][0] . " " . $matches[1][0];
	}

	$StopName = preg_replace("/ DIV /"," st ",$StopName);
	$StopName = preg_replace("/ SNT /"," st ",$StopName);
	
	$url = "http://maps.google.com/maps/geo?q=" . urlencode(trim($StopName)) . ",+Greater+Vancouver+Regional+District,+British+Columbia,+Canada" . 
		"&output=xml" . 
		//"&key=ABQIAAAAsoId6vcYTryAHuA5N6tF-RQ-AQXfUoj-fxZyucz622sI5Pq9BBRe13pAt9n09tcqGEiH3Zzia12kVA";
		"&key=ABQIAAAAsoId6vcYTryAHuA5N6tF-RSLPZazQXkywg-K-KibHmLDboacqhT4uS_fIpPPbpfkiyI474dwCFLSSw";

	$results = file_get_contents($url);
	//	print nl2br(htmlspecialchars(var_export($results), true));
	preg_match_all("#<code>([^<]*)</code>#si", $results, $code);
	if($code[1][0]!="200") return "Address Not Found - ".$StopName." (".$code[1][0].")";
	preg_match_all("#<address>([^<]*)</address>#si", $results, $address);
	preg_match_all("#<coordinates>([^<]*)</coordinates>#si", $results, $coordinates);
	$addressShort = split(',',$address[1][0]);
	$name = join(',',array_slice($addressShort, 0, 2));
	$cordShort = split(',',$coordinates[1][0]);
	if($cordShort[1]<49 || $cordShort[1]>49.6 || $cordShort[0]< -123.3 || $cordShort[0]> -121.5 ) return "Address Not Found in the GVRD - ".$StopName." (".$name.")";
	if(strlen($number)>0 && !strstr($name,$number)) $name = $number . " " . $name;
	if(strlen($direction)>0) $name = $direction . " " . $name;
	if(strlen($bay)>0) $name .= ucwords(strtolower($bay));


	
	
	return array($name, $coordinates[1][0]);
	
}

function updateStops() {

	$stops = query("select Name from `stops` where latatude is null group by Name order by LineDirID, sequence");
	foreach($stops as $stop) {
		$stopInfo = getLocationOf($stop['Name']);
		if(count($stopInfo)<=1) {
			echo $stop['Name'] . " - " . $stopInfo . "<br>";
		}
		else {
			$loc = split(',',$stopInfo[1]);
			queryNR( "update `stops` set name = '" .$stopInfo[0]. "', latatude = '".$loc[1]."', longitude = '".$loc[0]."' where Name = '". $stop['Name'] ."'");
			echo $stop['Name'] . " - " .$stopInfo[0] . " - " . $stopInfo[1] . "<br>";
		}	
		ob_flush();
		flush();
		sleep(3);
	}

}

function testStop($stopName) {
		$stopInfo = getLocationOf($stopName);
		if(count($stopInfo)<=1) {
			echo $stopName . " - " . $stopInfo . "<br>";
		}
		else {
			$loc = split(',',$stopInfo[1]);
			echo $stopName . " - " .$stopInfo[0] . " - " . $stopInfo[1] . "<br>";
		}	
		ob_flush();
		flush();
}


?>

