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


$usuario = $_GET [ "usuario" ];
$estado = '';
if ( isset ( $_GET [ "estado" ] ) ) {
	$estado = $_GET ["estado"];
}

$cur_page = '1';
if ( isset ($_GET [ "pq_curpage" ]) && $_GET [ "pq_curpage" ] != 0 ){
	$cur_page = $_GET [ "pq_curpage" ];
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
$correos = array ();


DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$filtro_adicional = utf8_decode ( $filterQuery );

	if ($estado != ''){
		if ( $filtro_adicional == "" ) {
			$filtro_adicional = "WHERE Estado =".$estado;
		} else {
			$filtro_adicional = $filtro_adicional. " AND Estado = ".$estado;
		}
	}

$q = "
	SELECT *
	FROM (	
			SELECT [Id]
				--,(Select Name from [GestionIDM].dbo.LDAP Where sAMAccountName = Solicitado) as Solicitado
				,[Solicitado]
      			 ,[SolicitadoEmail]
      			 ,[Asunto]
			     ,CAST((CONVERT(varchar(20), FechaSolicitud,103)) as varchar) 	AS FechaSolicitud
				 ,SUBSTRING(CAST((CONVERT(varchar(20), FechaSolicitud,114)) as varchar),1,8) AS HoraSolicitud
			     ,[MensajeCorreo]
			     ,[Estado]
			     ,[Asignado]
			     ,[Prioridad]
		      	 ,[Oficina]
		      	 ,[MotivoRechazoOAceptacion]
			     ,[Tipo]
			     --,[Usuario]
				,(Select Name from [GestionIDM].dbo.LDAP Where sAMAccountName = Usuario)as [Usuario]
				 ,[Horas]
				 ,[Catalogacion]
				 ,(Select ObligadoHoras FROM [GestionPDT].[dbo].[Tipos] Where Tipo= 0 AND Descripcion = E.Tipo AND Oficina = E.Oficina) as Obligadas
				 ,[NoPlanificado]
				--,CAST((CONVERT(varchar(20), [FechaObjetivo],103)) as varchar) as FechaObjetivo
				--,ISNULL([FechaObjetivo], ' ') as FechaObjetivo
				-- ,CASE WHEN FechaObjetivo = NULL THEN '' ELSE CAST((CONVERT(varchar(20), FechaObjetivo, 103)) AS varchar) END AS FechaObjetivo
				,(CASE 
					WHEN FechaObjetivo is NULL THEN '' 
					WHEN FechaObjetivo = '1900-01-01' THEN ''	
					ELSE
						CAST((CONVERT(varchar(20), FechaObjetivo, 103)) AS varchar) 
					END) AS FechaObjetivo
				,[AreaSolicitante]
				,[Planta]
				,[RutaCorreo]
				,[IdTarea]
				,[Actividad]
				,(CASE 
					WHEN [FechaPlanificada] is NULL THEN '' 
					WHEN [FechaPlanificada] = '1900-01-01' THEN ''	
					ELSE
						CAST((CONVERT(varchar(20), [FechaPlanificada], 103)) AS varchar) 
					END) AS [FechaPlanificada]
				 ,ROW_NUMBER() OVER( $sortQuery  ) AS Fila
				 ,(	SELECT COUNT(*) 
					FROM [Entrada_Correo]  $filtro_adicional  ) AS Num_Registros 
		FROM [Entrada_Correo] as E $filtro_adicional  ) AS Resultado 
	WHERE  Fila > ($cur_page * $records_per_page )- $records_per_page AND Fila <= $cur_page *  $records_per_page  $sortQuery ";
//die($q);
$q = DBSelect ( utf8_decode ( $q ) );

for(; DBNext ( $q ) ;) {
	$Id = DBCampo ( $q , "Id" );
	$Solicitado =utf8_encode (DBCampo ( $q , utf8_decode ( "Solicitado" ) ));
	$SolicitadoEmail = utf8_encode (DBCampo ( $q , utf8_decode ( "SolicitadoEmail" ) ));
	$Asunto = utf8_encode (DBCampo ( $q , utf8_decode ( "Asunto" ) ) );
	$FechaSolicitud = utf8_encode ( DBCampo ( $q , utf8_decode ( "FechaSolicitud" ) ) );
	$HoraSolicitud = utf8_encode ( DBCampo ( $q , utf8_decode ( "HoraSolicitud" ) ) );
	$MensajeCorreo = utf8_encode(DBCampo ( $q , utf8_decode ( "MensajeCorreo" )  ));
	$Estado = utf8_encode ( DBCampo ( $q , utf8_decode ( "Estado" ) ) );
	$Asignado = utf8_encode ( DBCampo ( $q , utf8_decode ( "Asignado" ) ) );
	$Prioridad = utf8_encode ( DBCampo ( $q , utf8_decode ( "Prioridad" ) ) );
	$Oficina = utf8_encode ( DBCampo ( $q , utf8_decode ( "Oficina" ) ) );
	$MotivoRechazoOAceptacion = utf8_encode ( DBCampo ( $q , utf8_decode ( "MotivoRechazoOAceptacion" ) ) );
	$Tipo = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tipo" ) ) );
	$Usuario = utf8_encode ( DBCampo ( $q , utf8_decode ( "Usuario" ) ) );
	$Horas = utf8_encode ( DBCampo ( $q , utf8_decode ( "Horas" ) ) );
	$Catalogacion = utf8_encode ( DBCampo ( $q , utf8_decode ( "Catalogacion" ) ) );
	$Obligadas = utf8_encode( DBCampo ( $q  , utf8_decode ( "Obligadas" )  ) );
	$NoPlanificado = utf8_encode( DBCampo ( $q  , utf8_decode ( "NoPlanificado" )  ) );
	$FechaObjetivo = utf8_encode( DBCampo ( $q  , utf8_decode ( "FechaObjetivo" )  ) );
	$AreaSolicitante = utf8_encode( DBCampo ( $q  , utf8_decode ( "AreaSolicitante" )  ) );
	$Planta = utf8_encode( DBCampo ( $q  , utf8_decode ( "Planta" )  ) );
	$RutaCorreo =utf8_encode( DBCampo ( $q  , utf8_decode ( "RutaCorreo" )  ) );
	$IdTarea =utf8_encode( DBCampo ( $q  , utf8_decode ( "IdTarea" )  ) );
	
	$Fila = utf8_encode ( DBCampo ( $q , utf8_decode ( "Fila" ) ) );
	$Actividad = utf8_encode(DBCampo ( $q , utf8_decode ( "Actividad" )  ));
	
	$FechaPlanificada = utf8_encode( DBCampo ( $q  , utf8_decode ( "FechaPlanificada" )  ) );
	$filas_totales = DBCampo ( $q , "Num_Registros" );
	
	//$MensajeCorreo = strip_tags($MensajeCorreo, "<img>");
	
	$MensajeCorreo = preg_replace("/<img[^>]+\>/i", " ", $MensajeCorreo);
	$MensajeCorreo = preg_replace("/<hr[^>]+\>/i", " ", $MensajeCorreo);
	$MensajeCorreo = preg_replace("/<font[^>]+\>/i", " ", $MensajeCorreo);
	
	/*
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
	}
	
	$color_tiempo_real_dia = '';
	if($Tiempo_Real_Dia >= $Tiempo_Estimado){
		$color_tiempo_real_dia = 'pqcell_rojo';
	}
	
	$color_tiempo_real = '';
	if($Tiempo_Real >= $Tiempo_Estimado_Tarea){
		$color_tiempo_real = 'pqcell_rojo';
	}
	
	$pq_cellcls = array (
			"Situacion" => $color_situacion,
			"Tiempo_Real_Dia" => $color_tiempo_real_dia,
			"Tiempo_Real_Semana" => $color_tiempo_real_semana,
			"Tiempo_Real" => $color_tiempo_real
	);
	*/
	
	$correos [ $contador ] = array (
			"Id" => $Id ,
			"Solicitado" => $Solicitado ,
			"SolicitadoEmail" => $SolicitadoEmail ,
			"Asunto" => $Asunto ,
			"FechaSolicitud" => $FechaSolicitud ,
			"HoraSolicitud" => $HoraSolicitud ,
			"MensajeCorreo" => $MensajeCorreo ,
			"Estado" => $Estado ,
			"Asignado" => $Asignado ,
			"Prioridad" => $Prioridad ,
			"Oficina" => $Oficina ,
			"MotivoRechazoOAceptacion" => $MotivoRechazoOAceptacion ,
			"Tipo" => $Tipo ,
			"Usuario" => $Usuario ,
			"Horas" => $Horas ,
			"Catalogacion" => $Catalogacion,
			"Obligadas" => $Obligadas,
			"NoPlanificado" => $NoPlanificado,
			"FechaObjetivo" => $FechaObjetivo,
			"AreaSolicitante" => $AreaSolicitante,
			"Planta" => $Planta,
			"RutaCorreo" => $RutaCorreo,
			"IdTarea" => $IdTarea,
			"Fila" => $Fila ,
			"Actividad" => $Actividad,
			"FechaPlanificada" => $FechaPlanificada
			//"pq_cellcls" => $pq_cellcls 
	);
	$contador = $contador + 1;
}

DBFree ( $q );
DBClose ();

$json_arr = array (
		'totalRecords' => $filas_totales ,
		'curPage' => $cur_page ,
		'data' => $correos
);
$php_json = json_encode ( $json_arr );

echo $php_json;


?>