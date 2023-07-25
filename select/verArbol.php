<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$tarea                = $_GET["tarea"];
$Usuario              = $_GET["usuario"];
$Menu                 = $_GET["departamento"];
$Oficina              = $_GET["oficina"];
$nombreusuariovalidado= $_GET["nombreusuariovalidado"];
$contador             = 0;

DBConectar($GLOBALS["cfg_DataBase"]);

$Mi_Menu_Toca = '-1';
$query=DBSelect(utf8_decode("SELECT Numero as Numero FROM Tipos WHERE Tipo = 100 and Descripcion = '".utf8_decode($Menu)."'"));
for(;DBNext($query);)
{		
	$Mi_Menu_Toca=(DBCampo($query,"Numero"));					
}
DBFree($query);	

if ($Mi_Menu_Toca == '1')
{
	$id_mh='1';
	$responsable = 1;
}
else
{
	$id_mh='2';
	if($Oficina=='Responsable')		
	{
		$responsable = 1;			
	}
	else
	{
		$responsable = 0;
	}
}	
$Descripcion = '';

$query= "
	(SELECT
		Id as TAREA
		,Estado
		,Tipo as tipo_tarea
		,CAST(Título as varchar(MAX)) as TITULO
		,1 as TIPO
		,Solicitado
		,CAST([Departamento Solicitud] AS VARCHAR(200)) as Dep
		,CAST([Programa Solicitud] AS VARCHAR(200)) as Prog
		,(	SELECT TOP 1 CAST(Departamento.[Departamento Solicitud] AS VARCHAR(200))
			FROM [Tareas y Proyectos] Departamento
			WHERE Departamento.solicitado='"./*utf8_decode*/($nombreusuariovalidado)."'
			ORDER BY Departamento.[Fecha alta] desc) as departamento_usuario
		,(	SELECT TOP 1 CAST(Departamento.[Programa Solicitud] AS VARCHAR(200))
			FROM [Tareas y Proyectos] Departamento
			WHERE Departamento.solicitado='"./*utf8_decode*/($nombreusuariovalidado)."'
			ORDER BY Departamento.[Fecha alta] desc) as programa_usuario
	FROM [Tareas y Proyectos]
	WHERE [Tarea / Proyecto] = $tarea
		AND ([Control] = 0 OR [Control] is null))
		
	UNION
	
	(SELECT
		TOP 1 Padre.Id as TAREA
		,Padre.Estado
		,Padre.Tipo as tipo_tarea
		,CAST(Padre.Título as varchar(MAX)) as TITULO
		,2 as TIPO
		,Padre.Solicitado
		,CAST(Padre.[Departamento Solicitud] AS VARCHAR(200)) as Dep
		,CAST(Padre.[Programa Solicitud] AS VARCHAR(200)) as Prog
		,(	SELECT TOP 1 CAST(Departamento.[Departamento Solicitud] AS VARCHAR(200))
			FROM [Tareas y Proyectos] Departamento
			WHERE Departamento.solicitado='"./*utf8_decode*/($nombreusuariovalidado)."'
				AND (Departamento.[Control] = 0 OR Departamento.[Control] is null)
			ORDER BY Departamento.[Fecha alta] desc) as departamento_usuario
		,(	SELECT TOP 1 CAST(Departamento.[Programa Solicitud] AS VARCHAR(200))
			FROM [Tareas y Proyectos] Departamento
			WHERE Departamento.solicitado='"./*utf8_decode*/($nombreusuariovalidado)."'
				AND (Departamento.[Control] = 0 OR Departamento.[Control] is null)
			ORDER BY Departamento.[Fecha alta] desc) as programa_usuario
	FROM [Tareas y Proyectos] Padre
	INNER JOIN [Tareas y Proyectos] Hijo ON Padre.Id = Hijo.[Tarea / Proyecto]
	WHERE Padre.Id = $tarea
		AND (Padre.[Control] = 0 OR Padre.[Control] is null))
		
	UNION
	
	SELECT
		Id as TAREA
		,Estado
		,Tipo as tipo_tarea
		,CAST(Título as varchar(MAX)) as TITULO
		,1 as TIPO
		,Solicitado
		,CAST([Departamento Solicitud] AS VARCHAR(200)) as Dep
		,CAST([Programa Solicitud] AS VARCHAR(200)) as Prog
		,(	SELECT TOP 1 cast(Departamento.[Departamento Solicitud] as varchar(200))
			FROM [Tareas y Proyectos] Departamento
			WHERE Departamento.solicitado='"./*utf8_decode*/($nombreusuariovalidado)."'
				AND (Departamento.[Control] = 0 OR Departamento.[Control] is null)
			ORDER BY Departamento.[Fecha alta] desc) as departamento_usuario
		,(	SELECT TOP 1 cast(Departamento.[Programa Solicitud] as varchar(200))
			FROM [Tareas y Proyectos] Departamento
			WHERE Departamento.solicitado='"./*utf8_decode*/($nombreusuariovalidado)."'
				AND (Departamento.[Control] = 0 OR Departamento.[Control] is null)
			ORDER BY Departamento.[Fecha alta] desc) as programa_usuario
	FROM [Tareas y Proyectos]
	WHERE ([Tarea / Proyecto] IS NOT NULL OR [Tarea / Proyecto] = '')
		AND Id = $tarea
		AND ([Control] = 0 OR [Control] is null)
		
	UNION
	
	(SELECT
		TOP 1 Padre.Id as TAREA
		,Padre.Estado
		,Padre.Tipo as tipo_tarea
		,CAST(Padre.Título as varchar(MAX)) as TITULO
		,2 as TIPO
		,Padre.Solicitado
		,CAST(Padre.[Departamento Solicitud] AS VARCHAR(200)) as Dep
		,CAST(Padre.[Programa Solicitud] AS VARCHAR(200)) as Prog
		,(	SELECT TOP 1 CAST(Departamento.[Departamento Solicitud] AS VARCHAR(200))
			FROM [Tareas y Proyectos] Departamento
			WHERE Departamento.solicitado='"./*utf8_decode*/($nombreusuariovalidado)."'
				AND (Departamento.[Control] = 0 OR Departamento.[Control] is null)
			ORDER BY Departamento.[Fecha alta] desc) as departamento_usuario
		,(	SELECT TOP 1 CAST(Departamento.[Programa Solicitud] AS VARCHAR(200))
			FROM [Tareas y Proyectos] Departamento
			WHERE Departamento.solicitado='"./*utf8_decode*/($nombreusuariovalidado)."'
				AND (Departamento.[Control] = 0 OR Departamento.[Control] is null)
			ORDER BY Departamento.[Fecha alta] desc) as programa_usuario
	FROM [Tareas y Proyectos] Padre
	INNER JOIN [Tareas y Proyectos] Hijo ON Padre.Id = Hijo.[Tarea / Proyecto]
	WHERE Hijo.Id = $tarea
		AND (Padre.[Control] = 0 OR Padre.[Control] is null)
		AND (Hijo.[Control]  = 0 OR Hijo.[Control]  is null))
	ORDER BY TIPO Desc";

$query = utf8_decode($query);
//die($query);
$query=DBSelect($query);
				
	for(;DBNext($query);)
	{
		$solicitado           = utf8_encode(DBCampo($query,"Solicitado"));
		$dep                  = utf8_encode(DBCampo($query,"Dep"));
		$prog                 = utf8_encode(DBCampo($query,"Prog"));
		$departamento_usuario = utf8_encode(DBCampo($query,"departamento_usuario"));
		$programa_usuario     = utf8_encode(DBCampo($query,"programa_usuario"));
		$estado               = utf8_encode(DBCampo($query,"Estado"));
		$tipo                 = utf8_encode(DBCampo($query,"tipo_tarea"));
		$contador             = $contador + 1;
		if ($id_mh=='1')
		{
			$Descripcion=$Descripcion.'<div style="cursor: pointer;" onClick="selecciono_tarea_historial('.(DBCampo($query,"TAREA")).','.$id_mh.');" class="div_aviso"><img src="imagenes/Arbol_'.(DBCampo($query,"TIPO")).'.png"/>'.utf8_encode(DBCampo($query,"TITULO")).'</div>';					
		}
		else		
		{
			if ($responsable == 1)
			{
				if($departamento_usuario==$dep && $programa_usuario==$prog)
				{
					$Descripcion=$Descripcion.'<div style="cursor: pointer;" onClick="selecciono_tarea_historial('.(DBCampo($query,"TAREA")).','.$id_mh.');" class="div_aviso"><img src="imagenes/Arbol_'.(DBCampo($query,"TIPO")).'.png"/>'.utf8_encode(DBCampo($query,"TITULO")).'</div>';									
				}
				else
				{
					$Descripcion=$Descripcion.'<div class="div_aviso"><img src="imagenes/Arbol_'.(DBCampo($query,"TIPO")).'.png"/>'.utf8_encode(DBCampo($query,"TITULO")).'</div>';									
				}			
			}
			else
			{
				if($solicitado==$nombreusuariovalidado)
				{
					$Descripcion=$Descripcion.'<div style="cursor: pointer;" onClick="selecciono_tarea_historial('.(DBCampo($query,"TAREA")).','.$id_mh.');" class="div_aviso"><img src="imagenes/Arbol_'.(DBCampo($query,"TIPO")).'.png"/>'.utf8_encode(DBCampo($query,"TITULO")).'</div>';					
				}
				else
				{
					$Descripcion=$Descripcion.'<div class="div_aviso"><img src="imagenes/Arbol_'.(DBCampo($query,"TIPO")).'.png"/>'.utf8_encode(DBCampo($query,"TITULO")).'</div>';					
				}
			
			}			
		}
		$Descripcion=$Descripcion.'<div class="Informacion_Arbol">'.$tipo.'-'.$estado.'</div>';
	}
	DBFree($query);	

if ($contador==0)
{
	echo "No tiene avisos pendientes";
}
else
{
	echo $Descripcion;
}	
DBClose();
?>