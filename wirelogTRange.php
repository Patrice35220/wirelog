<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<?php
   // extract range from parameters 
   // from=dd/mm/yy&to=dd/mm/yy
   $fromDate = $_GET["from"];
   $toDate = $_GET["to"];

   $day = date("d");
   $month = date("m");
   $year = date("y");

   if (($fromDate != "") && ($toDate != "")) {
      $graphTitle="Wirelog from $fromDate to $toDate"; 
   } else {
      $graphTitle="Wirelog $day/$month/$year"; 
   }
?>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script type="text/javascript">
      var view;
      var options;
      // Create the data table.
      var data = new google.visualization.DataTable();
      var query;
      var t;

      function drawVisualization() {
<?php
   include_once("logFileParser.inc");
   include_once("settings.inc");

   // Read data and in order to generate maximum values (TODO maybe to be changed...)
   $lines = generateXYForDates($fromDate, $toDate);

   $nbOfLines=sizeof($lines);
   $nbOfMeasurements=sizeof($lines[0]);
?>
         // Create and draw the visualization.
         options = { curveType: "function", interpolateNulls: true, 
                     chartArea:{left:50,top:50, width:"90%", height:"75%"},
<?php
   print("                     title: '$graphTitle',\n");
   print("                     legend: 'none',\n");
   print("                     vAxis: {title:'Temperatures', gridlines:{count:10}}\n");
   $first = true;
   print("                         , colors:[");
   for($i=1; $i<$nbOfLines; $i++) {
      if ($first) {
         print("'$colors[$i]'");
         $first=false;
      } else {
         print(",'$colors[$i]'");
      } 
   }
   print("]\n");

   print("          };\n");
?>
         // Make a Query to datasource
         // Create a view (to be able to hide / show measurement)
<?php
         // $datasource is set in settings
         print("         query = new google.visualization.Query('$datasource');\n");
         print("         query.setQuery('select:from $fromDate to $toDate');\n");
?>
         //query.setRefreshInterval(20); // not working
         // Send the query with a callback function.
         query.send(handleQueryResponse);
      }

      function resendQuery()
      {
         query.send(handleQueryResponse);
      }

      function handleQueryResponse(response) {
         if (response.hasWarning() ||response.isError()) {
            // No data
            t=setTimeout("resendQuery()",60000);
            return;
         } else {
            data = response.getDataTable();
            view = new google.visualization.DataView(data);
            // Process in same way as clickOnSensor button, so, hidden sensors remain hidden
            clickOnSensor();
            t=setTimeout("resendQuery()",60000);
         }
      }

      function clickOnSensor() {
<?php
      $listOfColumns="[0";
      for($i=1; $i<$nbOfLines; $i++) {
         $listOfColumns = $listOfColumns.",".$i;
      }
      $listOfColumns = $listOfColumns."]";
      print("         var updatedColors = new Array();\n");
      print("         view.setColumns($listOfColumns);\n"); 
      for($i=1; $i<$nbOfLines; $i++) {
         print("         if (document.getElementById('buttonSensor$i').checked == 0) {\n"); 
         print("            view.hideColumns([$i]);\n");
         print("         } else {\n"); 
         print("            // Push back color in colorOption \n");
         print("            updatedColors.push('$colors[$i]');\n");
         print("         }\n"); 
      }
      print("         options.colors=updatedColors;\n"); 
      print("         var linechart = new google.visualization.LineChart(document.getElementById('visualization')).draw(view, options);\n");
?>
      }

      google.setOnLoadCallback(drawVisualization);
    </script>
  </head>
  <body style="font-family: Arial;border: 0 none;">
   <div id="container" style="width:840px; height.480px; position:relative;">
      <div id="visualization" style="width: 640px; height:480px; float:left;"></div>
      <div id="menu" style="width:200px;height:480px;position:relative;float:right;">
<?php
   $sensors = getSensorsLabels();
   for($i=1; $i<$nbOfLines; $i++) {
      print("         <p style=\"color:$colors[$i]\"><input id=buttonSensor$i type='checkbox' checked='checked' onclick='clickOnSensor()' />$sensors[$i]</p>\n");
   }
?>
      </div>
   </div>

  </body>
</html>
