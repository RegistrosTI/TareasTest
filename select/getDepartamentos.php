<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);

$Descripcion = '';
$query=DBSelect(utf8_decode("SELECT Código as Codigo,Nombre,Bloqueado,Fila FROM (SELECT *,ROW_NUMBER() OVER(ORDER BY Código) Fila FROM OPENQUERY (NavisionSQL,'SELECT * FROM [NAVSQL].[dbo].[Departamento]')) Consulta"));
$Descripcion=utf8_encode(DBCampo($query,"Codigo"));
for(;DBNext($query);)
{
	$Descripcion=$Descripcion.','.utf8_encode(DBCampo($query,"Codigo"));					
}

DBFree($query);		
DBClose();

echo $Descripcion;
?>