<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">

<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
	<meta name="description" content="Seguimiento de correo electrónico. Universidad de Sevilla."/>
	<meta name="keywords" content="Seguimiento, Universidad, Sevilla" />
	<meta name="author" content="Víctor Téllez - vtellez@us.es Servicio de Informática y Comunicaciones" />
        <link href='https://fonts.googleapis.com/css?family=Josefin+Sans+Std+Light' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="<?php echo site_url('css/estilo.css'); ?>" media="screen" />

	<link rel="shortcut icon" type="image/x-icon" href="<?php echo site_url('img/favicon.ico'); ?>" />

	<?php
	if (isset($css_adicionales)) {
        	foreach ($css_adicionales as $css) { ?>
                        <link rel="stylesheet" href="<?php echo site_url($css) ?>"
                        type="text/css" media="screen" />
                        <?php
	        }
	}
	?>

	<title>Seguimiento de mensajes | <?php echo $subtitulo; ?></title>

<script language="JavaScript" type="text/javascript">
//<![CDATA[

// Variables útiles para procesado Javascript
var url_base = '<?php echo base_url()?>';

//]]>
</script>

<?php
if (isset($js_adicionales)) {
        foreach ($js_adicionales as $js) {
                ?>
                        <script type="text/javascript"
                        src="<?php echo site_url($js) ?>"></script>
                        <?php
        }
}
?>


</head>

<body id="top">

<div id="navigation-wrapper">
	<div id="navigation-wrapper-2">
		<div class="center-wrapper">
	
			<div id="navigation">

<ul class="tabbed">
<?php if($controlador == "inicio"){ echo "<li class='current_page_item current_page".$parent."'>";} else{ echo "<li>";  } ?>
<a href="<?php echo site_url('inicio'); ?>"><img src="<?php echo site_url('img/menu/home.png'); ?>"/>Inicio</a></li>

<?php if($controlador == "mensajes"){ echo "<li class='current_page_item current_page".$parent."'>";} else{ echo "<li>";  } ?>
<a href="<?php echo site_url('mensajes/lista/todos'); ?>"><img src="<?php echo site_url('img/menu/Mail.png'); ?>" style="margin-top:-5px; margin-right:4px; "/>Mensajes</a></li>

<?php if($controlador == "accesos"){ echo "<li class='current_page_item current_page".$parent."'>";} else{ echo "<li>";  } ?><a href="<?php echo site_url('accesos/lista/'); ?>"><img src="<?php echo site_url('img/menu/accesos.png'); ?>"/>Accesos</a></li>

<?php if($controlador == "buscador"){ echo "<li class='current_page_item current_page".$parent."'>";} else{ echo "<li>";  } ?><a href="<?php echo site_url('buscador'); ?>"><img src="<?php echo site_url('img/menu/search.png'); ?>"/>Buscador</a></li>

<?php if($controlador == "indicadores"){ echo "<li class='current_page_item current_page".$parent."'>";} else{ echo "<li>";  } ?><a href="<?php echo site_url('indicadores'); ?>"><img src="<?php echo site_url('img/menu/pie_chart.png'); ?>"  style="margin-top:0px;"/>Indicadores</a></li>

<?php if($controlador == "ayuda"){ echo "<li class='current_page_item current_page".$parent."'>";} else{ echo "<li>";  } ?><a href="<?php echo site_url('ayuda'); ?>"><img src="<?php echo site_url('img/menu/help.png'); ?>"  style="margin-top:0px;"/>Ayuda</a></li>

<?php  if ($this->controlacceso->permisoAdministracion()){
	if($controlador == "admin"){ echo "<li class='current_page_item current_page".$parent."'>";} else{ echo "<li>";  } ?><a href="<?php echo site_url('admin/task'); ?>"><img src="<?php echo site_url('img/menu/admin.png'); ?>"/>Admin</a></li>

<?php } ?>

<?php if($controlador == "logout"){ echo "<li class='current_page_item current_page".$parent."'>";} else{ echo "<li>";  } ?><a href="<?php echo site_url('logout'); ?>"><img src="<?php echo site_url('img/menu/logout.png'); ?>"/>Salir</a></li>
				</ul>

				<div class="clearer">&nbsp;</div>

			</div>

		</div>
	</div>
</div>


<?php if($parent != ""){ ?>
<div id="subnav-wrapper">
	<div id="subnav-wrapper-2">

		<div class="center-wrapper">
		
			<div id="subnav">

				<ul class="tabbed">
				<?php if($controlador == "mensajes"){ ?>
					<li><a href="<?php echo site_url('mensajes/lista/todos'); ?>"><img src="<?php echo site_url('img/menu/all.png'); ?>"/>Todos mis mensajes</a></li>
					<li><a href="<?php echo site_url('mensajes/lista/recibidos'); ?>"><img src="<?php echo site_url('img/menu/mail_receive.png'); ?>" />Mensajes recibidos</a></li>
					<li><a href="<?php echo site_url('mensajes/lista/enviados'); ?>"><img src="<?php echo site_url('img/menu/mail_send.png'); ?>"/>Mensajes enviados</a></li>
				<?php } ?>

				<?php if($controlador == "accesos"){ ?>
                                        <li><a href="<?php echo site_url('accesos/lista/todos'); ?>"><img src="<?php echo site_url('img/menu/globe_up.png'); ?>"/>Todos mis accesos</a></li>
                                        <li><a href="<?php echo site_url('accesos/lista/buzonweb'); ?>"><img src="<?php echo site_url('img/menu/globe_up.png'); ?>" />Accesos Buzón Web</a></li>
                                        <li><a href="<?php echo site_url('accesos/lista/imap'); ?>"><img src="<?php echo site_url('img/menu/globe_up.png'); ?>"/>Accesos IMAP</a></li>
                                        <li><a href="<?php echo site_url('accesos/lista/pop'); ?>"><img src="<?php echo site_url('img/menu/globe_download.png'); ?>" />Accesos POP</a></li>
                                <?php } ?>

				 <?php if($controlador == "buscador" && $this->controlacceso->permisoAdministracion() ){ ?>
                                        <li><a href="<?php echo site_url('buscador/'); ?>"><img src="<?php echo site_url('img/menu/search.png'); ?>"/>Buscador de mensajes</a></li>
                                        <li><a href="<?php echo site_url('buscador/accesos'); ?>"><img src="<?php echo site_url('img/menu/search_globe.png'); ?>" />Buscador de accesos</a></li>
                                <?php } ?>

				</ul>

				<div class="clearer">&nbsp;</div>

			</div>

		</div>

	</div>
</div>
<?php } ?>
