<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id = $_GET["Id"];

DBConectar($GLOBALS["cfg_DataBase"]);
$q=DBSelect(utf8_decode("INSERT INTO [Colas_Usuarios] ([Cola],[Usuario]) VALUES  (".$id.",'')"));	
DBFree($q);				
DBClose();
?>