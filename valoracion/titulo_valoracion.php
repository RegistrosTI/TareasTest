<?php
if(file_exists("./conf/config.php"))
{		
	include_once "./php/funciones.php";
	include_once "./conf/config.php";
	include_once "./conf/config_$portal.php";
	include_once "./../soporte/DB.php";
	include_once "./../soporte/funcionesgenerales.php";	
}
else
{
	include_once "../php/funciones.php";
	include_once "../conf/config.php";
	include_once "../conf/config_$portal.php";
	include_once "../../soporte/DB.php";
	include_once "../../soporte/funcionesgenerales.php";	
}

$envio      = $_GET['envio'];
$contador   = 0;
$Descripcion= '';

DBConectar($GLOBALS["cfg_DataBase"]);

$insert=DBSelect(utf8_decode("SELECT TOP 1 CAST(Tareas.Título    as varchar(MAX)) as TITULO,Mail.Tarea AS TAREA	FROM [Mail] Mail INNER JOIN [Tareas y Proyectos] Tareas ON Tareas.Id = Mail.Tarea where Mail.Tipo = 0 AND Mail.Envio = ".$envio." "));
	
$TITULO      = (DBCampo($insert,"TITULO"));
$TAREA       = (DBCampo($insert,"TAREA"));
$Descripcion = '<div style="clear: both;"  class="div_valoracion_plegador" id="PLEGADOR_VALORACION_-1">'.$TAREA.' - '.$TITULO.'</div>';
			
echo $Descripcion;

DBClose();
?>