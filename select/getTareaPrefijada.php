<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

if(!isset($_GET [ 'usuario' ])){
	die ( "-1" );
}
if(!isset($_GET [ 'numero' ])){
	die ( "-1" );
}

$usuario = $_GET [ 'usuario' ];
$numero = $_GET [ 'numero' ];

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

// OBTENER ID DE TAREA
$select = " SELECT [tarea] FROM [Tareas_Predefinidas] where usuario = '$usuario' AND numero = $numero ";
$select = DBSelect ( utf8_decode ( $select ) );
$tarea = utf8_encode ( DBCampo ( $select , "tarea" ) );
DBFree ( $select );

DBClose ();

echo $tarea;
?>