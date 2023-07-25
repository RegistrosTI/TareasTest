<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id        = $_GET["tarea"];
$numero    = $_GET["id"];
$resultado = '';

DBConectar($GLOBALS["cfg_DataBase"]);
$insert=DBSelect(utf8_decode("DELETE FROM [Adjuntos] WHERE Tarea = ".$id." AND Numero = ".$numero));
DBFree($insert);	
$insert=DBSelect(utf8_decode("DELETE FROM [Palabras] WHERE Tarea = ".$id." AND Campo = 5 AND Numero = ".$numero));
DBFree($insert);	

DBClose();
echo $resultado;
?>