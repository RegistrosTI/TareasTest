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
include_once "../conf/config_MENU.php";
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


$filtro_adicional = utf8_decode ( $filterQuery );

if ( $filtro_adicional == "" ) {
	$filtro_adicional = " WHERE TIP.Oficina IN ($oficinas) ";
} else {
	$filtro_adicional = $filtro_adicional . " AND TIP.Oficina IN ($oficinas) ";
}

if ( $filtro_adicional == "" ) {
	$filtro_adicional = " WHERE TIP.Descripcion <> '-' ";
} else {
	$filtro_adicional = $filtro_adicional . " AND TIP.Descripcion <> '-' ";
}

if($evaluador == 0){
	if ( $filtro_adicional == "" ) {
		$filtro_adicional = " WHERE [IdUsuario] = '$usuario' ";
	} else {
		$filtro_adicional = $filtro_adicional . " AND [IdUsuario] = '$usuario' ";
	}
}

$insert = "
	INSERT INTO ActividadesUsuario (
				IdTipo
				,NumTipo
				,OficinaTipo
				,IdUsuario
				,AporteOrganizacion
				,AporteEmpresa
				,AporteJefe)
		SELECT
			T.Tipo
			,T.Numero
			,T.Oficina
			,'$usuario'
			,'-'
			,'-'
			,'-'
		FROM Tipos AS T LEFT OUTER JOIN ActividadesUsuario AS A
		ON T.Tipo = A.IdTipo
			AND T.Numero = A.NumTipo
			AND T.Oficina = A.OficinaTipo
			AND A.IdUsuario = '$usuario'
		WHERE A.Id IS null
			AND T.Tipo = 106
			AND T.visible = 1
			AND T.Oficina IN ($oficinas) ;
	";


DBSelect ( utf8_decode ( $insert ) );

$q = "
	SELECT *
	FROM (	SELECT 
			[OficinaTipo]
			,[Id]
			,[IdUsuario] 
			,[AporteOrganizacion] 
			,[AporteEmpresa] 
			,[AporteJefe] 
			,TIP.Descripcion AS Actividad
				,ROW_NUMBER() OVER(  order by ACT.[Id] desc  ) AS Fila
				,(	SELECT COUNT(*) 
					FROM [ActividadesUsuario] AS ACT  
					INNER JOIN Tipos as TIP ON ACT.IdTipo = TIP.Tipo AND TIP.Numero = ACT.NumTipo AND TIP.visible = 1 $filtro_adicional  ) AS Num_Registros 
			FROM [ActividadesUsuario] AS ACT  
			INNER JOIN Tipos as TIP ON  ACT.IdTipo = TIP.Tipo  AND TIP.Numero = ACT.NumTipo  AND TIP.visible = 1 $filtro_adicional  ) AS Resultado 
	WHERE Fila > ($cur_page * $records_per_page ) - $records_per_page AND Fila <= $cur_page *  $records_per_page  $sortQuery ;
	
";


$q = DBSelect ( utf8_decode ( $q ) );
$data = array();
$contador = 0;
$filas_totales = 0;
for(; DBNext ( $q ) ;) {

	$Id = utf8_encode ( DBCampo ( $q , utf8_decode ( "Id" ) ) );
	$IdUsuario = utf8_encode ( DBCampo ( $q , utf8_decode ( "IdUsuario" ) ) );
	$Actividad = utf8_encode ( DBCampo ( $q , utf8_decode ( "Actividad" ) ) );
	$AporteOrganizacion = utf8_encode ( DBCampo ( $q , utf8_decode ( "AporteOrganizacion" ) ) );
	$AporteEmpresa = utf8_encode ( DBCampo ( $q , utf8_decode ( "AporteEmpresa" ) ) );
	$AporteJefe = utf8_encode ( DBCampo ( $q , utf8_decode ( "AporteJefe" ) ) );
	$OficinaTipo = utf8_encode ( DBCampo ( $q , utf8_decode ( "OficinaTipo" ) ) );
	
	$filas_totales = DBCampo ( $q , "Num_Registros" );
	
	$colorAportes = '';
	if ( $AporteOrganizacion == '-' && $AporteEmpresa == '-' ) {
		$colorAportes = 'pqcell_rojo';
	}
	
	$colorAporteJefe = '';
	if ( $AporteJefe == '-' ) {
		$colorAporteJefe = 'pqcell_rojo';
	}
	
	$pq_cellcls = array (
			"AporteOrganizacion" => $colorAportes,
			"AporteEmpresa" => $colorAportes,
			"AporteJefe" => $colorAporteJefe
	);
	
	$data [ $contador ] = array (
			"OficinaTipo" => $OficinaTipo ,
			"Id" => $Id ,
			"IdUsuario" => $IdUsuario ,
			"Actividad" => $Actividad ,
			"AporteOrganizacion" => $AporteOrganizacion ,
			"AporteEmpresa" => $AporteEmpresa ,
			"AporteJefe" => $AporteJefe ,
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