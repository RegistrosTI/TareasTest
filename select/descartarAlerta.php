<?php
// UTF ñáç
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$id = $_POST["id"];
$postponer = $_POST["postponer"];

if($postponer == 0){
	$update ="UPDATE Alertas SET Leido = 'SI' WHERE Id = $id";
}else{
	$update ="UPDATE Alertas SET FechaPostponer = DATEADD(DAY,1,GETDATE()) WHERE Id = $id";
}

DBSelect ( utf8_decode ( $update ) );

DBClose ();
?>