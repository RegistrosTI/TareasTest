<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_" . curPageName () . ".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$usuario = $_GET ["usuario"];

$query = "
	INSERT INTO [Consultas_Personalizadas] 
		   ([Usuario],[Descripcion],[Consulta]) 
	VALUES ('" . utf8_decode ( $usuario ) . "','Ejemplo','Id between 999 AND 1200')
";
DBConectar ( $GLOBALS ["cfg_DataBase"] );
$query = DBSelect ( utf8_decode ($query) );
DBFree ( $query );

DBClose ();
?>