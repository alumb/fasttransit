
from dataReaderModel import dataReaderModel
from dbUpdateModel import dbUpdateModel

class UpdateController:
	dataReader = None
	dbUpdate = None
	
	def __init__(self):
		self.dataReader = dataReaderModel()
		self.dataReader.load()
		self.dbUpdate = dbUpdateModel()
	
	def loadData(self):
		print 
		"""print "Loading routes"
		c.dbUpdate.updateTable("routes",c.dataReader.getData("routes"))
		print 
		print "loading stop_times"
		stop_times = c.dataReader.getData("stop_times")
		print "loading trips"
		trips = c.dataReader.getData("trips","trip_id")
		print "loading calendar"
		calendar = c.dataReader.getData("calendar","service_id")
		
		for row in trips:
			#print trips[row]
			if calendar[trips[row]['service_id']]['saturday'] == "1":
				trips[trips[row]['trip_id']]['dow'] = "sat"
			elif calendar[trips[row]['service_id']]['sunday'] == "1":
				trips[trips[row]['trip_id']]['dow'] = "sun"
			else:
				trips[trips[row]['trip_id']]['dow'] = "week"
		print "updating times"
		c.dbUpdate.updateTable("times",stop_times,trips)
		print """
		
		print "loading stops"
		c.dbUpdate.updateTable("stops",c.dataReader.getData("stops"))

	
if __name__ == "__main__":
		import sys
		c = UpdateController()
		c.loadData()

		