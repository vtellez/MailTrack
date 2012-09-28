#!/usr/bin/perl/
#
# Procesamiento de maillog de perdition para Seguimiento de correo
# ------------------------------------------------------------------
#/*
# *    Copyright 2011 Víctor Téllez Lozano <vtellez@us.es>
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

use Encode;
use Time::Local qw(timelocal);
use POSIX qw(strftime);

$PATH = $ARGV[0];
require("$PATH/etc/config.pl");

# Realizamos la conexión a la base de datos
$dbh = DBI->connect($connectionInfo,$userid,$passwd, {mysql_enable_utf8=>1}) or die "Can't connect to the database.\n";

my %meses = ('Jan'=>0,'Feb'=>1,'Mar'=>2,'Apr'=>3,'May'=>4,'Jun'=>5,'Jul'=>6,'Aug'=>7,'Sep'=>8,'Oct'=>9,'Nov'=>10,'Dec'=>11);

open(IN,"$PATH/log/temp") or die("No puedo abrir el fichero temp!\n");

while (<IN>) {
	if($_=~/Auth: 127\.0\.0\.1/){
		#ignoramos los casos de conexiones smtp
	}
        elsif($_=~/(.+) nueva_e_entrada perdition(.+) Auth: (.+):(.+)->(.+) authentication_id="(.+)" (.+) protocol=(.+) server-secure=(.+) status="(.+)"/){
		my $ip = $3;
		my $protocolo = $8."(".$9.")";
		my $status = $10;
		my $usuario = $6;
		my $descripcion = "Conexión correcta.";
		my $estado = 0;
                my @fechaent = split(/\s+/,$1);

		my $tipo = "imap";

		if($protocolo=~/pop/i)
		{
			$tipo = "pop";
		}
		
		if($status ne "ok")
		{ 
			$descripcion = "Conexión fallida.";
			$estado = 1;
		}

                my $mes_actual = strftime "%m", localtime;
                my $anio_actual = strftime "%Y", localtime;

                my $anio_mensaje = $anio_actual;

                if($meses{$linea[0]} > $mes_actual)
                {
                        $anio_mensaje = $anio_actual - 1;
                }

                my @hora =  split(/:/,$fechaent[2]);
                my $fecha = timelocal($hora[2],$hora[1],$hora[0],$fechaent[1],$meses{$fechaent[0]},$anio_mensaje);

		$query = "INSERT INTO accesos (usuario,ip,tipo,protocolo,fecha,estado) VALUES ('$usuario','$ip','$tipo','$protocolo',$fecha,'$estado') ON DUPLICATE KEY UPDATE fecha = $fecha;";
                $sth = $dbh->prepare($query);
                $sth->execute();
		
		$query = "UPDATE accesos SET contador=contador+1 WHERE usuario='$usuario' AND ip='$ip' AND protocolo='$protocolo' AND estado='$estado';";
                $sth = $dbh->prepare($query);
                $sth->execute();

                #Incrementamos el número de accesos totales
                $query = "UPDATE estadisticas SET accesos = accesos + 1;";
                $sth = $dbh->prepare($query);
                $sth->execute();

		#Incrementamos el número de accesos de dicho tipo
                $query = "UPDATE estadisticas SET $tipo = $tipo + 1;";
                $sth = $dbh->prepare($query);
                $sth->execute();

	} #elsif
}	#while principal

$dbh->disconnect;
undef %meses;
close(IN);
exit;
