from os import listdir
from os.path import isfile, join
from datetime import datetime
from datetime import tzinfo, timedelta, datetime
import sys
import re
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext


# CREATE spark and SQL context
sc = SparkContext()
sqlContext = SQLContext(sc)


FORMAT_DATE = "%Y-%m-%d"

def timestamp_to_minutes(timestampData, formadf30imestamp):
	# first date 1970 01 01
	epoch = datetime.utcfromtimestamp(0)
	# get time in the correct format
	timeT = datetime.strptime(timestampData, formadf30imestamp)
	# return number of minutes
	return (timeT - epoch).total_seconds() / 60


def date_range (dateEnd, day_range, formadf30imestamp):
	epoch = datetime.utcfromtimestamp(0)
	date_start = datetime.strptime(dateEnd, formadf30imestamp)
	date_end = datetime.strptime(dateEnd, formadf30imestamp)
	date_start = date_start - timedelta(days=day_range)
	print date_start
	date_start = (date_start-epoch).total_seconds() / 60
	date_end = (date_end-epoch).total_seconds() / 60
	return (date_start, date_end)


timeMinutes = timestamp_to_minutes(sys.argv[1], FORMAT_DATE)

start, end = date_range(sys.argv[1], 29 ,FORMAT_DATE)



# REQUETE TOP10 30jrs + TREND
# Save top 10 in 30days to csv
df30 = sqlContext.read.format("org.apache.spark.sql.cassandra")
					  .options(keyspace='nosql_exams', table='top10_in_30days')
					  .load();
df30 = df30.filter(df30.timestamp >= int(timeMinutes)).filter(df30.timestamp <= int(timeMinutes))
df30.toPandas().to_csv("/var/www/wiki_month.csv",mode = 'w', index=False)

# Load view by day for a page
df = sqlContext.read.format("org.apache.spark.sql.cassandra")
					.options(keyspace='nosql_exams', table='viewspage_day')
					.load();
# Join the two tables to get the right pages and pivot to have page as column and views as value
dfN = df.filter(df.timestamp >= int(start)) \
		.filter(df.timestamp <= int(end)) \
		.join(df30, (df.pagename == df30.pagename) & (df.project == df30.project), 'inner') \
		.drop(df30.pagename).drop(df30.views).drop(df30.timestamp).drop(df30.project) \
		.toPandas() \
		.pivot(index='timestamp', columns='pagename', values='views') \
		.fillna(0) \
		.reset_index()

dfN["timestamp"] = dfN["timestamp"].apply(lambda x : datetime.fromtimestamp(x * 60))
								   .apply(lambda x : str(x)[0:10])
								   
dfN = dfN.rename(columns={'timestamp': 'date'})
dfN.to_csv("/var/www/wiki_month_line.csv",mode = 'w', index=False)



