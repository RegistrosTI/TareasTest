<!-- 
	<li><a href='#' onClick='cambia_menu(7)'>CONFIGURACION</a></li>
	<li><a href="#" onclick="cambia_menu(11)">TAREAS FRECUENTES</a></li>
	<li><a href="#" onclick="cambia_menu(8)">INFORMES</a></li>
	<li><a href="#" onclick="cambia_menu(20)">PLANIFICADOR</a></li>
 -->
<?php
DBConectar($GLOBALS["cfg_DataBase"]);
$select ="
	SELECT 
		PRE.numero		AS Numero
		,PRE.tarea		AS Tarea
		,TAR.[Título]	AS Titulo
	FROM [Tareas_Predefinidas] AS PRE
	inner join [Tareas y Proyectos] AS TAR
		on pre.tarea = tar.id
	where pre.usuario = '$usuario' 
		and tarea is not null 
	order by numero		
";
$select = DBSelect ( utf8_decode ( $select ) );


for(;DBNext($select);)
{	
	$NUMERO = /*utf8_encode*/ ( DBCampo ( $select , "Numero" ) );
	$TITULO = /*utf8_encode*/ ( DBCampo ( $select , "Titulo" ) );
	
	echo "<li><a href='#' onClick='IrATareaPrefijada($NUMERO)'>$NUMERO - $TITULO</a></li>";
}


DBFree($select);	
DBClose();
?>