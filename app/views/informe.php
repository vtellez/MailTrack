<div id="content-wrapper">
        <div class="center-wrapper">
                                <div id="main">

<div class="buttons" style="margin-top:-9px;">
    <a href="<?php echo site_url('');?>buscador">
        <img src="<?php echo site_url('img/buttons/filter.png'); ?>"/> 
       Aplicar nuevo filtro
    </a>
    <a href="<?php echo site_url('mensajes/lista/resultados');?>">
        <img src="<?php echo site_url('img/buttons/search.png'); ?>"/> 
        Ver resultados
    </a>
</div>

   <img src="<?php echo site_url('img/seg/chart.png'); ?>" style="margin-top:-15px;" width="48" height="48" align="left" /> <h2 class="left"> &nbsp;Informe: Resultados de la búsqueda</h2>
                                               <div class="content-separator"></div>



<?php 
	//Cargamos el cuadro resumen de condiciones de filtrado
	$this->load->view('info_condiciones');
?>

<fieldset class="informe" >
	<legend> Descripción general del informe</legend>
<p>A continuación, se detalla el resumen del envío.</p>

<?php  
	$where = $this->session->userdata('where_consulta');
	$totales = $num_rows; 
	$datos = array();
	$etiquetas = array();
	$i = 0;

        $etiquetas[$i] = "Correos entregados";
        $this->db->where($where." AND estado > 300 AND estado < 400");
        $this->db->from('mensajes');
        $datos[$i] =  $this->db->count_all_results();
        $total_entregados = $datos[$i];
        $i++;

	$etiquetas[$i] = "Correos encolados";
	$this->db->where($where." AND estado < 200");
	$this->db->from('mensajes');
	$datos[$i] =  $this->db->count_all_results();
	$total_encolados = $datos[$i];
	$i++;

	$etiquetas[$i] = "Correos detenidos";
        $this->db->where($where." AND estado > 400");
        $this->db->from('mensajes');
        $datos[$i] =  $this->db->count_all_results();
	$total_detenidos = $datos[$i];
	$i++;

	$data = array(
        	'titulo' => "Estados de los correos electrónicos encontrados en la búsqueda actual",
                'datos' => $datos,
		'color' => '48B0FF',
                'etiquetas' => $etiquetas,
                'totales' => $totales
	);

        $this->load->view('graph', $data);
?>
</fieldset>
<br/><br/>





<?php if($total_entregados > 0) { ?>

<fieldset class="informe" >
        <legend> Estado de los correos ENTREGADOS</legend>
<?php
	$where = $this->session->userdata('where_consulta');
	$totales = $total_entregados; 
	$datos = array();
	$etiquetas = array();
	$i = 0;

	$etiquetas[$i] = "Correos HAM";
	$this->db->where($where." AND estado > 350 AND estado < 400");
	$this->db->from('mensajes');
	$datos[$i] =  $this->db->count_all_results();
	$i++;
	
	$etiquetas[$i] = "Correos SPAM";
        $this->db->where($where." AND estado > 300 AND estado < 350");
        $this->db->from('mensajes');
        $datos[$i] =  $this->db->count_all_results();
	$i++;

	$data = array(
        	'titulo' => "Estado de los correos entregados.",
                'datos' => $datos,
		'color' => '9ACD32',
                'etiquetas' => $etiquetas,
                'totales' => $totales
        );
        $this->load->view('graph', $data);
	} //if total = 0
?>
</fieldset>
<br/><br/>




<?php if($total_encolados > 0) { ?>

<fieldset class="informe" >
        <legend> Estado de los correos ENCOLADOS</legend>
<?php
	$where = $this->session->userdata('where_consulta');
	$totales = $total_encolados; 
	$datos = array();
	$etiquetas = array();
	$i = 0;

	$etiquetas[$i] = "Encolados en entrada";
	$this->db->where($where." AND estado = 1");
	$this->db->from('mensajes');
	$datos[$i] =  $this->db->count_all_results();
	$i++;
	
	$etiquetas[$i] = "Encolados en antivirus";
        $this->db->where($where." AND estado = 31");
        $this->db->from('mensajes');
        $datos[$i] =  $this->db->count_all_results();
	$i++;
		
	$etiquetas[$i] = "Encolados en listas";
	$this->db->where($where." AND estado = 51");
        $this->db->from('mensajes');
        $datos[$i] =  $this->db->count_all_results();
	$i++;

        $etiquetas[$i] = "Encolados en salida";
        $this->db->where($where." AND estado > 150 AND estado < 170");
        $this->db->from('mensajes');
        $datos[$i] =  $this->db->count_all_results();
        $i++;

        $etiquetas[$i] = "Encolados en buzones";
        $this->db->where($where." AND estado > 170 AND estado < 200");
        $this->db->from('mensajes');
        $datos[$i] =  $this->db->count_all_results();
        $i++;

	$data = array(
        	'titulo' => "Estado de los correos encolados.",
                'datos' => $datos,
		'color' => '',
                'etiquetas' => $etiquetas,
                'totales' => $totales
        );
        $this->load->view('graph', $data);
	} //if total = 0
?>
</fieldset>
<br/><br/>


<?php if($total_detenidos > 0) { ?>

<fieldset class="informe" >
        <legend> Estado de los correos DETENIDOS</legend>
<?php
	$where = $this->session->userdata('where_consulta');
	$totales = $total_detenidos; 
	$datos = array();
	$etiquetas = array();
	$i = 0;

	$etiquetas[$i] = "Correos con virus";
	$this->db->where($where." AND estado = 431");
	$this->db->from('mensajes');
	$datos[$i] =  $this->db->count_all_results();
	$i++;
	
	$etiquetas[$i] = "Correos mal formados";
        $this->db->where($where." AND estado = 432");
        $this->db->from('mensajes');
        $datos[$i] =  $this->db->count_all_results();
	$i++;
		
	$etiquetas[$i] = "Errores en mailman";
	$this->db->where($where." AND estado = 433");
        $this->db->from('mensajes');
        $datos[$i] =  $this->db->count_all_results();
	$i++;

        $etiquetas[$i] = "Errores en MTA destino";
        $this->db->where($where." AND estado = 452");
        $this->db->from('mensajes');
        $datos[$i] =  $this->db->count_all_results();
        $i++;

        $etiquetas[$i] = "Errores en buzón";
        $this->db->where($where." AND estado = 472");
        $this->db->from('mensajes');
        $datos[$i] =  $this->db->count_all_results();
        $i++;



	$data = array(
        	'titulo' => "Estado de los correos detenidos.",
                'datos' => $datos,
		'color' => 'FF0000',
                'etiquetas' => $etiquetas,
                'totales' => $totales
        );
        $this->load->view('graph', $data);
	} //if total = 0
?>
</fieldset>
<br/><br/>
                                </div>
                        </div>


        </div>
</div>
