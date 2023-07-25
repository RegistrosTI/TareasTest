<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$numero = $_GET["Id"];

DBConectar($GLOBALS["cfg_DataBase"]);
DBSelect(utf8_decode("DELETE FROM [Consultas_Personalizadas] WHERE Numero = ".$numero." "));
DBClose();
?>
