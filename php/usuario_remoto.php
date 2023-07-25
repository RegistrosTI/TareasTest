<?php
include_once "../soporte/funcionesgenerales.php";
//$usuario = $_SERVER [ 'REMOTE_USER' ];
$usuario = getServerRemoteUser();

if($usuario == 'alberto.ruiz'){
	//$usuario = 'maricarmen.cordoba';
}

if ( $usuario == 'ti.pruebas' ) {
	include "./visibilidad/usuario_comodin.php";
}

include "./visibilidad/visibilidad_departamento.php";
?>