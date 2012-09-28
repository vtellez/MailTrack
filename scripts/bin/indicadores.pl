#!/usr/bin/perl
#
# Generación de estadísticas por hora/día/semana/mes para Seguimiento
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
use Time::Local;
use POSIX;

$PATH = "/var/www/html/Seguimiento/scripts";
require("$PATH/etc/config.pl");
require("$PATH/bin/funciones.pl");

# Realizamos la conexión a la base de datos
$dbh = DBI->connect($connectionInfo,$userid,$passwd, {mysql_enable_utf8=>1}) or die "Can't connect to the database.\n";

#Mapa para guardar los acumuladores
my %ac = (
		'ham'=>0,
		'spam'=>0,
		'virus'=>0,
		'errormta'=>0,
		'politicas'=>0,
		'interno'=>0,
		'externo'=>0,
		'totales'=>0
	);

#Mapa para guardar los resultados por horas
my %ach = ();

$hace_dias = 1;

$day = strftime "%d", localtime (time-86400*$hace_dias);
$month = strftime "%m", localtime (time-86400*$hace_dias);
$year = strftime "%Y", localtime (time-86400*$hace_dias);

for($i = 0; $i < 24; $i++) {
	my $fechamin = timelocal(0,0,$i,$day,$month-1,$year);
	my $fechamax = timelocal(59,59,$i,$day,$month-1,$year);
	my $tiempo = " fecha > $fechamin and fecha < $fechamax ";

	#Damos de alta para cada hora del día
        my $query = "INSERT IGNORE INTO est_horas (year,month,day,hour) VALUES ($year,$month,$day,$i)";
        my $sth = $dbh->prepare($query);
        $sth->execute();

	#Contamos cuantos mensajes hay de cada caso
	$query = "select count(*) from mensajes where $tiempo AND estado = $VIRUS;";
        $sth = $dbh->prepare($query);
        $sth->execute();
	$contador = $sth->fetchrow;
	$ach{"virus-$i"} = $contador;
	$ac{"virus"} += $contador;
	$sth->finish();

        $query = "select count(*) from mensajes where $tiempo AND estado = $POLITICA_ANTIVIRUS;";
        $sth = $dbh->prepare($query);
        $sth->execute();
        $contador = $sth->fetchrow;
        $ach{"politicas-$i"} = $contador;
        $ac{"politicas"} += $contador;
	$sth->finish();

        $query = "select count(*) from mensajes where $tiempo AND estado = $ERROR_SALIDA;";
        $sth = $dbh->prepare($query);
        $sth->execute();
        $contador = $sth->fetchrow;
        $ach{"errormta-$i"} = $contador;
        $ac{"errormta"} += $contador;
	$sth->finish();

        $query = "select count(*) from mensajes where $tiempo AND (estado=$ENTREGADO_LOCAL or estado=$SPAM_ENTREGADO_LOCAL);";
        $sth = $dbh->prepare($query);
        $sth->execute();
        $contador = $sth->fetchrow;
        $ach{"interno-$i"} = $contador;
        $ac{"interno"} += $contador;
	$sth->finish();

        $query = "select count(*) from mensajes where $tiempo AND (estado=$ENTREGADO_REMOTO or estado=$SPAM_ENTREGADO_REMOTO);";
        $sth = $dbh->prepare($query);
        $sth->execute();
        $contador = $sth->fetchrow;
        $ach{"externo-$i"} = $contador;
        $ac{"externo"} += $contador;
	$sth->finish();

        $query = "select count(*) from mensajes where $tiempo AND (estado=$SPAM_ENTREGADO_LOCAL or estado=$SPAM_ENTREGADO_REMOTO or estado=$SPAM_ENTREGADO_ALIAS or estado=$SPAM_ENTREGADO_REDIRECCION);";
        $sth = $dbh->prepare($query);
        $sth->execute();
        $contador = $sth->fetchrow;
        $ach{"spam-$i"} = $contador;
        $ac{"spam"} += $contador;
	$sth->finish();

        $query = "select count(*) from mensajes where $tiempo AND (estado=$ENTREGADO_LOCAL or estado=$ENTREGADO_REMOTO or estado=$ENTREGADO_ALIAS or estado=$ENTREGADO_REDIRECCION);";
        $sth = $dbh->prepare($query);
        $sth->execute();
        $contador = $sth->fetchrow;
        $ach{"ham-$i"} = $contador;
        $ac{"ham"} += $contador;
	$sth->finish();

        $query = "select count(*) from mensajes where $tiempo;";
        $sth = $dbh->prepare($query);
        $sth->execute();
        $contador = $sth->fetchrow;
        $ach{"totales-$i"} = $contador;
        $ac{"totales"} += $contador;
        $sth->finish();
}

#Damos de alta en la tabla de años
$query = "INSERT IGNORE INTO est_anios (year) VALUES ($year)";
$sth = $dbh->prepare($query);
$sth->execute();

for($i = 1; $i <= &DaysInMonth($month,$year); $i++)
{
	#Damos de alta en la tabla de días
	$query = "INSERT IGNORE INTO est_dias (year,month,day) VALUES ($year,$month,$i)";
	$sth = $dbh->prepare($query);
	$sth->execute();
}

for($i = 1; $i < 13; $i++) 
{
	#Damos de alta en la tabla de meses
	$query = "INSERT IGNORE INTO est_meses (year,month) VALUES ($year,$i)";
	$sth = $dbh->prepare($query);
	$sth->execute();
}

@claves=keys(%ac);
foreach $param (@claves){
	#Recorremos el mapa horario y actualizamos la tabla de est_horas
	for($i = 0; $i < 24; $i++) {
		my $value = $ach{"$param-$i"};
		$query = "update est_horas set $param = $value where year=$year and month=$month and day=$day and hour=$i;";
		$sth = $dbh->prepare($query);
		$sth->execute();
	}
	#Actualizamos la tabla de días
	my $value = $ac{"$param"};
        $query = "update est_dias set $param = $param + $value where year=$year and month=$month and day=$day;";
        $sth = $dbh->prepare($query);
        $sth->execute();
	#Actualizamos la tabla de meses
        $query = "update est_meses set $param = $param + $value where year=$year and month=$month;";
        $sth = $dbh->prepare($query);
        $sth->execute();
	#Actualizamos la tabla de años
        $query = "update est_anios set $param = $param + $value where year=$year;";
        $sth = $dbh->prepare($query);
        $sth->execute();
}

$sth->finish();
$dbh->disconnect;
exit;
