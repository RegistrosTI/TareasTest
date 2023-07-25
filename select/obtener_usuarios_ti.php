<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id    =$_GET['id'];
$select='';
$count = 0;

DBConectar($GLOBALS["cfg_DataBase"]);

$where_montado = '';
$tipos=DBSelect(utf8_decode("SELECT [Descripcion] FROM [Tipos] where Tipo = 100 and visible = 1"));
for(;DBNext($tipos);)
{
	$Descripcion=(DBCampo($tipos,utf8_decode("Descripcion")));		
	if($count==0)
	{
		$where_montado = " department=''".$Descripcion."'' ";	
	}
	else
	{
		$where_montado = $where_montado." or department=''".$Descripcion."'' ";	
	}
	$count = $count + 1;
}	

if ($id=='1')
{
	$u_programa=DBSelect(utf8_decode("SELECT DISTINCT CAST([Asignado a] AS varchar(250)) as USUARIO FROM [Tareas y Proyectos] where [Asignado a] is not null ORDER BY USUARIO"));
	for(;DBNext($u_programa);)
	{
		$USUARIO=(DBCampo($u_programa,utf8_decode("USUARIO")));		
		$select =$select.'<option value="'.utf8_encode($USUARIO).'">'.utf8_encode($USUARIO).'</option>';
	}
}
if ($where_montado != '')
{	
	$where_montado = ' and ('.$where_montado.')';
	if ($id=='2')
	{		
		$u_programa=DBSelect(utf8_decode("SELECT sAMAccountName as valor,Name as nombre FROM OpenQuery(ADSI, 'SELECT sAMAccountName,Name,department FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user'' AND objectClass=''user''  ".$where_montado."') Consulta ORDER BY Name"));
		for(;DBNext($u_programa);)
		{
				$USUARIO      =(DBCampo($u_programa,utf8_decode("valor")));		
				$NOMBREUSUARIO=(DBCampo($u_programa,utf8_decode("nombre")));
				$select       =$select.'<option value="'.utf8_encode($USUARIO).'">'.utf8_encode($NOMBREUSUARIO).'</option>';			
		}
		$select=$select.'<option value=""></option>';
	}
	if ($id=='3') // aqui entra desde obtener incidencia, pasando parametro id=3
	{		
		$query="SELECT sAMAccountName as valor,Name as nombre	FROM OpenQuery(ADSI, 'SELECT sAMAccountName,Name,department FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user'' AND objectClass=''user''  ".$where_montado."') Consulta ORDER BY Name";
		$u_programa=DBSelect($query);
		$select=utf8_encode(DBCampo($u_programa,("nombre")));	
		for(;DBNext($u_programa);)
		{			
			$select=$select.','.utf8_encode(DBCampo($u_programa,("nombre")));
		}
		//$select=$select.'<option value=""></option>';
		$select=$select.', ';
	}
	if ($id=='4')
	{		
		$u_programa=DBSelect(utf8_decode("SELECT sAMAccountName as valor,Name as nombre	FROM OpenQuery(ADSI, 'SELECT sAMAccountName,Name,department FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user'' AND objectClass=''user''  ".$where_montado."') Consulta ORDER BY Name"));
		$select=utf8_encode(DBCampo($u_programa,("valor")));	
		for(;DBNext($u_programa);)
		{			
			$select=$select.','.utf8_encode(DBCampo($u_programa,("valor")));					
		}
		$select=$select.'<option value=""></option>';
		
	}
}
DBFree($u_programa);	
DBClose();
echo $select;
?>