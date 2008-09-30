<?php
	include_once 'db.inc.php';

	$LineDirID = $_SERVER['QUERY_STRING'];
	if($LineDirID == null || $LineDirID == "") echo "<error>No Line selected</error>";

	$exits = querySingleRecord("select 1 from `lines` where `LineDirID`='".$LineDirID."' and active=0"); 
	if($exits) {
		echo "<li>No data for this line.</li>";
		return;
	}

	queryNR("update `lines` set requestCount=requestCount+1 where lineDirID = ".$LineDirID);

	$records = query("SELECT * from `stops` where LineDirID = '".$LineDirID."' order by sequence");

	foreach($records as $key=>$stop) {
		echo "<li class=\"active".($key ==0 ? " first":"")."\" onclick=\"javascript:addStopTimes('".ucwords(strtolower($stop['Name']))."','".$stop['LineDirID'].",".$stop['StopID']."')\" data=\"".$stop['LineDirID'].",".$stop['StopID']."\">".ucwords(strtolower($stop['Name']))."</li>";
	}
?>
