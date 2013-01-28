
<div id="content-wrapper">
	<div class="center-wrapper">
		
		<div class="content" id="content-two-columns">

			<div id="main-wrapper">
				<div id="main">



   <img src="<?php echo site_url('img/seg/search.png'); ?>" style="margin-top:-15px;" width="48" height="48" align="left" /> <h2 class="left"> &nbsp;Buscador de mensajes</h2>
                                                <div class="content-separator"></div>
<fieldset>
<legend>Formulario de busqueda</legend>
<form method="POST" action="<?php echo site_url('mensajes/lista/resultados'); ?>">
<input type="hidden" name="oculto" value="1" />
	<label>Emisor</label>
	<?php 
	$data = array(
              'name'        => 'mfrom',
              'id'          => 'mfrom',
              'value'       => '',
              'maxlength'   => '150',
              'size'        => '50',
              'style'       => 'width:50%',
            );

	echo form_input($data);
	?>
	<br/><br/>
	<label>Destinatario</label>
	        <?php
        $data = array(
              'name'        => 'mto',
              'id'          => 'mto',
              'value'       => '',
              'maxlength'   => '150',
              'size'        => '50',
              'style'       => 'width:50%',
            );

        echo form_input($data);
        ?>
	<br/><br/>
        <label>Message-id</label>
                <?php
        $data = array(
              'name'        => 'message_id',
              'id'          => 'message_id',
              'value'       => '',
              'maxlength'   => '200',
              'size'        => '50',
              'style'       => 'width:390px;',
            );

        echo form_input($data);
        ?>

<?php if ($this->controlacceso->permisoAdministracion()){ ?>
	<br/><br/>
        <label>Nombre de virus</label>
        <?php
        $data = array(
              'name'        => 'historial',
              'id'          => 'historial',
              'value'       => '',
              'maxlength'   => '250',
              'size'        => '50',
              'style'       => 'width:50%',
            );

        echo form_input($data);
        ?>

<?php } ?>
	<br/><br/>
        <label>Asunto</label>
        <?php $options = array(
                  'es'  => 'Es',
                  'contiene_alguna'    => 'Contiene alguna',
                  'contiene_todas'   => 'Contiene todas',
                );

        $adicional = 'style="width: 150px;"';

        echo form_dropdown('condicion_asunto', $options, 'contiene_alguna',$adicional);
        ?>
                <?php
        $data = array(
              'name'        => 'asunto',
              'id'          => 'asunto',
              'value'       => '',
              'maxlength'   => '250',
              'size'        => '50',
              'style'       => 'width:40%',
            );

        echo form_input($data);
        ?>
        <br/><br/>
        <label>Estado</label>
<select name="estado" style="width: 400px; word-wrap:break-word;">
<option value="0" selected="selected">Cualquier estado</option>
<option value="0"></option>
<option value="< 200">>> CORREO ENCOLADO</option>
<?php if ($this->controlacceso->permisoAdministracion()){ ?>
<option value="= 1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Correo encolado en la estafeta de entrada.</option>
<option value="= 31">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Correo válido encolado en estafetas antivirus.</option>
<option value="= 131">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Correo SPAM encolado en estafetas antivirus.</option>
<option value="= 51">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Correo encolado en la estafeta de listas de distribución.</option>
<option value="= 151">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Correo encolado en la estafeta de salida.</option>
<option value="= 161">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Correo SPAM encolado en estafetas salida.</option>
<option value="= 171">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Correo encolado en la estafeta de buzones.</option>
<option value="= 181">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Correo SPAM encolado en la estafeta de buzones.</option>
<?php } ?>
<option value="0"></option>

<option value="> 400">>> CORREO DETENIDO</option>
<?php if ($this->controlacceso->permisoAdministracion()){ ?>
<option value="= 431">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Correo marcado como virus o malware.</option>
<option value="= 432">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Correo marcado como violación de política.</option>
<option value="= 433">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-  Correo marcado como violación de política de mailman.</option>
<option value="= 452">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-  Correo con error en el destinatario.</option>
<option value="= 472">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-  Correo con error al entregar en buzones.</option>
<?php } ?>

<option value="0"></option>

<option value="> 300 AND estado < 400">>> CORREO ENTREGADO</option>
<?php if ($this->controlacceso->permisoAdministracion()){ ?>
<option value="= 331">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-  Correo SPAM entregado con éxito en el buzón del destinatario.</option>
<option value="= 332">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-  Correo SPAM entregado con éxito al MTA remoto del destinatario.</option>
<option value="= 351">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-  Correo entregado con éxito en el buzón del destinatario.</option>
<option value="= 352">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-  Correo entregado con éxito al MTA remoto del destinatario.</option>
<option value="= 362">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-  Correo entregado con redirección a cuenta secundaria.</option>
<option value="= 372">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-  Correo entregado a cuenta de dominio virtual o alias.</option>
<?php } ?>
</select>

	<br/><br/>
 	<label>Fecha inicial</label>
	<input type="text" name="fecha1" id="fecha1" size="10" value="Cualquier fecha" readonly="readonly"/>
	<img src="<?php echo site_url('img/buttons/calendar.png'); ?>" class="left" /><br />
	<br/>	
	<label>Fecha final</label>
	<input type="text" name="fecha2" id="fecha2" size="10" value="Cualquier fecha" readonly="readonly"/>
	<img src="<?php echo site_url('img/buttons/calendar.png'); ?>" class="left"/><br />

    <script type="text/javascript">//<![CDATA[

	$(function() {
		$( "#fecha1" ).datepicker( { minDate: "-20D", maxDate: "+0D"});
	});

    //]]></script>

    <script type="text/javascript">//<![CDATA[

        $(function() {
                $( "#fecha2" ).datepicker( { minDate: "-20D", maxDate: "+0D"});
        });

    //]]></script>
	<br/>
</fieldset>
<div class="buttons">
    <button type="submit">
        <img src="<?php echo site_url('img/buttons/search.png'); ?>" alt=""/>
        Realizar búsqueda
    </button>
</div>	

</form>
	<br/><br/>
				</div>
			</div>


<?php $this->load->view('lateral_buscadores.php'); ?>
