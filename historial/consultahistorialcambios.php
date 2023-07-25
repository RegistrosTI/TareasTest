<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


$id     = $_GET["tarea"];
$fecha  = $_GET["fecha"];
$tool   = array();
$Campo  = '';

DBConectar($GLOBALS["cfg_DataBase"]);
$q=DBSelect(utf8_decode("EXECUTE [CAMPOS_CAMBIADOS] ".$id.",".$fecha));
for(;DBNext($q);)
{				
	$Campo=$Campo.utf8_encode(DBCampo($q,"Campo")).'<br>';		
}
DBFree($q);	
DBClose();

echo '<div align="center" style="overflow:visible;clear: both; font-size:10px;">'.$Campo.'</div>';
?>