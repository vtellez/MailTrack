
<div id="content-wrapper">
	<div class="center-wrapper">
		
		<div class="content" id="content-two-columns">

			<div id="main-wrapper">
				<div id="main">



   <img src="<?php echo site_url('img/seg/search_globe.png'); ?>" style="margin-top:-15px;" width="48" height="48" align="left" /> <h2 class="left"> &nbsp;Buscador de accesos</h2>
                                                <div class="content-separator"></div>
<fieldset>
<legend>Formulario de busqueda</legend>
<form method="POST" action="<?php echo site_url('accesos/lista/resultados'); ?>">
<input type="hidden" name="oculto" value="1" />
	<label>UVUS del usuario</label>
	<?php 
	$data = array(
              'name'        => 'usuario',
              'id'          => 'usuario',
              'value'       => '',
              'maxlength'   => '150',
              'size'        => '50',
              'style'       => 'width:50%',
            );

	echo form_input($data);
	?>
	<br/><br/>
	<label>Dirección IP</label>
	        <?php
        $data = array(
              'name'        => 'ip',
              'id'          => 'ip',
              'value'       => '',
              'maxlength'   => '150',
              'size'        => '50',
              'style'       => 'width:50%',
            );

        echo form_input($data);
        ?>
        <br/><br/>
 	<label>Tipo de acceso</label>
	<?php $options = array(
		  'cualquiera' => 'Cualquier tipo de acceso',
                  'buzonweb'  => 'Acceso desde buzonweb',
                  'pop'    => 'Acceso POP',
                  'imap'   => 'Acceso IMAP',
                );

	$adicional = 'style="width: 300px;"';

	echo form_dropdown('tipo', $options, 'cualquiera',$adicional);
	?>
	<br/><br/>
        <label>Estado de conexión</label>
        <?php $options = array(
                  'cualquiera' => 'Cualquier estado',
                  '0'    => 'Conexiones correctas',
                  '1'   => 'Conexiones fallidas',
                );

        $adicional = 'style="width: 300px;"';

        echo form_dropdown('estado', $options, 'cualquiera',$adicional);
        ?>
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
                $( "#fecha1" ).datepicker( { minDate: "-7D", maxDate: "+0D"});
        });

    //]]></script>

    <script type="text/javascript">//<![CDATA[

        $(function() {
                $( "#fecha2" ).datepicker( { minDate: "-7D", maxDate: "+0D"});
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
