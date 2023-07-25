<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$usuario = $_GET['usuario'];
$grid    = $_GET['grid'];
$objeto  = $_GET['objeto'];

DBConectar($GLOBALS["cfg_DataBase"]);
$json='';
$q=DBSelect(utf8_decode("SELECT [json] FROM [Config_arrays] WHERE usuario = '".$usuario."' and grid = '".$grid."' and objeto = '".$objeto."'"));
for(;DBNext($q);)
{	
	$json=(DBCampo($q,("json")));
}
DBFree($q);				
echo (unserialize(utf8_encode($json)));

DBClose();
?>