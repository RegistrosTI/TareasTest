<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);
$q=DBSelect(utf8_decode("INSERT INTO [Tipos] ([Tipo],[Numero],[Descripcion],[Predeterminado],[visible])	SELECT 2,ISNULL(MAX([Numero]),0) + 1,'' ,0,1 FROM [Tipos] where tipo = 2"));	
DBFree($q);				

DBClose();
?>