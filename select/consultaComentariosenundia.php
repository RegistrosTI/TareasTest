<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$fecha           = $_GET["fecha"]; 
if ( isset($_GET["pq_curpage"]))
{
	$cur_page = $_GET["pq_curpage"]; 
}
else
{
	$cur_page='1';
}
if ($cur_page=='0')
{
	$cur_page='1';
}  
$records_per_page=$_GET["pq_rpp"]; 
$pq_sort         = $_GET["pq_sort"];   
$usuario         = $_GET["usuario"];  
$sortQuery       = SortHelper::deSerializeSort_Simple($pq_sort);
$filterQuery     = "";
$filterParam     = array();	
if ( isset($_GET["pq_filter"]))
{ 
	$pq_filter   = $_GET["pq_filter"];
	$dsf         = FilterHelper::deSerializeFilter($pq_filter);    
	$filterQuery = $dsf->query;    
	$filterParam = $dsf->param;
}    
$contador      = 0;
$filas_totales =0;
$tareas_hora   = array();


DBConectar($GLOBALS["cfg_DataBase"]);
$filtro_adicional = utf8_decode($filterQuery);
if ($filtro_adicional == "")
{
	$filtro_adicional = " WHERE Fecha BETWEEN '".$fecha." 00:00:00' AND '".$fecha." 23:59:59' AND Tarea IN (SELECT Id FROM [Tareas y Proyectos] T WHERE T.Control <> 1 AND [Asignado a] = '".$usuario."') ";
}
else
{
	$filtro_adicional = $filtro_adicional." WHERE Fecha BETWEEN '".$fecha." 00:00:00' AND '".$fecha." 23:59:59' AND Tarea IN (SELECT Id FROM [Tareas y Proyectos] T WHERE T.Control <> 1 AND [Asignado a] = '".$usuario."') ";
}	

$sortQuery = ' ORDER BY Tarea ';

$q=DBSelect(utf8_decode("Select Tarea as Hora,0 as Minutos,CAST(ISNULL((SELECT TOP 1 [Título] FROM [Tareas y Proyectos] WHERE Id = Tarea),'') as varchar(250)) AS Titulo,
						CAST(ISNULL(Comentario,'') as varchar(MAX)) as Comentario,0 as Tipo,1 AS ESTADO_ACTUAL,Numero as Numero,Tarea AS Tarea,CAST((CONVERT(varchar(20),  
						Fecha,108)) as varchar) AS Hora_Inicio,CAST((CONVERT(varchar(20),  Fecha,103)) as varchar) AS Fecha_Inicio      ,
						CAST((CONVERT(varchar(20),  Fecha,108)) as varchar) AS Hora_Fin      ,CAST((CONVERT(varchar(20),  Fecha,103)) as varchar) AS Fecha_Fin, 
						Fecha AS Inicio,  Fecha AS Fin, CAST(Usuario as varchar(255)) as Usuario,Fila,Num_Registros 
						FROM (SELECT Comentario,Numero,Tarea, Fecha,Usuario,ROW_NUMBER() OVER(".utf8_decode($sortQuery).") Fila,(SELECT COUNT(*) FROM [Comentarios] ").$filtro_adicional.utf8_decode(" ) AS Num_Registros FROM [Comentarios] ").$filtro_adicional.utf8_decode(" ) Resultado WHERE Fila > (".$cur_page."*".$records_per_page.")-".$records_per_page." AND Fila<= ".$cur_page."*".$records_per_page." ".utf8_decode($sortQuery)." "));
for(;DBNext($q);)
{		
	$numero_hora           =DBCampo($q,"Numero");
	$Minutos               =DBCampo($q,"Minutos");
	$HORA                  =DBCampo($q,"Hora");
	$estado_actual         =DBCampo($q,"ESTADO_ACTUAL");
	$tarea_hora            =utf8_encode(DBCampo($q,utf8_decode("Tarea")));
	$titulo                =utf8_encode(DBCampo($q,utf8_decode("Titulo")));
	$inicio_hora           =utf8_encode(DBCampo($q,utf8_decode("Hora_Inicio")));
	$inicio                =(DBCampo($q,utf8_decode("Inicio")));
	$fin_hora              =utf8_encode(DBCampo($q,utf8_decode("Hora_Fin")));
	$inicio_dia            =utf8_encode(DBCampo($q,utf8_decode("Fecha_Inicio")));
	$fin_dia               =utf8_encode(DBCampo($q,utf8_decode("Fecha_Fin")));
	$usuario_hora          =utf8_encode(DBCampo($q,utf8_decode("Usuario")));
	$usuario_comentario    =utf8_encode(DBCampo($q,utf8_decode("Comentario")));
	$Tipo                  =utf8_encode(DBCampo($q,utf8_decode("Tipo")));
	$filas_totales         =DBCampo($q,"Num_Registros");		
	$tareas_hora[$contador]=array("Hora"=>$HORA,"Tipo"=>$Tipo,"Minutos"=>$Minutos,"Inicio"=>$inicio,"Titulo"=>$titulo,"Numero"=>$numero_hora,"Tarea"=>$tarea_hora,"Dia Inicio"=>$inicio_dia,"Hora Inicio"=>$inicio_hora,"Dia Fin"=>$fin_dia,"Hora Fin"=>$fin_hora,"Usuario"=>$usuario_hora,"Estado"=>$estado_actual,"Comentario"=>$usuario_comentario);
	$contador              =$contador+1;
}
DBFree($q);	
DBClose();

$json_arr = array('totalRecords' => $filas_totales, 'curPage' => $cur_page, 'data'=>$tareas_hora);
$php_json = json_encode($json_arr);

echo $php_json;
?>