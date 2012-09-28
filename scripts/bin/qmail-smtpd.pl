#!/usr/bin/perl
#
# Procesamiento de log de qmail-smtpd de salida para Seguimiento de correo
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

my $from, $to, $fecha, $estado, $mensaje, $qp, $mid, $tipo, $query;

open(IN,"$PATH/log/temp") or die("No puedo abrir el fichero temp!\n");

while (<IN>) {

	chomp($_);
        if($_=~/(.+) qmail-smtpd(.+): MAIL FROM:<(.+)>/)
	{
		$fecha = tai2unix($1); #convertimos la fecha tai64 de qmail a timestamp de unix
		$from = $3;
	}
	elsif($_=~/(.+) qmail-smtpd(.+): RCPT TO:<(.+)>/)
	{
                $to = $3;
        }
        elsif($_=~/(.+) < Message-ID: <(.+)>/i)
        {
		$mid = $2;
		$tipo = ">";
        }
        elsif($_=~/(.+) < Message-ID: <(.+)\+/i)
        {
                $mid = $2;
                $tipo = "+";
        }
	elsif($_=~/(.+) (.+) > (.+) ok (.+) qp (.+)/)
	{
		$qp = $5;
		$estado = 71;
		$mensaje = "$3 ok $4 qp $5";
		$mensaje =~s/\_/ /g;
		$mensaje =~s/'//g;
		$mensaje =~s/"//g;
		$mensaje =~s/\(//g;
		$mensaje =~s/\)//g;

	#	print "Fecha: $fecha, De: $from, A: $to, Mensaje-id: $mid, Qp: $mensaje, Tipo: $tipo\n"; 

		#Insertamos el mensaje en la BBDD

		$query = "INSERT INTO mensajes (message_id,mfrom,mto,asunto,estado,fecha) VALUES ('$mid',".$dbh->quote($from).",".$dbh->quote($to).",'<i>Correo aceptado en estafeta de salida</i>',$estado,$fecha) ON DUPLICATE KEY UPDATE estado=estado;";
                my $sth = $dbh->prepare($query);
                $sth->execute();

                $query = "INSERT IGNORE INTO historial (message_id,estado,hto,fecha,maquina,descripcion) VALUES ('$mid',$estado,".$dbh->quote($to).",$fecha,'Salida','$mensaje');";
                $sth = $dbh->prepare($query);
                $sth->execute();

	}
} #while fichero

$dbh->disconnect;
close(IN);
exit;
