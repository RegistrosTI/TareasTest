<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$usuario  =$_GET['usuario'];
$parametro=$_GET['parametro'];
$Valor    = '6';
DBConectar($GLOBALS["cfg_DataBase"]);

$query=DBSelect(("SELECT TOP 1 [Valor] FROM [Configuracion] where usuario = '".utf8_decode($usuario)."' AND Parametro = '".utf8_decode($parametro)."'"));
for(;DBNext($query);)
{				
	$Valor=utf8_encode(DBCampo($query,"Valor"));
}
DBFree($query);		
DBClose();

echo $Valor;
?>