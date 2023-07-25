<?php
// utf8 çáñá
class ColumnHelper
{
	public static function isValidColumn($dataIndx)
	{
		return true;
		if (preg_match('/^[a-z,A-Z]*$/', $dataIndx))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
class SortHelper
{
	public static function deSerializeSort_Simple($pq_sort)
	{
		$sorters = json_decode($pq_sort);
		$columns = array();
		$sortby = "";
		foreach ($sorters as $sorter)
		{
			$dataIndx = $sorter->dataIndx;
			$dir = $sorter->dir;

			if ($dir == "up")
			{
				$dir = "asc";
			}
			else
			{
				$dir = "desc";
			}
			if (ColumnHelper::isValidColumn($dataIndx))
			{
				$columns[] = '['. $dataIndx . ']' . " " . $dir;
			}
			else
			{
				throw new Exception("invalid column ".$dataIndx);
			}
		}
		if (sizeof($columns) > 0)
		{
			$sortby = " order by " . join(", ", $columns);
		}
		return $sortby;
	}
	public static function deSerializeSort($pq_sort,$table)
	{
		$sorters = json_decode($pq_sort);
		$columns = array();
		$sortby = "";
		foreach ($sorters as $sorter)
		{
			$dataIndx = $sorter->dataIndx;
			$dir = $sorter->dir;

			if ($dir == "up")
			{
				$dir = "asc";
			}
			else
			{
				$dir = "desc";
			}
			if (ColumnHelper::isValidColumn($dataIndx))
			{
				$columns[] = $table.'['. $dataIndx . ']' . " " . $dir;
			}
			else
			{
				throw new Exception("invalid column ".$dataIndx);
			}
		}
		if (sizeof($columns) > 0)
		{
			$sortby = " order by " . join(", ", $columns);
		}
		return $sortby;
	}
}
class FilterHelper
{
	public static function deSerializeFilter($pq_filter)
	{

		$filterObj = json_decode($pq_filter);
		$mode = $filterObj->mode;
		$filters = $filterObj->data;
		$fc = array();
		$param= array();
		foreach ($filters as $filter)
		{
			$dataIndx = utf8_encode($filter->dataIndx);
			if (ColumnHelper::isValidColumn($dataIndx) == false)
			{
				throw new Exception("Invalid column name");
			}
			$text = $filter->value;
			$condition = $filter->condition;
			if ($condition == "contain")
			{
				include_once "../php/funciones.php";
				include_once "../conf/config.php";
				include_once "../conf/config_".curPageName().".php";
				include_once "../../soporte/DB.php";
				include_once "../../soporte/funcionesgenerales.php";
				DBConectar($GLOBALS["cfg_DataBase"]);
				

				
				$nav=DBSelect(("SELECT [GestionPDT].[dbo].[CONVERT_NAV_FILTER] ('".'['.$dataIndx . ']'."','". $text."') AS Resultado"));
				for(;DBNext($nav);)
				{
					$fc[] = DBCampo($nav,("Resultado"));
					$param[] = $text;
				}
				DBFree($nav);
				

				
				
				
				DBClose();
			}
			else if ($condition == "notcontain")
			{
				$fc[] = '['.$dataIndx . ']'. " not like ('%".$text."%')";
				$param[] = $text;
			}
			else if ($condition == "begin")
			{
				$fc[] = '['.$dataIndx . ']'. " like ('".$text."%')";
				$param[] = $text;
			}
			else if ($condition == "end")
			{
				$fc[] = '['.$dataIndx . ']'. " like ('%".$text."')";
				$param[] = $text;
			}
			else if ($condition == "equal")
			{
				$fc[] = '['.$dataIndx . ']'. " = '".$text."'" ;
				$param[] = $text;
			}
			else if ($condition == "notequal")
			{
				$fc[] = '['.$dataIndx . ']'. " <> '".$text."'" ;
				$param[] = $text;
			}
			else if ($condition == "empty")
			{
				$fc[] = "ifnull(" . '['.$dataIndx . ']'. ",'')=''";
			}
			else if ($condition == "notempty")
			{
				$fc[] = "ifnull(" . '['.$dataIndx . ']'. ",'')!=''";
			}
			else if ($condition == "less")
			{
				$fc[] = '['.$dataIndx . ']'. " < ?";
				$param[] = $text;
			}
			else if ($condition == "great")
			{
				$fc[] = '['.$dataIndx . ']'. " > ?";
				$param[] = $text;
			}
		}
		$query = "";
		if (sizeof($filters) > 0)
		{
			$query = " where " . join(" ".$mode." ", $fc);
		}
		$ds = new stdClass();
		$ds->query = $query;
		$ds->param = $param;
		return $ds;
	}
}


//include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


$tarea = $_GET [ "tarea" ];
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
$contador = 0;
$filas_totales = 0;
$planificador = array ();

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$filtro_adicional = utf8_decode ( $filterQuery );


if ( $tarea !=  - 1 ) {
	if ( $filtro_adicional == "" ) {
		$filtro_adicional = " WHERE col.Id_Tarea = $tarea ";
	} else {
		$filtro_adicional = $filtro_adicional . " AND col.Id_Tarea = $tarea ";
	}
}

if ( $filtro_adicional == "" ) {
	$filtro_adicional = " WHERE COL.Estado = 'Activo' ";
} else {
	$filtro_adicional = $filtro_adicional . " AND COL.Estado = 'Activo' ";
}

$q = "
		SELECT *
		FROM (	SELECT 
					COL.[Id]
					,CAST((CONVERT(varchar(20), COL.[FechaAlta],103)) as varchar) 	AS FechaAlta   
					,COL.[Id_Tarea]
					,COL.[Colaborador]
					,COL.[Colaborador_Nombre]
					,COL.[Funciones]
					,COL.[Externo]
					,COL.[Acceso]
					,COL.[AporteOrganizacion]
					,COL.[AporteEmpresa]
					,COL.[AporteJefe]
					,ROW_NUMBER() OVER(  order by COL.[FechaAlta] desc  ) AS Fila
					,(	SELECT COUNT(*) 
						FROM [Colaboradores] AS COL  
						INNER JOIN [Tareas y Proyectos] as T ON  COL.Id_Tarea = T.Id $filtro_adicional  ) AS Num_Registros 
				FROM [Colaboradores] AS COL  
				INNER JOIN [Tareas y Proyectos] as T ON  COL.Id_Tarea = T.Id $filtro_adicional  ) AS Resultado 
		WHERE Fila > ($cur_page * $records_per_page )- $records_per_page AND Fila <= $cur_page *  $records_per_page  $sortQuery ";
//die($q);
$q = DBSelect ( utf8_decode ( $q ) );

for(; DBNext ( $q ) ;) {
	$Id = DBCampo ( $q , "Id" );
	$FechaAlta = DBCampo ( $q , "FechaAlta" );
	$Id_Tarea = DBCampo ( $q , "Id_Tarea" );
	$Colaborador = utf8_encode ( DBCampo ( $q , utf8_decode ( "Colaborador" ) ) );
	$Colaborador_Nombre = utf8_encode ( DBCampo ( $q , utf8_decode ( "Colaborador_Nombre" ) ) );
	$Funciones = utf8_encode ( DBCampo ( $q , utf8_decode ( "Funciones" ) ) );
	$Acceso = utf8_encode ( DBCampo ( $q , utf8_decode ( "Acceso" ) ) );
	$AporteOrganizacion = utf8_encode ( DBCampo ( $q , utf8_decode ( "AporteOrganizacion" ) ) );
	$AporteEmpresa = utf8_encode ( DBCampo ( $q , utf8_decode ( "AporteEmpresa" ) ) );
	$AporteJefe = utf8_encode ( DBCampo ( $q , utf8_decode ( "AporteJefe" ) ) );
	
	$filas_totales = DBCampo ( $q , "Num_Registros" );
	
	$color_acceso = '';
	
	if ( $Acceso == 'Denegado' ) {
		$color_acceso = 'pqcell_rojo';
	}
	
	if ( $Acceso == 'Permitido' ) {
		$color_acceso = 'pqcell_verde';
	}
	
	$color_aportes = '';
	if($AporteOrganizacion == '-' && $AporteEmpresa == '-'){
		$color_aportes = 'pqcell_rojo';
	}
	
	$pq_cellcls = array (
			"Acceso" => $color_acceso,
			"AporteOrganizacion" => $color_aportes,
			"AporteEmpresa" => $color_aportes
	);
	
	$planificador [ $contador ] = array (
			"Id" => $Id ,
			"Id_Tarea" => $Id_Tarea,
			"FechaAlta" => $FechaAlta,
			"Colaborador" => $Colaborador , 
			"Colaborador_Nombre" => $Colaborador_Nombre , 
			"Funciones" => $Funciones , 
			"Acceso" => $Acceso , 
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
		'data' => $planificador 
);
$php_json = json_encode ( $json_arr );

echo $php_json;
?>