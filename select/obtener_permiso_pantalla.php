<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id     =$_GET['id'];
$usuario=$_GET['usuario'];
$select ='';

DBConectar($GLOBALS["cfg_DataBase"]);

if ($id=='1')
{
	$u_programa=DBSelect(utf8_decode("SELECT COUNT(*) AS RESULTADO FROM [Config_pantallas_usuarios] where Pantalla = ".$id." AND Usuario = '".$usuario."' AND Activa = 1"));
	for(;DBNext($u_programa);)
	{
		$RESULTADO=(DBCampo($u_programa,utf8_decode("RESULTADO")));									
	}	
}
DBFree($u_programa);	
DBClose();
echo $RESULTADO;
?>