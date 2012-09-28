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

class Indicadores extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		$this->controlacceso->control();
	}
	
	function index()
	{
		//Comprobamos si nos pasan una fecha para la vista de estadísticas
		//en caso contrario cogemos el día actual
		if(!isset($_POST['fecha']))
		{
			$date = date( "Y-m-d" );
 			$dia = date("d", strtotime( "-1 day", strtotime( $date ) )); // Dia de ayer 
                        $mes = date("m", strtotime( "-1 day", strtotime( $date ) )); // Dia de ayer
                        $anio = date("Y", strtotime( "-1 day", strtotime( $date ) )); // Dia de ayer 
		}else
		{
			list($dia,$mes,$anio) = split('/',$_POST['fecha']);
		}

                $data = array(
                                'subtitulo' => 'Indicadores de la aplicación',
				'controlador' => 'indicadores',
				'dia' => $dia,
				'mes' => $mes,
				'anio' => $anio,
				'parent' => '',
                                'js_adicionales' => array(
					'js/jquery/js/jquery-1.5.1.min.js',
					'js/jquery/js/jquery-ui-1.8.13.custom.min.js',
                                ),
                                'css_adicionales' => array(
					'js/jquery/css/custom/jquery-ui-1.8.13.custom.css'
                                ),
                );
                $this->load->view('cabecera', $data);
		$this->load->view('chart.php');
		$this->load->view('pie.php');
	}

}
