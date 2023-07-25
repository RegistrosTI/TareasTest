<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


$cur_page = $_GET["pq_curpage"]; 
if ($cur_page=='0')
{
	$cur_page='1';
}  
$records_per_page=$_GET["pq_rpp"]; 
$usuario         =$_GET["usuario"]; 
$pq_sort         = $_GET["pq_sort"];   
$sortQuery       = SortHelper::deSerializeSort($pq_sort,'');
$filterQuery     = "";
$filterParam     = array();
if ( isset($_GET["pq_filter"]))
{ 
	$pq_filter   = $_GET["pq_filter"];
	$dsf         = FilterHelper::deSerializeFilter($pq_filter);    
	$filterQuery = $dsf->query;    
	$filterParam = $dsf->param;
}    
$filtro_adicional = " WHERE Usuario = '".utf8_decode($usuario)."' ";
$contador         = 0;
$filas_totales    =0;
$colas            = array();

DBConectar($GLOBALS["cfg_DataBase"]);

$q=DBSelect(utf8_decode("Select Numero,Descripcion,Consulta,Fila,Num_Registros FROM (SELECT Numero,Descripcion,
						Consulta,ROW_NUMBER() OVER(".utf8_decode($sortQuery).") Fila,(SELECT COUNT(*) FROM [Consultas_Personalizadas] ").$filtro_adicional.utf8_decode(" ) AS Num_Registros FROM [Consultas_Personalizadas] ").$filtro_adicional.utf8_decode(" ) Resultado WHERE Fila > (".$cur_page."*".$records_per_page.")-".$records_per_page." AND Fila<= ".$cur_page."*".$records_per_page." ".utf8_decode($sortQuery)." "));
for(;DBNext($q);)
{		
	$Numero          =DBCampo($q,"Numero");
	$Consulta        =utf8_encode(DBCampo($q,"Consulta"));
	$Descripcion     =utf8_encode(DBCampo($q,("Descripcion")));
	$filas_totales   =DBCampo($q,"Num_Registros");
	$colas[$contador]=array("Numero"=>$Numero,"Consulta"=>$Consulta,"Descripcion"=>$Descripcion);
	$contador        =$contador+1;
}

DBFree($q);	
DBClose();

$json_arr = array('totalRecords' => $filas_totales, 'curPage' => $cur_page, 'data'=>$colas);
$php_json = json_encode($json_arr);

echo $php_json;
?>