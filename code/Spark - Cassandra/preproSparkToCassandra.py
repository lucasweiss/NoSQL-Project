from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext
from os import listdir
from os.path import isfile, join
from datetime import datetime
from datetime import tzinfo, timedelta, datetime
from pyspark.sql import Row
import re

# CREATE spark and SQL context
sc = SparkContext()
sqlContext = SQLContext(sc)


FORMAT_DATE = "%Y%m%d"
FORMAT_DATE_HOUR = "%Y%m%d %H%M%S"
FORMAT_HOUR = "%H%M%S"
PATH = "/wikistat/wikistats/"

# We keep only file started with page
onlyfiles = [f for f in listdir(PATH) if f.startswith("page")]

# We extract the date from the filename
def date_from_file(filename):
	return re.findall(r'[0-9]{8}', filename)[0]

# We extract the datehour from filename
def datehour_from_file(filename):
	date = date_from_file(filename)[0]
	hour = re.findall(r'([\d]{6})(.gz|$)', filename)[0]
	return date + " " + hour

def hour_from_file(filename):
	hour = re.findall(r'([\d]{6})(.gz|$)', filename)[0]
	return hour[0][0:2]


def timestamp_to_minutes(timestampData, formatTimestamp):
	# first date 1970 01 01
	epoch = datetime.utcfromtimestamp(0)
	# get time in the correct format
	timeT = datetime.strptime(timestampData, formatTimestamp)
	# return number of minutes
	return (timeT - epoch).total_seconds() / 60


def line_to_dict(project, pagename, timestamp, views):
   return Row(
       project=project,
       pagename=pagename,
       timestamp=timestamp_to_minutes(timestamp,FORMAT_DATE),
       views=int(views))

def line_to_dictHour(project, pagename, timestamp, views, hour):
   return Row(
       project=project,
       pagename=pagename,
       timestamp=timestamp_to_minutes(timestamp,FORMAT_DATE),
       views=int(views),
       hour=int(hour))


def date_range (dateEnd, day_range, formatTimestamp):
	epoch = datetime.utcfromtimestamp(0)
	date_start = datetime.strptime(dateEnd, formatTimestamp)
	date_end = datetime.strptime(dateEnd, formatTimestamp)
	date_start = date_start - timedelta(days=day_range)
	date_start = (date_start-epoch).total_seconds() / 60
	date_end = (date_end-epoch).total_seconds() / 60
	return (date_start, date_end)


# We create the list of rdd
rdds_dict = {}
rdds = []
# We create a dictionary to get all record by date
dict_date = {}
# We add the file in the dictionary
for file_name in onlyfiles:
	date = date_from_file(file_name)
	if date in dict_date:
		dict_date[date].append("file://" + PATH + file_name)
	else:
		dict_date[date] = ["file://" + PATH + file_name]


# STOCKAGE RDD DANS CASSANDRA
# TOP 100 PAR JOUR
for keys_file in dict_date.keys():
	# We built our string from list
	listF = dict_date[keys_file]
	strFiles = ','.join(listF)
	rdd_day = sc.textFile(strFiles).map(lambda line: line.split(' ')) \
								   .filter(lambda line: int(line[2]) > 10) \
								   .map(lambda line: ((line[0],line[1]), int(line[2]))) \
								   .reduceByKey(lambda value1, value2: int(value1) + int(value2)) \
								   .map(lambda x: (x[1],x[0])) \
								   .sortByKey(False) \
								   .map(lambda x: (x[1],x[0])) \
								   .map(lambda x: line_to_dict(x[0][0], x[0][1], keys_file,x[1]))
	rdd_day_100 = rdd_day.take(100)
	rdds_dict[keys_file] = rdd_day_100
	sqlContext.createDataFrame(rdd_day_100).write \
										   .format('org.apache.spark.sql.cassandra') \
										   .options(keyspace='nosql_exams', table='top100_by_day') \
										   .save(mode='append')
	
	sqlContext.createDataFrame(rdd_day_100).write \
										   .format('org.apache.spark.sql.cassandra') \
										   .options(keyspace='nosql_exams', table='viewspage_day') \
										   .save(mode='append')
	
	for file_names in dict_date[keys_file]:
		hour = hour_from_file(file_names)
		rdd_hour = sc.textFile(file_names).map(lambda line: line.split(' ')) \
										  .filter(lambda line: int(line[2]) > 10) \
										  .map(lambda line: ((line[0],line[1]), int(line[2]))) \
										  .reduceByKey(lambda value1, value2: value1 + value2) \
										  .map(lambda x: (x[1],x[0])) \
										  .sortByKey(False) \
										  .map(lambda x: (x[1],x[0])) \
										  .map(lambda x: line_to_dictHour(x[0][0], x[0][1], keys_file,x[1], int(hour)))
		
		sqlContext.createDataFrame(rdd_hour.take(100)).write \
													  .format('org.apache.spark.sql.cassandra') \
													  .options(keyspace='nosql_exams', table='viewspage_hour') \
													  .save(mode='append')

# Load table
df = sqlContext.read.format("org.apache.spark.sql.cassandra") \
					.options(keyspace='nosql_exams', table='top100_by_day') \
					.load()

for keys_file in dict_date.keys():
	date_start, date_end = date_range(keys_file, 30, FORMAT_DATE)
	top100_range = df.filter(df["timestamp"] <= date_end).filter(df["timestamp"] >= date_start)

	top100_range = top100_range.rdd.map(lambda line: ((line.project, line.pagename), line.views)) \
								   .reduceByKey(lambda value1, value2: value1 + value2) \
								   .map(lambda x: (x[1],x[0])) \
								   .sortByKey(False) \
								   .map(lambda x: (x[1],x[0])) \
								   .map(lambda x: line_to_dict(x[0][0], x[0][1], keys_file,x[1]))
	# To deal with wrong date
	if not top100_range.isEmpty():
		sqlContext.createDataFrame(top100_range.take(10)).write \
														 .format('org.apache.spark.sql.cassandra') \
														 .options(keyspace='nosql_exams', table='top10_in_30days') \
														 .save(mode='append')

