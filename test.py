#!/usr/bin/python
import cgi
import cgitb; cgitb.enable()
import re
import os
import urllib

print "Content-type: text/html\n"


print """
<html>
<head><title>test</title></head>
<body>
this is a test<br>
"""  
form = cgi.FieldStorage()
print form

print """</body>
</html>
"""