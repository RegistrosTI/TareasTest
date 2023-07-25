<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);
$contador = 0;
$Usuario  = $_GET["usuario"];
$Banner   = $_GET["banner"];

$query=DBSelect(utf8_decode("INSERT INTO [Noticias_Leidas] ([Numero],[Usuario]) VALUES (".$Banner.",'".$Usuario."')"));
DBFree($query);	
	
DBClose();
?>