<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id          = $_GET["Id"];

$Descripcion = $_GET["Descripcion"];
$Descripcion = strip_tags($Descripcion);
$Descripcion = str_replace("'", "''", ($Descripcion));

$Consulta = $_GET["Consulta"];
$Consulta = strip_tags($Consulta);
$Consulta = str_replace("'", "''", ($Consulta));

$query = "UPDATE [Consultas_Personalizadas] SET [Descripcion] = '$Descripcion' , [Consulta] = '$Consulta' WHERE Numero = $id";

DBConectar($GLOBALS["cfg_DataBase"]);
DBSelect(utf8_decode($query));
DBClose();
?>
