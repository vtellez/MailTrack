#!/usr/bin/perl/
#
# Funciones auxiliares para trabajar con la BBDD
# --------------------------------------------------------------
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

sub calcula_estado()
{

# Función auxiliar que dado un nuevo estado y un estado actual calcula cual
# es el siguiente estado que puede alcanzarse

	my ($nuevo,$actual)=@_;

#casos de la estafeta de salida

	if($nuevo eq $ENTREGADO_REMOTO)
	{
		if(($actual eq $ENCOLADO_SPAM_ANTIVIRUS) || ($actual eq $ENCOLADO_SPAM_SALIDA))
		{
			$nuevo = $SPAM_ENTREGADO_REMOTO;
		}
	}
        if($nuevo eq $ENTREGADO_ALIAS)
        {
                if(($actual eq $ENCOLADO_SPAM_ANTIVIRUS) || ($actual eq $ENCOLADO_SPAM_SALIDA))
                {
                        $nuevo = $SPAM_ENTREGADO_ALIAS;
                }
        }
	elsif($nuevo eq $ENCOLADO_SALIDA)
	{
		if(($actual eq $ENCOLADO_SPAM_ANTIVIRUS) || ($actual eq $ENCOLADO_SPAM_SALIDA))
		{
			$nuevo = $ENCOLADO_SPAM_SALIDA;
		}
	}
	elsif($nuevo eq $ENCOLADO_BUZONES)
	{
		if(($actual eq $ENCOLADO_SPAM_ANTIVIRUS) || ($actual eq $ENCOLADO_SPAM_SALIDA))
		{
			$nuevo = $ENCOLADO_SPAM_BUZONES;
		}
	}

#casos de las estafetas de buzones

	elsif($nuevo eq $ENTREGADO_LOCAL)
	{
		if(($actual eq $ENCOLADO_SPAM_ANTIVIRUS) || ($actual eq $ENCOLADO_SPAM_SALIDA) || ($actual eq $ENCOLADO_SPAM_BUZONES))
		{
			$nuevo = $SPAM_ENTREGADO_LOCAL;
		}
	}
	elsif($nuevo eq $ENTREGADO_REDIRECCION)
        {
                if(($actual eq $ENCOLADO_SPAM_ANTIVIRUS) || ($actual eq $ENCOLADO_SPAM_SALIDA) || ($actual eq $ENCOLADO_SPAM_BUZONES))
                {
                        $nuevo = $SPAM_ENTREGADO_REDIRECCION;
                }
        }	


#Caso genérico
	elsif($actual > $nuevo)
	{
		# Un estado nuevo nunca será menor que el estado actual
		$nuevo = $estado_actual;
	}
	
	return $nuevo;
}




sub DaysInMonth {
   my $month = shift;
   my $year = shift; # need full year, i.e. 03 is _not_ sufficient
   my @month_length = (0,31,28,31,30,31,30,31,31,30,31,30,31);

   # only special month is February
   return $month_length[$month] unless $month == 2;

   # February has always 28 days, except if the year is
   # dividable by 4 (without remainder). Exceptions are
   # full centuries

   return 29 if $year % 4 == 0 and $year % 100 != 0;
   return 29 if $year % 400 == 0;
   return 28;
}



 
1;
