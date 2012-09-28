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
class Controlacceso {
        private $CI;
        private $o;
        private $identidad=false;

        function Controlacceso() {
                require_once('libopensso-php/Opensso_wrapper.php');
                $this->CI =& get_instance();
                $this->o = new Opensso_wrapper();
		$this->identidad = $this->CI->session->userdata('identidad');
        }

        /**
         * Fuerza la autenticación si no se ha producido aún
         */
        function control() {
                if ($this->identidad !== FALSE) {
                        return TRUE;
                } else {
                        if ($this->o->check_and_force_sso(current_url())) {
                                $rel = $this->o->attribute('usesrelacion');
                                $rel = is_array($rel) ? $rel : array($rel);
                                $data = array(
                                                'identidad' => $this->o->attribute('uid'),
                                                'uid' => $this->o->attribute('uid'),
                                                'mail' => $this->o->attribute('mail'),
                                                'sexo' => $this->o->attribute('schacgender'),
                                                'nombre' => ucwords(strtolower($this->o->attribute('cn')))
                                );

                                // Guardar sesión
                                $this->CI->session->set_userdata($data);

				//incrementamos el número de visitas a la aplicación
                                $this->CI->db->query('UPDATE estadisticas SET visitas = visitas + 1;');

		                //Registramos el acceso en los logs si estaba activa la opción desde config.php
                		if ($this->CI->config->item('admin_log') == "true")
                		{
                                	log_message('info', 'Usuario "'.$this->o->attribute('uid').'" inició sesión en seguimiento."');
                		}
                                
				redirect();
                        } else {
                                // Permitimos la redirección
                                exit;
                        }
                }
        }


        /**
         * Comprueba si el usuario actual tiene permiso de administración
         */

        function permisoAdministracion() {
                $admins = $this->CI->config->item('administradores');

                return in_array($this->identidad, $admins);
        }

	
	/**
	 * Logout de usuario
	 */

	function logout() {
		$this->o->logout(TRUE);
	}

}
