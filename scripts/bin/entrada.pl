#!/usr/bin/perl
#
# Procesamiento de log de qmail de entrada para Seguimiento de correo
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

# Realizamos la conexión a la base de datos
$dbh = DBI->connect($connectionInfo,$userid,$passwd, {mysql_enable_utf8=>1}) or die "Can't connect to the database.\n";

#hashes que usaremos para guardar la información de cada correo
my %fecha=();
my %ip=();
my %from=();
my %to=();
my %mid=();
my %status=();

open(IN,"$PATH/log/temp") or die("No puedo abrir el fichero temp!\n");
while (<IN>) {
#@400000004c5b34b604c9b734 tcpserver: deny 6944 us.es:192.168.1.13:25 :193.147.171.74::40714
#@400000004c5bf2a61d073c7c tcpserver: ok 4389 us.es:192.168.1.113:2525 :192.168.7.153::54949
        if($_=~/(.+) (.+) \< EHLO \[(.+)\]/){
		$fecha{$2}=tai2unix($1);#convertimos la fecha tai64 de qmail a timestamp de unix
		$ip{$2}=$3;
	}
	elsif($_=~/(.+) (.+) \< MAIL FROM:<(.+)>/)
	{
		$from{$2}=$3;
        }
	elsif($_=~/(.+) (.+) \< RCPT TO:<(.+)>/)
	{
		$to{$2}=$3;
        }
	elsif($_=~/(.+) (.+) \< Message-Id: <(.+)>/)
	{
		$mid{$2}=$3;
        }
	elsif($_=~/(.+) (.+) \< Message-Id: <(.+)\+/)
	{
		#Procesamos un message-id partido
		$mid{$2}=$3;
        }
	elsif($_=~/(.+) (.+) \< (.+)>/)
	{
		#Segunda parte del message-id partido
		$mid{$2}=$mid{$2}.$3;
        }
	elsif($_=~/(.+) tcpserver: end (.+) status (.+)/)
	{

		$status{$2}=$3;
		
		#TODO: Comprobamos que todos los valores tienen parámetros antes de guardarlos en la BBDD

	$query = "INSERT INTO mensajes (message_id,mfrom,mto,fecha,estado,ip) VALUES ('$mid{$2}', '$from{$2}', '$to{$2}',$fecha{$2},1,'$ip{$2}') ON DUPLICATE KEY UPDATE ip = '$ip{$2}';";
        $sth = $dbh->prepare($query);
	$sth->execute();

	#Añadimos una entrada al historial del correo
	$query = "INSERT INTO historial (message_id,estado,hto,fecha,maquina) VALUES ('$mid{$2}',1,'$to{$2}','$fecha{$2}','Entrada') ON DUPLICATE KEY UPDATE hto='$to{$2}';";
        $sth = $dbh->prepare($query);
  	$sth->execute();

	$sth->finish();
		
	}
} #while fichero

# Indicamos que hemos acabado y desconectamos
$dbh->disconnect;

#Liberamos memoria
undef %fecha;
undef %ip;
undef %from;
undef %to;
undef %mid;
undef %status;

close(IN);
exit;
