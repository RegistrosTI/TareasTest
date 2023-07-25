<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$Numero   =$_GET["Numero"]; 
$contador = 0;
$colas    = array();

DBConectar($GLOBALS["cfg_DataBase"]);
$q=DBSelect(utf8_decode("SELECT [Cola] ,[Usuario],[Numero] FROM [Colas_Usuarios] where cola = ".$Numero.""));
for(;DBNext($q);)
{			
	$Cola             =DBCampo($q,"Cola");
	$Numero           =DBCampo($q,"Numero");
	$Usuario          =utf8_encode(DBCampo($q,utf8_decode("Usuario")));
	$colas[$contador] =array("Cola"=>$Cola,"Usuario"=>$Usuario,"Numero"=>$Numero);
	$contador         =$contador+1;
}
DBFree($q);	
DBClose();

$json_arr = array('data'=>$colas);
$php_json = json_encode($json_arr);
echo $php_json;
?>