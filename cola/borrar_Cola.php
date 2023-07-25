<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$numero = $_GET["Id"];

DBConectar($GLOBALS["cfg_DataBase"]);
DBSelect(utf8_decode("UPDATE [Tareas y Proyectos] SET [Cola] = null WHERE [Cola] =  (SELECT [Descripcion] FROM [Colas] WHERE Numero = ".$numero.")
					  UPDATE [Tipos] SET [Cola] = null WHERE [Cola] = (SELECT [Descripcion] FROM [Colas] WHERE Numero = ".$numero.")
					  DELETE FROM [Colas_Usuarios] WHERE Cola = ".$numero." DELETE FROM [Colas] WHERE Numero = ".$numero."	"));

DBClose();

?>
