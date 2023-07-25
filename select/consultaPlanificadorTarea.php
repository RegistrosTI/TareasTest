<?php
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
			$dataIndx = utf8_encode ( $filter -> dataIndx );
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
				DBConectar ( $GLOBALS [ "cfg_DataBase" ] );
				
				$nav = DBSelect ( ( "SELECT [GestionPDT].[dbo].[CONVERT_NAV_FILTER] ('" . '[' . $dataIndx . ']' . "','" . $text . "') AS Resultado" ) );
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

// include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

// SI NO TENGO TAREA (-1) ES QUE SE DEBE MOSTRAR EN EL GRID DE TODAS LAS TAREAS CON TODAS LAS TAREAS QUE TENGO ASIGNADAS A MI
// SI NO VENGO CON TAREAS DEBO CONSULTAR SI SOY EVALUADOR Y SI LO SOY MOSTRAR TODAS LAS PLANIFICACIONES
// SI VIENE CON TAREA ES EL GRID QUE SE ABRE DESDE DENTRO DE LA TAREA CON LA PLANIFICACION DE UNA SOLA TAREA
$tarea = $_GET [ "tarea" ];

$usuario = $_GET [ "usuario" ];
$evaluador = $_GET [ "evaluador" ];
$planificador_comun = 0;
if(isset($_GET [ "planificador_comun" ])){
	$planificador_comun = $_GET [ "planificador_comun" ];
}

$oficinausuariovalidado = $_GET [ "oficinausuariovalidado" ];
$semanaamostrar = -1;
if(isset($_GET [ "semana" ])){
	$semanaamostrar = $_GET [ "semana" ];
}
$tareasvivas = 0;
if(isset($_GET [ "tareasvivas" ])){
	$tareasvivas = $_GET [ "tareasvivas" ];
}
$oficinausuariovalidado = str_replace(",", "','", $oficinausuariovalidado);
$oficinausuariovalidado = "'".$oficinausuariovalidado ."'";

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
$planificador = array ();

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

// Si es evaluador o el planificador es común, se ve todo
$filtro_adicional = utf8_decode ( $filterQuery );
if ( $evaluador == 0 && $planificador_comun == 0) {
	if ( $tarea ==  - 1 ) {
		if ( $filtro_adicional == "" ) {
			$filtro_adicional = " WHERE [Asignado_Usuario] = '$usuario' ";
		} else {
			$filtro_adicional = $filtro_adicional . " AND [Asignado_Usuario] = '$usuario' ";
		}
	}
}

if ( $tarea !=  - 1 ) {
	if ( $filtro_adicional == "" ) {
		$filtro_adicional = " WHERE Tarea = $tarea ";
	} else {
		$filtro_adicional = $filtro_adicional . " AND Tarea = $tarea ";
	}
}

if ( $tareasvivas ==  1 ) {
	if ( $filtro_adicional == "" ) {
		$filtro_adicional = " WHERE 0 = (SELECT COUNT(Finalizado) from Tipos TIP where TIP.Tipo = 5 and oficina = T.Oficina and TIP.Finalizado = 1 and TIP.Descripcion = T.Estado ) ";
	} else {
		$filtro_adicional = $filtro_adicional . " AND 0 = (SELECT COUNT(Finalizado) from Tipos TIP where TIP.Tipo = 5 and oficina = T.Oficina and TIP.Finalizado = 1 and TIP.Descripcion = T.Estado ) ";
	}
}
if ( $filtro_adicional == "" ) {
	$filtro_adicional = " WHERE P.Estado = 'Activo' ";
} else {
	$filtro_adicional = $filtro_adicional . " AND P.Estado = 'Activo' ";
}

if ( $filtro_adicional == "" ) {
	$filtro_adicional = " WHERE T.Oficina IN ($oficinausuariovalidado) ";
} else {
	$filtro_adicional = $filtro_adicional . " AND T.Oficina IN ($oficinausuariovalidado) ";
}

/*
if ( $filtro_adicional == "" ) {
	$filtro_adicional = " WHERE T.Oficina IN ($oficinausuariovalidado) ";
} else {
	$filtro_adicional = $filtro_adicional . " AND T.Oficina IN ($oficinausuariovalidado) ";
}
*/


if( isset($_GET["tipo"])){
	$tipo = $_GET["tipo"];
	if($tipo == 0){	
		if ( $filtro_adicional == "" ) {
			//$filtro_adicional = " WHERE Anyo = YEAR(GETDATE())";
			$filtro_adicional = " WHERE (Anyo = YEAR(GETDATE()) OR Anyo IS NULL)";
		} else {
			//$filtro_adicional = $filtro_adicional . " AND Anyo = YEAR(GETDATE()) ";
			$filtro_adicional = $filtro_adicional . " AND (Anyo = YEAR(GETDATE()) OR Anyo IS NULL) ";
		}
	}
}

if ( $semanaamostrar !=  - 1 ) {
	if ( $filtro_adicional == "" ) {
		$filtro_adicional = " WHERE Semana = $semanaamostrar ";
	} else {
		$filtro_adicional = $filtro_adicional . " AND Semana = $semanaamostrar ";
	}
}

// ++ BUSCAR SI HAY TAREA ABIERTA
$query = " SELECT TOP 1 Tarea FROM [Horas] where usuario = '".utf8_encode($usuario)."' AND Fin is null ";
$query=DBSelect(utf8_decode($query));
$tarea_abierta = -1;
for(;DBNext($query);){
	$tarea_abierta = DBCampo($query,"Tarea");
}
DBFree ( $query );
// -- BUSCAR SI HAY TAREA ABIERTA

$q = "
	SELECT *
	FROM (	SELECT 
				 P.Id
				,CAST((CONVERT(varchar(20), Fecha,103)) as varchar) 	AS Fecha   
				,(SELECT COUNT(*) FROM [GestionPDT].[dbo].[Horas] WHERE Usuario = '$usuario' AND Fin IS NULL AND Tarea = P.Tarea) AS Iniciada
				,Anyo
				,Mes
				,Semana
				,Observaciones
				,Comentario_Adicional
				,[Asignado_Nombre]
				,[Asignado_Usuario]
				,[Situacion]
				,p.[Prioridad]
				,[No_Planificado]
				,[Tarea]
				,ISNULL(Tiempo_Estimado,0) 																AS Tiempo_Estimado 
				,T.[Título]
				,ISNULL(CAST((CONVERT(varchar(20), T.[Fecha objetivo],103)) as varchar) ,'')			AS Fecha_Objetivo   
				,ISNULL(T.[Horas estimadas], 0) * 60 													AS Tiempo_Estimado_Tarea
				,(SELECT SUM(Minutos) 
					FROM Horas HORAS 
					WHERE P.Asignado_Usuario = HORAS.Usuario AND P.Tarea = HORAS.Tarea) 				AS Tiempo_Real
				,(SELECT SUM(Minutos) 
					FROM Horas HORAS 
					WHERE P.Asignado_Usuario = HORAS.Usuario 
						AND P.Tarea = HORAS.Tarea
						AND Inicio >= Fecha 
						AND Fin < DATEADD(DAY,1,Fecha)and Fin is not null)								AS Tiempo_Real_Dia
				,(SELECT SUM(Minutos) 
					FROM Horas HORAS 
					WHERE P.Asignado_Usuario = HORAS.Usuario 
						AND P.Tarea = HORAS.Tarea
						AND P.Semana = DATEPART(ISO_WEEK,[HORAS].Inicio) 
						AND P.Anyo = dbo.ISOyear([HORAS].Inicio) )										AS Tiempo_Real_Semana
				,(SELECT Predeterminado 
					FROM TIPOS AS T 
					WHERE p.situacion = t.descripcion 
					AND T.TIPO = 13 
					AND T.OFICINA = (SELECT OFICINA FROM [Tareas y Proyectos] WHERE ID = P.Tarea)    ) AS SITUACION_PREDETERMINADO
				,(SELECT Pendiente 
					FROM TIPOS AS T 
					WHERE p.situacion = t.descripcion 
					AND T.TIPO = 13 
					AND T.OFICINA = (SELECT OFICINA FROM [Tareas y Proyectos] WHERE ID = P.Tarea)    ) AS SITUACION_PENDIENTE
				,(SELECT Finalizado
					FROM TIPOS AS T 
					WHERE p.situacion = t.descripcion 
					AND T.TIPO = 13 
					AND T.OFICINA = (SELECT OFICINA FROM [Tareas y Proyectos] WHERE ID = P.Tarea)    ) AS SITUACION_TERMINADO
				,(SELECT EnCurso
					FROM TIPOS AS T 
					WHERE p.situacion = t.descripcion 
					AND T.TIPO = 13 
					AND T.OFICINA = (SELECT OFICINA FROM [Tareas y Proyectos] WHERE ID = P.Tarea)    ) AS SITUACION_ENCURSO
				,(select Estado from [Tareas y Proyectos] tar where tar.Id = p.Tarea ) as EstadoTarea
				,ROW_NUMBER() OVER( $sortQuery  ) AS Fila
				,(	SELECT COUNT(*) 
					FROM [Planificador] AS P  
			INNER JOIN [Tareas y Proyectos] as T ON  P.Tarea = T.Id $filtro_adicional  ) AS Num_Registros 
			FROM [Planificador] AS P  
			inner join [Tareas y Proyectos] as T ON  P.Tarea = T.Id $filtro_adicional  ) AS Resultado 
	WHERE Fila > ($cur_page * $records_per_page )- $records_per_page AND Fila <= $cur_page *  $records_per_page  $sortQuery ";
//die($q);
$q = DBSelect ( utf8_decode ( $q ) );

for(; DBNext ( $q ) ;) {
	$Id = DBCampo ( $q , "Id" );
	$Fecha = DBCampo ( $q , "Fecha" );
	$Anyo = DBCampo ( $q , "Anyo" );
	$Mes = utf8_encode ( DBCampo ( $q , utf8_decode ( "Mes" ) ) );
	$Semana = utf8_encode ( DBCampo ( $q , utf8_decode ( "Semana" ) ) );
	$Observaciones = utf8_encode ( DBCampo ( $q , utf8_decode ( "Observaciones" ) ) );
	$Comentario_Adicional = utf8_encode ( DBCampo ( $q , utf8_decode ( "Comentario_Adicional" ) ) );
	$Asignado_Nombre = utf8_encode ( DBCampo ( $q , utf8_decode ( "Asignado_Nombre" ) ) );
	$Asignado_Usuario = utf8_encode ( DBCampo ( $q , utf8_decode ( "Asignado_Usuario" ) ) );
	$Situacion = utf8_encode ( DBCampo ( $q , utf8_decode ( "Situacion" ) ) );
	$Tarea = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tarea" ) ) );
	$Titulo = utf8_encode ( DBCampo ( $q , utf8_decode ( "Título" ) ) );
	$Prioridad = utf8_encode ( DBCampo ( $q , utf8_decode ( "Prioridad" ) ) );
	
	$Fecha_Objetivo = utf8_encode ( DBCampo ( $q , utf8_decode ( "Fecha_Objetivo" ) ) );
	$Tiempo_Estimado = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tiempo_Estimado" ) ) );
	$Tiempo_Estimado_Tarea = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tiempo_Estimado_Tarea" ) ) );
	$Tiempo_Real = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tiempo_Real" ) ) );
	$Tiempo_Real_Dia = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tiempo_Real_Dia" ) ) );
	$Tiempo_Real_Semana = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tiempo_Real_Semana" ) ) );
	
	$SITUACION_PREDETERMINADO = utf8_encode ( DBCampo ( $q , utf8_decode ( "SITUACION_PREDETERMINADO" ) ) );
	$SITUACION_PENDIENTE = utf8_encode ( DBCampo ( $q , utf8_decode ( "SITUACION_PENDIENTE" ) ) );
	$SITUACION_TERMINADO = utf8_encode ( DBCampo ( $q , utf8_decode ( "SITUACION_TERMINADO" ) ) );
	$SITUACION_ENCURSO = utf8_encode ( DBCampo ( $q , utf8_decode ( "SITUACION_ENCURSO" ) ) );
	
	$EstadoTarea = utf8_encode ( DBCampo ( $q , utf8_decode ( "EstadoTarea" ) ) );
	
	$Iniciada = utf8_encode ( DBCampo ( $q , utf8_decode ( "Iniciada" ) ) );
	
	//T#28631 - JUAN ANTONIO ABELLAN - (07/02/2019) - (CCARRASCOSA)
	$NO_PLANIFICADO = utf8_encode ( DBCampo ( $q , utf8_decode ( "No_Planificado" ) ) );
	$filas_totales = DBCampo ( $q , "Num_Registros" );
	
	$color_noplanificado='';
	$color_situacion = '';
	
	if ( $SITUACION_PREDETERMINADO == '1' ) {
		$color_situacion = 'pqcell_rojo';
	}
	if ( $SITUACION_PENDIENTE == '1' ) {
		$color_situacion = 'pqcell_amarillo';
	}
	if ( $SITUACION_TERMINADO == '1' ) {
		$color_situacion = 'pqcell_verde';
	}
	if ( $SITUACION_ENCURSO == '1' ) {
		$color_situacion = 'pqcell_NARANJA';
	}
	
	$color_tiempo_real_semana = '';
	if($Tiempo_Real_Semana >= $Tiempo_Estimado){
		$color_tiempo_real_semana = 'pqcell_rojo';
	}else{
		if($Tiempo_Real_Semana > 0 && $Tiempo_Real_Semana < $Tiempo_Estimado){
			$color_tiempo_real_semana = 'pqcell_verde';
		}
	}
	
	$color_tiempo_real_dia = '';
	if($Tiempo_Real_Dia >= $Tiempo_Estimado){
		$color_tiempo_real_dia = 'pqcell_rojo';
	}else{
		if($Tiempo_Real_Dia > 0 && $Tiempo_Real_Dia < $Tiempo_Estimado){
			$color_tiempo_real_dia = 'pqcell_verde';
		}
	}
	
	$color_tiempo_real = '';
	if($Tiempo_Real >= $Tiempo_Estimado_Tarea){
		$color_tiempo_real = 'pqcell_rojo';
	}else{
		if($Tiempo_Real > 0 && $Tiempo_Real < $Tiempo_Estimado_Tarea){
			$color_tiempo_real = 'pqcell_verde';
		}
	}
	
	$color_prioridad = '';
	if($Prioridad == 'Alta'){
		$color_prioridad = 'rojo';
	}
	
	$pq_cellcls = array (
			"Situacion" => $color_situacion,
			"Tiempo_Real_Dia" => $color_tiempo_real_dia,
			"Tiempo_Real_Semana" => $color_tiempo_real_semana,
			"Tiempo_Real" => $color_tiempo_real,
			"Prioridad" => $color_prioridad
	);
	
	if($NO_PLANIFICADO == 'SI'){
		$color_noplanificado ='pqcell_NOPLANIFICADO';
	}
	
	
	$Tiempo_Estimado = number_format(round((int)$Tiempo_Estimado / 60 , 2), 2, '.', '');
	$pq_rowcls = $color_noplanificado;
	
	$Tiempo_Estimado_Tarea = number_format(round((int)$Tiempo_Estimado_Tarea / 60 , 2), 2, '.', '');
	$Tiempo_Real_Dia = number_format(round((int)$Tiempo_Real_Dia / 60 , 2), 2, '.', '');
	$Tiempo_Real_Semana = number_format(round((int)$Tiempo_Real_Semana / 60 , 2), 2, '.', '');
	$Tiempo_Real = number_format(round((int)$Tiempo_Real / 60 , 2), 2, '.', '');
	
	$planificador [ $contador ] = array (
			"Id" => $Id ,
			"Fecha" => $Fecha ,
			"Anyo" => $Anyo ,
			"Mes" => $Mes ,
			"Semana" => $Semana ,
			"Observaciones" => $Observaciones ,
			"Comentario_Adicional" => $Comentario_Adicional ,
			"Asignado_Nombre" => $Asignado_Nombre ,
			"Asignado_Usuario" => $Asignado_Usuario ,
			"No_Planificado" => $NO_PLANIFICADO,
			"Situacion" => $Situacion ,
			"Tarea" => $Tarea ,
			"Tarea_Abierta" => $tarea_abierta ,
			"Título" => $Titulo ,
			"Fecha_Objetivo" => $Fecha_Objetivo ,
			"Tiempo_Estimado" => $Tiempo_Estimado ,
			"Tiempo_Estimado_Tarea" => $Tiempo_Estimado_Tarea ,
			"Tiempo_Real_Dia" => $Tiempo_Real_Dia ,
			"Tiempo_Real_Semana" => $Tiempo_Real_Semana ,
			"Tiempo_Real" => $Tiempo_Real ,
			"EstadoTarea" => $EstadoTarea,
			"Prioridad" => $Prioridad,
			"Iniciada" => $Iniciada,
			"pq_cellcls" => $pq_cellcls,
			"pq_rowcls" => $pq_rowcls
	);
	$contador = $contador + 1;
}

DBFree ( $q );
DBClose ();

$json_arr = array (
		'totalRecords' => $filas_totales ,
		'curPage' => $cur_page ,
		'data' => $planificador 
);
$php_json = json_encode ( $json_arr );

echo $php_json;


?>