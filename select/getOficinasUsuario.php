<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);

$usuario = $_GET [ "usuario" ];

$Descripcion = '';
$query = "SELECT oficina FROM Configuracion_Usuarios WHERE usuario = '$usuario'";
$query = DBSelect ( utf8_decode ( $query ) );
for(; DBNext ( $query ) ;) {
	$Descripcion = utf8_encode ( DBCampo ( $query , "oficina" ) );
}
DBFree ( $query );

DBClose();	

echo $Descripcion;
?>