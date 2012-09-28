
<div id="content-wrapper">
<div class="center-wrapper">


<div id="main">
<div class="buttons" style="margin-top:-9px;">
<a href="javascript:history.go(-1);">
<img src="<?php echo site_url('img/buttons/back.png'); ?>"/> 
Volver a la lista de mensajes
</a>
</div>
<img src="<?php echo site_url('img/seg/mail_search.png'); ?>" style="margin-top:-15px;" width="48" height="48" align="left" /> <h2 class="left"> &nbsp;Vista detallada de mensaje</h2>

<div class="content-separator"></div>
<br/>

<?php 
//Comprobamos que no nos llegan errores desde el controlador

if($error > 0){	?>

	<div class="error">Se ha producido un error. El mensaje que intenta ver no existe o bien no tiene permiso para verlo.</div>	

		<?php 
}else{
	?>
		<table border="0" style="width:100px; margin:auto;">
		<tr>
		<td><img align="right" src="<?php echo site_url("img/seg/".$emisor."user$est->efrom.png"); ?>" /></td>
		<td><img src="<?php echo site_url("img/seg/next.png"); ?>"/></td>
		<td><img src="<?php echo site_url("img/seg/mail$est->email.png");?>" /></td>
		<td><img src="<?php echo site_url("img/seg/next.png");?>"/></td>
		<td><img align="left" src="<?php echo site_url("img/seg/".$destinatario."user$est->eto.png");?>"/></td>
		</tr>
		<tr style="font-size:16px; font-weight:bold;">
		<td colspan="2"><center>
		<?php echo wordwrap($mensaje->mfrom, 33, "<br />",true); ?>
		</center></td>
		<td><center>Mensaje</center></td>
		<td colspan="2"><center>
		<?php $var = ($mensaje->redirect != '') ? "$mensaje->mto >>>> $mensaje->redirect" : "$mensaje->mto";
	echo wordwrap($var, 53, "<br />",true);
	?></center></td>
		</tr>
		</table>

		<br/>
		<?php 
		if($est->codigo < 350) $div="notice";
	elseif($est->codigo < 400) $div="success";
		else $div="error";  
	?>
		<div class="<?php echo $div; ?>"><a href="<?php echo site_url('ayuda#5'); ?>"><i>#Cod <?php echo $est->codigo; ?></i>:</a> <?php echo utf8_decode($est->descripcion); ?></div>

		<br/>

		<h2>Datos del mensaje</h2>


		<table class="data-table">

		<tr>
		<th>Parámetro</th>
		<th width="80%">Valor</th>
		</tr>
		<tr class="even">
		<td>Remitente</td>
		<td><?php echo $mensaje->mfrom; ?></td>
		</tr>
		<tr>	
		<td>Destinatario</td>
		<td><?php echo $mensaje->mto; ?></td>
		</tr>
		<tr class="even">
		<td>Asunto</td>
		<td><?php echo $this->texto->parsea_texto($mensaje->asunto); ?></td>
		</tr>
		<tr>
		<td>Fecha envío</td>
		<td><?php  echo date("d/m/Y - H:i",$mensaje->fecha);?></td>
		</tr>
		<tr class="even">
		<td>Dirección IP de envío</td>	
		<td><?php echo ($mensaje->ip != '') ? $mensaje->ip : 'Desconocida';  ?></td>
		</tr>
		<tr class="even">
		<td>Message-id</td>
		<td><?php echo $mensaje->message_id; ?></td>
		</tr>
		</table>


		<br/>

		<h2>Historial del mensaje</h2>

		<table class="data-table">
		<tr>
		<th>Fecha - hora</th>
		<th>Máquina</th>
		<th>Descripción</th>
		</tr>

		<?php  $cont = 1;
//Guardamos la fecha max y la min para calcular el tiempo de entrega del mensaje

	$fecha_max = 0;
	$fecha_min = 999999999999999999999999999;

	foreach ($historial->result() as $row)
	{

		//obtenemos la información de dicho estado para la descripción:
		$this->db->where('codigo', $row->estado);
		$info_estados = $this->db->get('estados');
		$info_estado = $info_estados->row();

		$fecha_max = max($fecha_max, $row->fecha);
		$fecha_min = min($fecha_min, $row->fecha);

		if($cont % 2 == 0) {$tr="";} else { $tr="class=\"even\""; }
		$cont++;
		?>
			<tr <?php echo $tr; ?>>
			<td nowrap="nowrap"><?php  echo date("d/m/Y - H:i",$row->fecha);?></td>
			<td><?php echo $row->maquina; ?></td>
			<td><a href="<?php echo site_url('ayuda'); ?>"<i>#Cod <?php echo $row->estado; ?>:</i></a> <?php echo utf8_decode($info_estado->descripcion); ?> 
			<?php if ($row->descripcion != ""){ ?>
				<br/><br/><?php 
					$descripcion = utf8_decode($row->descripcion); 
				$info = utf8_decode($row->adicional); 
				echo $descripcion;

				//Vemos si el usuario tiene permiso para ver la carpeta destino dentro del buzón
				if (preg_match("/\"SPAM\"/i",$info) || preg_match("/\"INBOX\"/i",$info)) {
					echo "<br/> $info";
				} else {
					if($this->session->userdata('mail') == $mensaje->mto || $this->controlacceso->permisoAdministracion())
					{
						echo "<br/> $info";
					}elseif($info != "")
					{
						echo "<br/>- Dovecot (sieve-manager) informó: El correo se ha entregado correctamente en el buzón del usuario, un filtro ha movido el mensaje a una carpeta privada.";
					}
				}

				//Si hay redirección, mostramos un enlace para su seguimiento
				if($row->estado == 362 && $this->controlacceso->permisoAdministracion())
				{
					$this->db->where('mto', $mensaje->redirect);
					$this->db->where('message_id', $mensaje->message_id);
					$mensajes = $this->db->get('mensajes');	

					if($mensajes->num_rows())
					{
						$red = $mensajes->row();
						echo "<br/><br/> Administración: <a href=\"".site_url("mensajes/ver/$red->mid")."\">Realizar seguimiento de la redirección</a>";	
					}
				}

			} ?>
		</td>
			</tr>
			<?php } ?>
			</table>


			<br/>

			<h2>Estadísticas del seguimiento</h2>

			<table class="data-table">
			<tr>
			<th width="120px">Parámetro</th>
			<th width="100px">Valor</th>
			<th>Descripción</th>
			</tr>
			<tr class="even">
			<td>Tiempo de entrega</td>		
			<td><?php echo ($mensaje->estado > 300) ? $fecha_max - $fecha_min." segundo(s)" : 
			"<img src=\"".site_url('/img/seg/32x32/wait.png')."\" style=\"width:24px; height:24px;\"/> En proceso";
			?></td>		
			<td>Define el valor en segundos del tiempo que el mensaje ha empleado en recorrer
			todas las estafetas de correo de la Universidad de Sevilla hasta llegar al buzón corporativo o al MTA remoto del destinatario.</td>		
			</tr>
			</table>
			
			<br/>

			<div class="buttons" style="margin-top:-9px;">
			<a href="javascript:history.go(-1);">
			<img src="<?php echo site_url('img/buttons/back.png'); ?>"/> 
			Volver a la lista de mensajes
			</a>
			</div>

			<?php } //else ?>

			<br/><br/>
			</div>
			</div>
			</div>
			</div>
