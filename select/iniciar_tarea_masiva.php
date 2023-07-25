<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$contador      = 0;
$filas_totales = 0;
$tareas        = array();
$filas_totales = 0;
$id            =$_GET['id'];
$tarea         =$_GET['tarea'];
$usuario       =$_GET['usuario'];	
$fechainicio   =$_GET['fechainicio'];	
$fechafin      =$_GET['fechafin'];	
$tipoestablecer=$_GET['tipoestablecer'];
$minimo        =$_GET['minimo'];
if(isset($_GET['pq_curpage']))
{
	$cur_page = $_GET["pq_curpage"]; 
	if ($cur_page=='0')
	{
		$cur_page='1';
	}	
}
else
{
	$cur_page = '1'; 
}

DBConectar($GLOBALS["cfg_DataBase"]);
if ($tipoestablecer=='1')
{
	$q=DBSelect(utf8_decode("EXECUTE [INICIAR_TAREA_HORA_MASIVA] '".$fechainicio."','".$fechafin."','".utf8_encode($usuario)."',".$tarea.",".$id.",".$minimo));
}
else
{
	$q=DBSelect(utf8_decode("EXECUTE [INICIAR_TAREA_HORA_MASIVA_SECUNDARIA] '".$fechainicio."','".$fechafin."','".utf8_encode($usuario)."',".$tarea.",".$id.",".$minimo));
}
for(;DBNext($q);)
{	
	$numero            =DBCampo($q,"NUMERO");
	$tarea             =(DBCampo($q,("TAREA")));
	$inicio            =utf8_encode(DBCampo($q,utf8_decode("INICIO")));
	$fin               =DBCampo($q,utf8_decode("FIN"));
	$usuario           =utf8_encode(DBCampo($q,utf8_decode("USUARIO")));
	$titulo            =utf8_encode(DBCampo($q,utf8_decode("TITULO")));
	$minutos           =(DBCampo($q,("MINUTOS")));
	$nuevoinicio       =utf8_encode(DBCampo($q,utf8_decode("NUEVO_INICIO")));
	$nuevofin          =utf8_encode(DBCampo($q,utf8_decode("NUEVO_FIN")));
	$accion            =utf8_encode(DBCampo($q,utf8_decode("ACCION")));
	$filas_totales     =DBCampo($q,"TOTALES");
	$tareas[$contador] =array("Numero"=>$numero,"Tarea"=>$tarea,"Inicio"=>$inicio,"Fin"=>$fin,"Usuario"=>$usuario,"Minutos"=>$minutos,"Nuevo Inicio"=>$nuevoinicio,"Nuevo Fin"=>$nuevofin,"Accion"=>$accion,"Titulo"=>$titulo);
	$contador          =$contador+1;
}		
DBFree($q);				
DBClose();

$json_arr = array('totalRecords' => $filas_totales, 'curPage' => $cur_page, 'data'=>$tareas);
$php_json = json_encode($json_arr);
echo $php_json;
?>