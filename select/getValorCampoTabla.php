<?php
/***
 * @author 20/09/2017 Alberto 
 * @desc Obtener cualquier valor de un campo de una tabla
 * @param Id, ,Tabla, Campo
 * @return Valor
 */
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$Tabla = "[" . $_POST [ 'Tabla' ] . "]";
$Campo = "[" . $_POST [ 'Campo' ] . "]";
$Id    = $_POST [ 'Id' ];

$Indice = 'Id';

$Valor = 'NULL';

$query = "SELECT COALESCE($Campo,'NULL') AS Valor FROM $Tabla WHERE $Indice = $Id";
//die($query);
$query = DBSelect ( ( $query ) );

for(; DBNext ( $query ) ;) {
	$Valor = utf8_encode ( DBCampo ( $query , "Valor" ) );
}

DBFree ( $query );
DBClose ();

echo $Valor;
?>