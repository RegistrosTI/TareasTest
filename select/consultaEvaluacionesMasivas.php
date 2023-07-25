<?php
// UTF Á á Ñ ñ
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";
class ColumnHelper {
	public static function isValidColumn( $dataIndx ) {
		return true;
		if ( preg_match ( '/^[a-z,A-Z]*$/' , $dataIndx ) ) {
			return true;
		} else {
			return false;
		}
	}
}
class SortHelper {
	public static function deSerializeSort_Simple( $pq_sort ) {
		$sorters = json_decode ( $pq_sort );
		$columns = array ();
		$sortby = "";
		foreach ( $sorters as $sorter ) {
			$dataIndx = $sorter -> dataIndx;
			$dir = $sorter -> dir;
			
			if ( $dir == "up" ) {
				$dir = "asc";
			} else {
				$dir = "desc";
			}
			if ( ColumnHelper :: isValidColumn ( $dataIndx ) ) {
				$columns [ ] = '[' . $dataIndx . ']' . " " . $dir;
			} else {
				throw new Exception ( "invalid column " . $dataIndx );
			}
		}
		if ( sizeof ( $columns ) > 0 ) {
			$sortby = " order by " . join ( ", " , $columns );
		}
		return $sortby;
	}
	public static function deSerializeSort( $pq_sort , $table ) {
		$sorters = json_decode ( $pq_sort );
		$columns = array ();
		$sortby = "";
		foreach ( $sorters as $sorter ) {
			$dataIndx = $sorter -> dataIndx;
			$dir = $sorter -> dir;
			
			if ( $dir == "up" ) {
				$dir = "asc";
			} else {
				$dir = "desc";
			}
			if ( ColumnHelper :: isValidColumn ( $dataIndx ) ) {
				$columns [ ] = $table . '[' . $dataIndx . ']' . " " . $dir;
			} else {
				throw new Exception ( "invalid column " . $dataIndx );
			}
		}
		if ( sizeof ( $columns ) > 0 ) {
			$sortby = " order by " . join ( ", " , $columns );
		}
		return $sortby;
	}
}
class FilterHelper {
	public static function deSerializeFilter( $pq_filter ) {
		$filterObj = json_decode ( $pq_filter );
		$mode = $filterObj -> mode;
		$filters = $filterObj -> data;
		$fc = array ();
		$param = array ();
		foreach ( $filters as $filter ) {
			$dataIndx = $filter -> dataIndx;
			if ( ColumnHelper :: isValidColumn ( $dataIndx ) == false ) {
				throw new Exception ( "Invalid column name" );
			}
			$text = $filter -> value;
			$condition = $filter -> condition;
			if ( $condition == "contain" ) {
				include_once "../php/funciones.php";
				include_once "../conf/config.php";
				include_once "../conf/config_" . curPageName () . ".php";
				include_once "../../soporte/DB.php";
				include_once "../../soporte/funcionesgenerales.php";
				
				//echo "##$dataIndx##$text##";
				
				if ( ( $dataIndx == 'Med_Evaluador' || $dataIndx == 'Med_Eficiencia' || $dataIndx == 'Med_En_Tiempo' || $dataIndx == 'Med_Calidad' ) && $text == 'Pdte' ) {
					$text = -1;
				}
				
				DBConectar ( $GLOBALS [ "cfg_DataBase" ] );
				$nav = "SELECT [GestionPDT].[dbo].[CONVERT_NAV_FILTER] ('" . '[' . $dataIndx . ']' . "','" . $text . "') AS Resultado";
				// die($nav);
				$nav = DBSelect ( ( $nav ) );
				for(; DBNext ( $nav ) ;) {
					$fc [ ] = DBCampo ( $nav , ( "Resultado" ) );
					$param [ ] = $text;
				}
				DBFree ( $nav );
				DBClose ();
			} else 
				if ( $condition == "notcontain" ) {
					$fc [ ] = '[' . $dataIndx . ']' . " not like ('%" . $text . "%')";
					$param [ ] = $text;
				} else 
					if ( $condition == "begin" ) {
						$fc [ ] = '[' . $dataIndx . ']' . " like ('" . $text . "%')";
						$param [ ] = $text;
					} else 
						if ( $condition == "end" ) {
							$fc [ ] = '[' . $dataIndx . ']' . " like ('%" . $text . "')";
							$param [ ] = $text;
						} else 
							if ( $condition == "equal" ) {
								$fc [ ] = '[' . $dataIndx . ']' . " = '" . $text . "'";
								$param [ ] = $text;
							} else 
								if ( $condition == "notequal" ) {
									$fc [ ] = '[' . $dataIndx . ']' . " <> '" . $text . "'";
									$param [ ] = $text;
								} else 
									if ( $condition == "empty" ) {
										$fc [ ] = "ifnull(" . '[' . $dataIndx . ']' . ",'')=''";
									} else 
										if ( $condition == "notempty" ) {
											$fc [ ] = "ifnull(" . '[' . $dataIndx . ']' . ",'')!=''";
										} else 
											if ( $condition == "less" ) {
												$fc [ ] = '[' . $dataIndx . ']' . " < ?";
												$param [ ] = $text;
											} else 
												if ( $condition == "great" ) {
													$fc [ ] = '[' . $dataIndx . ']' . " > ?";
													$param [ ] = $text;
												}
		}
		$query = "";
		if ( sizeof ( $filters ) > 0 ) {
			$query = " where " . join ( " " . $mode . " " , $fc );
		}
		$ds = new stdClass ();
		$ds -> query = $query;
		$ds -> param = $param;
		return $ds;
	}
}

$usuario = $_GET [ "usuario" ];
$pdte = $_GET [ "pdte" ];

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

$externo = '';
if ( isset ( $_GET [ "externo" ] ) ) {
	$externo = $_GET [ "externo" ];
}

$contador = 0;
$filas_totales = 0;
$tareas_hora = array ();

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$filtro_adicional = $filterQuery;

if ( $filtro_adicional == "" ) {
	$filtro_adicional = " WHERE Automatica = 0 ";
} else {
	$filtro_adicional = $filtro_adicional . " AND Automatica = 0 ";
}

// FILTRO DE EVALUACIONES DE EVALUADOR PENDIENTES
if($pdte == 1){
	if ( $filtro_adicional == "" ) {
		$filtro_adicional = " WHERE (Med_Evaluador = '-1' OR Med_Calidad = '-1')";
	} else {
		$filtro_adicional = $filtro_adicional . " AND (Med_Evaluador = '-1' OR Med_Calidad = '-1') ";
	}
}

// FILTRO, SOLO PARCIALES (AUTOMATICAS)
if ( $filtro_adicional == "" ) {
	$filtro_adicional = " WHERE Automatica = 0 ";
} else {
	$filtro_adicional = $filtro_adicional . " AND Automatica = 0 ";
}

// LOS EVALUADORES QUE ENTRAN COMO EXTERNO, SOLO PUEDEN VER LAS EVALUACIONES DE TAREAS EN LAS QUE ESTAN COMO EVALUADORES EXTERNOS
if ( $externo == '' ) {
	// En caso de que no sea externo inicializar la variable a vacio para evitar error de variable no definida. LUIS 22/08/2017 T#19103
	$query_nombre = '';
	
	if ( $filtro_adicional == "" ) {
		$filtro_adicional = " WHERE (TAR.Evaluador = '' OR TAR.Evaluador IS NULL) ";
	} else {
		$filtro_adicional = $filtro_adicional . " AND (TAR.Evaluador = '' OR TAR.Evaluador IS NULL) ";
	}
} else {
	
	$query_nombre = "DECLARE @NOMBREDOMINIO VARCHAR(200); SET @NOMBREDOMINIO =  dbo.SF_OBTENER_NOMBRE_DOMINIO( '$usuario');";
	
	if ( $filtro_adicional == "" ) {
		$filtro_adicional = " WHERE TAR.Evaluador = @NOMBREDOMINIO  ";
	} else {
		$filtro_adicional = $filtro_adicional . " AND TAR.Evaluador = @NOMBREDOMINIO  ";
	}
}

// PREPARAMOS LAS OFICINAS Y AREAS PARA EL FILTRO DE LA CONSULTA PARA LOS EVALUADORES INTERNOS
$oficinas_usuario = $externo;
$area_usuario = '';
if ( $externo == '' ) {
	$q = " SELECT Oficina, Area FROM Configuracion_Usuarios AS USU WHERE USU.Usuario = '$usuario' ";
	$q = DBSelect ( utf8_decode ( $q ) );
	for(; DBNext ( $q ) ;) {
		$oficinas_usuario = utf8_encode ( DBCampo ( $q , utf8_decode ( "Oficina" ) ) );
		$area_usuario     = utf8_encode ( DBCampo ( $q , utf8_decode ( "Area" ) ) );
	}
	DBFree ( $q );
}

$oficinas_usuario = str_replace ( "," , "','" , $oficinas_usuario );
$oficinas_usuario = "'" . $oficinas_usuario . "'";

if ( $filtro_adicional == "" ) {
	$filtro_adicional = " WHERE TAR.Oficina IN ($oficinas_usuario) ";
} else {
	$filtro_adicional = $filtro_adicional . " AND TAR.Oficina IN ($oficinas_usuario) ";
}

// Si tiene area solo puede evaluar las suyas, si no tiene es que puede evaluarlas todas y no filtramos
if ( $area_usuario != ""){
	if ( $filtro_adicional == "") {
		$filtro_adicional = " WHERE and eva.usuario in (select usuario from Configuracion_Usuarios where area = '$area_usuario') ";
	} else {
		$filtro_adicional = $filtro_adicional . " and eva.usuario in (select usuario from Configuracion_Usuarios where area = '$area_usuario') ";
	}
}
//
$q = "
		
	$query_nombre 	
	
	Select
		 [Id]
		 ,Id_Anterior
		,Tarea
		,[Periodo]
		,[Anyo_ISO]
		,[Semana_ISO]
		,[Mes]
		,[Anyo]
		,TiempoFichado
		--,DescripcionTarea
		,COALESCE(DescripcionTarea,'')		AS DescripcionTarea
		--,COALESCE(AporteJefe,'')			AS AporteJefe
		,COALESCE(AporteJefeColaborador,'')	AS AporteJefe
		,COALESCE(Usuario_Nombre,'')		AS Usuario_Nombre
		,COALESCE(Evaluador_Nombre,'')		AS Evaluador_Nombre
		,(SELECT Med_Calidad FROM Evaluaciones AS E WHERE ID = Id_Anterior) AS Med_Calidad_Anterior
		,(SELECT Med_Evaluador FROM Evaluaciones AS E WHERE ID = Id_Anterior) AS Med_Evaluador_Anterior
		,Med_Calidad 			-- CASE [Med_Calidad] WHEN -1 THEN '' ELSE Med_Calidad END Med_Calidad 
		,Med_Enfoque_Cliente 	-- CASE [Med_Enfoque_Cliente] WHEN -1 THEN '' ELSE [Med_Enfoque_Cliente] END Med_Enfoque_Cliente
		,Med_En_Tiempo 			-- CASE [Med_En_Tiempo] WHEN -1 THEN '' ELSE [Med_En_Tiempo] END Med_En_Tiempo
		,Med_Eficiencia 		-- CASE [Med_Eficiencia] WHEN -1 THEN '' ELSE [Med_Eficiencia] END Med_Eficiencia
		,Med_Evaluador 			-- CASE [Med_Evaluador] WHEN -1 THEN '' ELSE [Med_Evaluador] END Med_Evaluador
		,[Finalizada]
		,[Automatica]
		,Comentario_Evaluador
		,NUM_COMENTARIOS + NUM_OBSERVACIONES AS Num_Observaciones
		,CAST((CONVERT(varchar(20), [Fecha_Usuario],103)) as varchar) AS Fecha_Usuario
		,CAST((CONVERT(varchar(20), [Fecha_Evaluador],103)) as varchar) AS Fecha_Evaluador
		,CAST((CONVERT(varchar(20), [FechaCreacion],112)) as varchar) AS FechaCreacion
		,CAST([Usuario] as varchar(255)) as Usuario
		,CAST([Evaluador] as varchar(255)) as Evaluador
		,CAST([Usuario_Nombre] as varchar(255)) as Usuario_Nombre
		,CAST([Evaluador_Nombre] as varchar(255)) as Evaluador_Nombre
		,Fila
		,Num_Registros 
	FROM (	SELECT 
				EVA.[Id]
				,CASE Periodo
					WHEN 'SEMANAL' THEN
						(SELECT TOP 1 Id FROM Evaluaciones 
							WHERE Periodo = EVA.Periodo 
								AND Usuario = EVA.Usuario 
								AND Tarea = EVA.Tarea
								AND Semana_ISO < EVA.Semana_ISO
								AND Anyo_ISO <= EVA.Anyo_ISO
								AND Automatica = 0
							ORDER BY Anyo_ISO DESC, Semana_ISO DESC)
					WHEN 'MENSUAL' THEN
						(SELECT TOP 1 Id FROM Evaluaciones 
							WHERE Periodo = EVA.Periodo 
								AND Usuario = EVA.Usuario 
								AND Tarea = EVA.Tarea
								AND Mes < EVA.Mes
								AND Anyo <= Anyo
								AND Automatica = 0
							ORDER BY Anyo DESC, Mes DESC)
					END Id_Anterior
				,EVA.Tarea
			  	,TAR.[Título] AS DescripcionTarea
			  	,TAR.[AporteJefe]
				,CASE 
					WHEN EVA.Usuario_Nombre = TAR.[Asignado a]
						THEN TAR.AporteJefe
					WHEN (SELECT COUNT(*) FROM Colaboradores AS COL WHERE COL.Colaborador = EVA.Usuario AND COL.Id_Tarea = EVA.Tarea AND COL.Estado = 'Activo' AND [Acceso] = 'Permitido') > 0
						THEN (SELECT top 1 AporteJefe FROM Colaboradores  AS COL WHERE COL.Colaborador = EVA.Usuario AND COL.Id_Tarea = EVA.Tarea AND COL.Estado = 'Activo'  AND [Acceso] = 'Permitido')
					ELSE '-'
				END AS AporteJefeColaborador
		      	,[Periodo]
			  	,[Anyo_ISO]
			  	,[Semana_ISO]
			  	,[Mes]
			  	,[Anyo]
				,EVA.Usuario
				,Usuario_Nombre
				,Evaluador_Nombre
			  	,EVA.[Evaluador]
			  	,[Fecha_Usuario]
			  	,[Fecha_Evaluador]
				,[FechaCreacion]
			  	,[Med_Calidad]
			  	,[Med_Enfoque_Cliente]
			  	,[Med_En_Tiempo]
			  	,[Med_Eficiencia]
			  	,[Med_Evaluador]
			  	,[Finalizada]
			  	,[Automatica]
				,CASE [Periodo]
					WHEN 'SEMANAL' THEN 
						(SELECT SUM(Minutos) 
							FROM Horas HORAS 
							WHERE EVA.Usuario = HORAS.Usuario 
								AND EVA.Tarea = HORAS.Tarea
								AND EVA.Semana_ISO = HORAS.Semana_ISO
								AND EVA.Anyo_ISO = HORAS.Anyo_ISO	)
					WHEN 'MENSUAL' THEN
						(SELECT SUM(Minutos) 
							FROM Horas HORAS 
							WHERE EVA.Usuario = HORAS.Usuario 
								AND EVA.Tarea = HORAS.Tarea
								AND EVA.Mes = HORAS.Mes
								AND EVA.Anyo = HORAS.Anyo )	
					END	AS TiempoFichado
				,Comentario_Evaluador
				,(select count(COM.Numero) from Comentarios AS COM where COM.Tarea = TAR.id) AS NUM_COMENTARIOS
				,(SELECT CASE WHEN TAR.Descripción  IS NULL THEN 0 ELSE 1 END) AS NUM_OBSERVACIONES
				,ROW_NUMBER() OVER($sortQuery) Fila
				,(	SELECT COUNT(*) 
					FROM [Evaluaciones]  as EVA 
					INNER JOIN  [Tareas y Proyectos] AS TAR ON EVA.Tarea = TAR.Id AND TAR.Control = 0 
					INNER JOIN Configuracion_Oficinas AS OFI ON TAR.Oficina = OFI.Oficina  AND OFI.Evaluacion = EVA.Periodo $filtro_adicional ) AS Num_Registros 
			FROM [Evaluaciones]  as EVA
			INNER JOIN  [Tareas y Proyectos] AS TAR ON EVA.Tarea = TAR.Id AND TAR.Control = 0 
			INNER JOIN Configuracion_Oficinas AS OFI ON TAR.Oficina = OFI.Oficina  AND OFI.Evaluacion = EVA.Periodo " . $filtro_adicional . " ) Resultado 
	WHERE Fila > ($cur_page * $records_per_page ) - $records_per_page AND Fila <= $cur_page * $records_per_page $sortQuery";

// die( utf8_decode ( $q ));
$q = DBSelect ( utf8_decode ( $q ) );

for(; DBNext ( $q ) ;) {
	$Id = utf8_encode ( DBCampo ( $q , utf8_decode ( "Id" ) ) );
	$Id_Anterior = utf8_encode ( DBCampo ( $q , utf8_decode ( "Id_Anterior" ) ) );
	$Anyo_ISO = utf8_encode ( DBCampo ( $q , utf8_decode ( "Anyo_ISO" ) ) );
	$Semana_ISO = utf8_encode ( DBCampo ( $q , utf8_decode ( "Semana_ISO" ) ) );
	$Mes = utf8_encode ( DBCampo ( $q , utf8_decode ( "Mes" ) ) );
	$Anyo = utf8_encode ( DBCampo ( $q , utf8_decode ( "Anyo" ) ) );
	$Usuario = utf8_encode ( DBCampo ( $q , utf8_decode ( "Usuario" ) ) );
	$Evaluador = utf8_encode ( DBCampo ( $q , utf8_decode ( "Evaluador" ) ) );
	$Fecha_Usuario = utf8_encode ( DBCampo ( $q , utf8_decode ( "Fecha_Usuario" ) ) );
	$Fecha_Evaluador = utf8_encode ( DBCampo ( $q , utf8_decode ( "Fecha_Evaluador" ) ) );
	$FechaCreacion = utf8_encode ( DBCampo ( $q , utf8_decode ( "FechaCreacion" ) ) );
	$Med_Calidad = utf8_encode ( DBCampo ( $q , utf8_decode ( "Med_Calidad" ) ) );
	$Med_Calidad_Anterior = utf8_encode ( DBCampo ( $q , utf8_decode ( "Med_Calidad_Anterior" ) ) );
	$Med_Enfoque_Cliente = utf8_encode ( DBCampo ( $q , utf8_decode ( "Med_Enfoque_Cliente" ) ) );
	$Med_En_Tiempo = utf8_encode ( DBCampo ( $q , utf8_decode ( "Med_En_Tiempo" ) ) );
	$Med_Eficiencia = utf8_encode ( DBCampo ( $q , utf8_decode ( "Med_Eficiencia" ) ) );
	$Med_Evaluador = utf8_encode ( DBCampo ( $q , utf8_decode ( "Med_Evaluador" ) ) );
	$Med_Evaluador_Anterior = utf8_encode ( DBCampo ( $q , utf8_decode ( "Med_Evaluador_Anterior" ) ) );
	$Automatica = utf8_encode ( DBCampo ( $q , utf8_decode ( "Automatica" ) ) );
	$Periodo = utf8_encode ( DBCampo ( $q , utf8_decode ( "Periodo" ) ) );
	$Comentario_Evaluador = utf8_encode ( DBCampo ( $q , utf8_decode ( "Comentario_Evaluador" ) ) );
	$Num_Observaciones = utf8_encode ( DBCampo ( $q , utf8_decode ( "Num_Observaciones" ) ) );
	
	$Usuario_Nombre = utf8_encode ( DBCampo ( $q , utf8_decode ( "Usuario_Nombre" ) ) );
	$Evaluador_Nombre = utf8_encode ( DBCampo ( $q , utf8_decode ( "Evaluador_Nombre" ) ) );
	$Tarea = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tarea" ) ) );
	$DescripcionTarea = utf8_encode ( DBCampo ( $q , utf8_decode ( "DescripcionTarea" ) ) );
	$AporteJefe = utf8_encode ( DBCampo ( $q , utf8_decode ( "AporteJefe" ) ) );

	$TiempoFichado = utf8_encode ( DBCampo ( $q , utf8_decode ( "TiempoFichado" ) ) );
	$TiempoFichado = number_format(round($TiempoFichado / 60 , 2), 2, ':', '');
	
	$filas_totales = DBCampo ( $q , "Num_Registros" );
	
	$tareas_hora [ $contador ] = array (
			"Id" => $Id , 
			"Anyo_ISO" => $Anyo_ISO , 
			"Semana_ISO" => $Semana_ISO , 
			"Mes" => $Mes , 
			"Anyo" => $Anyo , 
			"Usuario" => $Usuario , 
			"Evaluador" => $Evaluador , 
			"Fecha_Usuario" => $Fecha_Usuario ,
			"Fecha_Evaluador" => $Fecha_Evaluador ,
			"FechaCreacion" => $FechaCreacion , 
			"Med_Calidad" => $Med_Calidad , 
			"Med_Enfoque_Cliente" => $Med_Enfoque_Cliente , 
			"Med_En_Tiempo" => $Med_En_Tiempo , 
			"Med_Eficiencia" => $Med_Eficiencia , 
			"Med_Evaluador" => $Med_Evaluador , 
			"Automatica" => $Automatica , 
			"Periodo" => $Periodo , 
			"Comentario_Evaluador" => $Comentario_Evaluador , 
			"Num_Observaciones" => $Num_Observaciones , 
			
			"Usuario_Nombre" => $Usuario_Nombre , 
			"Evaluador_Nombre" => $Evaluador_Nombre, 
			"Tarea" => $Tarea , 
			"DescripcionTarea" => $DescripcionTarea,
			"AporteJefe" => $AporteJefe,
			
			"TiempoFichado" => $TiempoFichado,
			
			"Med_Calidad_Anterior" => $Med_Calidad_Anterior, 
			"Med_Evaluador_Anterior" => $Med_Evaluador_Anterior
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