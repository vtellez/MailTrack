#!/usr/bin/perl/
#
# Procesamiento de log de fsecure para Seguimiento de correo
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

open(IN,"$PATH/log/temp") or die("No puedo abrir el fichero temp!\n");

while (<IN>) {

	if($_=~/(\d+)\.(.+) GET mail:(.+) - DIRECT(.+)DETECT-STAT:INFECTED:(.+)::: ACTION:BLACKHOLE:(.+) PROTOCOL-STAT:(.+):<(.+)>:(.+)/){

#1327592718.674    107 192.168.1.13 TCP_MISS/000 226490 GET mail:fjsaavedra@us.es - DIRECT/127.0.0.1 multipart/mixed DETECT-STAT:INFECTED:Trojan.Generic.KD.520271:WellsFargo_Checking_Account_Status_Report-Jan2012-66061.zip::: ACTION:BLACKHOLE: PROXY-STAT:smtp:15:15448:192.168.1.13:1:0:91:: PROTOCOL-STAT:forthrightness667@wellsfargo.com:<6720766197.Q0NE2LHE989478@vjkevbpkj.vgafoean.tv>: PROXY-ERROR::

		my $fecha = $1;
		my $from = $3;
		my $info = $5;
		my $to = $7;
		my $mid = $8;
		my $estado = $VIRUS;

		$descripcion = "- F-Secure informó: Virus encontrado $info";
		$query = "UPDATE estadisticas SET virus = virus + 1;";
		$sth = $dbh->prepare($query);
		$sth->execute();

		my $query = "INSERT INTO mensajes (message_id,mfrom,mto,asunto,estado,fecha,virus) VALUES ('$mid','$from','$to','<i>Mensaje marcado como virus.</i>',$estado,$fecha,'$info') ON DUPLICATE KEY UPDATE fecha=$fecha;";
		my $sth = $dbh->prepare($query);
		$sth->execute();

		$query = "INSERT IGNORE INTO historial (message_id,estado,hto,fecha,maquina,descripcion) VALUES ('$mid',$estado,'$to',$fecha,'antivirus','$descripcion');";
		$sth = $dbh->prepare($query);
		$sth->execute();

		$sth->finish();
	
	} #if

}	#while principal

$dbh->disconnect;
close(IN);
exit;
