<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


$buscar      = $_GET['buscar'];
$tipo        = $_GET['tipo'];// 1 Devuelve el nombre. 2 Devuelve el nombre y el usuario
$oficinas	 = $_GET['oficinas'];
$oficinaTarea	 = $_GET['oficinaTarea'];
$Descripcion = '';
$externo	 = $_GET['externo'];





DBConectar($GLOBALS["cfg_DataBase"]);

if($oficinaTarea == '' && false){
	
	$oficinas = implode ("," , $oficinas);
	$oficinas = str_replace(",","','", $oficinas);
	if($externo != ''){
		$oficinas = $externo;
	}
	
	
	$query = "
	SELECT department,sAMAccountName ,Name,mail,Fila,Activo ,USU.Oficina
	 FROM (
		SELECT TOP 100 department,sAMAccountName ,Name,mail,Fila,Activo 
		FROM	(	SELECT 
						department
						,sAMAccountName 
						,Name
						,mail
						,ROW_NUMBER() OVER(ORDER BY sAMAccountName) Fila
						,CASE WHEN ((2 & userAccountControl) = 2 ) THEN 0 ELSE 1 END AS Activo  
					FROM OpenQuery(ADSI, 'SELECT department,sAMAccountName,Name,mail,userAccountControl FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user'' AND objectClass=''user''  AND Name = ''*".$buscar."*''') AS DOM
				
					) AS Consulta 
	
		WHERE Activo = 1 order by Name ) AS TODO
	LEFT JOIN Configuracion_Usuarios AS USU
	ON TODO.sAMAccountName = USU.Usuario
	WHERE USU.Oficina IN ('$oficinas')		
	";
}else{
	
	$query ="
		SELECT LDAP.Name, LDAP.sAMAccountName
		FROM GestionIDM.dbo.LDAP AS LDAP 
		LEFT JOIN Configuracion_Usuarios AS USU
		ON LDAP.sAMAccountName = USU.Usuario
		WHERE  LDAP.Name like '%$buscar%' AND '$oficinaTarea' IN (SELECT NOMBRE FROM [dbo].[StringSplit] (  (select  TOP 1 Oficina from Configuracion_Usuarios WHERE Usuario = USU.Usuario )  ,','  ,100))
			AND USU.Baja = 0
	";
}
//die($query);
$query=DBSelect(utf8_decode($query));


if ($tipo==1)
{
	$Descripcion=utf8_encode(DBCampo($query,"Name"));
}
if ($tipo==2)
{
	$Descripcion=utf8_encode(DBCampo($query,"Name")).' - ('.utf8_encode(DBCampo($query,"sAMAccountName")). ')';
}
for(;DBNext($query);)
{
	if ($tipo==1)
	{
		$Descripcion=$Descripcion.'|'.utf8_encode(DBCampo($query,"Name"));					
	}
	else
	{
		$Descripcion=$Descripcion.'|'.utf8_encode(DBCampo($query,"Name")).' - ('.utf8_encode(DBCampo($query,"sAMAccountName")). ')';					
	}
}
DBFree($query);		
DBClose();

$Descripcion=trim($Descripcion,'|');
echo $Descripcion;
?>