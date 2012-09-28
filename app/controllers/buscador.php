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
class Buscador extends CI_Controller {

	function Buscador()
	{
		parent::__construct();	
		$this->controlacceso->control();
	}

	function accesos() {


                $data = array(
                                'subtitulo' => 'Buscador de accesos',
                                'controlador' => 'buscador',
				'parent' => '_parent',
                                'js_adicionales' => array(
                                        'js/jquery/js/jquery-1.5.1.min.js',
                                        'js/jquery/js/jquery-ui-1.8.13.custom.min.js'
                                ),
                                'css_adicionales' => array(
                                        'js/jquery/css/ui-lightness/jquery-ui-1.8.13.custom.css'
                                ),
                );
                $this->load->view('cabecera', $data);

	        if (!$this->controlacceso->permisoAdministracion()) {
                        show_error(403, 'Acceso denegado');
                }else{
                	$this->load->view('buscador_accesos.php');
		}

                $this->load->view('pie.php');
	}
	
	function index($type="")
	{
		 //Obtenemos la tabla de estados
		$this->db->order_by("codigo", "asc"); 
                $estados = $this->db->get('estados');
                $data = array(
                                'subtitulo' => 'Buscador',
				'controlador' => 'buscador',
				'estados' => $estados,
				'parent' => '_parent',
                                'js_adicionales' => array(
                                        'js/jquery/js/jquery-1.5.1.min.js',
                                        'js/jquery/js/jquery-ui-1.8.13.custom.min.js'
                                ),
                                'css_adicionales' => array(
                                        'js/jquery/css/ui-lightness/jquery-ui-1.8.13.custom.css'
                                ),
                );
                $this->load->view('cabecera', $data);

		$this->load->view('buscador.php');
		$this->load->view('pie.php');
	}

}
