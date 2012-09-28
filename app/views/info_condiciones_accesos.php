<fieldset style="width: 820px; margin:auto;">
        <legend>Datos de la búsqueda realizada (<?php echo number_format($num_rows); ?> resultados)</legend>

        <p>Mostrando todos los mensajes accesibles por <?php echo $this->session->userdata('uid'); ?> que cumplen las siguientes condiciones de filtrado:</p>
                <?php

                        $query = $this->session->userdata('where_consulta');
                        $path = "<img src='".site_url('img/buttons')."/";
                        $query = str_replace("OR aid < 0","",$query);
                        $query = str_replace("AND aid > 0","",$query);
                        $query = str_replace("aid > 0 AND","",$query);
                        $query = str_replace("aid > 0","Ninguna restricción definida. Tabla completa de accesos.",$query);
                        $query = str_replace("fecha",$path."calendar.png'/> "."Fecha",$query);
                        $query = str_replace("usuario",$path."user.png'/> "."Usuario",$query);
                        $query = str_replace("estado",$path."info.png'/> "."Estado",$query);
                        $query = str_replace("(","",$query);
                        $query = str_replace(")","",$query);
                        $query = str_replace("%","\"",$query);
                        $query = str_replace("\"","'",$query);
                        $query = str_replace(" LIKE "," contiene la palabra ",$query);
                        $query = str_replace(" OR "," Ó ",$query);
                        $query = str_replace(" AND "," Y ",$query);
                        $query = str_replace(" >= "," es mayor o igual que ",$query);
                        $query = str_replace(" <= "," es menor o igual que ",$query);
                        $query = str_replace(" = "," es ",$query);
                        $query = str_replace(" < "," es menor que ",$query);
                        $query = str_replace(" > "," es mayor que ",$query);
                //      $query = preg_replace('/(\d\d\d\d\d\d\d\d\d\d)/i',date("d/m/Y",intval("$1")), $query);
                        echo "<ul class='informe'>";
                        $array = split(" Y ",$query);
                        foreach($array as $val){
                                echo "<li>$val</li>";
                        }
                        echo "</ul>";
                ?>
</fieldset>
<br/><br/>
