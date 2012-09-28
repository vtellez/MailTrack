
<div id="content-wrapper">
	<div class="center-wrapper">
		

				<div id="main">
<div class="buttons" style="margin-top:-9px;">
    <a href="javascript:history.go(-1);">
        <img src="<?php echo site_url('img/buttons/back.png'); ?>"/> 
        Volver a la lista de accesos
    </a>
</div>
   <img src="<?php echo site_url('img/seg/search_globe.png'); ?>" style="margin-top:-15px;" width="48" height="48" align="left" /> <h2 class="left"> &nbsp;Vista detallada de acceso</h2>

                                                <div class="content-separator"></div>
					<br/>

<?php 
	//Comprobamos que no nos llegan errores desde el controlador

	if($error > 0){	?>
	
		<div class="error">Se ha producido un error. El acceso que intenta ver no existe o bien no tiene permiso para verlo.</div>	
		
	<?php 
	}else{
?>
					<table border="0" style="width:100px; margin:auto;">
						<tr>
							<td><img align="right" src="<?php echo site_url("img/seg/".$usuario."user_acc.png"); ?>" /></td>
							<td><img align="right" src="<?php echo site_url("img/seg/pc".$acceso->estado.".png"); ?>" /></td>
							<td><img align="right" src="<?php echo site_url("img/seg/next.png"); ?>" /></td>
							<td><img align="right" src="<?php echo site_url("img/seg/world".$acceso->estado.".png"); ?>" /></td>
							<td><img align="right" src="<?php echo site_url("img/seg/next.png"); ?>" /></td>
							<td><img align="right" src="<?php echo site_url("img/seg/us.png"); ?>" /></td>
						</tr>
						<tr style="font-size:16px; font-weight:bold;">
							<td colspan="2"><center><?php echo $acceso->usuario; ?>@<?php echo $acceso->ip; ?></center></td>
							<td>&nbsp;</td>
							<td colspan="1"><center>Acceso vía <?php echo utf8_decode($acceso->protocolo); ?></center></td>
							<td>&nbsp;</td>
							<td colspan="1"><center>Sistema de correo US</center></td>
						</tr>
					</table>

					<br/>
<?php 
	if($acceso->estado == 0) 
	{
		$div="success";
		$mensaje = "Conexión realizada: Se concedió el acceso tras la validación de los credenciales del usuario.";
	}
	else{ 
		$div="error"; 
		$mensaje = "Conexión fallida: Se denegó el acceso debido a un problema con el nombre de usuario o con la contraseña.";
	}
?>
					<div class="<?php echo $div; ?>"><?php echo $mensaje;?></div>

<br/>

					<h2>Datos del acceso</h2>


					<table class="data-table">

						<tr>
							<th>Parámetro</th>
							<th width="80%">Valor</th>
						</tr>
						<tr class="even">
							<td>Usuario</td>
							<td><?php echo $acceso->usuario; ?></td>
						</tr>
                                                <tr>
                                                        <td>Fecha del último acceso</td>
                                                        <td><?php  echo date("d/m/Y - H:i",$acceso->fecha);?></td>
                                                </tr>
						<tr class="even">
							<td>Tipo de acceso</td>
							<td><?php echo utf8_decode($acceso->protocolo); ?></td>
						</tr>
                                                <tr>
                                                        <td>Número de accesos</td>
                                                        <td><?php echo $acceso->contador; ?></td>
                                                </tr>
                                                <tr class="even">
							<td>Dirección IP</td>
							<td><?php echo $acceso->ip; ?> &nbsp;<a href="http://www.geoiptool.com/es/?IP=<?php echo $acceso->ip; ?>" target="_blank">[Ver más información de esta dirección IP]</a>	</td>
						</tr>
						<tr class="even">
							<td>Información</td>
							<td>
							<p>
							<?php if($acceso->estado == "0"){ //Acceso correcto ?>
							<font color="green">Acceso correcto</font>: Se concede el acceso al sistema de correo de la Universidad de Sevilla desde la dirección IP <?php echo $acceso->ip; ?> para el usuario "<?php echo $acceso->usuario; ?>".
							<?php } else{ //Acceso fallido ?>
							<font color="red">Acceso fallido</font>: Se deniega el acceso al sistema de correo de la Universidad de Sevilla desde la dirección IP <?php echo $acceso->ip; ?> para el usuario "<?php echo $acceso->usuario; ?>" debido a que los credenciales de usuario (nombre de usuario y/o contraseña) eran incorrectos.
							<?php } ?>
							</p>

							<p>
							<?php if($acceso->tipo == "pop"){ ?>
							- Este acceso a su cuenta fue realizado utilizando el protocolo POP. El sistema POP toma los correos del servidor, los descarga en el dispositivo desde el que accede (por ejemplo en su computadora o móvil), y luego trabaja con ellos de manera independiente. 
							<?php } elseif ($acceso->tipo == "imap"){ ?>
						- Este acceso a su cuenta fue realizado utilizando el protocolo IMAP. <br/>A diferencia del protocolo POP, los mensajes no se descargan al dispositivo de consulta sino que se sincronizan con el servidor, manteniendo dichos correos en su buzón corporativo. Además, utilizando IMAP, lo que haga en el dispositivo, se verá reflejado en el servidor, y al consultar su cuenta desde cualquier otro dispositivo, verá también todos los cambios efectuados. 
							<?php }else { //Acceso via web ?>
							Este acceso se realizó via web, a través de la URL <a href="https://buzonweb.us.es/" target="_blank">https://buzonweb.us.es/</a>. Debe tener en cuenta que los accesos web funcionan exactamente igual que los accesos bajo protocolo imap, es decir, los mensajes no se descargan del servidor, sino que simplemente se sincronizan con el cliente de correo.
							<?php } ?>
							</p>
						</tr>
					</table>
					
					
					<br/>


<div class="buttons" style="margin-top:-9px;">
    <a href="javascript:history.go(-1);">
        <img src="<?php echo site_url('img/buttons/back.png'); ?>"/> 
        Volver a la lista de accesos
    </a>
</div>

<?php } //else ?>

<br/><br/>
				</div>
			</div>
	</div>
</div>
