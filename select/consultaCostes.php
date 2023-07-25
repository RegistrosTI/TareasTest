<?php
// UTF Á á Ñ ñ
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

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

$filtro_adicional = $filterQuery;
if ( $filtro_adicional == "" ) {
	$filtro_adicional = ' WHERE Tarea = ' . $tarea;
} else {
	$filtro_adicional = $filtro_adicional . ' AND Tarea = ' . $tarea;
}
$q = "
	Select ISNULL(Importe,0) as Importe
		,CAST(Tipo as varchar(MAX)) as Tipo
		,CAST(Comentario as varchar(MAX)) as Comentario
		,CASE WHEN Fin IS NULL THEN 
								CASE WHEN DATEADD(dd, 0, DATEDIFF(dd, 0, GETDATE())) <>	DATEADD(dd, 0, DATEDIFF(dd, 0, Inicio)) 
									THEN 0 ELSE 1 
								END ELSE -1 
								END AS ESTADO_ACTUAL
		,Numero as Numero
		,Tarea AS Tarea
		,CAST((CONVERT(varchar(20), Inicio,108)) as varchar) AS Hora_Inicio
		,CAST((CONVERT(varchar(20), Inicio,103)) as varchar) AS Fecha_Inicio
		,CAST((CONVERT(varchar(20), Fin,108)) as varchar) AS Hora_Fin
		,CAST((CONVERT(varchar(20), Fin,103)) as varchar) AS Fecha_Fin
		,Inicio AS Inicio
		,Fin AS Fin
		,CAST(Usuario as varchar(255)) as Usuario
		,Fila
		,Num_Registros 
	FROM (	SELECT 
				Importe
				,Tipo
				,Comentario
				,Numero
				,Tarea
				,Inicio
				,Fin
				,Usuario
				,ROW_NUMBER() OVER(" . $sortQuery . ") Fila
				,(SELECT COUNT(*) FROM [Costes] " . $filtro_adicional . " ) AS Num_Registros 
			FROM [Costes] " . $filtro_adicional . " ) Resultado 
			WHERE Fila > (" . $cur_page . "*" . $records_per_page . ")-" . $records_per_page . " AND Fila<= " . $cur_page . " * " . $records_per_page . " " . $sortQuery;

$q = DBSelect ( utf8_decode ( $q ) );

// $q=DBSelect(utf8_decode("Select ISNULL(Importe,0) as Importe,CAST(Tipo as varchar(MAX)) as Tipo,CAST(Comentario as varchar(MAX)) as Comentario,CASE WHEN Fin IS NULL THEN
// CASE WHEN DATEADD(dd, 0, DATEDIFF(dd, 0, GETDATE())) <>
// DATEADD(dd, 0, DATEDIFF(dd, 0, Inicio)) THEN 0 ELSE 1 END ELSE -1 END AS ESTADO_ACTUAL,Numero as Numero,Tarea AS Tarea,CAST((CONVERT(varchar(20), Inicio,108)) as varchar) AS Hora_Inicio,CAST((CONVERT(varchar(20), Inicio,103)) as varchar) AS Fecha_Inicio ,CAST((CONVERT(varchar(20), Fin,108)) as varchar) AS Hora_Fin ,CAST((CONVERT(varchar(20), Fin,103)) as varchar) AS Fecha_Fin, Inicio AS Inicio, Fin AS Fin, CAST(Usuario as varchar(255)) as Usuario,Fila,Num_Registros
// FROM (SELECT Importe,Tipo,Comentario,Numero,Tarea, Inicio,Fin,Usuario,ROW_NUMBER() OVER(".utf8_decode($sortQuery).") Fila,(SELECT COUNT(*) FROM [Costes] ").$filtro_adicional.utf8_decode(" ) AS Num_Registros FROM [Costes] ").$filtro_adicional.utf8_decode(" ) Resultado WHERE Fila > (".$cur_page."*".$records_per_page.")-".$records_per_page." AND Fila<= ".$cur_page."*".$records_per_page." ".utf8_decode($sortQuery)." "));

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
	$Importe = utf8_encode ( DBCampo ( $q , utf8_decode ( "Importe" ) ) );
	$filas_totales = DBCampo ( $q , "Num_Registros" );
	
	$tareas_hora [ $contador ] = array (
			"Numero" => $numero_hora , 
			"Importe" => $Importe , 
			"Tipo" => $Tipo , 
			"Tarea" => $tarea_hora , 
			"Inicio" => $Inicio , 
			"Dia Inicio" => $inicio_dia , 
			"Hora Inicio" => $inicio_hora , 
			"Dia Fin" => $fin_dia , 
			"Hora Fin" => $fin_hora , 
			"Usuario" => $usuario_hora , 
			"Estado" => $estado_actual , 
			"Comentario" => $usuario_comentario 
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