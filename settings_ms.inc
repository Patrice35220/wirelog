<?php

   function getSensorsLabels() {

   $sensors = array("none", "Heizung Vorlauf", "Heizung Ruecklauf", "Solar Ruecklauf", "Solar Vorlauf", "Solar Boiler", "Nordseite", "Warm Wasser zapfen", "Warm Wasser heizen", "temp3", "temp4", "temp5", "temp6", "temp7", "temp8", "temp9", "temp10", "temp11");

      return $sensors;

   }

   $colors = array("black", "red", "slateblue", "orange", "BlueViolet", "green", "blue", "white", "grey", "yellowGreen", "purple", "salmon", "chartreuse");



   $datasource = 'http://www.mstehr.site50.net/datasource.php';

?>

