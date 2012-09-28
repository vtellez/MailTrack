#!/bin/sh
#
# Script de borrado  de la base de datos de seguimiento
# -----------------------------------------------------------------------
#/*
# *    Copyright 2010 Víctor Téllez Lozano <vtellez@us.es>
# *
# *    This file is part of Seguimiento.
# *
# *    Seguimiento is free software: you can redistribute it and/or modify it
# *    under the terms of the GNU Affero General Public License as
# *    published by the Free Software Foundation, either version 3 of the
# *    License, or (at your option) any later version.
# *
# *    Seguimiento is distributed in the hope that it will be useful, but
# *    WITHOUT ANY WARRANTY; without even the implied warranty of
# *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
# *    Affero General Public License for more details.
# *
# *    You should have received a copy of the GNU Affero General Public
# *    License along with Seguimiento.  If not, see
# *    <http://www.gnu.org/licenses/>.
# */

RUTA=`echo /var/www/html/Seguimiento/scripts`;
FLAG_BORRADO=`echo $RUTA/bin/seguimiento_flag_delete_lock`;

if [ -f $FLAG_BORRADO ]; then
	#El bloqueo esta activo por otra ejecución concurrente de borrado
	exit
else
	#Creamos el flag de bloqueo de borrado
	touch $FLAG_BORRADO
	
	perl /var/www/html/Seguimiento/scripts/bin/vaciaBD.pl

	#Liberamos el flag de bloqueo de borrado
	rm $FLAG_BORRADO
fi
