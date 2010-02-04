#!/usr/bin/python
import cgi
import cgitb; cgitb.enable()
import sqlite3

print "Content-type: text/html\n"

conn = sqlite3.connect('update/data/fasttranist.db')
routes = conn.execute("SELECT ");
form = cgi.FieldStorage()


route_id = form['route_id']
if route_id == None or route_id == "":
	print "<error>No Line selected</error>"
	return
	
exists = conn.execute("select 1 from `routes` where `route_id`='"+ route_id + "' and active=0")
if exits.rowcount <= 0:
	print "<li>No data for this line.</li>"
	return

	#queryNR("update `lines` set requestCount=requestCount+1 where lineDirID = ".$LineDirID);

records = conn.execute("SELECT * from `stops` where LineDirID = '".$LineDirID."' order by sequence");

	foreach($records as $key=>$stop) {
		echo "<li class=\"active" + 
			($key ==0 ? " first":"") + 
			"\" onclick=\"javascript:addStopTimes('" + ucwords(strtolower($stop['Name'])) + 
			"','".$stop['LineDirID'].",".$stop['StopID']."')\" data=\"".$stop['LineDirID'].",".$stop['StopID']."\">".ucwords(strtolower($stop['Name']))."</li>";
	}
?>
