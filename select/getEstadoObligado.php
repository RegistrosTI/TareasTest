<?php
// UTF ñáç
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

if(isset($_POST["oficina"])){
	$oficina = $_POST["oficina"];
}

if(isset($_POST["estado"])){
	$estado = $_POST["estado"];
}

$comprobacion = "SELECT Finalizado as f FROM [GestionPDT].[dbo].[Tipos] WHERE Tipo = 5 AND Oficina ='".$oficina."' AND Descripcion ='".$estado."'";
$consulta = DBSelect ( utf8_decode ( $comprobacion ) );


$a=utf8_encode(DBCampo($consulta, "f" ));
echo $a;

DBClose ();
?>