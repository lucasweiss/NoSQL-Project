from os import listdir
from os.path import isfile, join
from datetime import datetime
from datetime import tzinfo, timedelta, datetime
import sys
import re
from pyspark.sql import SQLContext
from pyspark import SparkConf, SparkContext

# CREATE spark and SQL context
sc = SparkContext()
sqlContext = SQLContext(sc)

FORMAT_DATE = "%Y-%m-%d"

# Fonction recuperant les 24h glissantes
def get24Hour_sliced (date, hour, formatTimestamp):
	epoch = datetime.utcfromtimestamp(0)
	# Set date end
	date_end = datetime.strptime(date, formatTimestamp)
	hour_end = hour
	
	if hour < 24:
		date_start = date_end - timedelta(days=1)
		date_start = (date_start-epoch).total_seconds() / 60
		date_end = (date_end - epoch).total_seconds() / 60
		hour_start = hour
	else:
		date_end = (date_end - epoch).total_seconds() / 60
		hour_start = 0
		date_start = date_end
	
	return date_start, date_end, hour_start, hour_end

# Get the start,end date and hour
date_start, date_end, hour_start, hour_end = get24Hour_sliced(sys.argv[1][0:10],
															  int(sys.argv[1][11:]), 
															  FORMAT_DATE
															  )

# Get the top 10 of the day
df30 = sqlContext.read.format("org.apache.spark.sql.cassandra")
				 .options(keyspace='nosql_exams', table='top10_in_30days')
				 .load();

df30 = df30.filter(df30.timestamp >= int(date_end)).filter(df30.timestamp <= int(date_end))
# Load the table of viewspage by hour

df = sqlContext.read.format("org.apache.spark.sql.cassandra")
					.options(keyspace='nosql_exams', table='viewspage_hour')
					.load();

# Join result to get only the right pages
dfH = df.filter(df.timestamp >= int(date_start)) \
		.filter(df.timestamp <= int(date_end)) \
		.join(df30, (df.pagename == df30.pagename) & (df.project == df30.project), 'inner') \
		.drop(df30.pagename) \
		.drop(df30.views) \
		.drop(df30.timestamp) \
		.drop(df30.project) \
		.drop(df.project) \
		.toPandas()
# Get date from minutes
dfH["timestamp"] = dfH.apply(lambda x : datetime.fromtimestamp(x["timestamp"] * 60) 
										+ timedelta(hours=x["hour"]), axis=1
							)
# Get only the right date
dfH = dfH[dfH['timestamp'] >= datetime.fromtimestamp(date_start * 60) + timedelta(hours=hour_start)]
# rename column timestamp as date
dfH = dfH.rename(columns={'timestamp':'date'})
# Drop hour column
dfH = dfH.drop("hour", axis=1)

df24 = dfH.groupby("pagename").sum()
df24 = df24.reset_index()
df24.to_csv("/var/www/wiki_day.csv",mode = 'w', index=False)

# Get pagename as column and views as value
dfH = dfH.pivot(index='date', columns='pagename', values='views').fillna(0).reset_index()
# SAVE TO CSV
dfH.to_csv("/var/www/wiki_day_line.csv",mode = 'w', index=False)




