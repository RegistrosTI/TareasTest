<?php
if(isset($_SERVER [ 'HTTP_HOST' ]) && strpos ( strtoupper ( $_SERVER [ 'HTTP_HOST' ])  , 'TEST' ) !== false ){
	
	//++PARAMETRIZACIÓN DEL ORIGEN DE DATOS
	$GLOBALS["cfg_HostDB"]  = 'SQL2016-EXPRESS\SQL2016EXPRESS';
	$GLOBALS["cfg_UserDB"]  = 'gestionti';
	$GLOBALS["cfg_PassDB"]  = 'gestionti';
	
	//++PARAMETRIZACIÓN ESPECÍFICA DE ESTE PORTAL
	$GLOBALS["cfg_DataBase"]= 'GestionPDT';
	
	//Webservice NAV desarrollo
	if(file_exists("../../soporte/WebServices_Desarrollo.php")){
		include_once('../../soporte/WebServices_Desarrollo.php');
	}else{
		include_once('../soporte/WebServices_Desarrollo.php');
	}
}else{
	
	//++PARAMETRIZACIÓN DEL ORIGEN DE DATOS
	$GLOBALS["cfg_HostDB"]  = 'SQL2016';
	$GLOBALS["cfg_UserDB"]  = 'gestionti';
	$GLOBALS["cfg_PassDB"]  = 'gestionti';
	
	//++PARAMETRIZACIÓN ESPECÍFICA DE ESTE PORTAL
	$GLOBALS["cfg_DataBase"]= 'GestionPDT';
	
	//Webservice NAV desarrollo
	if(file_exists("../../soporte/WebServices.php"))	{
		include_once('../../soporte/WebServices.php');
	}else{
		include_once('../soporte/WebServices.php');
	}
}
?>