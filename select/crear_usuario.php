<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

if ( isset ( $_GET [ 'tarea' ] ) ) {
	$tarea = $_GET [ 'tarea' ];
} else
	$tarea = '';

if ( isset ( $_GET [ 'test' ] ) ) {
	$test = $_GET [ 'test' ];
} else
	$test = '';

$ref = getenv ( 'HTTP_REFERER' );
if (  ! $ref || strtoupper ( $ref ) != strtoupper ( 'https://tareas.sp-berner.com/' ) ) {
	echo '403 Crear Usuario Navision Acceso Denegado';
} else {
	if ( iniciaWS () ) {
		$consultaWS = new consultaWS ( 'SP Berner' , 'InterfaceWS' );
		$params = array (
				'tarea' => $tarea , 
				'test' => $test , 
				'resultadohtml' => '' 
		);
		$result = $consultaWS -> CrearUsuario ( $params );
		$consultaWS -> ejecucionErronea ( $result );
		$resultadohtml = $result -> resultadohtml;
		
		finalizaWS ();
		
		echo $resultadohtml;
	}
	if ( $test == '0' ) {
		DBConectar ( $GLOBALS [ "cfg_DataBase" ] );
		$insert = DBSelect ( utf8_decode ( "EXECUTE [INICIAR_HISTORICO] '" . $usuario . "'," . $tarea . ",66002" ) );
		DBClose ();
	}
}

?>
