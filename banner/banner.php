<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);
$contador    = 0;
$Usuario     = $_GET["usuario"];
$menu        = $_GET["menu"];
$descripcion = '';
$url         = '';

$query=DBSelect(utf8_decode("SELECT [Numero],[Descripcion],[Activo],[Inicio],[Fin],[Pantalla],[url] FROM [Noticias] Noticias WHERE Noticias.Activo = 1
						 AND Noticias.Inicio <= GETDATE() AND Noticias.Fin >= GETDATE() AND Pantalla = ".$menu." AND Numero NOT IN
						(SELECT [Numero] FROM [Noticias_Leidas] WHERE [Usuario] = '".$Usuario."')"));
				
for(;DBNext($query);)
{	
	$contador    = $contador + 1;
	$Numero      = DBCampo($query,"Numero");
	$descripcion = DBCampo($query,"Descripcion");
	$url         = DBCampo($query,"url");
}
DBFree($query);	
DBClose();

if ($contador==0)
{
	echo "";
}
else
{
	echo $descripcion.'|'.$url.'|'.$Numero;
}	
?>