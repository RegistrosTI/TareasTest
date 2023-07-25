<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

if ( isset ( $_GET [ 'usuario' ] ) ) {
	$usuario = $_GET [ 'usuario' ];
}
$respuesta = '';

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );






$select = "
		SELECT 
			PRE.[usuario]
			,PRE.[numero]
			,COALESCE(PRE.[tarea],0) AS tarea
			,TAR.[Título] as titulo
			,UPPER(PRE.alias) AS alias 
		FROM [GestionPDT].[dbo].[Tareas_Predefinidas]  AS PRE
		INNER JOIN [GestionPDT].[dbo].[Tareas y Proyectos] AS TAR
			ON PRE.TAREA = TAR.ID
		WHERE PRE.usuario = '$usuario' 
			and PRE.alias <> ''
		ORDER BY pre.NUMERO; ";

$select = DBSelect ( utf8_decode ( $select ) );

for(; DBNext ( $select ) ;) {
	
	$TITULO = utf8_encode ( DBCampo ( $select , "titulo" ) );
	$TAREA = utf8_encode ( DBCampo ( $select , "tarea" ) );
	$ALIAS = utf8_encode ( DBCampo ( $select , "alias" ) );
	
	

	$respuesta = $respuesta . "<div class='resumen_horas_dia float_left ' title='$TAREA - $TITULO' onclick='IniciarFrecuente($TAREA);'>$ALIAS</div>";
}
DBFree ( $select );



echo $respuesta;
DBClose ();
?>