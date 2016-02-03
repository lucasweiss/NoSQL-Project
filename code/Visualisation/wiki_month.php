<?php
     exec('dse pyspark --conf spark.executor.memory=2g /var/www/wiki_month.py '.$_GET['date']);
     header('location:index.php?date='.$_GET['date']);
?>
