<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$Descripcion = '';
DBConectar($GLOBALS["cfg_DataBase"]);

//$query=DBSelect(utf8_decode("SELECT TOP 100 department,sAMAccountName ,Name,mail,Fila,Activo FROM	(SELECT department,sAMAccountName ,Name,mail,ROW_NUMBER() OVER(ORDER BY sAMAccountName) Fila,	CASE WHEN ((2 & userAccountControl) = 2 ) THEN 0 ELSE 1 END AS Activo  FROM OpenQuery(ADSI, 'SELECT department,sAMAccountName,Name,mail,userAccountControl FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user'' AND Name = ''*".$buscar."*''')) Consulta WHERE Activo = 1 order by Name"));
$query = DBSelect(utf8_decode("SELECT Name FROM [GestionIDM].[dbo].[LDAP] ORDER BY Name ASC"));

$Descripcion=utf8_encode(DBCampo($query,"Name"));

for(;DBNext($query);)
{
	$Descripcion=$Descripcion.'|'.utf8_encode(DBCampo($query,"Name"));					
}
DBFree($query);		
DBClose();

$Descripcion=trim($Descripcion,'|');
echo $Descripcion;

?>