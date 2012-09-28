#!/usr/bin/perl
#
# Borrado de los mensajes y accesos antiguos de la BBDD de seguimiento
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

$PATH = "/var/www/html/Seguimiento/scripts";

require("$PATH/etc/config.pl");

# Realizamos la conexión a la base de datos
$dbh = DBI->connect($connectionInfo,$userid,$passwd, {mysql_enable_utf8=>1}) or die "Can't connect to the database.\n";

my $hoy = time();

# $dias_activos definidos en el fichero config.pl

my $tiempo_vida = $dias_activos * 86400;
my $fecha = $hoy - $tiempo_vida;

my $query = "DELETE FROM mensajes WHERE fecha < $fecha;";
my $sth = $dbh->prepare($query);
$sth->execute();
$sth->finish();

my $query = "DELETE FROM accesos WHERE fecha < $fecha;";
my $sth = $dbh->prepare($query);
$sth->execute();
$sth->finish();

my $query = "OPTIMIZE TABLE accesos;";
my $sth = $dbh->prepare($query);
$sth->execute();
$sth->finish();

my $query = "OPTIMIZE TABLE mensajes;";
my $sth = $dbh->prepare($query);
$sth->execute();
$sth->finish();

my $query = "OPTIMIZE TABLE historial;";
my $sth = $dbh->prepare($query);
$sth->execute();
$sth->finish();

#Indicamos que hemos acabado y desconectamos
$dbh->disconnect;
exit;
