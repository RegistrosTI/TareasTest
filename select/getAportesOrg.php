<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);

$Descripcion = 'N/A,Bajo,Medio,Alto';
/*
$t=DBSelect(("SELECT Descripcion FROM Colas order by Descripcion"));
$Descripcion=utf8_encode(DBCampo($t,"Descripcion"));			
for(;DBNext($t);)
{				
	$Descripcion=$Descripcion.','.utf8_encode(DBCampo($t,"Descripcion"));					
}

DBFree($t);
DBClose();*/

echo $Descripcion;
?>