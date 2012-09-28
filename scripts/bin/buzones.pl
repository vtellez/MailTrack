#!/usr/bin/perl/
#
# Procesamiento de maillog de buzones (Postfix + Dovecot) para Seguimiento de correo
# -------------------------------------------------------------------------------------
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
require("$PATH/bin/funciones.pl");

# Realizamos la conexión a la base de datos
$dbh = DBI->connect($connectionInfo,$userid,$passwd, {mysql_enable_utf8=>1}) or die "Can't connect to the database.\n";

my %mapa=();
my %meses = ('Jan'=>0,'Feb'=>1,'Mar'=>2,'Apr'=>3,'May'=>4,'Jun'=>5,'Jul'=>6,'Aug'=>7,'Sep'=>8,'Oct'=>9,'Nov'=>10,'Dec'=>11);

open(IN,"$PATH/log/temp") or die("No puedo abrir el fichero temp!\n");

while (<IN>) {
	if($_=~/(.+) postfix\/cleanup\[(.+)\]: (.+): message-id=<(.+)>/){
		my $etiqueta = $3;
		my $mid = $4;

		my $key = "$etiqueta-mid";
		$mapa{$key} = $mid;

		my $key = "$mid-eti";
		$mapa{$key} = $etiqueta;
	}
	elsif($_=~/(.+) postfix\/qmgr\[(.+)\]: (.+): from=<(.+)>/){
		my $key = "$3-from";
		$mapa{$key} = $4;
	} #elseif
#	elsif($_=~/(.+) dovecot: lda\((.+)\): sieve: msgid=<(.+)>: sent vacation response to <(.+)>/){
#	} #elseif
#       elsif($_=~/(.+) postfix/smtpd(.+): NOQUEUE: reject: RCPT from unknown[192.168.3.12]: 550 5.1.1 <bellidomgb@us.es>: Recipient address rejected: User unknown in virtual mailbox table; from=<info@llamanosfuturo.es> to=<bellidomgb@us.es>/){
#       } #elseif
#       elsif($_=~/(.+) dovecot: lda\((.+)\): sieve: msgid=<(.+)>: sent vacation response to <(.+)>/){
#       } #elseif
#       elsif($_=~/(.+) dovecot: lda(.+): Error: sieve: msgid=<(.+)>: failed to store into mailbox 'INBOX': Quota exceeded (mailbox for user is full)/){
#       } #elseif
	elsif($_=~/(.+) (.+) dovecot: lda\((.+)\): sieve: msgid=<(.+)>: stored mail into mailbox '(.+)'/){
		my $mid = $4;
		my $usuario = $3;
		$usuario =~s/\@(.+)//;
		my $carpeta = $5;

		my $info = "- Dovecot (sieve-manager) informó: Correo guardado en la carpeta \"$carpeta\" del usuario <i>$usuario</i>";
		my $key = "$mid-$usuario";
		$mapa{$key} = $info;		

	} #elseif
	elsif($_=~/(.+) (.+) postfix.+\[(.+)\]: (.+): to=<(.+?)>(.+) status=(.+) \((.+)\)/){
		@fechaent = split(/\s+/,$1);
		$maquina = $2;
		my $mes_actual = strftime "%m", localtime;
		my $anio_actual = strftime "%Y", localtime;

		my $anio_mensaje = $anio_actual;

		if($meses{$linea[0]} > $mes_actual)
		{
			$anio_mensaje = $anio_actual - 1;
		}

		my @hora =  split(/:/,$fechaent[2]);
		my $fecha = timelocal($hora[2],$hora[1],$hora[0],$fechaent[1],$meses{$fechaent[0]},$anio_mensaje);
		my $mid = $mapa{"$4-mid"};
		my $from = $mapa{"$4-from"};
		my $to = $5;
		my $redireccion = $6;
		my $nuevo_destino = ""; #Guardamos en esta variable la redirección si existe
		my $status = $7;
		my $desc = "- Postfix informó del siguiente estado: $7 ($8)";
		
		#Obtenemos el mensaje de la carpeta destino del manejador sieve
		my $nombre_usuario = lc($to);
		$nombre_usuario =~s/@(.+)//;

		my $key = "$mid-$nombre_usuario";
		my $adicional = $mapa{$key};
		my $query = "";

		$estado = $ENTREGADO_LOCAL;
		#Comprobamos si se está utilizando una redirección de correo
		if($redireccion=~/ orig_to=<(.+?)>/){
			$nuevo_destino = $to;
			$to = $1;
			$estado = $ENTREGADO_REDIRECCION;
			$adicional = "- Postfix/smtp informó: El destinatario utiliza una redirección desde la cuenta <i>$to</i> a la cuenta <i>$nuevo_destino</i>.";
			
			 #Incrementamos el número de correos redirigidos
                        $query = "UPDATE estadisticas SET redirecciones = redirecciones + 1;";
                        $sth = $dbh->prepare($query);
                        $sth->execute();

		}
                if($status ne 'sent')
                {
                        $estado = $ERROR_BUZONES;
                }

		#Miramos si ya existia el mensaje en la BBDD
		$mid =~s/AAAAAA==@(.+)//g; # Esto lo hacemos pq postfix añade este sufijo a ciertos message_id
		my $midcadena = "%".$mid."%";
		$query = "SELECT message_id,estado FROM mensajes WHERE message_id LIKE '$midcadena' AND mto='$to';";
		$sth = $dbh->prepare($query);
		$sth->execute;
		my @row;

#Sino se encontró ningún mensaje, vamos a buscar uno parecido teniendo en cuenta 
# que postfix, modifica algunos message_id a su antojo
                if (! $sth->rows )
                {
			#No encontró el mensaje, hacemos un cruce temporal
                        $fechainf = $fecha - 500;
                        $fechasup = $fecha + 100;
                        $query = "SELECT message_id,estado FROM mensajes WHERE mfrom='$from' AND mto='$to' AND fecha > $fechainf AND fecha < $fechasup AND estado < 300 ORDER BY fecha DESC;";
		#print "$query\n";
                        $sth = $dbh->prepare($query);
                        $sth->execute;
			@row = $sth->fetchrow_array(  );
		}

		if ( $sth->rows )
		{
			@row = $sth->fetchrow_array(  );

			my $estado_actual = $row[1];
			$mid = $row[0];
			$estado = calcula_estado($estado,$estado_actual);
			
			$query = "UPDATE mensajes SET estado=$estado, redirect='$nuevo_destino' WHERE mto='$to' AND message_id='$mid';";
			$sth = $dbh->prepare($query);
			$sth->execute();
		}
		else
		{
			#No existía, lo añadimos
			$asunto = "<i>Mensaje procesado en $maquina</i>";
			$query = "INSERT INTO mensajes (message_id,mfrom,mto,redirect,asunto,estado,fecha) VALUES ('$mid','$from','$to','$nuevo_destino',\"$asunto\",$estado,$fecha) ON DUPLICATE KEY UPDATE estado = $estado;";

			$sth = $dbh->prepare($query);
			$sth->execute();

                        #Incrementamos el número de correos procesados
                        $query = "UPDATE estadisticas SET procesados = procesados + 1;";
                        $sth = $dbh->prepare($query);
                        $sth->execute();
		}

		#En cualquier caso, actualizamos el historial del mensaje
		$query = "INSERT IGNORE INTO historial (message_id,estado,hto,fecha,maquina,descripcion,adicional) VALUES ('$mid',$estado,'$to',$fecha,'$maquina','$desc','$adicional');";
		$sth = $dbh->prepare($query);
		$sth->execute();

		#Incrementamos el numero de correos para dominios propios si el estado fue entregado local
		if ($estado eq $ENTREGADO_LOCAL)
		{
			#Incrementamos el número de correos redirigidos a alias o dom. virtuales
                        my $query = "UPDATE estadisticas SET interno = interno + 1;";
                        $sth = $dbh->prepare($query);
                        $sth->execute();
		}

	} #elseif

}	#while principal

$dbh->disconnect;
undef %mapa;
undef %meses;
close(IN);
exit;
