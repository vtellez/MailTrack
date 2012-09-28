<div id="content-wrapper">
	<div class="center-wrapper">
				<div id="main">
<?php 
	if($this->controlacceso->permisoAdministracion()) {
?>
<div class="buttons" style="margin-top:-9px;">
    <a href="<?php echo site_url('buscador/accesos');?>">
        <img src="<?php echo site_url('img/buttons/filter.png'); ?>"/> 
      Aplicar nuevo filtro
    </a>
        <?php if($num_rows > 0){ ?>
    <a href="<?php echo site_url('accesos/estadisticas');?>">
        <img src="<?php echo site_url('img/menu/chart.png'); ?>"/> Estadísticas</a>
        <?php } ?>
</div>
<?php } ?>
<?php 
	if($filtro == "todos" ){ $titulo = "Todos mis accesos"; $img = "globe_up.png"; } 
	elseif($filtro == "buzonweb"){
		$titulo="Accesos desde Buzón Web"; $img="globe_up.png";
	}
        elseif($filtro == "pop"){
                $titulo="Accesos POP"; $img="globe_download.png";
        }
        elseif($filtro == "imap"){
                $titulo="Accesos IMAP"; $img="globe_up.png";
        }
        elseif($filtro == "resultados"){
                $titulo="Resultados de la búsqueda"; $img="search_globe.png";
        }

	$titulo =  $titulo." (".number_format($num_rows).")";

 ?>


   <img src="<?php echo site_url('img/seg/'.$img); ?>" style="margin-top:-15px;" width="48" height="48" align="left" /> <h2 class="left"> &nbsp;<?php echo $titulo; ?></h2>
                                               <div class="content-separator"></div>

<?php
        //Cargamos el cuadro resumen de condiciones de filtrado
        $this->load->view('info_condiciones_accesos');
?>


<div style="float:left; font-size:14px;">Haga clic en el icono de estado para la vista detallada del acceso.</div>

<?php echo $pagination->create_links(); ?>

<br/>
<br/>
					<table class="data-table" class="tablesorter" style="white-space: nowrap; table-layout:fixed; width:100%;" >
					    <thead>
						<tr>
<?php $img = "<img src='".site_url('img/'.$sentido.'.png')."' />"; ?>
<?php if($campo == "estado") { $cad = $img; $order = $contrario;}else{ $cad = ""; $order = $sentido; }?>
<th width="45px"><a href="<?php echo $base; ?>/estado/<?php echo $order;?>"><?php echo $cad;?> Estado</a></th>

<?php if($campo == "usuario") { $cad = $img; $order = $contrario;}else{ $cad = ""; $order = $sentido; }?>
<th width="110px"><a href="<?php echo $base; ?>/usuario/<?php echo $order;?>"><?php echo $cad;?> Usuario</a></th>

<?php if($campo == "protocolo") { $cad = $img; $order = $contrario;}else{ $cad = ""; $order = $sentido; }?>
<th width="80px"><a href="<?php echo $base; ?>/protocolo/<?php echo $order;?>"><?php echo $cad;?> Tipo acceso</a></th>

<?php if($campo == "contador") { $cad = $img; $order = $contrario;}else{ $cad = ""; $order = $sentido; }?>
<th width="100px"><a href="<?php echo $base; ?>/contador/<?php echo $order;?>"><?php echo $cad;?> Num. accesos</a></th>

<?php if($campo == "ip") { $cad = $img; $order = $contrario;}else{ $cad = ""; $order = $sentido; }?>
<th width="95x"><a href="<?php echo $base; ?>/ip/<?php echo $order;?>"><?php echo $cad;?> Dirección IP</a></th>
							
<?php if($campo == "fecha") { $cad = $img; $order = $contrario;}else{ $cad = ""; $order = $sentido; }?>
<th width="210px;"><a href="<?php echo $base; ?>/fecha/<?php echo $order;?>"><?php echo $cad; ?> Fecha de último acceso</a></th>
						</tr>
					     </thead>
					     <tbody>
<?php
 if ($accesos->num_rows() ==  0)
                {
			echo "<tr class='even'><td colspan='5'>No se ha encontrado ningún acceso en \"$titulo\"</td></tr>";
                }else
                {
                        $cont = 1;
                        foreach ($accesos->result() as $row)
                        {
                                if($cont % 2 == 0) {$tr="";} else { $tr="class=\"even\""; }
                                $cont++;

			//En función del estado actual del acceso, mostramos un icono u otro

				if($row->estado == 0){$img="valid";}
				else{$img="error";}

				?>
                                                <tr <?php echo $tr; ?>>
                                                        <td><a href="<?php echo site_url(''); ?>accesos/ver/<?php echo $row->aid;?>"><center><img src="<?php echo site_url("img/seg/32x32/$img.png"); ?>" border="0" width="24" height="24" title="Ver detalle"/></center></a></td>
	                                                <td><?php echo $row->usuario;?></td> 
						        <td><center><?php echo utf8_decode($row->protocolo);?></center></td>
                                                        <td><?php  echo $row->contador;?>
							<?php echo ($row->contador > 1)?'accesos':'acceso'; ?>
							</td>
                                                        <td><?php echo $row->ip;?></td>
                                                        <td><?php echo "Realizado el día ".date("d/m/Y, H:i",$row->fecha);?></td>
                                                </tr>
		<?php } //foreach ?>

<?php
}//else
?>

					     </tbody>
					</table>


<?php echo $pagination->create_links(); ?>
<br/><br/>
				</div>
			</div>


	</div>
</div>


