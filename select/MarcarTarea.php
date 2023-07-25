<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id         =$_GET['id'];
$usuario    =$_GET['usuario'];
$guardar_log='';

DBConectar($GLOBALS["cfg_DataBase"]);

$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_MARCAR_HISTORICO] '".$usuario."',".$id." "));

DBClose();
echo '';
?>