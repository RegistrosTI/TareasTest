<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$asignadoa	= $_GET [ 'asignadoa' ];
$tarea		= $_GET [ 'tarea' ];
$Evaluaciones = 0;
DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$query = "
	SELECT COUNT(Id) AS Evaluaciones FROM Evaluaciones
	WHERE Tarea = $tarea
		AND Usuario_Nombre = '$asignadoa'
		AND PERIODO = (SELECT Evaluacion 
						FROM Configuracion_Oficinas AS O 
							INNER JOIN [Tareas y Proyectos] AS T ON O.Oficina = T.Oficina AND T.Id = $tarea)		
		";
//die($query);
$query = DBSelect ( utf8_decode( $query ) );

for(; DBNext ( $query ) ;) {
	$Evaluaciones = utf8_encode ( DBCampo ( $query , "Evaluaciones" ) );
}
DBFree ( $query );
DBClose ();

echo $Evaluaciones;
?>