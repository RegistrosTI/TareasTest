<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$usuario=$_GET['usuario'];
$marcada=$_GET['marcada'];

DBConectar($GLOBALS["cfg_DataBase"]);

$q=DBSelect(utf8_decode("DELETE FROM [Historico] WHERE usuario = '".$usuario."' AND Marcada = ".$marcada." "));
DBFree($q);				
DBClose();
?>