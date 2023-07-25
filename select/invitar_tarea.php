<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$tarea          =$_GET['tarea'];
$usuario_destino=$_GET['usuario_destino'];
$usuario_origen =$_GET['usuario_origen'];
$guardar_log    ='';

DBConectar($GLOBALS["cfg_DataBase"]);

$insert=DBSelect(utf8_decode("DELETE FROM [Avisos] WHERE Aviso = 8 AND Tarea = ".$tarea." AND Usuario = '".$usuario_destino."'
						INSERT INTO [Avisos] ([Tarea],[Usuario],[Aviso],[Descripcion],[Tipo],[Usuario Realiza]) VALUES (".$tarea.",'".$usuario_destino."',8,'Has sido invitado a participar en la Tarea',3 ,'".$usuario_origen."' )		"));

DBClose();
echo '';
?>