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
class Inicio extends CI_Controller {

	function __construct()
	{
		parent::__construct();
                $this->controlacceso->control();
	}
	
	function index()
	{
                $data = array(
                                'subtitulo' => 'Inicio',
				'controlador' => 'inicio',
				'parent' => '',
                );
                $this->load->view('cabecera', $data);

		$this->load->view('inicio.php');
		$this->load->view('pie.php');
	}
}
