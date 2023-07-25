<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$usuario=$_GET['usuario'];

DBConectar($GLOBALS["cfg_DataBase"]);
$q=DBSelect(utf8_decode("DELETE FROM [Avisos] WHERE Usuario = '".$usuario."' "));
		
DBFree($q);				
DBClose();
?>