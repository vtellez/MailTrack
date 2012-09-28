<div id="content-wrapper">
        <div class="center-wrapper">
                                <div id="main">

<div class="buttons" style="margin-top:-9px;">
    <a href="<?php echo site_url('');?>buscador">
        <img src="<?php echo site_url('img/buttons/filter.png'); ?>"/> 
       Aplicar nuevo filtro
    </a>
    <a href="javascript:history.go(-1);">
        <img src="<?php echo site_url('img/buttons/search.png'); ?>"/> 
        Ver resultados
    </a>
</div>

   <img src="<?php echo site_url('img/seg/chart.png'); ?>" style="margin-top:-15px;" width="48" height="48" align="left" /> <h2 class="left"> &nbsp;Informe: Resultados de la búsqueda</h2>
                                               <div class="content-separator"></div>

<?php
        //Cargamos el cuadro resumen de condiciones de filtrado
        $this->load->view('info_condiciones_accesos');
?>

<fieldset class="informe" >
	<legend> Descripción general del informe</legend>
<p>A continuación, se detalla el resumen de accesos.</p>

<?php  
	$where = $this->session->userdata('where_consulta');
	$totales = $num_rows; 
	$datos = array();
	$etiquetas = array();
	$i = 0;


	$etiquetas[$i] = "Accesos POP";
        $this->db->where($where." AND tipo = 'pop'");
        $this->db->from('accesos');
        $datos[$i] =  $this->db->count_all_results();
        $total_detenidos = $datos[$i];
        $i++;

        $etiquetas[$i] = "Accesos IMAP";
        $this->db->where($where." AND tipo = 'imap'");
        $this->db->from('accesos');
        $datos[$i] =  $this->db->count_all_results();
        $total_detenidos = $datos[$i];
        $i++;

        $etiquetas[$i] = "Accesos Buzón Web";
        $this->db->where($where." AND tipo = 'buzonweb'");
        $this->db->from('accesos');
        $datos[$i] =  $this->db->count_all_results();
        $total_detenidos = $datos[$i];
        $i++;

        $data = array(
                'titulo' => "Accesos en función del protocolo usado.",
                'datos' => $datos,
                'etiquetas' => $etiquetas,
                'totales' => $totales
        );

        $this->load->view('graph', $data);


?>
</fieldset>
<br/><br/>


<?php 

//	if($total_detenidos > 0) { 

?>

<fieldset class="informe" >
        <legend> Clasificación de accesos por estado</legend>
<?php
        $where = $this->session->userdata('where_consulta');
        $totales = $num_rows;
        $datos = array();
        $etiquetas = array();
        $i = 0;

        $etiquetas[$i] = "Conexiones con éxito";
        $this->db->where($where." AND estado = 0");
        $this->db->from('accesos');
        $datos[$i] =  $this->db->count_all_results();
        $i++;

        $etiquetas[$i] = "Conexiones fallidas";
        $this->db->where($where." AND estado = 1");
        $this->db->from('accesos');
        $datos[$i] =  $this->db->count_all_results();
        $i++;

        $data = array(
                'titulo' => "Estado de los accesos.",
                'datos' => $datos,
                'color' => '9ACD32,FF5555',
                'etiquetas' => $etiquetas,
                'totales' => $totales
        );
        $this->load->view('graph', $data);
 //       } //if total = 0
?>
</fieldset>
<br/><br/>


                                </div>
                        </div>


        </div>
</div>
