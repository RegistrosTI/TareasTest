<?php

//echo 0;
//die();
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$tarea = $_POST [ 'tarea' ];
$control = 0;
DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$query = "SELECT [Control] FROM [GestionPDT].[dbo].[Tareas y Proyectos] where id = $tarea";
$query = DBSelect ( utf8_decode ( $query ) );
$control = ( DBCampo ( $query , "Control" ) );

DBFree ( $query );
DBClose ();
echo $control;
?>