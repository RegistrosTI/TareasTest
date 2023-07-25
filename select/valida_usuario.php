<?php
include_once('../conf/config.php');

$codusuario = $_GET["idUsuario"];
$codpassword= $_GET["idPassword"];
$codid      = $_GET["id"];

if(iniciaWS())
{
	$consultaWS=new consultaWS('SP Berner','InterfaceWS');
	$params = array('txtUsuario' => $codusuario,'txtPassword' => $codpassword,'id' => $codid);
	
	$result = $consultaWS->PermisoUsuario($params);
	
	//Ejecutamos la CodeUnit
	$consultaWS->ejecucionErronea($result);	
	
	$id_resultado = $result->return_value;
		
	finalizaWS();
	
	//Devolvemos el resultado
    echo $id_resultado;
}
?>