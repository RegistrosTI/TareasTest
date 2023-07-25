<?php
include_once "./conf/config.php";
include_once "./conf/config_".curPageName().".php";
include_once "../soporte/DB.php";
include_once "../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);

$q=DBSelect(utf8_decode("(SELECT valor FROM [Configuracion] Config WHERE Config.USuario='' and Config.parametro = 'usuariocomodin')"));
for(;DBNext($q);)
{		
	$usuario=/*utf8_encode*/(DBCampo($q,"valor"));					
}
DBFree($q);	




DBClose();
?>