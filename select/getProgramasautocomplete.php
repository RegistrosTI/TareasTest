<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


$buscar       = $_GET['buscar'];
$departamento = $_GET['departamento'];
$Descripcion  = '';

DBConectar($GLOBALS["cfg_DataBase"]);
$query=DBSelect(utf8_decode("SELECT Departamento,Programa,Nombre_Departamento,Nombre_Programa FROM OPENQUERY (NavisionSQL,'SELECT Relacion.Departamento,Relacion.Programa,	Departamento.Nombre AS Nombre_Departamento,		Programa.Nombre AS Nombre_Programa FROM [NAVSQL].[dbo].[Relación dpto_ y prog_] Relacion INNER JOIN Departamento Departamento ON Departamento.Código = Relacion.Departamento INNER JOIN 		Programa Programa ON Programa.Código = Relacion.Programa WHERE Relacion.Bloqueado = 0 AND Relacion.Departamento = ''".$departamento."'' AND Relacion.Programa like ''%".$buscar."%''') Consulta"));
$Descripcion=utf8_encode(DBCampo($query,"Programa"));
for(;DBNext($query);)
{
	$Descripcion=$Descripcion.'|'.utf8_encode(DBCampo($query,"Programa"));					
}
DBFree($query);		
DBClose();
$Descripcion=trim($Descripcion,'|');
echo $Descripcion;
?>