<?php
//UTF8 PÍCÁÑÁ
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_" . curPageName () . ".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id = $_GET [ "tarea" ];
$fecha = $_GET [ "fecha" ];
$tool = array ();

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$q = "
	SELECT 
		(SELECT COUNT(*)  FROM [Log_Tareas_y_Proyectos] where Id = " . $id . ")	as FILAS
		,CAST(ISNULL((	SELECT TOP 1 TyP.[Título] FROM [Tareas y Proyectos] TyP where TyP.id = [Log].[Tarea / Proyecto]),'') as VARCHAR(MAX)) AS TAREA_PROYECTO_DESCRIPCION
		,ISNULL([Tarea / Proyecto],-1) AS TAREA_PROYECTO
		,UsuarioCambia 
		,Tipo as Tipo 
		,CAST([Departamento Solicitud] AS VARCHAR(255)) as Departamento --ERROR UNICODE
		,CAST([Programa Solicitud] AS VARCHAR(255)) as Programa	--ERROR UNICODE
		,[Categoría] as Categoria,CAST([SubCategoría] as varchar(250)) as SubCategoria,CAST([Valoración] as varchar(250)) as Valoracion,Estado as Estado,usuario as usuario,solicitado as solicitado
		,[% Completado] AS PORCENTAJE
		,[Asignado a] as asignado
		,CAST(Título as varchar(255)) AS TITULO
		,CAST(Descripción as text) AS DESCRIPCION
		,[horas estimadas] as horas
		,prioridad as prioridad
		,BAP
		,CAST(Referencia as VARCHAR(MAX)) as Referencia
		,Cola
		,CONVERT(varchar(50),FechaCambio,105)+' '+CONVERT(varchar(50),FechaCambio,108) as FechaCambio
		,ISNULL(CONVERT(varchar(20),[Fecha Solicitud],105),'') as [Fecha_Solicitud]
		,ISNULL(CONVERT(varchar(20),[Fecha alta],105),'') as [Fecha_alta] ,ISNULL(CONVERT(varchar(20),[Fecha inicio],105),'') as [Fecha_inicio]
		,ISNULL(CONVERT(varchar(20),[Fecha fin],105),'') as  [Fecha_fin],ISNULL(CONVERT(varchar(20),[Fecha objetivo],105),'') as [Fecha_objetivo]
		,ISNULL(CONVERT(varchar(20),[Fecha Necesidad],105),'') as [Fecha_Necesidad] 
	FROM [Log_Tareas_y_Proyectos] [Log] 
	WHERE 
		Id = " . $id . "
		AND FechaCambio IN (SELECT 
								FechaCambio 
							FROM(	SELECT 
										FechaCambio,
										ROW_NUMBER() OVER(order by FechaCambio desc) as fila
									FROM [Log_Tareas_y_Proyectos] where Id = " . $id . ") AS cambios 
							WHERE fila = " . $fecha . ") 
	ORDER BY FechaCambio desc";

//DIE($q);
$q = DBSelect ( utf8_decode ( $q ) );

for(; DBNext ( $q ) ;) {
	
	$FILAS = ( DBCampo ( $q , "FILAS" ) );
	
	$TAREA_PROYECTO_DESCRIPCION = utf8_encode ( DBCampo ( $q , "TAREA_PROYECTO_DESCRIPCION" ) );
	$TAREA_PROYECTO = DBCampo ( $q , "TAREA_PROYECTO" );
	$UsuarioCambia = utf8_encode ( DBCampo ( $q , "UsuarioCambia" ) );
	
	
	$BAP = ( DBCampo ( $q , "BAP" ) );
	$Referencia = utf8_encode ( DBCampo ( $q , "Referencia" ) );
	$Cola = utf8_encode ( DBCampo ( $q , "Cola" ) );
	$usuario = utf8_encode ( DBCampo ( $q , "usuario" ) );
	$solicitado = utf8_encode ( DBCampo ( $q , "solicitado" ) );
	$asignado = utf8_encode ( DBCampo ( $q , "asignado" ) );
	$prioridad = utf8_encode ( DBCampo ( $q , "prioridad" ) );
	$FechaCambio = ( DBCampo ( $q , "FechaCambio" ) );
	$PORCENTAJE = ( DBCampo ( $q , "PORCENTAJE" ) );
	$Fecha_Solicitud = ( DBCampo ( $q , "Fecha_Solicitud" ) );
	$Fecha_alta = ( DBCampo ( $q , "Fecha_alta" ) );
	$Fecha_inicio = ( DBCampo ( $q , "Fecha_inicio" ) );
	$Fecha_fin = ( DBCampo ( $q , "Fecha_fin" ) );
	$Fecha_objetivo = ( DBCampo ( $q , "Fecha_objetivo" ) );
	$Fecha_Necesidad = ( DBCampo ( $q , "Fecha_Necesidad" ) );
	$Tipo = utf8_encode ( DBCampo ( $q , "Tipo" ) );
	$horas = ( DBCampo ( $q , "horas" ) );
	$DESCRIPCION = utf8_encode ( DBCampo ( $q , "DESCRIPCION" ) );
	$TITULO = utf8_encode ( DBCampo ( $q , "TITULO" ) );
	$Categoria = utf8_encode ( DBCampo ( $q , "Categoria" ) );
	$SubCategoria = utf8_encode ( DBCampo ( $q , "SubCategoria" ) );
	$Valoracion = utf8_encode ( DBCampo ( $q , "Valoracion" ) );
	$Estado = utf8_encode ( DBCampo ( $q , "Estado" ) );
	$Departamento = utf8_encode ( DBCampo ( $q , "Departamento" ) );
	$Programa = utf8_encode ( DBCampo ( $q , "Programa" ) );
	

	if ( $TAREA_PROYECTO != "-1" ) {
		$TAREA_PROYECTO_DESCRIPCION = $TAREA_PROYECTO . ' - ' . $TAREA_PROYECTO_DESCRIPCION;
	} else {
		$TAREA_PROYECTO_DESCRIPCION = '';
	}
	
	$tool [ 0 ] = array (
			"FILAS" => $FILAS ,
			"TAREA_PROYECTO_DESCRIPCION" => $TAREA_PROYECTO_DESCRIPCION ,
			"USUARIOCAMBIA" => $UsuarioCambia ,
			"PORCENTAJE" => $PORCENTAJE ,
			"BAP" => $BAP ,
			"Referencia" => $Referencia ,
			"Cola" => $Cola ,
			"prioridad" => $prioridad ,
			"horas" => $horas ,
			"usuario" => $usuario ,
			"solicitado" => $solicitado ,
			"asignado" => $asignado ,
			"DESCRIPCION" => $DESCRIPCION ,
			"TITULO" => $TITULO ,
			"Departamento" => $Departamento ,
			"Programa" => $Programa ,
			"Categoria" => $Categoria ,
			"SubCategoria" => $SubCategoria ,
			"Valoracion" => $Valoracion ,
			"Estado" => $Estado ,
			"Tipo" => $Tipo ,
			"FECHACAMBIO" => $FechaCambio ,
			"Fecha_Necesidad" => $Fecha_Necesidad ,
			"Fecha_objetivo" => $Fecha_objetivo ,
			"Fecha_fin" => $Fecha_fin ,
			"Fecha_inicio" => $Fecha_inicio ,
			"Fecha_alta" => $Fecha_alta ,
			"Fecha_Solicitud" => $Fecha_Solicitud 
	);
	
}
DBFree ( $q );
DBClose ();

$json_arr = array (
		'data' => $tool 
);
$php_json = json_encode ( $json_arr );
echo $php_json;
?>