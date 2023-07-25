<?php
// UTF Á á Ñ ñ
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$cur_page = $_GET [ "pq_curpage" ];
if ( $cur_page == '0' ) {
	$cur_page = '1';
}
$records_per_page = $_GET [ "pq_rpp" ];
$pq_sort = $_GET [ "pq_sort" ];
$sortQuery = SortHelper :: deSerializeSort_Simple ( $pq_sort );
$filterQuery = "";
$filterParam = array ();

if ( isset ( $_GET [ "pq_filter" ] ) ) {
	$pq_filter = $_GET [ "pq_filter" ];
	$dsf = FilterHelper :: deSerializeFilter ( $pq_filter );
	$filterQuery = $dsf -> query;
	$filterParam = $dsf -> param;
}
$contador = 0;
$filas_totales = 0;
$tareas_hora = array ();

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$filtro_adicional = $filterQuery;
$usuario = $_GET [ "usuario" ];
$tarea = $_GET [ "tarea" ];

if ($tarea != '-1'){
	if($filtro_adicional == ''){
		$filtro_adicional = $filtro_adicional . " WHERE OFI.Evaluacion = EVA.Periodo AND EVA.Usuario = '$usuario' AND Tarea = $tarea ";
	}else{
		$filtro_adicional = $filtro_adicional . " AND OFI.Evaluacion = EVA.Periodo AND EVA.Usuario = '$usuario' AND Tarea = $tarea ";
	}
}



if ($tarea == '-1'){
	if($filtro_adicional == ''){
		$filtro_adicional = $filtro_adicional . " WHERE OFI.Evaluacion = EVA.Periodo AND EVA.Usuario = '$usuario' AND Automatica = 0 " ;
	}else{
		$filtro_adicional = $filtro_adicional . " AND OFI.Evaluacion = EVA.Periodo AND EVA.Usuario = '$usuario' AND Automatica = 0 " ;
	}
}

$q = "
	Select
		 [Id]
		,[Tarea]
		,[Título]
		,[Periodo]
		,[Anyo_ISO]
		,[Semana_ISO]
		,[Mes]
		,[Anyo]
		,Med_Calidad 			-- CASE [Med_Calidad] WHEN -1 THEN '' ELSE Med_Calidad END Med_Calidad 
		,Med_Enfoque_Cliente 	-- CASE [Med_Enfoque_Cliente] WHEN -1 THEN '' ELSE [Med_Enfoque_Cliente] END Med_Enfoque_Cliente
		,Med_En_Tiempo 			-- CASE [Med_En_Tiempo] WHEN -1 THEN '' ELSE [Med_En_Tiempo] END Med_En_Tiempo
		,Med_Eficiencia 		-- CASE [Med_Eficiencia] WHEN -1 THEN '' ELSE [Med_Eficiencia] END Med_Eficiencia
		,Med_Evaluador 			-- CASE [Med_Evaluador] WHEN -1 THEN '' ELSE [Med_Evaluador] END Med_Evaluador
		,[Finalizada]
		,[Automatica]
		,Comentario_Evaluador
		,CAST((CONVERT(varchar(20), [Fecha_Usuario],103)) as varchar) AS Fecha_Usuario
		,CAST((CONVERT(varchar(20), [Fecha_Evaluador],103)) as varchar) AS Fecha_Evaluador
		,CAST([Usuario] as varchar(255)) as Usuario
		,Usuario_Nombre
		,CAST([Evaluador] as varchar(255)) as Evaluador
		,CAST([Evaluador_Nombre] as varchar(255)) as Evaluador_Nombre
		,Fila
		,Num_Registros 
	FROM (	SELECT 
				EVA.[Id]
				,EVA.Tarea
				,TAR.Título
				,EVA.[Periodo]
				,EVA.[Anyo_ISO]
				,EVA.[Semana_ISO]
				,EVA.[Mes]
				,EVA.[Anyo]
				,EVA.[Usuario]
				,EVA.Usuario_Nombre
				,EVA.[Evaluador]
				,EVA.[Evaluador_Nombre]
				,EVA.[Fecha_Usuario]
				,EVA.[Fecha_Evaluador]
				,EVA.[Med_Calidad]
				,EVA.[Med_Enfoque_Cliente]
				,EVA.[Med_En_Tiempo]
				,EVA.[Med_Eficiencia]
				,EVA.[Med_Evaluador]
				,EVA.[Finalizada]
				,EVA.[Automatica]
				,EVA.Comentario_Evaluador
				,ROW_NUMBER() OVER(" . $sortQuery . ") Fila
				,(SELECT COUNT(*) 
					FROM [Evaluaciones] AS EVA
					INNER JOIN [Tareas y Proyectos] AS TAR ON EVA.Tarea = TAR.Id 
					INNER JOIN Configuracion_Oficinas AS OFI ON TAR.Oficina =  OFI.Oficina $filtro_adicional) AS Num_Registros 
			FROM [Evaluaciones] AS EVA
				INNER JOIN [Tareas y Proyectos] AS TAR ON EVA.Tarea = TAR.Id 
				INNER JOIN Configuracion_Oficinas AS OFI ON TAR.Oficina =  OFI.Oficina $filtro_adicional  ) Resultado 
			WHERE Fila > (" . $cur_page . " * " . $records_per_page . ") -" . $records_per_page . " AND Fila <= " . $cur_page . " * " . $records_per_page . " " . $sortQuery;

//die($q);
$q = DBSelect ( utf8_decode ( $q ) );


for(; DBNext ( $q ) ;) {
	$Id 					= utf8_encode ( DBCampo ( $q , utf8_decode ( "Id" ) ) );
	$Tarea 					= utf8_encode ( DBCampo ( $q , utf8_decode ( "Tarea" ) ) );
	$Titulo					= utf8_encode ( DBCampo ( $q , utf8_decode ( "Título" ) ) );
	$Anyo_ISO 				= utf8_encode ( DBCampo ( $q , utf8_decode ( "Anyo_ISO" ) ) );
	$Semana_ISO 			= utf8_encode ( DBCampo ( $q , utf8_decode ( "Semana_ISO" ) ) );
	$Mes 					= utf8_encode ( DBCampo ( $q , utf8_decode ( "Mes" ) ) );
	$Anyo 					= utf8_encode ( DBCampo ( $q , utf8_decode ( "Anyo" ) ) );
	$Usuario 				= utf8_encode ( DBCampo ( $q , utf8_decode ( "Usuario" ) ) );
	$Usuario_Nombre			= utf8_encode ( DBCampo ( $q , utf8_decode ( "Usuario_Nombre" ) ) );
	$Evaluador 				= utf8_encode ( DBCampo ( $q , utf8_decode ( "Evaluador" ) ) );
	$Evaluador_Nombre		= utf8_encode ( DBCampo ( $q , utf8_decode ( "Evaluador_Nombre" ) ) );
	$Fecha_Usuario 			= utf8_encode ( DBCampo ( $q , utf8_decode ( "Fecha_Usuario" ) ) );
	$Fecha_Evaluador 		= utf8_encode ( DBCampo ( $q , utf8_decode ( "Fecha_Evaluador" ) ) );
	$Med_Calidad 			= utf8_encode ( DBCampo ( $q , utf8_decode ( "Med_Calidad" ) ) );
	$Med_Enfoque_Cliente	= utf8_encode ( DBCampo ( $q , utf8_decode ( "Med_Enfoque_Cliente" ) ) );
	$Med_En_Tiempo			= utf8_encode ( DBCampo ( $q , utf8_decode ( "Med_En_Tiempo" ) ) );
	$Med_Eficiencia			= utf8_encode ( DBCampo ( $q , utf8_decode ( "Med_Eficiencia" ) ) );
	$Med_Evaluador			= utf8_encode ( DBCampo ( $q , utf8_decode ( "Med_Evaluador" ) ) );
	$Automatica				= utf8_encode ( DBCampo ( $q , utf8_decode ( "Automatica" ) ) );
	$Periodo				= utf8_encode ( DBCampo ( $q , utf8_decode ( "Periodo" ) ) );
	$Comentario_Evaluador	= utf8_encode ( DBCampo ( $q , utf8_decode ( "Comentario_Evaluador" ) ) );
	
	$filas_totales = DBCampo ( $q , "Num_Registros" );
	
	$tareas_hora [ $contador ] = array (
			"Id" 					=> $Id ,
			"Tarea" 				=> $Tarea ,
			"Título" 				=> $Titulo ,
			"Anyo_ISO" 				=> $Anyo_ISO ,
			"Semana_ISO" 			=> $Semana_ISO ,
			"Mes" 					=> $Mes ,
			"Anyo" 					=> $Anyo ,
			"Usuario" 				=> $Usuario,
			"Usuario_Nombre"		=> $Usuario_Nombre,
			"Evaluador" 			=> $Evaluador ,
			"Evaluador_Nombre"		=> $Evaluador_Nombre ,
			"Fecha_Usuario" 		=> $Fecha_Usuario ,
			"Fecha_Evaluador" 		=> $Fecha_Evaluador ,
			"Med_Calidad" 			=> $Med_Calidad ,
			"Med_Enfoque_Cliente" 	=> $Med_Enfoque_Cliente ,
			"Med_En_Tiempo" 		=> $Med_En_Tiempo ,
			"Med_Eficiencia" 		=> $Med_Eficiencia ,
			"Med_Evaluador" 		=> $Med_Evaluador ,
			"Automatica" 			=> $Automatica ,
			"Comentario_Evaluador"	=> $Comentario_Evaluador ,
			"Periodo" 				=> $Periodo
	);
	
	$contador = $contador + 1;
}

DBFree ( $q );
DBClose ();

$json_arr = array (
		'totalRecords' => $filas_totales , 
		'curPage' => $cur_page , 
		'data' => $tareas_hora 
);
$php_json = json_encode ( $json_arr );

echo $php_json;

?>
