
<div id="content-wrapper">
	<div class="center-wrapper">
		
		<div class="content">

			<div id="main">

   <img src="<?php echo site_url('img/seg/chart.png'); ?>" style="margin-top:-15px;" width="48" height="48" align="left" /> <h2 class="left"> &nbsp;Indicadores de seguimiento de mensajes</h2>
                                                <div class="content-separator"></div>


<p style="font-size:14px;">A continuación se detallan los indicadores de uso de la aplicación de Seguimiento de Mensajes, en la Universidad de Sevilla.</p>



<?php
                $query = $this->db->get('estadisticas');
                $stats = $query->first_row();
?>

<script language="JavaScript" type="text/javascript">
//<![CDATA[

 $(document).ready(function(){
               
                $("#tabs").tabs();
   });

//]]>
</script>

<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Indicadores Globales</a></li>
		<li><a href="#tabs-2">Vista diaria</a></li>
		<li><a href="#tabs-3">Vista mensual</a></li>
		<li><a href="#tabs-4">Vista anual</a></li>
		<li><a href="#tabs-5">Histórico del sistema</a></li>
	</ul>
<br/>
	<div id="tabs-1">
	<fieldset>
        <legend><img src="<?php echo site_url('img/menu/chart.png'); ?>"  border="0" /> Indicadores de uso de Seguimiento</legend>
        <label>Número de visitas:</label><div id="label2"><?php echo number_format($stats->visitas); ?> visitas</div><br/>
        <label>Búsquedas:</label><div id="label2"><?php echo number_format($stats->busquedas); ?> búsquedas realizadas</div><br/>
        <label>Informes generados:</label><div id="label2"><?php echo number_format($stats->informes); ?> informes</div><br/>
        <label>Correos procesados:</label><div id="label2"><?php echo number_format($stats->procesados); ?> correos procesados</div><br/>
        <label>Redirecciones:</label><div id="label2"><?php echo number_format($stats->redirecciones); ?> redirecciones realizadas.</div><br/>
        <label>Accesos registrados:</label><div id="label2"><?php echo number_format($stats->accesos); ?> accesos registrados.</div>
</fieldset>
<br/><br/>

<fieldset style="background-color:#ffffff;">
        <legend><img src="<?php echo site_url('img/menu/chart.png'); ?>"  border="0" /> Indicadores de mensajes</legend>

        <?php
                $etiquetas = array('HAM','SPAM','VIRUS','Incumplimiento de políticas','Listas','Errores de listas');
                $datos = array($stats->ham,$stats->spam,$stats->virus,$stats->politicas,$stats->listas,$stats->listas_error);

                $data = array(
                                'titulo' => "Indicadores totales de seguimiento (mensajes)",
                                'datos' => $datos,
				'color' => '9ACD32',
                                'etiquetas' => $etiquetas,
                                'totales' => $stats->procesados
                );

                $this->load->view('graph', $data);

                ?>
</fieldset>
<br/><br/>
<fieldset style="background-color:#ffffff;">
        <legend><img src="<?php echo site_url('img/menu/chart.png'); ?>"  border="0" /> Indicadores de entregas</legend>

        <?php
                $etiquetas = array('Dominios propios (us.es)','Dominios externos','Redirecciones');
                $datos = array($stats->interno,$stats->externo,$stats->redirecciones);

                $data = array(
                                'titulo' => "Indicadores totales de seguimiento (entregas)",
                                'datos' => $datos,
				'color' => '20B2AA',
                                'etiquetas' => $etiquetas,
                                'totales' => $stats->accesos
                );

                $this->load->view('graph', $data);

                ?>
</fieldset>
<br/><br/>
<fieldset style="background-color:#ffffff;">
        <legend><img src="<?php echo site_url('img/menu/chart.png'); ?>"  border="0" /> Indicadores de accesos</legend>

        <?php
                $etiquetas = array('POP','IMAP','Buzón Web');
                $datos = array($stats->pop,$stats->imap,$stats->buzonweb);

                $data = array(
                                'titulo' => "Indicadores totales de seguimiento (accesos)",
                                'datos' => $datos,
				'color' => 'DB7093',
                                'etiquetas' => $etiquetas,
                                'totales' => $stats->accesos
                );

                $this->load->view('graph', $data);

                ?>
</fieldset>
        </div>









<div id="tabs-2">
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
 <fieldset>
        <legend><img src="<?php echo site_url('img/menu/chart.png'); ?>"  border="0" /> Indicadores de seguimiento diarios</legend>
        <label>Determinar fecha:</label>
	<form action="<?php echo site_url('indicadores#tabs-2');?>" method="POST" />
        <input type="text" name="fecha" id="fecha1" size="10" value="<?php echo "$dia/$mes/$anio"; ?>" readonly="readonly"/>
	<script type="text/javascript">//<![CDATA[
        $(function() {
              $( "#fecha1" ).datepicker({ minDate: "-365D", maxDate: "-1D",changeMonth: true,changeYear: true});
        });
	//]]></script>
        <input type="submit" value="OK" />
	</form>
</fieldset>

<?php
	//Obtenemos las indicadores del día en cuestión
	$array = array('year' => $anio, 'month' => $mes, 'day' => $dia);
	$this->db->where($array); 
	$query = $this->db->get('est_horas');

	if(!$query->num_rows()){
		echo "<div class=\"error\">No hay indicadores del día $dia/$mes/$anio en el sistema.</div><br/>";
	}else{
		echo "<div class=\"success\">Mostrando indicadores  del día $dia/$mes/$anio.</div><br/>";

		$datos = array(	);
		$variables = array('Correos procesados');
		$i = 0;
		foreach ($query->result() as $row)
		{
			$datos[$i][0] = "'$row->hour'";
			$datos[$i][1] = $row->totales;
		$i++;
		}
                $data = array(
                                'titulo' => "Indicadores 'Volumen de correo' ($dia/$mes/$anio)",
				'titulox' => "Horas del día",
				'colores' => "colors:['#20B2AA']",
				'tam' => $query->num_rows(),
                                'datos' => $datos,
				'apilar' => "false",
				'variables' => $variables,
				'nombreid' => "dia1",
                );
                $this->load->view('graph_bar', $data);
		
		echo "<br/><br/>";

		//GRAFICA SPAM HAM
		$datos = array( );
                $variables = array('Correos SPAM','Correos HAM');
                $i = 0;
                foreach ($query->result() as $row)
                {
                        $datos[$i][0] = "'$row->hour'";
                        $datos[$i][1] = $row->spam;
                        $datos[$i][2] = $row->ham;
                $i++;
                }
                $data = array(
                                'titulo' => "Indicadores 'SPAM-HAM' ($dia/$mes/$anio)",
                                'titulox' => "Horas del día",
                                'colores' => "colors:['orange','#9ACD32']",
                                'tam' => $query->num_rows(),
                                'datos' => $datos,
                                'apilar' => "false",
                                'variables' => $variables,
                                'nombreid' => "dia2",
                );
                $this->load->view('graph_bar', $data);

                echo "<br/><br/>";

		//GRÁFICA Entregas
 		$datos = array( );
                $variables = array('Entregas locales','Entregas remotas');
                $i = 0;
                foreach ($query->result() as $row)
                {
                        $datos[$i][0] = "'$row->hour'";
                        $datos[$i][1] = $row->interno;
                        $datos[$i][2] = $row->externo;
                $i++;
                }
                $data = array(
                                'titulo' => "Indicadores 'Correos entregados' ($dia/$mes/$anio)",
                                'titulox' => "Horas del día",
                                'colores' => "colors:['blue','#9ACD32','red']",
                                'tam' => $query->num_rows(),
                                'datos' => $datos,
                                'apilar' => "false",
                                'variables' => $variables,
                                'nombreid' => "dia3",
                );
                $this->load->view('graph_bar', $data);

                echo "<br/><br/>";


                //GRAFICA Detenciones
                $datos = array( );
                $variables = array('Virus detectados','Error en destinatario');
                $i = 0;
                foreach ($query->result() as $row)
                {
                        $datos[$i][0] = "'$row->hour'";
                        $datos[$i][1] = $row->virus;
                        $datos[$i][2] = $row->errormta;
                $i++;
                }
                $data = array(
                                'titulo' => "Indicadores 'Correos detenidos' ($dia/$mes/$anio)",
                                'titulox' => "Horas del día",
                                'colores' => "colors:['red','orange']",
                                'tam' => $query->num_rows(),
                                'datos' => $datos,
                                'apilar' => "false",
                                'variables' => $variables,
                                'nombreid' => "dia4",
                );
                $this->load->view('graph_bar', $data);

                echo "<br/><br/>";

	}
?>
</div>











<div id="tabs-3"> 
<fieldset>
        <legend><img src="<?php echo site_url('img/menu/chart.png'); ?>"  border="0" /> Indicadores de seguimiento mensuales</legend>
        <label>Determinar fecha:</label>
	<form action="<?php echo site_url('indicadores#tabs-3');?>" method="POST" />
        <input type="text" name="fecha" id="fecha2" size="10" value="<?php echo "$dia/$mes/$anio"; ?>" readonly="readonly"/>
	<script type="text/javascript">//<![CDATA[
        $(function() {
              $( "#fecha2" ).datepicker({ minDate: "-3Y", maxDate: "-1D" ,changeMonth: true,changeYear: true});
        });
	//]]></script>
        <input type="submit" value="OK" />
	</form>
</fieldset>

<?php
	//Obtenemos las indicadores del día en cuestión
	$array = array('year' => $anio, 'month' => $mes);
	$this->db->where($array); 
	$query = $this->db->get('est_dias');

	if(!$query->num_rows()){
		echo "<div class=\"error\">No hay indicadores del mes $mes/$anio en el sistema.</div><br/>";
	}else{
		echo "<div class=\"success\">Mostrando indicadores del mes $mes/$anio.</div><br/>";

		$datos = array(	);
		$variables = array('Correos procesados');
		$i = 0;
		foreach ($query->result() as $row)
		{
			$datos[$i][0] = "'$row->day'";
			$datos[$i][1] = $row->totales;
		$i++;
		}
                $data = array(
                                'titulo' => "Indicadores 'Volumen de correo' ($mes/$anio)",
				'titulox' => "Días del mes",
				'colores' => "colors:['#20B2AA']",
				'tam' => $query->num_rows(),
                                'datos' => $datos,
				'apilar' => "false",
				'variables' => $variables,
				'nombreid' => "mes1",
                );
                $this->load->view('graph_bar', $data);
		
		echo "<br/><br/>";

		//GRAFICA SPAM HAM
		$datos = array( );
                $variables = array('Correos SPAM','Correos HAM');
                $i = 0;
                foreach ($query->result() as $row)
                {
                        $datos[$i][0] = "'$row->day'";
                        $datos[$i][1] = $row->spam;
                        $datos[$i][2] = $row->ham;
                $i++;
                }
                $data = array(
                                'titulo' => "Indicadores 'SPAM-HAM' ($mes/$anio)",
				'titulox' => "Días del mes",
                                'colores' => "colors:['orange','#9ACD32']",
                                'tam' => $query->num_rows(),
                                'datos' => $datos,
                                'apilar' => "false",
                                'variables' => $variables,
                                'nombreid' => "mes2",
                );
                $this->load->view('graph_bar', $data);

                echo "<br/><br/>";

		//GRÁFICA Entregas
 		$datos = array( );
                $variables = array('Entregas locales','Entregas remotas');
                $i = 0;
                foreach ($query->result() as $row)
                {
                        $datos[$i][0] = "'$row->day'";
                        $datos[$i][1] = $row->interno;
                        $datos[$i][2] = $row->externo;
                $i++;
                }
                $data = array(
                                'titulo' => "Indicadores 'Correos entregados' ($mes/$anio)",
				'titulox' => "Días del mes",
                                'colores' => "colors:['blue','#9ACD32','red']",
                                'tam' => $query->num_rows(),
                                'datos' => $datos,
                                'apilar' => "false",
                                'variables' => $variables,
                                'nombreid' => "mes3",
                );
                $this->load->view('graph_bar', $data);

                echo "<br/><br/>";

		//GRAFICA Detenciones
                $datos = array( );
                $variables = array('Virus detectados','Error en destinatario');
                $i = 0;
                foreach ($query->result() as $row)
                {
                        $datos[$i][0] = "'$row->day'";
                        $datos[$i][1] = $row->virus;
                        $datos[$i][2] = $row->errormta;
                $i++;
                }
                $data = array(
                                'titulo' => "Indicadores 'Correos detenidos' ($mes/$anio)",
                                'titulox' => "Días del mes",
                                'colores' => "colors:['red','orange']",
                                'tam' => $query->num_rows(),
                                'datos' => $datos,
                                'apilar' => "false",
                                'variables' => $variables,
                                'nombreid' => "mes4",
                );
                $this->load->view('graph_bar', $data);

                echo "<br/><br/>";

	}
?>
</div>




<div id="tabs-4">
<fieldset>
        <legend><img src="<?php echo site_url('img/menu/chart.png'); ?>"  border="0" /> Indicadores de seguimiento anuales</legend>
        <label>Determinar fecha:</label>
	<form action="<?php echo site_url('indicadores#tabs-4');?>" method="POST" />
        <input type="text" name="fecha" id="fecha3" size="10" value="<?php echo "$dia/$mes/$anio"; ?>" readonly="readonly"/>
	<script type="text/javascript">//<![CDATA[
        $(function() {
              $( "#fecha3" ).datepicker({ minDate: "-10Y", maxDate: "-1D" ,changeMonth: true,changeYear: true});
		$( "#fecha3" ).change(function() {
			$( "#fecha3" ).datepicker( "option", "slide", $( this ).val() );
		});
        });
	//]]></script>
        <input type="submit" value="OK" />
	</form>
</fieldset>

<?php
	//Obtenemos las indicadores del año en cuestión
	$array = array('year' => $anio);
	$this->db->where($array); 
	$query = $this->db->get('est_meses');

        $meses = array (
                        1 => "Ene",
                        2 => "Feb",
                        3 => "Mar",
                        4 => "Abr",
                        5 => "May",
                        6 => "Jun",
                        7 => "Jul",
                        8 => "Ago",
                        9 => "Sept",
                        10 => "Oct",
                        11 => "Nov",
                        12 => "Dic"
                );


	if(!$query->num_rows()){
		echo "<div class=\"error\">No hay indicadores del año $anio en el sistema.</div><br/>";
	}else{
		echo "<div class=\"success\">Mostrando indicadores del año $anio.</div><br/>";

		$datos = array(	);
		$variables = array('Correos procesados');
		$i = 0;
		foreach ($query->result() as $row)
		{
			$datos[$i][0] = "'".$meses[$row->month]."'";
			$datos[$i][1] = $row->totales;
		$i++;
		}
                $data = array(
                                'titulo' => "Indicadores 'Volumen de correo' ($anio)",
				'titulox' => "Meses del año",
				'colores' => "colors:['#20B2AA']",
				'tam' => $query->num_rows(),
                                'datos' => $datos,
				'apilar' => "false",
				'variables' => $variables,
				'nombreid' => "anio1",
                );
                $this->load->view('graph_bar', $data);
		
		echo "<br/><br/>";

		//GRAFICA SPAM HAM
		$datos = array( );
                $variables = array('Correos SPAM','Correos HAM');
                $i = 0;
                foreach ($query->result() as $row)
                {
			$datos[$i][0] = "'".$meses[$row->month]."'";
                        $datos[$i][1] = $row->spam;
                        $datos[$i][2] = $row->ham;
                $i++;
                }
                $data = array(
                                'titulo' => "Indicadores 'SPAM-HAM' ($anio)",
				'titulox' => "Meses del año",
                                'colores' => "colors:['orange','#9ACD32']",
                                'tam' => $query->num_rows(),
                                'datos' => $datos,
                                'apilar' => "false",
                                'variables' => $variables,
                                'nombreid' => "anio2",
                );
                $this->load->view('graph_bar', $data);

                echo "<br/><br/>";

		//GRÁFICA Entregas
 		$datos = array( );
                $variables = array('Entregas locales','Entregas remotas');
                $i = 0;
                foreach ($query->result() as $row)
                {
			$datos[$i][0] = "'".$meses[$row->month]."'";
                        $datos[$i][1] = $row->interno;
                        $datos[$i][2] = $row->externo;
                $i++;
                }
                $data = array(
                                'titulo' => "Indicadores 'Correos entregados' ($anio)",
				'titulox' => "Meses del año",
                                'colores' => "colors:['blue','#9ACD32','red']",
                                'tam' => $query->num_rows(),
                                'datos' => $datos,
                                'apilar' => "false",
                                'variables' => $variables,
                                'nombreid' => "anio3",
                );
                $this->load->view('graph_bar', $data);

                echo "<br/><br/>";

		//GRAFICA Detenciones
                $datos = array( );
                $variables = array('Virus detectados','Error en destinatario');
                $i = 0;
                foreach ($query->result() as $row)
                {
			$datos[$i][0] = "'".$meses[$row->month]."'";
                        $datos[$i][1] = $row->virus;
                        $datos[$i][2] = $row->errormta;
                $i++;
                }
                $data = array(
                                'titulo' => "Indicadores 'Correos detenidos' ($anio)",
                                'titulox' => "Meses del año",
                                'colores' => "colors:['red','orange']",
                                'tam' => $query->num_rows(),
                                'datos' => $datos,
                                'apilar' => "false",
                                'variables' => $variables,
                                'nombreid' => "anio4",
                );
                $this->load->view('graph_bar', $data);

                echo "<br/><br/>";


	}
?>
</div>








<div id="tabs-5">

<?php
	//Obtenemos las indicadores del año en cuestión
	$query = $this->db->get('est_anios');

	if(!$query->num_rows()){
		echo "<div class=\"error\">No hay indicadores en el histórico del sistema.</div><br/>";
	}else{
		echo "<div class=\"success\">Mostrando indicadores del histórico del sistema.</div><br/>";

		$datos = array(	);
		$variables = array('Correos procesados');
		$i = 0;
		foreach ($query->result() as $row)
		{
			$datos[$i][0] = "'$row->year'";
			$datos[$i][1] = $row->totales;
		$i++;
		}
                $data = array(
                                'titulo' => "Indicadores 'Volumen de correo' (Histórico)",
				'titulox' => "Años en producción",
				'colores' => "colors:['#20B2AA']",
				'tam' => $query->num_rows(),
                                'datos' => $datos,
				'apilar' => "false",
				'variables' => $variables,
				'nombreid' => "hist1",
                );
                $this->load->view('graph_bar', $data);
		
		echo "<br/><br/>";

		//GRAFICA SPAM HAM
		$datos = array( );
                $variables = array('Correos SPAM','Correos HAM');
                $i = 0;
                foreach ($query->result() as $row)
                {
                        $datos[$i][0] = "'$row->year'";
                        $datos[$i][1] = $row->spam;
                        $datos[$i][2] = $row->ham;
                $i++;
                }
                $data = array(
                                'titulo' => "Indicadores 'SPAM-HAM' ($anio)",
				'titulox' => "Años en producción",
                                'colores' => "colors:['orange','#9ACD32']",
                                'tam' => $query->num_rows(),
                                'datos' => $datos,
                                'apilar' => "false",
                                'variables' => $variables,
                                'nombreid' => "hist2",
                );
                $this->load->view('graph_bar', $data);

                echo "<br/><br/>";

		//GRÁFICA Entregas
 		$datos = array( );
                $variables = array('Entregas locales','Entregas remotas');
                $i = 0;
                foreach ($query->result() as $row)
                {
                        $datos[$i][0] = "'$row->year'";
                        $datos[$i][1] = $row->interno;
                        $datos[$i][2] = $row->externo;
                $i++;
                }
                $data = array(
                                'titulo' => "Indicadores 'Correos entregados' ($anio)",
				'titulox' => "Años en producción",
                                'colores' => "colors:['blue','#9ACD32','red']",
                                'tam' => $query->num_rows(),
                                'datos' => $datos,
                                'apilar' => "false",
                                'variables' => $variables,
                                'nombreid' => "hist3",
                );
                $this->load->view('graph_bar', $data);

                echo "<br/><br/>";

		//GRAFICA Detenciones
                $datos = array( );
                $variables = array('Virus detectados','Error en destinatario');
                $i = 0;
                foreach ($query->result() as $row)
                {
                        $datos[$i][0] = "'$row->year'";
                        $datos[$i][1] = $row->virus;
                        $datos[$i][2] = $row->errormta;
                $i++;
                }
                $data = array(
                                'titulo' => "Indicadores 'Correos detenidos' ($anio)",
				'titulox' => "Años en producción",
                                'colores' => "colors:['red','orange']",
                                'tam' => $query->num_rows(),
                                'datos' => $datos,
                                'apilar' => "false",
                                'variables' => $variables,
                                'nombreid' => "hist4",
                );
                $this->load->view('graph_bar', $data);

                echo "<br/><br/>";


	}
?>
</div>

</div>
</div>
</div>


			<div class="clearer">&nbsp;</div>
		</div>
	</div>
</div>
