<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id = $_GET [ "Id" ];
$AporteOrganizacion	= $_GET [ "AporteOrganizacion" ];
$AporteEmpresa 		= $_GET [ "AporteEmpresa" ];
$AporteJefe 		= $_GET [ "AporteJefe" ];
if($AporteOrganizacion != '-' && $AporteEmpresa != '-'){
	$AporteOrganizacion = '-';
	$AporteEmpresa = '-';
} 
DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$query = "
	UPDATE ActividadesUsuario 
	SET 
		AporteOrganizacion = '$AporteOrganizacion'
		,AporteEmpresa = '$AporteEmpresa'
		,AporteJefe = '$AporteJefe'
	WHERE Id = $id";
$query=DBSelect(utf8_decode($query));
DBFree($query);
DBClose ();

?>
