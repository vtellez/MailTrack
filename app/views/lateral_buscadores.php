<div id="sidebar-wrapper">
	<div id="sidebar">
 <?php if($this->controlacceso->permisoAdministracion()) { ?>
		<div class="box nobborder">
				<div class="box-title">Uso del buscador</div>
				<p><br/>Como administrador, puede elegir si desea buscar mensajes o bien buscar accesos de cualquier usuario.</p>
				<p> Debe saber que toda consulta que no implique a su usuario quedará logueada en el sistema.</p>
<?php } else { ?>
                <div class="box">
			<div class="box-content">
				<div class="box-title">Uso del buscador</div>
                                                        <p><br/>Para buscar en su lista de mensajes, debe tener en cuenta que sólo se filtrará por los campos en los que se especifique un valor, en caso de no rellenar dicho campo se considera que dicho parámetro puede tomar cualquier valor.</p>
                                                        <p>Si desea leer una descripción más detallada del uso del buscador, puede consultar la <a href="<?php echo site_url('ayuda#4'); ?>">sección de ayuda</a>.
                                        </div>
<?php } ?>
					</div>

				</div>
			</div>
			<div class="clearer">&nbsp;</div>
		</div>
	</div>
</div>
