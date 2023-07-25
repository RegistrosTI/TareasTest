<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_" . curPageName () . ".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


$tarea = $_POST [ 'tarea' ];
$Preguntas = 0;
DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$q = "
	SELECT COUNT(*) as Preguntas FROM [GestionPDT].[dbo].[Feedback_Preguntas]
	WHERE (Borrada IS NULL OR Borrada <> 'SI')
		AND Tipo = (SELECT TIPO FROM [Tareas y Proyectos] WHERE ID = $tarea)
		AND Oficina = (SELECT Oficina FROM [Tareas y Proyectos] WHERE ID = $tarea)
";
$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$Preguntas = DBCampo ( $q , "Preguntas" );
}
echo $Preguntas;

DBFree ( $q );
DBClose ();
