<fieldset>
	<legend><img src="<?php echo site_url('img/menu/info.png'); ?>"  border="0" /> Información del sistema</legend>
	<label>Nombre del sistema</label> seguimiento.us.es<br/><br/>
	<label>Uptime</label> <?php system('uptime'); ?><br/><br/>
	<label>Who</label> <?php system('who'); ?><br/><br/>
	<label>Uso de disco</label> <?php system("df -h | tail -n 3 | head -n 1"); ?><br/><br/>
	<label>Uso de memoria</label> <?php system("free -m"); ?><br/><br/>
</fieldset>
