<?php
include_once "./php/funciones.php";
include_once "./conf/config.php";
include_once "./conf/config_".curPageName().".php";
include_once "../soporte/DB.php";
include_once "../soporte/funcionesgenerales.php";

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$q = " SELECT count(usuario) as usuarioActivo FROM [Configuracion_Usuarios] WHERE usuario = '$usuario' AND BAJA = 0 ";
$q = DBSelect ( utf8_decode ( $q ) );
$usuarioActivo = utf8_encode ( DBCampo ( $q , "usuarioActivo" ) );
DBFree ( $q );

$q = " SELECT count(Oficina) as Oficina FROM [Configuracion_Oficinas] WHERE Oficina = '$externo' ";
$q = DBSelect ( utf8_decode ( $q ) );
$existeExterno = utf8_encode ( DBCampo ( $q , "Oficina" ) );
DBFree ( $q );

if($existeExterno == 0){
	$externo = "";
}

DBClose ();
?>