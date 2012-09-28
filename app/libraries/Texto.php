<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Clase para hacer log con más detalle
 */

class Texto {

        function Texto() {
                $this->CI =& get_instance();
        }


	function corta_texto($texto, $num) {
		$txt = (strlen($texto) > $num) ? substr($texto,0,$num)."..." : $texto;
		return $txt;
	}

        function parsea_texto($mensaje) {
		$mensaje = str_replace("Ã³","ó",$mensaje);
		$mensaje = str_replace("Ã","í",$mensaje);
		$mensaje = str_replace("Â¡","¡",$mensaje);
		$mensaje = str_replace("Âº","º",$mensaje);
		$mensaje = str_replace("í©","é",$mensaje);
		$mensaje = str_replace("?=","",$mensaje);
		$mensaje = str_replace("í±","ñ",$mensaje);
		return strip_tags($mensaje,'<i></i>');
/*
"Ã¡", "á"
"Ã€", "À"
"Ã¤", "ä"
"Ã©", "é"
"Ã¨", "è"
"Ã‰", "É"
"Ãª", "ê"
"Ã¦", "æ"
"Ã*", "í"
"Ã³", "ó"
"Ã“", "Ó"
"Ã¶", "ö"
"Ãº", "ú"
"Ã¼", "ü"
"Ã±", "ñ"
"Ã‘", "Ñ"
"Ã§", "ç"
*/
//		$mensaje = str_replace("=?=","",$mensaje);
//		return html_entity_decode(htmlentities(quoted_printable_decode($mensaje)));		
       }
}


?>
