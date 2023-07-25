<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


DBConectar($GLOBALS["cfg_DataBase"]);
$Usuario     = $_GET["usuario"];
$Descripcion = '';

$query=DBSelect(utf8_decode("SELECT CASE WHEN (SELECT COUNT(*) as Resultado FROM [Avisos] A INNER JOIN [Tareas y Proyectos] T ON T.Id = A.Tarea AND T.Control <> 1 WHERE A.Usuario = '".$Usuario."') = 0 THEN 0 ELSE 	
						(SELECT CASE WHEN (SELECT COUNT(*) as Resultado FROM [Avisos] A INNER JOIN [Tareas y Proyectos] T ON T.Id = A.Tarea AND T.Control <> 1 WHERE A.Usuario = '".$Usuario."' AND A.Aviso IN (11,12,13)) = 0 THEN 1 ELSE 2 END) END AS Resultado"));
$Descripcion=(DBCampo($query,"Resultado"));			
DBFree($query);		

echo $Descripcion;
DBClose();
?>