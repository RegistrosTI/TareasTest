<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$tarea=$_GET['tarea'];

DBConectar($GLOBALS["cfg_DataBase"]);

$insert=DBSelect(utf8_decode("DELETE FROM [Tareas y Proyectos] WHERE Id = ".$tarea." "));
$insert=DBSelect(utf8_decode("DELETE FROM [Adjuntos] WHERE Tarea = ".$tarea." "));
$insert=DBSelect(utf8_decode("DELETE FROM [Comentarios] WHERE Tarea = ".$tarea." "));
$insert=DBSelect(utf8_decode("DELETE FROM [Horas] WHERE Tarea = ".$tarea." "));

DBClose();

echo '';
?>