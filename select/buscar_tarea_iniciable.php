<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$tarea = $_GET [ "tarea" ];
$estado_actual = '';
$Asignado = '';

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$q = "
  	SELECT 
  		(SELECT ISNULL([Imputable],0) 
		 FROM [Tipos] 
		 WHERE tipo = 5 
			AND Descripcion = [Estado] 
			AND Oficina LIKE TAR.Oficina) AS Estado 
		,CAST(TAR.[Asignado a] AS VARCHAR(256)) AS Asignado
	FROM [Tareas y Proyectos] AS TAR
	WHERE TAR.Id = $tarea ";

$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$estado_actual = DBCampo ( $q , "Estado" );
	$Asignado = DBCampo ( $q , "Asignado" );
}

DBFree ( $q );
DBClose ();

//******* respuesta **********
if ($estado_actual == '1'){
	echo 1;	
}else{
	if($Asignado == ''){
		echo 2;
	}else{
		echo 0;
	}
}
//******* respuesta **********
?>