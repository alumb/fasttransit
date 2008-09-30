<?php
	include_once 'db.inc.php';

	$records = query("SELECT `lines`.LineAbbr, `lines`.Cardinal, `lines`.LineDirID, `lines`.active, `lines`.routeMap, `lines`.validUntil FROM `lines` order by requestCount desc,LineAbbr");

	echo "<ul id=\"routeList\" name=\"routeList\">";
	 foreach($records as $key=>$line) {
	  echo "<li ";
		if($line['active']==0) 
			echo "class=\"disabled\" ";
		else {
			echo "class=\"active" . ($key ==0 ? " first":"") . "\" ";
			echo "onclick=\"listClick(this);changeRoute(this.innerHTML,'".$line[LineDirID].",".$line['routeMap'].",".$line['validUntil']."',this);\" ";
			echo "data=\"".$line['LineDirID'].",".$line['routeMap'] .",".$line['validUntil'] ."\"";
		}
		echo ">".$line['LineAbbr'] . " " . strtolower($line['Cardinal'])  ."</li>";
	}

	echo "</ul>";
?>
