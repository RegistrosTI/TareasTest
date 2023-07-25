<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id     = $_GET["id"];
$valor  = $_GET["valor"];
$usuario= $_GET["usuario"];

if($id == 'mi_hist_ban_tareas'){
	if (!is_numeric($valor)){
		$valor = 0;
		echo "-1;Formato de parámetro incorrecto.";
	}else{
		$valor = intval($valor);
		if ($valor < 0){
			$valor = 0;
			echo "-1;El rango del parámetro debe estar en [0,365]";
		}
		if($valor > 365){
			$valor = 365;
			echo "-1;El rango del parámetro debe estar en [0,365]";
		}
	}
}

if($id == 'alerta_nueva_asignacion'){
	if (!is_numeric($valor)){
		$valor = 0;
		echo "-1;Formato de parámetro incorrecto.";
	}else{
		$valor = intval($valor);
		if ($valor < 0 || $valor > 3){
			echo "-1;El rango del parámetro debe estar en [0,3]";
			$valor = 0;
		}
	}
}

if($id == 'alerta_fecha_objetivo'){
	$VALOR = explode(';', $valor);
	if( is_numeric($VALOR[0]) && is_numeric($VALOR[1])  ){
			if ($VALOR[0] < 0 || $VALOR[0] > 3){
				echo "-1;El rango del parámetro #1 debe estar en [0,3]";
				$valor = "0;0";
			}
			if ($VALOR[1] < -365 || $VALOR[1] > 365){
				echo "-1;El rango del parámetro #2 debe estar en [-365,365]";
				$valor = "0;0";
			}
	}else{
		$valor = "0;0";
		echo "-1;Formato de parámetro incorrecto.";
	}
}

DBConectar($GLOBALS["cfg_DataBase"]);
DBSelect(("UPDATE [Configuracion] SET [Valor] = '".utf8_decode(str_replace("'", "''", ($valor)))."' WHERE (Usuario = '".utf8_decode($usuario)."' OR Usuario = '') AND Parametro = '".utf8_decode($id)."'"));
DBClose();

?>
