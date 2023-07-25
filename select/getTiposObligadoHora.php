<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


if (  isset ( $_GET [ 'tipo' ] ) ) {
	$TIPO = $_GET [ 'tipo' ];
}else{
	die(2);
}

if (  isset ( $_GET [ 'selecttipo' ] ) ) {
	$SELECTTIPO = $_GET [ 'selecttipo' ];
}else{
	die(3);
}

if (  isset ( $_GET [ 'oficina' ] ) ) {
	$OFICINA = $_GET [ 'oficina' ];
}else{
	die(4);
}
	

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$ObligadoHoras = 0;
$query = "SELECT  [ObligadoHoras] FROM [GestionPDT].[dbo].[Tipos] WHERE TIPO = $TIPO AND Oficina = '$OFICINA' and Descripcion = '$SELECTTIPO' ";
$query = DBSelect ( utf8_decode( $query ) );
for(; DBNext ( $query ) ;) {
	$ObligadoHoras = utf8_encode ( DBCampo ( $query , "ObligadoHoras" ) );
}

DBFree ( $query );
DBClose ();

echo $ObligadoHoras;
	

?>