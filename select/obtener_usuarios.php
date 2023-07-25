<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);

$select    ='';
$u_programa=DBSelect(utf8_decode("SELECT Name FROM	(SELECT department,sAMAccountName ,	Name,mail,CASE WHEN ((2 & userAccountControl) = 2 ) THEN 0 ELSE 1 END AS Activo  
									FROM OpenQuery(ADSI, 'SELECT department,sAMAccountName,Name,mail,userAccountControl FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user'' AND objectClass=''user''  ')) Consulta	
									WHERE Activo = 1 order by name"));

$select=utf8_encode(DBCampo($u_programa,("Name")));	
for(;DBNext($u_programa);)
{			
	$select=$select.','.utf8_encode(DBCampo($u_programa,("Name")));					
}

DBFree($u_programa);	
DBClose();
echo $select;
?>