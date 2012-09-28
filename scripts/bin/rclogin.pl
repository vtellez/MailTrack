#!/usr/bin/perl/
#
# Procesamiento de maillog de rclogin  para Seguimiento de correo
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
        if($_=~/(.+) accesoUS rclogin(.+): (.+) (\d.+) ([FO].+)/){
#Viejo formato
#May 19 04:27:33 accesoUS rclogin[825]: corres 83.32.255.108 El usuario se ha autenticado correctamente
#May 19 06:42:06 accesoUS rclogin[707]: jgamore 88.0.219.228 Nombre de usuario o contraseña erróneos

#Nuevo formato
#Apr 10 07:50:49 accesoUS rclogin[5955]: jorgelp@us.es 150.214.142.56 FALLO (_buzonus, cod: -1)
#Apr 10 07:50:49 accesoUS rclogin[5965]: merisiriv@alum.us.es 79.150.94.42 OK
		
		my @list_usuario = split(/@/,$3);
		my $usuario = $list_usuario[0];
		my $ip = $4;
		my $protocolo = "Buzón Web";
		my $descripcion = "Conexión correcta.";
                my @fechaent = split(/\s+/,$1);

		my $tipo = "buzonweb";
		my $status = $5;
		my $estado = 0;

		if($status !~ /OK/)
		{ 
			$descripcion = "Conexión fallida.";
			$estado = 1;

			@lista = split(/\s+/,$usuario);
			$usuario = $lista[0];
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
                $query = "UPDATE estadisticas SET buzonweb = buzonweb + 1;";
                $sth = $dbh->prepare($query);
                $sth->execute();
	
	} #if

}	#while principal

$dbh->disconnect;
undef %meses;
close(IN);
exit;
