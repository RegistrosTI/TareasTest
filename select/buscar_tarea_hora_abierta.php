<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$usuario = $_GET [ "usuario" ];
$json = false;
if ( isset ( $_GET [ "json" ] ) ) { // Viene de angular
	$json = true;
	$JSON [ ] = array ();
}

$numero_hora = '';
$estado_actual = '';
$tarea_hora = '';
$tarea_encontrada = '';

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$q = DBSelect ( utf8_decode ( "SELECT TOP 1 Numero as NUMERO,CAST(Tarea as varchar(120)) as Tarea,
						'La Tarea ' + CAST(Tarea as varchar(120)) + ' tiene un proceso abierto desde el ' + CONVERT(varchar(20),Inicio,103) + ' a las ' + CONVERT(varchar(20),Inicio,108) as FRASE,
						CASE WHEN Fin IS NULL THEN CASE WHEN DATEADD(dd, 0, DATEDIFF(dd, 0, GETDATE())) <>
						DATEADD(dd, 0, DATEDIFF(dd, 0, Inicio)) THEN 0 ELSE 1 END ELSE -1 END AS ESTADO_ACTUAL
						FROM [Horas] where usuario = '" . utf8_encode ( $usuario ) . "' AND Fin is null" ) );
for(; DBNext ( $q ) ;) {
	$numero_hora = DBCampo ( $q , "NUMERO" );
	$estado_actual = DBCampo ( $q , "ESTADO_ACTUAL" );
	$tarea_hora = utf8_encode ( DBCampo ( $q , utf8_decode ( "FRASE" ) ) );
	$tarea_encontrada = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tarea" ) ) );
}

DBFree ( $q );
DBClose ();

if ( $json == true ) {
	$JSON = array (
			"NUMERO_HORA" => $numero_hora ,
			"ESTADO_ACTUAL" => $estado_actual ,
			"TEXTO" => $tarea_hora ,
			"TAREA_ENCONTRADA" => $tarea_encontrada 
	);
	echo json_encode ( $JSON );
} else {
	echo $numero_hora . '|' . $estado_actual . '|' . $tarea_hora . '|' . $tarea_encontrada;
}
?>