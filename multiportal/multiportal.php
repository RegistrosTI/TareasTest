<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$tool                = array();
$contador            = 0;
$multiportal_tipo    = 'MENU';
$multiportal_ventana = curPageName();

DBConectar($GLOBALS["cfg_DataBase"]);
include_once "../visibilidad/visibilidad_config.php";
DBClose();

$php_json = json_encode($opciones_ocultar);
echo $php_json;
?>