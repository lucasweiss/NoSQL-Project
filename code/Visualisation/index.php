<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
  <head>
     <title>Page de RANKING WIKIPEDIA</title>
     <meta http-equiv="X-UA-Compatible" content="IE=9">
  </head>
  <style>
  body {
    font: 10px sans-serif;
    background-image:url(photo_nosql.jpg);
  }

  .axis path,
  .axis line {
    fill: none;
    stroke: #000;
    shape-rendering: crispEdges;
  }



  .x.axis path {
    display: none;
  }

  .y.axis path {
   display: none;
  }
    .y.axis path {
   display: none;
  }

  .d3-tip {
    line-height: 1;
    font-weight: bold;
    padding: 12px;
    background: rgba(0, 0, 0, 0.8);
    color: #fff;
    border-radius: 2px;
  }

  /* Creates a small triangle extender for the tooltip */
  .d3-tip:after {
    box-sizing: border-box;
    display: inline;
    font-size: 14px;
    width: 100%;
    line-height: 1;
    color: rgba(0, 0, 0, 0.8);
    content: "\25BC";
    position: absolute;
    text-align: center;
  }

  /* Style northward tooltips differently */
  .d3-tip.n:after {
    margin: -1px 0 0 0;
    top: 100%;
    left: 0;
  }
  body { font: 12px Arial;}

path { 
   stroke: steelblue;
   stroke-width: 2;
   fill: none;
}

.axis path,
.axis line {
   fill: none;
   stroke: grey;
   stroke-width: 1;
   shape-rendering: crispEdges;
}
    </style>
<h1 style="text-align:center">Page de Ranking Wikipedia de Lucas, William, Paul et Florian</h1>

  <body>
    <form name="myform" onSubmit="return handleClick()">
            <input name="Submit"  type="submit" value="refresh" >
            <input type="text" id="myVal" placeholder="Add some text&hellip;">
        </form>
        <ul></ul>
    <button id="Chargement Mensuel" onclick="document.location.replace('wiki_month.php?date='+$_GETDATE('date'))">Chargement Mensuel</button>
    <button id="Chargement 24 hours" onclick="document.location.replace('wiki_day.php?date='+$_GETDATE('date'))">Chargement 24 hours</button>

        <ul></ul>
        <ul></ul>
    <button id="Graphe Mensuel" onclick="showMensuel()">Mensuel</button>
    <button id="Graphe 24 hours" onclick="showDay()">24 hours</button>

    <div id="chart"></div>

    <script src="http://d3js.org/d3.v2.min.js"></script>
    <script src="http://labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js"></script>
    <script>

function handleClick(event){
      console.log(document.getElementById("myVal").value)
      window.location.search='date='+String(document.getElementById("myVal").value );
      return false;
};

var commaFormat = d3.format(',');
function showMensuel() {
// oh l'ARNAQUEEEEE
d3.select("svg").remove();
  d3.select("svg").remove();
  d3.select("svg").remove();
   d3.select("svg").remove();
  d3.select("svg").remove();
  <?php
     // exec('dse pyspark --conf spark.executor.memory=2g wiki_month.py '.$_GET['date'])
  ?>

var data = d3.csv("wiki_month.csv", renderChartMensuel);
var dataz = d3.csv("wiki_month_line.csv", testlinebar);

};

function showDay() {
  d3.select("svg").remove();
  d3.select("svg").remove();
  d3.select("svg").remove();
  
<?php
      //exec('dse pyspark --conf spark.executor.memory=2g wiki_day.py '.$_GET['date'])
  ?>
var data = d3.csv("wiki_day.csv", renderChartDaily);
var dataz = d3.csv("wiki_day_line.csv", testlinebardaily);
//testlinebar()
};
var margin = {top: 40, right: 20, bottom: 30, left: 40},
    width = 960 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom;

var tip = d3.tip()
  .attr('class', 'd3-tip')
  .offset([-10, 0])
  .html(function(d) {var svg = d3.select("body").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
    .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

svg.call(tip);
    return "<strong>views:</strong> <span style='color:red' >" + commaFormat(d.views) + "</span>";
  })

function type(d) {
  d.views = +d.views;
  return d;
}
function renderChartMensuel(data) {

var svg = d3.select("body").append("svg")


var valueLabelWidth = 60; // space reserved for value labels (right)
var barHeight = 20; // height of one bar
var barLabelWidth = 400; // space reserved for bar labels
var barLabelPadding = 5; // padding between bar and bar labels (left)
var gridLabelHeight = 100; // space reserved for gridline labels
var gridChartOffset = 3; // space between start of grid and first bar
var maxBarWidth = 800; // width of the bar with the max value
var pos = 10; // width of the bar with the max value
// accessor functions 
var barLabel = function(d) { return d['pagename']; };
var barValue = function(d) { return parseFloat(d['views']); };

// sorting
var sortedData = data.sort(function(a, b) {
 return d3.descending(barValue(a), barValue(b));
}); 

// scales
var yScale = d3.scale.ordinal().domain(d3.range(0, sortedData.length)).rangeBands([0, sortedData.length * barHeight]);
var y = function(d, i) { return yScale(i); };
var yText = function(d, i) { return y(d, i) + yScale.rangeBand() / 2; };
var x = d3.scale.linear().domain([0, d3.max(sortedData, barValue)]).range([0, maxBarWidth]);
// svg container element
var chart = d3.select('#chart').append("svg")
  .attr('width', maxBarWidth + barLabelWidth + valueLabelWidth)
  .attr('height', gridLabelHeight + gridChartOffset + sortedData.length * barHeight);
// grid line labels
var gridContainer = chart.append('g')
  .attr('transform', 'translate(' + barLabelWidth + ',' + gridLabelHeight + ')'); 
gridContainer.selectAll("text").data(x.ticks(10)).enter().append("text")
  .attr("x", x)
  .attr("dy", -3)
  .attr("text-anchor", "middle")
  .style("font-size","12px")
  .style("font-weight","bold")
  .text(String);
// vertical grid lines
gridContainer.selectAll("line").data(x.ticks(10)).enter().append("line")
  .attr("x1", x)
  .attr("x2", x)
  .attr("y1", 0)
  .attr("y2", yScale.rangeExtent()[1] + gridChartOffset)
  .style("stroke", "#ccc");

// bar labels
var labelsContainer = chart.append('g')
  .attr('transform', 'translate(' + (barLabelWidth - barLabelPadding) + ',' + (gridLabelHeight + gridChartOffset) + ')'); 
labelsContainer.selectAll('text').data(sortedData).enter().append('text')
  .attr('y', yText)
  .attr('stroke', 'none')
  .attr('fill', 'black')
  .attr("dy", ".35em") // vertical-align: middle
  .attr('text-anchor', 'end')
  .style("font-size","12px")
  .style("font-weight","bold")
  .text(barLabel);
// bars

var barsContainer = chart.append('g')
  .attr('transform', 'translate(' + barLabelWidth + ',' + (gridLabelHeight + gridChartOffset) + ')'); 
barsContainer.selectAll("rect").data(sortedData).enter().append("rect")
  .attr('y', y)
  .attr('height', yScale.rangeBand())
  .attr('width', function(d) { return x(barValue(d)); })
  .attr('stroke', 'white')
  .attr('fill', 'steelblue')
  .on('mouseover', tip.show)
  .on('mouseout', tip.hide);

// bar value labels
barsContainer.selectAll("text").data(sortedData).enter().append("text")
  .attr("x", function(d) { return x(barValue(d)); })
  .attr("y", yText)
  .attr("dx", 3) // padding-left
  .attr("dy", ".35em") // vertical-align: middle
  .attr("text-anchor", "start") // text-align: right
  .attr("fill", "black")
  .attr("stroke", "none")
  .style("font-size","12px")
  .style("font-weight","bold")
  .text(function(d) { return commaFormat(d3.round(barValue(d), 2)); });

// start line
barsContainer.append("line")
  .attr("y1", -gridChartOffset)
  .attr("y2", yScale.rangeExtent()[1] + gridChartOffset)
  .style("stroke", "#000");

  //affichage du titre
  barsContainer .append("g")
      .attr("transform", "translate(" + (barLabelWidth/2) + ", -40)")
      .append("text")
      .text("TOP 10 mensuel Wikipedia "+$_GETDATE('date'))
      .style("font-size","30px")
      .style("fill","darkOrange")
      .style("font-weight","bold");


}
function renderChartDaily(data) {

var svg = d3.select("body").append("svg")


var valueLabelWidth = 60; // space reserved for value labels (right)
var barHeight = 20; // height of one bar
var barLabelWidth = 400; // space reserved for bar labels
var barLabelPadding = 5; // padding between bar and bar labels (left)
var gridLabelHeight = 100; // space reserved for gridline labels
var gridChartOffset = 3; // space between start of grid and first bar
var maxBarWidth = 800; // width of the bar with the max value
var pos = 10; // width of the bar with the max value
// accessor functions 
var barLabel = function(d) { return d['pagename']; };
var barValue = function(d) { return parseFloat(d['views']); };

// sorting
var sortedData = data.sort(function(a, b) {
 return d3.descending(barValue(a), barValue(b));
}); 

// scales
var yScale = d3.scale.ordinal().domain(d3.range(0, sortedData.length)).rangeBands([0, sortedData.length * barHeight]);
var y = function(d, i) { return yScale(i); };
var yText = function(d, i) { return y(d, i) + yScale.rangeBand() / 2; };
var x = d3.scale.linear().domain([0, d3.max(sortedData, barValue)]).range([0, maxBarWidth]);
// svg container element
var chart = d3.select('#chart').append("svg")
  .attr('width', maxBarWidth + barLabelWidth + valueLabelWidth)
  .attr('height', gridLabelHeight + gridChartOffset + sortedData.length * barHeight);
// grid line labels
var gridContainer = chart.append('g')
  .attr('transform', 'translate(' + barLabelWidth + ',' + gridLabelHeight + ')'); 
gridContainer.selectAll("text").data(x.ticks(10)).enter().append("text")
  .attr("x", x)
  .attr("dy", -3)
  .attr("text-anchor", "middle")
  .style("font-size","12px")
  .style("font-weight","bold")
  .text(String);
// vertical grid lines
gridContainer.selectAll("line").data(x.ticks(10)).enter().append("line")
  .attr("x1", x)
  .attr("x2", x)
  .attr("y1", 0)
  .attr("y2", yScale.rangeExtent()[1] + gridChartOffset)
  .style("stroke", "#ccc");

// bar labels
var labelsContainer = chart.append('g')
  .attr('transform', 'translate(' + (barLabelWidth - barLabelPadding) + ',' + (gridLabelHeight + gridChartOffset) + ')'); 
labelsContainer.selectAll('text').data(sortedData).enter().append('text')
  .attr('y', yText)
  .attr('stroke', 'none')
  .attr('fill', 'black')
  .attr("dy", ".35em") // vertical-align: middle
  .attr('text-anchor', 'end')
  .style("font-size","12px")
  .style("font-weight","bold")
  .text(barLabel);
// bars

var barsContainer = chart.append('g')
  .attr('transform', 'translate(' + barLabelWidth + ',' + (gridLabelHeight + gridChartOffset) + ')'); 
barsContainer.selectAll("rect").data(sortedData).enter().append("rect")
  .attr('y', y)
  .attr('height', yScale.rangeBand())
  .attr('width', function(d) { return x(barValue(d)); })
  .attr('stroke', 'white')
  .attr('fill', 'steelblue')
  .on('mouseover', tip.show)
  .on('mouseout', tip.hide);

// bar value labels
barsContainer.selectAll("text").data(sortedData).enter().append("text")
  .attr("x", function(d) { return x(barValue(d)); })
  .attr("y", yText)
  .attr("dx", 3) // padding-left
  .attr("dy", ".35em") // vertical-align: middle
  .attr("text-anchor", "start") // text-align: right
  .attr("fill", "black")
  .attr("stroke", "none")
  .style("font-size","12px")
  .style("font-weight","bold")
  .text(function(d) { return commaFormat(d3.round(barValue(d), 2)); });

// start line
barsContainer.append("line")
  .attr("y1", -gridChartOffset)
  .attr("y2", yScale.rangeExtent()[1] + gridChartOffset)
  .style("stroke", "#000");

  //affichage du titre
  barsContainer .append("g")
      .attr("transform", "translate(" + (barLabelWidth/2) + ", -40)")
      .append("text")
      .text("TOP 10 Last 24 hours Wikipedia " + $_GETDATE('date') )
      .style("font-size","30px")
      .style("fill","darkOrange")
      .style("font-weight","bold");


}



function testlinebar(data){
var margin = {top: 20, right: 80, bottom: 30, left: 50},
    width = 960 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom;

var parseDate = d3.time.format("%Y-%m-%d").parse;

var x = d3.time.scale()
    .range([0, width]);

var y = d3.scale.linear()
    .range([height, 0]);

var color = d3.scale.category10();

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left");

var line = d3.svg.line()
    .interpolate("basis")
    .x(function(d) { return x(d.date); })
    .y(function(d) { return y(d.temperature); });

var svg = d3.select("body").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");




  color.domain(d3.keys(data[0]).filter(function(key) { return key !== "date"; }));

  data.forEach(function(d) {
    d.date = parseDate(d.date);
  });

  var pages = color.domain().map(function(name) {
    return {
      name: name,
      values: data.map(function(d) {
        return {date: d.date, temperature: +d[name]};
      })
    };
  });

  x.domain(d3.extent(data, function(d) { return d.date; }));

  y.domain([
    d3.min(pages, function(c) { return d3.min(c.values, function(v) { return v.temperature; }); }),
    d3.max(pages, function(c) { return d3.max(c.values, function(v) { return v.temperature; }); })
  ]);

  svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis);

  svg.append("g")
      .attr("class", "y axis")
      .call(yAxis)
    .append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 6)
      .attr("dy", ".71em")
      .style("text-anchor", "end")
      .text("NB VIEWS");

  var city = svg.selectAll(".city")
      .data(pages)
    .enter().append("g")
      .attr("class", "city");

  city.append("path")
      .attr("class", "line")
      .attr("d", function(d) { return line(d.values); })
      .style("stroke", function(d) { return color(d.name); });

  city.append("text")
      .datum(function(d) { return {name: d.name, value: d.values[d.values.length - 1]}; })
      .attr("transform", function(d) { return "translate(" + x(d.value.date) + "," + y(d.value.temperature) + ")"; })
      .attr("x", 3)
      .attr("dy", ".35em")
      .text(function(d) { return d.name; });


}

function testlinebardaily(data){
var margin = {top: 20, right: 80, bottom: 30, left: 50},
    width = 960 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom;

var parseDate = d3.time.format("%Y-%m-%d %H:%M:%S").parse;

var x = d3.time.scale()
    .range([0, width]);

var y = d3.scale.linear()
    .range([height, 0]);

var color = d3.scale.category10();

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left");

var line = d3.svg.line()
    .interpolate("basis")
    .x(function(d) { return x(d.date); })
    .y(function(d) { return y(d.temperature); });

var svg = d3.select("body").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");




  color.domain(d3.keys(data[0]).filter(function(key) { return key !== "date"; }));

  data.forEach(function(d) {
    d.date = parseDate(d.date);
  });

  var pages = color.domain().map(function(name) {
    return {
      name: name,
      values: data.map(function(d) {
        return {date: d.date, temperature: +d[name]};
      })
    };
  });

  x.domain(d3.extent(data, function(d) { return d.date; }));

  y.domain([
    d3.min(pages, function(c) { return d3.min(c.values, function(v) { return v.temperature; }); }),
    d3.max(pages, function(c) { return d3.max(c.values, function(v) { return v.temperature; }); })
  ]);

  svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis);

  svg.append("g")
      .attr("class", "y axis")
      .call(yAxis)
    .append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 6)
      .attr("dy", ".71em")
      .style("text-anchor", "end")
      .text("NB VIEWS");

  var city = svg.selectAll(".city")
      .data(pages)
    .enter().append("g")
      .attr("class", "city");

  city.append("path")
      .attr("class", "line")
      .attr("d", function(d) { return line(d.values); })
      .style("stroke", function(d) { return color(d.name); });

  city.append("text")
      .datum(function(d) { return {name: d.name, value: d.values[d.values.length - 1]}; })
      .attr("transform", function(d) { return "translate(" + x(d.value.date) + "," + y(d.value.temperature) + ")"; })
      .attr("x", 3)
      .attr("dy", ".35em")
      .text(function(d) { return d.name; });


}

function $_GETDATE(param) {
  var vars = {};
  window.location.href.replace( 
    /[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
    function( m, key, value ) { // callback
      vars[key] = value !== undefined ? value : '';
    }
  );

  if ( param ) {
    return vars[param] ? vars[param] : null;  
  }
  return vars;
}

    </script>

  </body>
</html>

