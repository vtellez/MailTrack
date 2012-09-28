<div id="content-wrapper">
	<div class="center-wrapper">
				<div id="main">

<div class="buttons" style="margin-top:-9px;">
    <a href="<?php echo site_url('buscador');?>">
        <img src="<?php echo site_url('img/buttons/filter.png'); ?>"/> 
       Aplicar nuevo filtro
    </a>
	<?php if($num_rows > 0){ ?>
    <a href="<?php echo site_url('mensajes/lista/informe');?>">
        <img src="<?php echo site_url('img/menu/chart.png'); ?>"/> 
        Ver informe
    </a>
	<?php } ?>
</div>

<?php 

	$filtro = strtolower($filtro);

	if($filtro == "todos" ){ $titulo = "Todos mis mensajes"; $img = "all.png"; } 
	elseif($filtro == "resultados"){
		$titulo="Resultados de la búsqueda"; $img="mail_search.png";
	}
        elseif($filtro == "recibidos"){
                $titulo="Mensajes recibidos"; $img="mail_receive.png";
        }
        elseif($filtro == "enviados"){
                $titulo="Mensajes enviados"; $img="mail_send.png";
        }

	$titulo =  $titulo." (".number_format($num_rows).")";

 ?>


   <img src="<?php echo site_url('img/seg/'.$img); ?>" style="margin-top:-15px;" width="48" height="48" align="left" /> <h2 class="left"> &nbsp;<?php echo $titulo; ?></h2>
                                               <div class="content-separator"></div>

<?php
        //Cargamos el cuadro resumen de condiciones de filtrado
        $this->load->view('info_condiciones');
?>


<div style="float:left; font-size:14px;">Haga clic en el icono de estado para la vista detallada del mensaje.</div>

<?php echo $pagination->create_links(); ?>

<br/>
<br/>
					<table class="data-table" class="tablesorter" style="white-space: nowrap; table-layout:fixed; width:100%;" >
					    <thead>
						<tr>
<?php $img = "<img src='".site_url('img/'.$sentido.'.png')."' />"; ?>
<?php if($campo == "estado") { $cad = $img; $order = $contrario;}else{ $cad = ""; $order = $sentido; }?>
<th width="35px"><a href="<?php echo $base; ?>/estado/<?php echo $order;?>"><?php echo $cad;?>  Est.</a></th>

<?php if($campo == "fecha") { $cad = $img; $order = $contrario;}else{ $cad = ""; $order = $sentido; }?>
<th width="95px"><a href="<?php echo $base; ?>/fecha/<?php echo $order;?>"><?php echo $cad;?> Fecha envío</a></th>

<?php if($campo == "mfrom") { $cad = $img; $order = $contrario;}else{ $cad = ""; $order = $sentido; }?>
<th width="135px"><a href="<?php echo $base; ?>/mfrom/<?php echo $order;?>"><?php echo $cad;?> Remitente</a></th>

<?php if($campo == "mto") { $cad = $img; $order = $contrario;}else{ $cad = ""; $order = $sentido; }?>
<th width="135px"><a href="<?php echo $base; ?>/mto/<?php echo $order;?>"><?php echo $cad;?> Destinatario</a></th>
							
<?php if($campo == "asunto") { $cad = $img; $order = $contrario;}else{ $cad = ""; $order = $sentido; }?>
<th width="310px;"><a href="<?php echo $base; ?>/asunto/<?php echo $order;?>"><?php echo $cad; ?> Asunto</a></th>
						</tr>
					     </thead>
					     <tbody>
<?php
 if ($mensajes->num_rows() ==  0)
                {
			echo "<tr class='even'><td colspan='5'>No se ha encontrado ningún mensaje en \"$titulo\"</td></tr>";
                }else
                {
                        $cont = 1;
                        foreach ($mensajes->result() as $row)
                        {
                                if($cont % 2 == 0) {$tr="";} else { $tr="class=\"even\""; }
                                $cont++;

			//En función del estado actual del mensajes, mostramos un icono u otro

				if($row->estado < 200){$img="wait";}
				elseif($row->estado < 350){$img="warning";}
				elseif($row->estado < 400){$img="valid";}
				else{$img="error";}

				?>
                                                <tr <?php echo $tr; ?>>
                                                        <td><a href="<?php echo site_url(''); ?>mensajes/ver/<?php echo $row->mid;?>"><center><img src="<?php echo site_url("img/seg/32x32/$img.png"); ?>" border="0" width="24" height="24" title="Ver detalle"/></center></a></td>
                                                        <td><?php echo date("d/m/Y, H:i",$row->fecha);?></td>
                                                        <td><?php echo $this->texto->corta_texto($row->mfrom,22);?></td>
                                                        <td><?php echo $this->texto->corta_texto($row->mto,22);?></td>
                                                        <td><?php echo $this->texto->parsea_texto($this->texto->corta_texto($row->asunto,54));?></td>
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

