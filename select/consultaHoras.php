<?php
//include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

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
			
			if($dataIndx == 'Dia Fin' || $dataIndx == 'Hora Fin'){
				$dataIndx = 'Fin';
			}
			
			if($dataIndx == 'Dia Inicio' || $dataIndx == 'Hora Inicio'){
				$dataIndx = 'Inicio';
			}
			
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
			
			if($dataIndx == 'Dia Fin' || $dataIndx == 'Hora Fin'){
				$dataIndx == 'Fin';
			}
			
			if($dataIndx == 'Dia Inicio' || $dataIndx == 'Hora Inicio'){
				$dataIndx == 'Inicio';
			}
			
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
			$dataIndx = $filter->dataIndx;
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

$tarea = $_GET [ "tarea" ];

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

$filtro_adicional = utf8_decode ( $filterQuery );

// ++ GESTION DEL FILTRO POR TAREA
if ( $filtro_adicional == "" ) {
	$filtro_adicional = ' WHERE Tarea = ' . $tarea;
} else {
	$filtro_adicional = $filtro_adicional . ' AND Tarea = ' . $tarea;
}
// -- GESTION DEL FILTRO POR TAREA

// ++ GESTION DEL FILTRO POR PERIODO
$periodo = '';
if ( isset ( $_GET [ "periodo" ] ) ) {
	$periodo = $_GET [ "periodo" ];
}
$fechaCreacion = '';
if ( isset ( $_GET [ "fechaCreacion" ] ) ) {
	$fechaCreacion = $_GET [ "fechaCreacion" ];
}
$filtro_periodo = '';
if($periodo != '' && $fechaCreacion != '' ){
	
	if ( $filtro_adicional == "" ) {
		$filtro_periodo = ' WHERE ';
	}else{
		$filtro_periodo = ' AND ';
	}
	
	if($periodo == 'MENSUAL'){
		$filtro_periodo = $filtro_periodo . " MONTH(inicio) = MONTH('$fechaCreacion') AND YEAR(inicio) = YEAR('$fechaCreacion') ";
	}
	if($periodo == 'SEMANAL'){
		$filtro_periodo = $filtro_periodo . " DATEPART(WEEK,inicio) = DATEPART(WEEK,'$fechaCreacion') AND YEAR(inicio) = YEAR('$fechaCreacion')  ";
	}
	
	$filtro_adicional = $filtro_adicional . $filtro_periodo;
}

// -- GESTION DEL FILTRO POR PERIODO

// ++ GESTION DEL FILTRO POR USUARIO
$usuario = '';
$filtro_usuario = '';
if ( isset ( $_GET [ "usuario" ] ) && $_GET [ "usuario" ]  != '' ) {
	$usuario = $_GET [ "usuario" ];
	
	if ( $filtro_adicional == "" ) {
		$filtro_usuario = ' WHERE ';
	}else{
		$filtro_usuario = ' AND ';
	}
	
	$filtro_usuario = $filtro_usuario . " Usuario = '$usuario' ";
	$filtro_adicional = $filtro_adicional . $filtro_usuario;
}


// ++ GESTION DEL FILTRO POR USUARIO
//die($filtro_adicional);

$q = DBSelect ( utf8_decode ( "
		Select 
			CAST(Tipo as varchar(MAX)) 								AS Tipo
			,CAST(Comentario as varchar(MAX)) 						AS Comentario
			,CASE WHEN Fin IS NULL THEN CASE 	WHEN DATEADD(dd, 0, DATEDIFF(dd, 0, GETDATE())) <>
														DATEADD(dd, 0, DATEDIFF(dd, 0, Inicio)) THEN 0 ELSE 1 END ELSE -1 
												END 				AS ESTADO_ACTUAL
			,Numero 												AS Numero
			,Tarea 													AS Tarea
			,CAST((CONVERT(varchar(20), Inicio,108)) as varchar) 	AS Hora_Inicio
			,CAST((CONVERT(varchar(20), Inicio,103)) as varchar) 	AS Fecha_Inicio      
			,CAST((CONVERT(varchar(20), Fin,108)) as varchar) 		AS Hora_Fin      
			,CAST((CONVERT(varchar(20), Fin,103)) as varchar) 		AS Fecha_Fin
			,Inicio 												AS Inicio
			,Fin 													AS Fin
			,CAST(Usuario as varchar(255)) 							AS Usuario
			,Fila
			,ISNULL(Minutos,'')										AS Minutos
			,Num_Registros 
			,ISNULL(Gasto,'')										AS Gasto
			,ISNULL(Aporte,'')										AS Aporte
			,ISNULL(AporteEmpresa,'')								AS AporteEmpresa
	      ,[Anyo_ISO]
	      ,[Semana_ISO]
	      ,[Anyo]
	      ,[Mes]
		FROM (	SELECT 
					Tipo
					,Comentario
					,Numero
					,Tarea
					,Inicio
					,Minutos
					,Fin
					,Usuario
					,Gasto
					,Aporte
					,AporteEmpresa
			      ,[Anyo_ISO]
			      ,[Semana_ISO]
			      ,[Anyo]
			      ,[Mes]
					,ROW_NUMBER() OVER(" . utf8_decode ( $sortQuery ) . ") AS Fila
					,(	SELECT COUNT(*) 
						FROM [Horas] " ) . $filtro_adicional . utf8_decode ( " ) AS Num_Registros FROM [Horas] " ) . $filtro_adicional . utf8_decode ( " ) AS Resultado 
		WHERE Fila > (" . $cur_page . "*" . $records_per_page . ")-" . $records_per_page . " AND Fila<= " . $cur_page . "*" . $records_per_page . " " . utf8_decode ( $sortQuery ) . " " ) );

for(; DBNext ( $q ) ;) {
	$numero_hora = DBCampo ( $q , "Numero" );
	$estado_actual = DBCampo ( $q , "ESTADO_ACTUAL" );
	$Inicio = DBCampo ( $q , "Inicio" );
	$tarea_hora = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tarea" ) ) );
	$inicio_hora = utf8_encode ( DBCampo ( $q , utf8_decode ( "Hora_Inicio" ) ) );
	$fin_hora = utf8_encode ( DBCampo ( $q , utf8_decode ( "Hora_Fin" ) ) );
	$inicio_dia = utf8_encode ( DBCampo ( $q , utf8_decode ( "Fecha_Inicio" ) ) );
	$fin_dia = utf8_encode ( DBCampo ( $q , utf8_decode ( "Fecha_Fin" ) ) );
	$usuario_hora = utf8_encode ( DBCampo ( $q , utf8_decode ( "Usuario" ) ) );
	$usuario_comentario = utf8_encode ( DBCampo ( $q , utf8_decode ( "Comentario" ) ) );
	$Tipo = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tipo" ) ) );
	$filas_totales = DBCampo ( $q , "Num_Registros" );
	$Gasto = utf8_encode ( DBCampo ( $q , utf8_decode ( "Gasto" ) ) );
	$Aporte = utf8_encode ( DBCampo ( $q , utf8_decode ( "Aporte" ) ) );
	$AporteEmpresa = utf8_encode ( DBCampo ( $q , utf8_decode ( "AporteEmpresa" ) ) );
	$Minutos = DBCampo ( $q , "Minutos" );
	$Anyo_ISO = DBCampo ( $q , "Anyo_ISO" );
	$Semana_ISO = DBCampo ( $q , "Semana_ISO" );
	$Anyo = DBCampo ( $q , "Anyo" );
	$Mes = DBCampo ( $q , "Mes" );
	
	$pq_rowcls = "";
	if($fin_dia == ''){
		$pq_rowcls = 'pqcell_rosa';
	}
	
	$tareas_hora [ $contador ] = array (
			"Numero" => $numero_hora , 
			"Tipo" => $Tipo , 
			"Tarea" => $tarea_hora , 
			"Inicio" => $Inicio , 
			"Dia Inicio" => $inicio_dia , 
			"Hora Inicio" => $inicio_hora , 
			"Dia Fin" => $fin_dia , 
			"Hora Fin" => $fin_hora , 
			"Usuario" => $usuario_hora ,
			"Minutos" => $Minutos ,
			"Estado" => $estado_actual , 
			"Gasto" => $Gasto , 
			"Aporte" => $Aporte , 
			"AporteEmpresa" => $AporteEmpresa ,
			"Comentario" => $usuario_comentario ,
			"Anyo_ISO" => $Anyo_ISO ,
			"Semana_ISO" => $Semana_ISO ,
			"Anyo" => $Anyo ,
			"Mes" => $Mes ,
			"pq_rowcls" => $pq_rowcls
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