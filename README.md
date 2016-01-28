# NoSQL - Project
Create a dashboard of the most viewed article in Wikipedia using 150Go on AWS (Scholar project, 2016)
</br>
## User guide

### AWS cluster initialization
#### 1. Configure 6 instances (m3.xlarge) 
http://docs.datastax.com/en/datastax_enterprise/4.8/datastax_enterprise/install/installAMI.html

The cluster has the following caracteristics:

  - in "Advanced Details", write :
```
--clustername wikinosql 
--totalnodes 6 
--version enterprise 
--username <YourUsername> 
--password <YourPassword>
--analyticsnodes 6 
--cfsreplicationfactor 2
```

#### 2. Create the right security group

#### 3. give pem rights
```
$ chmod 400 <YourKeyPair>.pem
```

#### 4. connect to the master to initialize datastax enterprise
```
$ ssh -i <myKeyPair>.pem ubuntu@<ip_master>
```

#### 5. Copy files from local to cluster
```
$ scp -i <path/to/file> ubuntu@<ip>:~/<path/to/file>
```

#### 6. from the master, execute pre-processing data and insert to cassandra
```
$ dse spark-submit --conf spark.executor.memory=7g /home/ubuntu/dataToCassandra.py
```
<br>
### Packages installation
```
sudo apt-get install python-setuptools python-dev build-essential python-pip  
sudo pip install --upgrade setuptools 
sudo pip install pandas
sudo apt-get install git
```
