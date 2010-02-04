import sqlite3

class dbUpdateModel:
	conn = None
	
	def loadOnly(row, lookupTable):
		if row['pickup_type'] == "1":
			return "'dropoff'"
		elif row['drop_off_type'] == "1":
			return "'true'"
		else:
			return "'both'"
	
	structure = { 
		'routes': {
			'columns': [ 
				{ 'name':"route_short_name", 'type': "char(5) NOT NULL default ''", 'map': lambda row, lookupTable: "'" + row['route_short_name']  + "'" },
				{ 'name':"route_long_name", 'type': "varchar(50) NOT NULL default ''", 'map': lambda row, lookupTable: "'" + row['route_long_name']  + "'"  },
				{ 'name':"route_id", 'type': "varchar(20) NOT NULL default '0'", 'map': lambda row, lookupTable: "'" + row['route_id'] + "'" },
				{ 'name':"routeMap", 'type': "varchar(5) default NULL", 'map': lambda row, lookupTable: "'" + row['route_url']  + "'"  },
				{ 'name':"active", 'type': "tinyint(1) NOT NULL default '1'", 'map': lambda row, lookupTable: "1"  },
				{ 'name':"requestCount", 'type': "int(11) NOT NULL default '0'", 'map': lambda row, lookupTable: "0"  },
				{ 'name':"validUntil", 'type': "date default NULL", 'map': lambda row, lookupTable: "NULL"  }],
			'key':"`route_id`"},
		'stops': {
			'columns': [ 
				{ 'name':"stop_id", 'type': "varchar(20) NOT NULL default ''", 'map': lambda row, lookupTable: "'" + row['stop_id']  + "'" },
				{ 'name':"stop_name", 'type': "varchar(250) NOT NULL default ''", 'map': lambda row, lookupTable: "'" + row['stop_name']  + "'" },
				{ 'name':"stop_lat", 'type': "double default NULL", 'map': lambda row, lookupTable: row['stop_lat']},
				{ 'name':"stop_lon", 'type': "double default NULL", 'map': lambda row, lookupTable: row['stop_lon']}],
			'key':"`stop_id`"},
		'times': {
			'columns': [ 
				{ 'name':"route_id", 'type': "varchar(20) NOT NULL default '0'", 'map': lambda row, lookupTable: "'" + lookupTable[row['trip_id']]['route_id']  + "'" },
				{ 'name':"stop_id", 'type': "varchar(20) NOT NULL default '0'", 'map': lambda row, lookupTable: "'" + row['stop_id']  + "'" },
				{ 'name':"stop_sequence", 'type': "int(11) NOT NULL default '0'", 'map': lambda row, lookupTable: "'" + row['stop_sequence']  + "'" },
				{ 'name':"time", 'type': "time NOT NULL default '00:00:00'", 'map': lambda row, lookupTable: "'" + row['departure_time']  + "'" },
				{ 'name':"arrivalTime", 'type': "time default NULL", 'map': lambda row, lookupTable: "'" + row['arrival_time']  + "'" }, 
				{ 'name':"loadOnly", 'type': "varchar(6) NOT NULL default 'both'", 'map': loadOnly },
				{ 'name':"dow", 'type': "varchar(4) NOT NULL default 'week'", 'map': lambda row, lookupTable: "'" + lookupTable[row['trip_id']]['dow'] + "'" }],
			'key':"`route_id`,`stop_id`,`stop_sequence`,`time`, `dow`"}
	}
	

	def __init__(self):
		self.conn = sqlite3.connect('data/fasttranist.db')
		#self.emptyDB()
		self.checkDB()
		
	def emptyDB(self):
		self.conn.executescript("drop table `routes`; drop table `stops`; drop table `times`;")
		
	def checkDB(self):
		search = self.conn.cursor()
		update = self.conn.cursor()
		tableNames = search.execute("SELECT name FROM sqlite_master WHERE type='table'").fetchall()
		tableNames = [x[0] for x in tableNames]
		for tableName in self.structure.keys():
			if tableName not in tableNames:
				table = self.structure[tableName]
				updateSQL = "CREATE TABLE `%s` (" % tableName
				updateSQL += ", ".join(("`%s` %s" % (col['name'], col['type']) for col in table['columns']))
				updateSQL += ", PRIMARY KEY (%s))" % table["key"]
				update.execute(updateSQL)
		self.conn.commit()
		
		
	def updateTable(self, table, data, lookupTable=None):
		updater = self.conn.cursor()
		updater.execute("delete from %s" % table)
		templateString = "insert or replace into `%s` (" % table 
		templateString += ", ".join([col["name"] for col in self.structure[table]["columns"]]) 
		templateString += ") values ("
		count = 0
		for row in data:
			sql = templateString + ", ".join((col['map'](row, lookupTable) for col in self.structure[table]["columns"]) ) + ")"
			count += 1
			print count
			print sql
			print row
			print 
			updater.execute(sql)
		
		print "about to commit"
		self.conn.commit()
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		