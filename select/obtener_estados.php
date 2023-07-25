<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);

$select='';
$u_programa=DBSelect(utf8_decode("SELECT [Numero],[Descripcion],[Predeterminado] FROM [Tipos]  where Tipo = 5"));
for(;DBNext($u_programa);)
{
	$Numero        =(DBCampo($u_programa,utf8_decode("Numero")));		
	$Descripcion   =(DBCampo($u_programa,"Descripcion"));
	$Predeterminado=(DBCampo($u_programa,utf8_decode("Predeterminado")));		
	$select        =$select.'<option value="'.utf8_encode($Descripcion).'">'.utf8_encode($Descripcion).'</option>';		
}
DBFree($u_programa);
DBClose();	
echo $select;
?>