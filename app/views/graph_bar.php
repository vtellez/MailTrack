<fieldset style="background-color:#fff; padding:5px; margin-left: -20px;">
<legend><?php echo $titulo; ?></legend> 
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
	
	
		data.addColumn('string', 'Horas');
	<?php
		foreach($variables as $var)
		{
			echo " data.addColumn('number', '$var'); ";
		}

		echo "data.addRows($tam);";


		for($i=0; $i < $tam; $i++)
		{
			for($j=0; $j< count($variables)+1; $j++)
			{
				echo "data.setValue($i, $j, ".$datos[$i][$j].");\n";					
			}
		}
	?>
        var chart = new google.visualization.ColumnChart(document.getElementById('<?php echo $nombreid; ?>'));
        chart.draw(data, {width: 900, height: 300, title: '',
                        <?php if(isset($colores)) {
				echo "$colores,";
                        } ?>
			<?php if(isset($titulox)) {
                          echo "hAxis: {title: '$titulox', titleTextStyle: {size: '14px'}},";
			} ?>
			chartArea:{left:70,bottom:50,width:"70%",height:"70%"},
			<?php if(isset($apilar)) {
				echo "isStacked: $apilar,";
                        } ?>
			legend: 'right',
                         });
      }
    </script>
    <div id="<?php echo $nombreid; ?>"></div>
</fieldset>
