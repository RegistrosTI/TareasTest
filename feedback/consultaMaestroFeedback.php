<?php
// utf8 çáñá
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
include_once "../conf/config_" . curPageName () . ".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$usuario = $_GET [ "usuario" ];

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




DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

// OBTENER LAS OFICINAS Y AMBITO DEL USUARIO
$oficinas = '';
$ambito = 1;
$evaluador = 0;
$q = "SELECT Oficina, Ambito, Evaluador FROM Configuracion_Usuarios WHERE Usuario = '$usuario' ";
$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$oficinas = utf8_encode ( DBCampo ( $q , utf8_decode ( "Oficina" ) ) );
	$ambito = utf8_encode ( DBCampo ( $q , utf8_decode ( "Ambito" ) ) );
	$evaluador = utf8_encode ( DBCampo ( $q , utf8_decode ( "Evaluador" ) ) );
}
$oficinas = "'" . str_replace ( "," , "','" , $oficinas ) . "'";
DBFree ( $q );

$filtro_adicional = "";
$filtro_adicional = utf8_decode ( $filterQuery );

//die($filtro_adicional );

if ( $filtro_adicional == "" ) {
	$filtro_adicional = " WHERE ";
} else {
	$filtro_adicional = $filtro_adicional . " AND ";
}

$filtro_adicional = $filtro_adicional . " (oficina in ($oficinas) OR ((oficina IS NULL or oficina = '') AND Usuario = '$usuario')) AND (Borrada IS NULL OR Borrada <> 'SI') ";

$q = "
	SELECT *
	FROM (	SELECT 
				Id
				,Oficina 
				,Tipo 
				,Pregunta 
				,TipoFeedback 
				,ROW_NUMBER() OVER(  order by [Id] desc  ) AS Fila
				,(	SELECT COUNT(*) 
					FROM [GestionPDT].[dbo].[Feedback_Preguntas]
					$filtro_adicional ) AS Num_Registros 
			FROM [GestionPDT].[dbo].[Feedback_Preguntas]
			$filtro_adicional
		) AS Resultado 
	WHERE Fila > ($cur_page * $records_per_page ) - $records_per_page AND Fila <= $cur_page *  $records_per_page  $sortQuery ;
	
";

//die($q);

$q = DBSelect ( utf8_decode ( $q ) );
$data = array();
$contador = 0;
$filas_totales = 0;
for(; DBNext ( $q ) ;) {

	$Id = utf8_encode ( DBCampo ( $q , utf8_decode ( "Id" ) ) );
	$Oficina = utf8_encode ( DBCampo ( $q , utf8_decode ( "Oficina" ) ) );
	$Tipo = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tipo" ) ) );
	$Pregunta = utf8_encode ( DBCampo ( $q , utf8_decode ( "Pregunta" ) ) );
	$TipoFeedback = utf8_encode ( DBCampo ( $q , utf8_decode ( "TipoFeedback" ) ) );
	
	$filas_totales = DBCampo ( $q , "Num_Registros" );
	
	$colorOficina = '';
	if ( $Oficina == '' ) {
		$colorOficina = 'pqcell_rojo';
	}
	$colorTipo = '';
	if ( $Tipo == '' ) {
		$colorTipo = 'pqcell_rojo';
	}
	$colorPregunta = '';
	if ( $Pregunta == '' ) {
		$colorPregunta = 'pqcell_rojo';
	}
	$colorTipoFeedback = '';
	if ( $TipoFeedback == '' ) {
		$colorTipoFeedback = 'pqcell_rojo';
	}
	
	$pq_cellcls = array (
			"Oficina" => $colorOficina,
			"Tipo" => $colorTipo,
			"Pregunta" => $colorPregunta,
			"TipoFeedback" => $colorTipoFeedback
	);
	
	$data [ $contador ] = array (
			"Id" => $Id ,
			"Oficina" => $Oficina ,
			"Tipo" => $Tipo ,
			"Pregunta" => $Pregunta ,
			"TipoFeedback" => $TipoFeedback ,
			"pq_cellcls" => $pq_cellcls 
	);
	$contador = $contador + 1;
}

DBFree ( $q );
DBClose ();

$json_arr = array (
		'totalRecords' => $filas_totales ,
		'curPage' => $cur_page ,
		'data' => $data
);
$php_json = json_encode ( $json_arr );

echo $php_json;
?>