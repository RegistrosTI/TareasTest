<?php
//UTF8 pìcâñá
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);

$Usuario     = $_GET["usuario"];
$Tarea = "";

$query = "
	SELECT ISNULL(Horas.Tarea,-1) AS TAREA 
	FROM [Tareas y Proyectos] AS Tareas 
		INNER JOIN [Horas] Horas 
			ON Horas.Tarea = Tareas.Id 
			AND Horas.Fin is null AND Horas.Usuario = '$Usuario' ";

$query=DBSelect(utf8_decode($query));
$Tarea=(DBCampo($query,"TAREA"));			

DBFree($query);		
DBClose();

echo $Tarea;
?>