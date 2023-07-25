<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id     = $_GET["Id"];
$Titulo = $_GET["Titulo"];

DBConectar($GLOBALS["cfg_DataBase"]);
DBSelect(utf8_decode("UPDATE [Colas_Usuarios] SET [Usuario] = '".($Titulo)."' WHERE Numero = ".$id." "));

DBClose();
?>
