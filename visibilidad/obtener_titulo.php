<?php
// include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_" . curPageName () . ".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$id		= $_POST [ 'id' ];
$titulo	= '';

$q = "SELECT COALESCE([Título],'') as titulo FROM [GestionPDT].[dbo].[Tareas y Proyectos] WHERE ID = $id and control = 0";
$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$titulo = utf8_encode ( DBCampo ( $q , utf8_decode ( "titulo" ) ) );
	// $titulo = utf8_encode(DBCampo($q,"titulo"));
}
DBFree ( $q );

DBClose ();
if ($titulo	== ''){
	echo "$id";
}else{
	echo "$id - $titulo";
}

?>