<?php	
		$base = "http://chart.apis.google.com/chart?chs=500x200&chs=550x180&cht=p3";
		if(isset($color)){ $base = $base."&chco=$color";}
		$i = 0;
		$data = "";
		$names = "";
		foreach($etiquetas as $etiqueta){
			$data = ($i == 0) ? $data.'&chd=t:'.$datos[$i]/$totales : $data.($datos[$i]/$totales);
			$data = ($i < sizeof($etiquetas)-1) ? $data.',' : $data;

                        $names = ($i == 0) ? $names.'&chl='.$etiquetas[$i] : $names.$etiquetas[$i];
                        $names = ($i < sizeof($etiquetas)-1) ? $names.'|' : $names;

			$i = $i + 1;
		}
		$graph = $base.$data.$names;
        ?>


<center><img src="<?php echo $graph; ?>"/> <br/><i><?php echo $titulo; ?></i></center><br/>
	<table class="data-table">
		<tr>
			<th>Tipo de correo</th>
			<th>Valor</th>
			<th>Porcentaje</th>
		</tr>

<?php  	$i = 0;
	foreach($etiquetas as $etiqueta){ ?>
                <tr class="even">
                        <td><?php echo $etiqueta; ?></td>
                        <td><?php echo number_format($datos[$i]); ?></td>
                        <td><?php echo  number_format(($datos[$i]/$totales)*100, 2, ",", "."); ?>%</td>
                </tr>

<?php  $i = $i + 1; }?>
		<tr>
			<td><b>TOTALES</b></td>	
			<td><b><?php echo number_format($totales); ?></b></td>
			<td><b>100%</b></td>
		</tr>
	</table>


