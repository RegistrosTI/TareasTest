<?php
include_once "./php/funciones.php";
include_once "./conf/config.php";
include_once "./conf/config_" . curPageName () . ".php";
include_once "../soporte/DB.php";
include_once "../soporte/funcionesgenerales.php";

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );
$Numero_Grid = '-1';

/*
$query = "
		SELECT
			Numero as Numero
		FROM Tipos
		WHERE Tipo = 101 and Descripcion = '" . utf8_decode ( $departamento_usuario_validado ) . "'";
*/

$query = "
		SELECT usuario
		FROM	Configuracion_Usuarios
		WHERE	Usuario = '" . /*utf8_decode*/ ( $usuario ) . "'";

$query = DBSelect ( ( $query ) );
for(; DBNext ( $query ) ;) {
	$Numero_Grid = "1";
}

if($externo != ''){
	$Numero_Grid = "-1";
}

DBFree ( $query );
DBClose ();
?>