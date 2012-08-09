<!DOCTYPE html>
<html lang="de">

  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="style.css" />

<?php
   $day = date("d");
   $month = date("m");
   $year = date("y");
   $graphTitle="Wirelog ".$day."/".$month."/".$year;
?>
    <script type="text/javascript" src="dist/spin.js"></script>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">google.load('visualization', '1', {packages: ['corechart']});</script>
    <script type="text/javascript">
      var view;
      var options;
      // Create the data table.
      var data = new google.visualization.DataTable();
      var query;
      var t;
      var opts = {
         lines: 12, // The number of lines to draw
         length: 20, // The length of each line
         width: 4, // The line thickness
         radius: 10, // The radius of the inner circle
         color: '#aaa', // #rgb or #rrggbb
         speed: 1, // Rounds per second
         trail: 60, // Afterglow percentage
         shadow: false, // Whether to render a shadow
         hwaccel: false, // Whether to use hardware acceleration
         className: 'spinner', // The CSS class to assign to the spinner
         zIndex: 2e9, // The z-index (defaults to 2000000000)
         top: 'auto', // Top position relative to parent in px
         left: 'auto' // Left position relative to parent in px
      };
      var spinner = new Spinner(opts);

      function drawVisualization() {
	  var target = document.getElementById('visualization');
	  spinner.spin(target);
<?php
   include_once("logFileParser.inc");
   include_once("settings.inc");

   $nbOfSensors = getNumberOfSensorsForDay($day, $month, $year);
   $windowSizeInDays = $_GET["w"];
   if ($windowSizeInDays!="") {
      if ($windowSizeInDays > 1) {
         $startDay=date("d/m/y", time() - (86400 * ($windowSizeInDays-1)));
         $today=date("d/m/y");
         $graphTitle="Wirelog ".$startDay." - ".$day."/".$month."/".$year;
      } else {
         $graphTitle="Wirelog ".$day."/".$month."/".$year;
      }
   } else {
      $graphTitle="Wirelog ".$day."/".$month."/".$year;
   }

?>
         // Create and draw the visualization.
         options = { curveType: "function", interpolateNulls: true, backgroundColor: "#333",
             titleTextStyle: {color:'#aaa'},
             hAxis: {textStyle: {color: "#aaa"}},
             chartArea:{left:50,top:50, width:"90%", height:"75%"},

<?php
   print("                     title: '$graphTitle',\n");
   print("                     legend: 'none',\n");
   print(" vAxis: {textStyle: {color: '#aaa'}, titleTextStyle: {color:'#aaa'}, title:'Temperatures', gridlines:{count:10}}\n");
   $first = true;
   print("                         , colors:[");
   for($i=1; $i<$nbOfSensors; $i++) {
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
         if ($windowSizeInDays!="") {
            if ($windowSizeInDays > 1) {
               $startDay=date("d/m/y", time() - (86400 * ($windowSizeInDays-1)));
               $today=date("d/m/y");
               print("         query.setQuery('select:from $startDay to $today');\n");
            } else {
               print("         query.setQuery('select:today');\n");
            }
         } else {
            print("         query.setQuery('select:today');\n");
         }
?>
         //query.setRefreshInterval(20); // not working
         // Send the query with a callback function.
         query.send(handleQueryResponse);
      }

      function resendQuery() {
         query.send(handleQueryResponse);
      }

      function handleQueryResponse(response) {
		 spinner.stop();
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
      for($i=1; $i<$nbOfSensors; $i++) {
         $listOfColumns = $listOfColumns.",".$i;
      }
      $listOfColumns = $listOfColumns."]";
      print("         var updatedColors = new Array();\n");
      print("         view.setColumns($listOfColumns);\n"); 
      for($i=1; $i<$nbOfSensors; $i++) {
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
  <body>
     <article class="left" >
        <header>
           <h1>Measurements (OneWire based)</h1>
			<form method="get" action="http://patrice.den.free.fr/wirelog/wirelog.php">
				<p style="padding: 0px 0px 0px 12px;"><br/>Number of past days <input class="days" type="number" name="w" min="1" max="1000"></input></p>
			</form>
        </header>
        <section id="visualization" class="graph" ></section>
        <aside id="menu" class="menu">
<?php $sensors = getSensorsLabels();
for($i=1; $i<$nbOfSensors; $i++) {
    print(" <p style=\"color:$colors[$i]\"><input id=buttonSensor$i type='checkbox' checked='checked' onclick='clickOnSensor()' />$sensors[$i]</p>\n");
}
?>
        </aside>
	</article>
     <footer>
     </footer>
  </body>
</html>
