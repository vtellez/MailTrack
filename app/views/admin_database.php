<fieldset>
        <legend><img src="<?php echo site_url('img/menu/database.png'); ?>"  border="0" /> Base de datos</legend>
	<label>Actualizar BD</label> <a href="#">ejectuar script</a> (auto: 10 minutos)<br/><br/>

	<label>Cargar fecha</label>
	<input type="text" name="fecha1" id="fecha1" size="5" value="Cualquier fecha" readonly="readonly"/>
	<a href="#" id="f_btn1"><img src="http://seguimiento.us.es/img/buttons/calendar.png" title="elegir" class="left" /></a><input type="button" value="OK"/><br />
	<br/>

    <script type="text/javascript">//<![CDATA[
      var cal = Calendar.setup({
          onSelect: function(cal) { cal.hide() },
	  animation: false,
          bottomBar: false,
          showTime: false
      });
      cal.manageFields("f_btn1", "fecha1", "%Y-%m-%d");
    //]]></script>


	<label>Borrar mensajes viejos</label> <a href="#">ejectuar script</a> (auto: 14 dias)<br/><br/>
	<label>Optimizar tablas de BD</label> <a href="#">ejectuar script</a> (auto: 24 horas)<br/><br/>
	<label>Compactar BD</label> <a href="#">ejectuar script</a> (auto: 24 horas)<br/><br/>
</fieldset>

<?php //system('bash /var/www/html/Seguimiento/scripts/bin/actualiza.sh'); ?>

