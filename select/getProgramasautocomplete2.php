<?php
//UTF ñáç
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


$usuario      = $_GET['usuario'];
$DEPARTAMENTO = $_GET['departamento'];
$Descripcion  = '';

DBConectar($GLOBALS["cfg_DataBase"]);

$query=DBSelect(utf8_decode("SELECT TOP 1 [Departamento Solicitud] as DEPARTAMENTO,[Programa Solicitud] as PROGRAMA FROM [Tareas y Proyectos] where Solicitado = '".($usuario)."' AND [Departamento Solicitud] = '".($DEPARTAMENTO)."'
						AND [Control] <> 1 ORDER BY [Fecha alta] desc"));
$PROGRAMA=utf8_encode(DBCampo($query,"PROGRAMA"));
DBFree($query);		


$u=DBSelect(utf8_decode("SELECT Departamento,Programa,Nombre_Departamento,Nombre_Programa FROM OPENQUERY (NavisionSQL,'SELECT Relacion.Departamento,Relacion.Programa,	Departamento.Nombre AS Nombre_Departamento,		Programa.Nombre AS Nombre_Programa FROM [NAVSQL].[dbo].[Relación dpto_ y prog_] Relacion INNER JOIN Departamento Departamento ON Departamento.Código = Relacion.Departamento INNER JOIN 		Programa Programa ON Programa.Código = Relacion.Programa WHERE Relacion.Bloqueado = 0 AND Relacion.Departamento = ''".$DEPARTAMENTO."'' ') Consulta"));
$selectdepartamento='<select id="selectprograma" name="selectprograma" style="width:460;">';	
for(;DBNext($u);)
{
	$CodigoDepartamento   =(DBCampo($u,utf8_decode("Programa")));		
	$NombreDepartamento   =(DBCampo($u,"Nombre_Programa"));
	$BloqueadoDepartamento=0;
	if ($PROGRAMA == utf8_encode($CodigoDepartamento))
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
DBClose();

$selectdepartamento=$selectdepartamento.'</select>';	
echo $selectdepartamento;
?>