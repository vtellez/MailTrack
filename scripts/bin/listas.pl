#!/usr/bin/perl
#
# Procesamiento de maillog de listas para Seguimiento de correo
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

use Time::Local qw(timelocal);
use POSIX qw(strftime);

$PATH = $ARGV[0];
require("$PATH/etc/config.pl");

# Realizamos la conexión a la base de datos
$dbh = DBI->connect($connectionInfo,$userid,$passwd, {mysql_enable_utf8=>1}) or die "Can't connect to the database.\n";

my %from=();
my %mid=();
my %meses = ('Jan'=>0,'Feb'=>1,'Mar'=>2,'Apr'=>3,'May'=>4,'Jun'=>5,'Jul'=>6,'Aug'=>7,'Sep'=>8,'Oct'=>9,'Nov'=>10,'Dec'=>11);

open(IN,"$PATH/log/temp") or die("No puedo abrir el fichero temp!\n");

while (<IN>) {
        
	if($_=~/sendmail(.+?): (.+?): from=<(.+)>.+ msgid=<(.+)>/){
		$id = $2;
		$from{$id} = $3;
		$mid{$id} = $4;
        }
        elsif($_=~/sendmail(.+?): (.+?): to=(<.+>).+ stat=(.+?) (.+)$/){
		my @linea= split(/\s+/,$_);

		$mes_actual = strftime "%m", localtime;
		$anio_actual = strftime "%Y", localtime;

                my $anio_mensaje = $anio_actual;

                if($meses{$linea[0]} > $mes_actual)
                {
		        $anio_mensaje = $anio_actual - 1;
                }
		my @hora =  split(/:/,$linea[2]);
                my $fecha = timelocal($hora[2],$hora[1],$hora[0],$linea[1],$meses{$linea[0]},$anio_mensaje);
		my $maquina = $linea[3];

		$id = $2;

		$stat = $4;
		$stat =~s/://g;

		$descripcion = "Mailman informó del siguiente stado: $4 $5";
		$asunto = "<i>Mensaje de una lista de correo.</i>";
		@to = split(/,/,$3);
		
		#Vemos si podemos obtener de la BBDD el asunto del mensaje original
		my $query = "SELECT asunto FROM mensajes where message_id='$mid{$id}' and mfrom <>'$from{$id}';";
		my $sth = $dbh->prepare($query) or die "Can't prepare SQL statement: $DBI::errstr\n";
		$sth->execute or die "Can't execute SQL statement: $DBI::errstr\n";
		my @row;
		while ( @row = $sth->fetchrow_array(  ) ) {
			$asunto = $dbh->quote($row[0]);
			$asunto =~s/'//g;
		}

		#Para cada destinatario, guardamos el mensaje en la base de datos
		foreach $item (@to){
			$item =~s/<//g;
			$item =~s/>//g;
			$item =~s/ctladdr=//g;

			$estado = $ENCOLADO_LISTAS;
			
			if($maquina eq "listasc")
			{
				$estado = $ENCOLADO_BUZONES;
			}

                        if($stat ne "Sent") 
			{
	                        $estado = $ERROR_LISTAS;
                        }
                        elsif($item!~/\@(.+\.)?$DOMINIO/i)
			{
        	                $estado = $ENTREGADO_REMOTO;
                        }

			if(defined($from{$id}))
			{			

	                my $query = "INSERT INTO mensajes (message_id,mfrom,mto,estado,fecha,asunto) VALUES ('$mid{$id}',".$dbh->quote($from{$id}).",".$dbh->quote($item).",$estado,$fecha,".$dbh->quote($asunto).") ON DUPLICATE KEY UPDATE asunto=".$dbh->quote($asunto).";";
	                my $sth = $dbh->prepare($query);
        	        $sth->execute();
			
        	        $query = "INSERT IGNORE INTO historial (message_id,estado,hto,fecha,maquina,descripcion) VALUES ('$mid{$id}',$estado,".$dbh->quote($item).",$fecha,'$maquina','$descripcion');";
                        $sth = $dbh->prepare($query);
                       	$sth->execute();


                       	#Incrementamos el número de correos procesados
                        $query = "UPDATE estadisticas SET procesados = procesados + 1;";
	                $sth = $dbh->prepare($query);
        	        $sth->execute();


                        $query = "UPDATE estadisticas SET listas = listas + 1;";
                       	if($stat ne "Sent") 
			{
                               	$query = "UPDATE estadisticas SET listas_error = listas_error + 1;";
                        }
	                
			$sth = $dbh->prepare($query);
        	        $sth->execute();

 	   		$sth->finish();
			
			} #if defined
		} 
	}
}

$dbh->disconnect or warn "Error disconnecting: $DBI::errstr\n";

undef %from;
undef %mid;
undef %meses;

close(IN);
exit;
