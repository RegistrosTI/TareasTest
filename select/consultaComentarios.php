<?php
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

$filtro_adicional = utf8_decode ( $filterQuery );
if ( $filtro_adicional == "" ) {
	$filtro_adicional = ' WHERE Tarea = ' . $tarea;
} else {
	$filtro_adicional = $filtro_adicional . ' AND Tarea = ' . $tarea;
}

$q = "
	Select 
		CAST(Comentario AS text) as Comentario
		,Clase
		,Numero as Numero
		,Tarea AS Tarea
		,CAST((CONVERT(varchar(20), Fecha,108)) as varchar) AS Hora
		,CAST((CONVERT(varchar(20), Fecha,105)) as varchar) AS Fecha 
		,Usuario as Usuario
		,Fila
		,Num_Registros 
	FROM ( SELECT
				Numero
				,Tarea
				,Fecha
				,Usuario
				,Comentario
				,Clase
				,ROW_NUMBER() OVER($sortQuery) AS Fila
				,(	SELECT COUNT(*) 
					FROM [Comentarios] $filtro_adicional  ) AS Num_Registros 
			FROM [Comentarios] $filtro_adicional ) AS Resultado 
			WHERE Fila > ( $cur_page * $records_per_page ) - $records_per_page  AND Fila <=  $cur_page * $records_per_page $sortQuery ";

$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$numero_hora = DBCampo ( $q , "Numero" );
	$tarea_hora = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tarea" ) ) );
	$inicio_dia = utf8_encode ( DBCampo ( $q , utf8_decode ( "Fecha" ) ) );
	$inicio_hora = utf8_encode ( DBCampo ( $q , utf8_decode ( "Hora" ) ) );
	$comentario_hora = utf8_encode ( DBCampo ( $q , utf8_decode ( "Comentario" ) ) );
	$clase = utf8_encode ( DBCampo ( $q , utf8_decode ( "Clase" ) ) );
	$usuario_hora = utf8_encode ( DBCampo ( $q , utf8_decode ( "Usuario" ) ) );
	$filas_totales = DBCampo ( $q , "Num_Registros" );
	
	$tareas_hora [ $contador ] = array (
			"Numero" => $numero_hora , 
			"Tarea" => $tarea_hora , 
			"Fecha" => $inicio_dia , 
			"Hora" => $inicio_hora , 
			"Comentario" => $comentario_hora , 
			"Clase" => $clase ,
			"Usuario" => $usuario_hora 
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