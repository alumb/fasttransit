#!/usr/bin/python
import cgi
import cgitb; cgitb.enable()
import sqlite3

print "Content-type: text/html\n"

conn = sqlite3.connect('update/data/fasttranist.db')
routes = conn.execute("SELECT `routes`.route_short_name, `routes`.route_id, `routes`.active, `routes`.routeMap, `routes`.validUntil FROM `routes` order by requestCount desc,route_short_name");

print "<ul id=\"routeList\" name=\"routeList\">";
for route in routes:
	print "<li "
	if route[2]==0:
		print "class=\"disabled\" "
	else:
		print "class=\"active\" "
		print "onclick=\"listClick(this);changeRoute(this.innerHTML,'" + route[1] + "," + route[3] + ",'',this);\" "
		print "data=\"" + route[1] + "," + route[3] + ",''\""
	
	print ">" + route[0] + "</li>"


print "</ul>"

