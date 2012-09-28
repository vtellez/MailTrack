#!/usr/bin/perl
#
# Procesamiento de log de qmail-send de salida para Seguimiento de correo
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

use Time::TAI64 qw/tai2unix/;

$PATH = $ARGV[0];
require("$PATH/etc/config.pl");
require("$PATH/bin/funciones.pl");

# Realizamos la conexión a la base de datos
$dbh = DBI->connect($connectionInfo,$userid,$passwd, {mysql_enable_utf8=>1}) or die "Can't connect to the database.\n";

my $from, $to, $fecha, $estado, $mensaje, $codigo;

my %destinatarios=();

open(IN,"$PATH/log/temp") or die("No puedo abrir el fichero temp!\n");

while (<IN>) {
        if($_=~/(.+) info msg (.+) from <(.+)>/)
	{
		$from = $3;
		
	}
        elsif($_=~/(.+) info msg (.+) from <>/)
        {
                $from = '<>';
        }
	elsif($_=~/(.+) starting delivery (.+): msg (.+) to remote (.+)/)
	{
		my $id = $2;
		$to = $4;
		$to =~s/'//g;
		$destinatarios{$id} = $to;
        }
        elsif($_=~/(.+) starting delivery (.+): msg (.+) to local (.+)/)
        {
		my $id = $2;
		$to = $4;
		$to =~s/'//g;
               	$to =~s/alias-(.+)-//g;
               	$to =~s/alias-//g;
		$destinatarios{$id} = $to;
        }
	elsif($_=~/(.+) delivery (.+): (.+): (.+)/)
	{
		$fecha = tai2unix($1); #convertimos la fecha tai64 de qmail a timestamp de unix
		$to = $destinatarios{$2}; 
		$codigo = $3;
		$mensaje = $4;
		$mensaje =~s/\_/ /g;
		$mensaje =~s/'//g;
		$mensaje =~s/"//g;
		$mensaje =~s/\(//g;
		$mensaje =~s/\)//g;

		$estado = $ENCOLADO_BUZONES;

		if($to!~/\@(alum\.)?$DOMINIO/i)
		{
			#No se trata de ningún dominio de la US
			$estado = $ENTREGADO_REMOTO;
                        #Incrementamos el número de correos dirigidos a dominios externos
                        my $query = "UPDATE estadisticas SET externo = externo + 1;";
                        $sth = $dbh->prepare($query);
                        $sth->execute();
                }
		
		#Detectamos si es un dominio virtual o un alias, en ese caso le damos un estado distinto
		if($mensaje =~/forward:/i)
		{
			$estado = $ENTREGADO_ALIAS;
			#Incrementamos el número de correos redirigidos a alias o dom. virtuales
               	        my $query = "UPDATE estadisticas SET alias = alias + 1;";
                   	$sth = $dbh->prepare($query);
	                $sth->execute();
		}
		
		if($codigo eq 'failure')
		{
			$estado = $ERROR_SALIDA;
		}elsif($codigo eq 'deferral')
		{
			$estado = $ENCOLADO_SALIDA;
		}
		#Obtenemos el mensaje id con cruces temporales ya que no está presente en el log
		#Para ello establecemos distintos intervalos de tiempo para ver si el correo está presente en la BBDD
		@intervalos_tiempo = (300,1000,10000);
		$encontrado = 0;
		$cont = 0;
		while($intervalos_tiempo[$cont] && $encontrado eq 0)
		{
			$fechainf = $fecha - $intervalos_tiempo[$cont];
	                $fechasup = $fecha + 100;
			$query = "SELECT message_id,estado,mid FROM mensajes WHERE mfrom='$from' AND mto='$to' AND fecha > $fechainf AND fecha < $fechasup AND estado < 300 ORDER BY fecha DESC;";
                	$sth = $dbh->prepare($query);
	                $sth->execute;

        	        my @row; 
                	if ( @row = $sth->fetchrow_array(  ) ) 
			{
				$encontrado = 1;
	                	my $message_id = $row[0];
				my $estado_actual = $row[1];
				my $mid = $row[2];
	
				$estado = calcula_estado($estado,$estado_actual);
				$query = "UPDATE mensajes SET estado = $estado WHERE mid = $mid;";

	 			$sth = $dbh->prepare($query);
				$sth->execute();
				$mensaje = "- Qmail-send informó: (".$codigo.") ".$mensaje;
				$query = "INSERT IGNORE INTO historial (message_id,estado,hto,fecha,maquina,descripcion) VALUES ('$message_id',$estado,'$to',$fecha,'salida','$mensaje');";

				$sth = $dbh->prepare($query);
				$sth->execute();
				delete $mapa{$id};
			}#if @row
			$cont++;
		} 	# while intervalos de tiempo
	}	#elsif delivery
} 	#while fichero
$dbh->disconnect;
undef %destinatarios;
close(IN);
exit;
