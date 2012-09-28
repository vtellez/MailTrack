#!/bin/sh
#
# Script de recuperación de dia D para la base de datos de seguimiento
# -----------------------------------------------------------------------
#/*
# * Copyright 2010 Víctor Téllez Lozano <vtellez@us.es>
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

RUTA=`echo /var/www/html/Seguimiento/scripts`

echo "" > $RUTA/log/temp_recovery

grep -v '#' $RUTA/etc/recovery | while read line 
do 
	DIA_SUP=$1
	DIA_INF=$2
	HOST=`echo $line |  cut -d , -f1`
        IP=`echo $line |  cut -d , -f2`
        LOG=`echo $line |  cut -d , -f3`
        FILTER=`echo $line |  cut -d , -f4`
        PROGRAMA=`echo $line |  cut -d , -f5`

	ssh $IP find $LOG -maxdepth 1 -name $FILTER -type f -mtime +$DIA_SUP -mtime -$DIA_INF > $RUTA/log/temp_recovery
	
	cat $RUTA/log/temp_recovery | while read file
	do
		echo "$HOST,$IP,$file,$PROGRAMA"
	done
done

rm $RUTA/log/temp_recovery
