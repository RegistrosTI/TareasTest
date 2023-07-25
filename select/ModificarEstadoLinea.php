<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id     =$_GET['id'];
$usuario=$_GET['usuario'];

DBConectar($GLOBALS["cfg_DataBase"]);

$res=DBSelect(utf8_decode("SELECT CASE [Activo] WHEN 0 THEN 1 ELSE 0 END AS RESULTADO  FROM [Configuracion_Avisos]  WHERE USuario = '".$usuario."' and Id = ".$id.""));
for(;DBNext($res);)
{
	$RESULTADO=DBCampo($res,"RESULTADO");	
}
	
$insert=DBSelect(utf8_decode("UPDATE [Configuracion_Avisos] SET [Activo] = ".$RESULTADO." WHERE USuario = '".$usuario."' and Id = ".$id.""));

DBClose();
?>