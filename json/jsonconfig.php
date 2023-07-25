<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$myJsonString = $_POST['myJsonString'];	
$usuario      = $_POST['usuario'];
$grid         = $_POST['grid'];
$objeto       = $_POST['objeto'];

DBConectar($GLOBALS["cfg_DataBase"]);
$insert=serialize($myJsonString);
$q=DBSelect(utf8_decode("DELETE FROM [Config_arrays] WHERE usuario = '".$usuario."' and grid = '".$grid."' and objeto = '".$objeto."'	
						 INSERT INTO [Config_arrays] ([usuario],[grid],[objeto],[json]) VALUES ('".$usuario."','".$grid."','".$objeto."','".$insert."')"));		
DBFree($q);				
DBClose();
?>