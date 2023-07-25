<?php
//UTF Á á Ñ ñ
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$usuario_deseado = $_GET["usuario"];
$semana          = $_GET["semana"];
$contador        = 0;
$tool            = array();


DBConectar($GLOBALS["cfg_DataBase"]);
$q=DBSelect(utf8_decode("EXECUTE [SLIDE_SEMANAL_COMENTARIOS] ".$semana.",'".$usuario_deseado."' "));
for(;DBNext($q);)
{	
	$NUMERO_DIA     = DBCampo($q,"NUMERO_DIA");
	$MINUTOS        = (DBCampo($q,utf8_decode("MINUTOS")));
	$HORAS          = (DBCampo($q,utf8_decode("HORAS")));
	$FECHA          = (DBCampo($q,utf8_decode("FECHA")));
	$DIA_INICIO     = (DBCampo($q,utf8_decode("DIA_INICIO")));
	$DIA_FIN        = (DBCampo($q,utf8_decode("DIA_FIN")));
	$MES_INICIO     = (DBCampo($q,utf8_decode("MES_INICIO")));
	$MES_FIN        = (DBCampo($q,utf8_decode("MES_FIN")));
	$SOLAPADO       = DBCampo($q,"SOLAPADO");
	$tool[$contador]= array("NUMERO_DIA"=>$NUMERO_DIA,"HORAS"=>$HORAS,"MINUTOS"=>$MINUTOS,"DIA_INICIO"=>$DIA_INICIO,"DIA_FIN"=>$DIA_FIN,"MES_INICIO"=>$MES_INICIO,"MES_FIN"=>$MES_FIN,"FECHA"=>$FECHA,"SOLAPADO"=>$SOLAPADO);
	$contador       = $contador+1;
}
DBFree($q);	
DBClose();


$json_arr = array('data'=>$tool);
$php_json = json_encode($json_arr);
echo $php_json;
?>