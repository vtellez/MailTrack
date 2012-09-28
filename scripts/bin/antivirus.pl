#!/usr/bin/perl/
#
# Procesamiento de maillog de los antivirus para Seguimiento de correo
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

use Encode;
use Time::Local qw(timelocal);
use POSIX qw(strftime);
use MIME::Words qw(:all);

$PATH = $ARGV[0];
require("$PATH/etc/config.pl");

# Realizamos la conexión a la base de datos
$dbh = DBI->connect($connectionInfo,$userid,$passwd, {mysql_enable_utf8=>1}) or die "Can't connect to the database.\n";

#mapa para guardar las puntuaciones del spamassassin
my %mapa=();
my %meses = ('Jan'=>0,'Feb'=>1,'Mar'=>2,'Apr'=>3,'May'=>4,'Jun'=>5,'Jul'=>6,'Aug'=>7,'Sep'=>8,'Oct'=>9,'Nov'=>10,'Dec'=>11);

open(IN,"$PATH/log/temp") or die("No puedo abrir el fichero temp!\n");

while (<IN>) {

        if($_=~/spamd: result: (.) (.+),mid=<(.+)>/){
                if($1 eq "Y"){
			$mapa{$3} = "SPAM";
                }
        }
        elsif($_=~/qmail-scanner\[/){
               	@linea= split(/\s+/,$_);

       	        my $emisor = $linea[8];

		my $maquina = $linea[3];
		my $destinatario = $linea[9];
		if ($destinatario ne '<>')
		{
			my $asunto = $linea[10];
			my $id = $linea[11];
        	        $id =~ s/<//;
                	$id =~ s/>//;
		
			my $mes_actual = strftime "%m", localtime;
			my $anio_actual = strftime "%Y", localtime;

			my $anio_mensaje = $anio_actual;

			if($meses{$linea[0]} > $mes_actual)
			{
				$anio_mensaje = $anio_actual - 1;
			}
			
			my @hora =  split(/:/,$linea[2]);
			my $fecha = timelocal($hora[2],$hora[1],$hora[0],$linea[1],$meses{$linea[0]},$anio_mensaje);

			my $descripcion = "";
			my @cadena_qmail= split(/:/,$linea[5]);

        	        if($asunto=~/\*\*POSIBLE_SPAM\*\*/){
				$mapa{$id} = "SPAM";
                	}

		        $asunto =~s/\_/ /g;

                        if($asunto=~/=\?UTF-8\?B/i){
                                my $decoded = decode("MIME-Header", $asunto."?=");
				$asunto = $decoded;
			}

		 	if($asunto=~/=\?UTF-8\?/i){
				$asunto =  encode("ascii",decode("MIME-Header",$asunto), Encode::FB_HTMLCREF);
			}

			if($asunto=~/=\?ISO-8859-/i){
				$asunto = encode("ascii",decode_mimewords($asunto,), Encode::FB_HTMLCREF);
	                }

        	        $asunto =~s/=\?ISO-8859-1\?Q\??//ig;
        	        $asunto =~s/=\?ISO-8859-2\?Q\??//ig;
        	        $asunto =~s/=\?ISO-8859-15\?Q\??//ig;
                	$asunto =~s/=\?UTF-8\??//ig;

			$id =~ s/'//g;
			$id =~ s/"//g;
			$destinatario =~ s/'//g;
			$destinatario =~ s/"//g;

        	        #Incrementamos el número de correos procesados
                	$query = "UPDATE estadisticas SET procesados = procesados + 1;";
	                $sth = $dbh->prepare($query);
        	        $sth->execute();

			if($cadena_qmail[0] eq 'Clear'){
				if($mapa{$id} eq "SPAM"){
					$estado = $ENCOLADO_SPAM_ANTIVIRUS; 
					$descripcion = "- SpamAssassin marcó este correo como SPAM.";
	 				$query = "UPDATE estadisticas SET spam = spam + 1;";
				}else{
					$estado = $ENCOLADO_ANTIVIRUS; 
					$descripcion = "- SpamAssassin informó: correo no SPAM. <br/>- ClamAV y Qmailscanner informaron: correo libre de virus y/o malware.";
	   		        	$query = "UPDATE estadisticas SET ham = ham + 1;";
				}
			}else{ 
				if($cadena_qmail[0] eq CLAMDSCAN){
					$estado = $VIRUS;
					$descripcion = "- ClamAV informó: Virus encontrado en el mensaje: $cadena_qmail[1]";
					$query = "UPDATE estadisticas SET virus = virus + 1;";
				}else{
					$estado = $POLITICA_ANTIVIRUS;
					$descripcion = "- Qmail-Scanner informó: Política violada: $cadena_qmail[1]";
					$query = "UPDATE estadisticas SET politicas = politicas + 1;";
				}
			}

			$sth = $dbh->prepare($query);
                	$sth->execute();

			#Borramos la puntuación del spamassassin del hash
			delete $mapa{$id};

                	my $query = "INSERT INTO mensajes (message_id,mfrom,mto,asunto,estado,fecha) VALUES ('$id',".$dbh->quote($emisor).",".$dbh->quote($destinatario).",".$dbh->quote($asunto).",$estado,$fecha) ON DUPLICATE KEY UPDATE asunto=".$dbh->quote($asunto).";";
		        my $sth = $dbh->prepare($query);
		        $sth->execute();

			#Si era un virus, guardamos el nombre del virus
			if($estado eq $VIRUS)
			{
				my $query = "UPDATE mensajes SET virus=".$dbh->quote($cadena_qmail[1])." WHERE message_id='$id';";
                		my $sth = $dbh->prepare($query);
		                $sth->execute();
			}

                	my $query = "UPDATE mensajes SET asunto=".$dbh->quote($asunto)." WHERE message_id='$id';";
	                my $sth = $dbh->prepare($query);
        	        $sth->execute();

              		$query = "INSERT IGNORE INTO historial (message_id,estado,hto,fecha,maquina,descripcion) VALUES ('$id',$estado,".$dbh->quote($destinatario).",$fecha,'$maquina','$descripcion');";
		        $sth = $dbh->prepare($query);
        	        $sth->execute();

                	$sth->finish();

		}	#if destinatario ne <>
	}	#linea
}	#while principal
$dbh->disconnect;
undef %mapa;
undef %meses;
close(IN);
exit;
