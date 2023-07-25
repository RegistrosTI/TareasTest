<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$buscar      = $_GET['buscar'];
$tipo        = $_GET['tipo'];
$Descripcion = '';

DBConectar($GLOBALS["cfg_DataBase"]);

$query = "
		SELECT CAST(id as varchar(100)) + ' - ' + [Titulo] AS Name 
		FROM [GestionPDA].[dbo].[Cabecera_PDA] 
		WHERE [Control] = 0 
		AND (CAST(id as varchar(100)) like '%".$buscar."%' OR [Titulo] like '%".$buscar."%')";

$query=DBSelect(utf8_decode($query));
$Descripcion=utf8_encode(DBCampo($query,"Name"));
for(;DBNext($query);)
{
	$Descripcion=$Descripcion.'|'.utf8_encode(DBCampo($query,"Name"));					
}

DBFree($query);		
DBClose();

$Descripcion=trim($Descripcion,'|');
echo $Descripcion;
?>