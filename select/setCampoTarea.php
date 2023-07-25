<?php
//include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


if(isset($_GET [ "Tarea" ])){
	$Tarea = $_GET [ "Tarea" ];
}else{
	die('NO EXISTE TAREA');
}

if(isset($_GET [ "Campo" ])){
	$Campo = $_GET [ "Campo" ];
}else{
	die('NO EXISTE CAMPO');
}

if(isset($_GET [ "Valor" ])){
	$Valor = $_GET [ "Valor" ];
}else{
	die('NO EXISTE VALOR');
}

$Usuario = $_GET [ "usuario" ];
DBConectar ( $GLOBALS [ "cfg_DataBase" ] );


$update = " UPDATE [GestionPDT].[dbo].[Tareas y Proyectos] SET [$Campo] = '$Valor' WHERE Id = $Tarea ";

$update = DBSelect ( utf8_decode ( $update ) );
DBFree ( $update );

DBClose ();
?>
