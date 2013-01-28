<?php
/*
 * Copyright 2010 Víctor Téllez Lozano <vtellez@us.es>
 *
 *    This file is part of Seguimiento.
 *
 *    Seguimiento is free software: you can redistribute it and/or modify it
 *    under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    Seguimiento is distributed in the hope that it will be useful, but
 *    WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *    Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public
 *    License along with Seguimiento.  If not, see
 *    <http://www.gnu.org/licenses/>.
 */
class Accesos extends CI_Controller {

	function __construct()
	{
		parent::__construct();
                $this->controlacceso->control();
		$this->load->library('texto');
	}
	
	function lista($filtro='todos',$campo='fecha',$sentido='desc'){
		/*
		* $campo: Campo de la tabla mensajes por el que ordendar la consulta.
		* $sentido: Sentido (ascendente o descendente) para ordenar la consulta. 
		* $filtro:
		* Calculamos los accesos consultar en función del parámetro filtro: pop,imap,buzonweb,resultados,todos
		*/
		$cuenta = $this->session->userdata('mail');
		$uid = $this->session->userdata('uid');

                //Cargamos los enlaces a la paginacion
                $this->load->library('pagination');
                $config['per_page'] = $this->config->item('num_item_pagina');
                $config['first_link'] = '<<';
                $config['last_link'] = '>>';
		$config['uri_segment'] = 6;
		$config['num_links'] = 2;	
		$config['full_tag_open'] = '<div id="paginacion">';
		$config['full_tag_close'] = '</div>';
		$config['cur_tag_open'] = '<div id="paginacion_actual">';
		$config['cur_tag_close'] = '</div>';

		$where = "(usuario = '$cuenta' OR usuario = '$uid')";

		if($filtro == "buzonweb"){
			$config['base_url'] = site_url("accesos/lista/buzonweb");
			$where = $where." AND tipo = 'buzonweb'";
		}elseif ($filtro == "imap"){	
			$config['base_url'] = site_url("accesos/lista/imap");
			$where = $where." AND tipo = 'imap'";
		}elseif ($filtro == "pop"){	
			$config['base_url'] = site_url("accesos/lista/pop");
			$where = $where." AND tipo = 'pop'";
		}elseif ($filtro == "resultados"){
			$config['base_url'] = site_url("accesos/lista/resultados");
			$where = "aid > 0";

                        $this->load->library('session');

                        //Procesamos la consulta de la búsqueda
                        if(!isset($_POST['oculto'])){
                                $where = $this->session->userdata('where_consulta');
                                if($where == ""){
                                        redirect(site_url('buscador'), 'refresh');
                                }

                        }else{

			//Procesamos los campos de búsqueda, los limpiamos y preparamos la clausula where
				if($_POST['usuario'] != ""){
                                        $cadena = str_replace("*", "%", $_POST['usuario']);
                                        $cadena = str_replace("'", "\'", $cadena);
                                        $cadena = str_replace(";", "", $cadena);
                                        $cadena = str_replace("--", "", $cadena);
                                        $where = $where." AND usuario LIKE '$cadena'";
                                }
                                if($_POST['ip'] != ""){
                                        $cadena = str_replace("*", "%", $_POST['ip']);
                                        $cadena = str_replace("'", "\'", $cadena);
                                        $cadena = str_replace(";", "", $cadena);
                                        $cadena = str_replace("--", "", $cadena);
                                        $where = $where." AND LIKE '$cadena'";
                                }
                                if($_POST['tipo'] != "cualquiera"){
                                        $cadena = str_replace("'", "\'", $_POST['tipo']);
                                        $cadena = str_replace(";", "", $cadena);
                                        $cadena = str_replace("--", "", $cadena);
                                        $where = $where." AND tipo = '$cadena'";
                                }
                                if($_POST['estado'] != "cualquiera"){
                                        $cadena = str_replace("'", "\'", $_POST['estado']);
                                        $cadena = str_replace(";", "", $cadena);
                                        $cadena = str_replace("--", "", $cadena);
                                        $where = $where." AND estado = $cadena";
                                }
  
                                if($_POST['fecha1'] != "Cualquier fecha")
                                       $where = $where." AND fecha >= ".strtotime($_POST['fecha1']);

                                if($_POST['fecha2'] != "Cualquier fecha"){
                                        $fecha2 = strtotime($_POST['fecha2']) + 86400;
                                        $where = $where." AND fecha <= $fecha2";
                                }

                                //Si el usuario no es admin, solo podrá consultar SUS mensajes
                                if (!$this->controlacceso->permisoAdministracion()) {
        				$where = "(usuario = '$cuenta' OR usuario = '$uid')";                                
                                }
                                
				//Incrementamos el número de búsquedas realizadas en la aplicación
                                $this->db->query('UPDATE estadisticas SET busquedas = busquedas + 1;');


			}

		}else{
			//Solo nos queda la opción de todos los mensajes
                	$config['base_url'] = site_url("accesos/lista/todos");
			$filtro = "todos";
		}

                //Guardamos la consulta en la sesion del usuario:
                $this->session->set_userdata('where_consulta', $where);

		$base = $config['base_url'];
		$config['base_url'] = $config['base_url']."/$campo/$sentido/";

		if($sentido == "desc") { $contrario = "asc"; } else { $contrario = "desc"; }
		
		$this->db->where($where);
		$this->db->order_by("$campo $sentido");
		$accesos = $this->db->get('accesos',$config['per_page'], (int)$this->uri->segment(6));
		
		$this->db->where($where);
		$totales = $this->db->get('accesos');
                $config['total_rows'] = $totales->num_rows();
                $num_rows =  $config['total_rows'];  
		
	        $this->pagination->initialize($config);

	
		$error = 0;

			
		$data = array(
				'accesos' => $accesos,
                                'pagination' => $this->pagination,
				'campo' => $campo,
				'sentido' => $sentido,
				'contrario' => $contrario,
				'base' => $base,
				'num_rows' => $num_rows,
				'subtitulo' => 'Lista de accesos',
                                'controlador' => 'accesos',
                                'parent' => '_parent',
				'filtro' => $filtro,
                );

                $this->load->view('cabecera', $data);

		if($error == 1)
		{
			$this->load->view('search_error.php',$data);
		}
		else
		{
			if(isset($informe)){
				$this->load->view('informe.php',$data);
				$this->db->query('UPDATE estadisticas SET informes = informes + 1;');
			}else{
	        	        $this->load->view('lista_accesos.php',$data);
			}
		}
                
		$this->load->view('pie.php');
	}




        function estadisticas()
        {
                $where = $this->session->userdata('where_consulta');
		$this->db->where($where);
                $totales = $this->db->get('accesos');
                $num_rows = $totales->num_rows();

		 $data = array(
                                'subtitulo' => 'Estadísticas de accesos',
				'num_rows' => $num_rows,
                                'controlador' => 'accesos',
                                'parent' => '_parent',
                );

         
                $this->load->view('cabecera', $data);
		$this->load->view('informe_accesos.php',$data);
                $this->load->view('pie.php');
                $this->db->query('UPDATE estadisticas SET informes = informes + 1;');
/*
		       $this->load->view('cabecera', $data);
		$this->load->view('pie.php');
*/  
      }





	function ver($id)
	{
	
                // comprobamos que el mensaje existe y el usuario actual tiene  permisos para ver dicho mensaje         
                $this->db->where('aid', $id); 
                $accesos = $this->db->get('accesos');

                $cuenta_actual = $this->session->userdata('mail');
                $row = $accesos->row();


		$cuenta_actual = $this->session->userdata('mail');
                $cuenta = $this->session->userdata('mail');
                $sexo = $this->session->userdata('sexo');

                if($sexo == "2"){ $usuario = "she_";}else{ $usuario = "";} 

		$error = 0;

		$data = array(
                                'error' => $error,
				'usuario' => $usuario,
                                'acceso' => $row,
                                'subtitulo' => 'Vista detalla de acceso',
                                'controlador' => 'accesos',
                                'parent' => '_parent',
                );
                $this->load->view('cabecera', $data);
                $this->load->view('acceso.php');
                $this->load->view('pie.php');
	}



} //clase accesos
