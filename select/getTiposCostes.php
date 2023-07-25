<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);
$Descripcion = '';
$query=DBSelect(("SELECT CAST(Descripcion as varchar(MAX)) as Descripcion,Numero FROM Tipos WHERE Tipo = 10 AND visible = 1 order by Descripcion"));
$Descripcion=utf8_encode(DBCampo($query,"Descripcion"));			
for(;DBNext($query);)
{				
	$Descripcion=$Descripcion.','.utf8_encode(DBCampo($query,"Descripcion"));					
}
DBFree($query);		
DBClose();

echo $Descripcion;
?>