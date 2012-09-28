<fieldset>
        <legend><img src="<?php echo site_url('img/menu/block.png'); ?>"  border="0" /> IP's bloquedas</legend>
	<p>Por favor, seleccione el fichero de log que desee consultar. Tenga en cuenta que el contenido de este log se actualiza con cada nueva indexación de mensajes:</p>
	Log a mostrar:
	<select name="estado" style="width: 50%;">
<option value="0" selected="selected">Seleccione...</option>
<option value="31">Qmail 1</option>
<option value="31">Qmail 2</option>
</select> <input type="submit" value="OK" />
	<br/>
	<br/>

	<textarea style="        border:1px solid orange;
        -webkit-box-shadow: rgba(0, 0, 0, 0.1) 0 4px 5px;
        -moz-box-shadow: rgba(0, 0, 0, 0.1) 0 4px 5px; 
	width: 100%; 
	height:240px;
	scroll:auto;"><?php include('/var/www/html/Seguimiento/scripts/log/entrada_bloqueo_ip2'); ?></textarea>
</fieldset>
