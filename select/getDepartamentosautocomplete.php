<?php
//UTF ñáç
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


$usuario     = $_GET['usuario'];
$Descripcion = '';

DBConectar($GLOBALS["cfg_DataBase"]);

$query=DBSelect(utf8_decode("SELECT TOP 1 [Departamento Solicitud] as DEPARTAMENTO,[Programa Solicitud] as PROGRAMA FROM [Tareas y Proyectos] where Solicitado = '".($usuario)."' AND [Control] <> 1 ORDER BY [Fecha alta] desc"));
$DEPARTAMENTO=utf8_encode(DBCampo($query,"DEPARTAMENTO"));
$PROGRAMA    =utf8_encode(DBCampo($query,"PROGRAMA"));
DBFree($query);		

$u=DBSelect(utf8_decode("SELECT * FROM OPENQUERY (NavisionSQL,'SELECT COUNT(*) AS Resultado FROM [NAVSQL].[dbo].[Departamento]')"));
$RegistrosDepartamento=DBCampo($u,"Resultado");		
DBFree($u);	

$selectdepartamento='<select id="selectdepartamento" name="selectdepartamento" style="width:460;" onchange="javascript:cambia_departamento(this);">';	
$i = 1;
while ($i <= $RegistrosDepartamento) 
{
	$u=DBSelect(utf8_decode("SELECT Código,Nombre,Bloqueado,Fila FROM (SELECT *,ROW_NUMBER() OVER(ORDER BY Código) Fila FROM OPENQUERY (NavisionSQL,'SELECT * FROM [NAVSQL].[dbo].[Departamento]')) Consulta	WHERE Fila BETWEEN ".$i." AND ".(($i+200)-1)." ORDER BY Nombre"	));
	for(;DBNext($u);)
	{
		$CodigoDepartamento   =(DBCampo($u,utf8_decode("Código")));
		$NombreDepartamento   =(DBCampo($u,"Nombre"));
		$BloqueadoDepartamento=(DBCampo($u,"Bloqueado"));
		if ($DEPARTAMENTO == utf8_encode($CodigoDepartamento))
		{
			$selectdepartamento=$selectdepartamento.'<option value="'.utf8_encode($CodigoDepartamento).'" selected="selected">'.utf8_encode($CodigoDepartamento).' ('.utf8_encode($NombreDepartamento).')</option>';
		}
		else
		{
			if (utf8_encode($BloqueadoDepartamento) == 0)
			{
				$selectdepartamento=$selectdepartamento.'<option value="'.utf8_encode($CodigoDepartamento).'">'.utf8_encode($CodigoDepartamento).' ('.utf8_encode($NombreDepartamento).')</option>';
			}
		}						
	}
	DBFree($u);	
	$i=$i+200;  
}
$selectdepartamento=$selectdepartamento.'</select>';	
DBClose();

echo $selectdepartamento;
?>