<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


$valoracion   =$_GET['valoracion'];
$usuario      =$_GET['usuario'];	
$nota         =$_GET['nota'];
$envio        =$_GET['envio'];	
$observaciones=$_POST['observaciones'];	
$opcion       =$_POST['opcion'];	
	
DBConectar($GLOBALS["cfg_DataBase"]);
$q=DBSelect(utf8_decode("EXECUTE [INICIAR_VALORACION] '".utf8_encode($usuario)."',".$envio.",".$valoracion.",".$nota.",'".utf8_encode($observaciones)."' "));
DBFree($q);				
DBClose();

echo $opcion;
?>