<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );
$numero = $_GET [ "lista_personalizada" ];
$Descripcion = '';

$query = "SELECT [Descripcion] FROM [GestionPDT].[dbo].[Consultas_Personalizadas] WHERE [Numero] IN ($numero)";
$query = DBSelect ( utf8_decode ( $query ) );

for(; DBNext ( $query ) ;) {
	if ($Descripcion == ''){
		$Descripcion = $Descripcion . utf8_encode ( DBCampo ( $query , "Descripcion" ) );
	}else{
		$Descripcion = $Descripcion .  ', '  . utf8_encode ( DBCampo ( $query , "Descripcion" ) );
	}
}

DBFree ( $query );
DBClose ();

echo strtolower($Descripcion);
?>