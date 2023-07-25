<?php
include_once('../conf/config.php');

//Inicio del WEB SERVICE
if(iniciaWS())
{
	$consultaWS=new consultaWS('SP Berner','InterfaceWS');
	$params = array('diainicial' => '', 'diafinal' => '','diasfestivo'=>'');       

	//Ejecutamos la CodeUnit
	$result = $consultaWS->DiasPosiblesIncorporacion($params); 

	$consultaWS->ejecucionErronea($result);       
	
	$diainicial  = $result->diainicial;
	$diafinal    = $result->diafinal;
	$diasfestivo = $result->diasfestivo;
	
	finalizaWS();

	//Devolvemos el resultado
	echo $diainicial.'|'.$diafinal.'|'.$diasfestivo;
}


?>
