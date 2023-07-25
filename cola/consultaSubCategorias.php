<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";



$cur_page        = $_GET["pq_curpage"]; 
$records_per_page= $_GET["pq_rpp"]; 
$pq_sort         = $_GET["pq_sort"];   
$filterQuery     = "";
$filterParam     = array();
if ($cur_page=='0')
{
	$cur_page='1';
}  
$sortQuery = SortHelper::deSerializeSort_Simple($pq_sort);
if (isset($_GET["pq_filter"]))
{ 
	$pq_filter   = $_GET["pq_filter"];
	$dsf         = FilterHelper::deSerializeFilter($pq_filter);    
	$filterQuery = $dsf->query;    
	$filterParam = $dsf->param;
}    
$filtro_adicional = '';
$contador         = 0;
$filas_totales    = 0;
$colas            = array();
$filtro_adicional = ' where Tipo = 2 ';

DBConectar($GLOBALS["cfg_DataBase"]);

$q=DBSelect(utf8_decode("Select [Tipo],[Numero],CAST([Descripcion] as varchar(250)) as Descripcion,[Predeterminado],[Pendiente],[icono],[color],[visible],CAST([Cola] as varchar(250)) as Cola,
						Fila,Num_Registros FROM (SELECT [Tipo],[Numero],[Descripcion],[Predeterminado],[Pendiente],[icono],[color],[visible],[Cola],ROW_NUMBER() OVER(".utf8_decode($sortQuery).") Fila,(SELECT COUNT(*) FROM [Tipos]  ").$filtro_adicional.utf8_decode(" ) AS Num_Registros FROM  [Tipos] ").$filtro_adicional.utf8_decode(" ) Resultado WHERE Fila > (".$cur_page."*".$records_per_page.")-".$records_per_page." AND Fila<= ".$cur_page."*".$records_per_page." ".utf8_decode($sortQuery)." "));
for(;DBNext($q);)
{		
	$Numero          =DBCampo($q,"Numero");
	$Tipo            =DBCampo($q,"Tipo");
	$visible         =DBCampo($q,"visible");
	$Predeterminado  =DBCampo($q,"Predeterminado");
	$Descripcion     =utf8_encode(DBCampo($q,utf8_decode("Descripcion")));
	$Cola            =utf8_encode(DBCampo($q,utf8_decode("Cola")));
	$filas_totales   =DBCampo($q,"Num_Registros");
	$colas[$contador]=array("Numero"=>$Numero,"Tipo"=>$Tipo,"visible"=>$visible,"Predeterminado"=>$Predeterminado,"Descripcion"=>$Descripcion,"Cola"=>$Cola);
	$contador        =$contador+1;
}
DBFree($q);	
DBClose();

$json_arr = array('totalRecords' => $filas_totales, 'curPage' => $cur_page, 'data'=>$colas);
$php_json = json_encode($json_arr);
echo $php_json;
?>