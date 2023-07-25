<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);
$q=DBSelect(utf8_decode("INSERT INTO [Colas] ([Descripcion],[Predeterminado]) VALUES  ('',0)"));	
DBFree($q);				

DBClose();
?>