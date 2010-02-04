import zipfile

class dataReaderModel:
	data = None
	
	def __init__(self):
		pass
		
	def updateZip(self):
		pass
	
	def load(self):
		self.data = zipfile.ZipFile('data/google_transit.zip')
	
	def getData(self, name, index=None):
		if self.data == None:
			self.load();
		rows = self.data.read(name + '.txt').split("\r\n")
		columns = rows[0].split(',')
		data = (dict(zip(columns,row.split(","))) for row in rows[1:] if len(row) > 1)
		if index != None:
			dictData = {}
			for row in data:
				if row[index] not in dictData:
					dictData[row[index]] = row
				else:
					raise ValueError("Duplicate Values for index column: " + row[index])
			data = dictData
		return data