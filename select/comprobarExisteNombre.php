<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$nombre = $_GET [ 'nombre' ];
$Encontrado = '0';
$oficina = '';
$buscar = '';// donde se va a realizar la busqueda
if ( isset ( $_GET [ 'oficina' ] ) ) {
	$oficina = $_GET [ 'oficina' ];
}

if ( isset ( $_GET [ 'buscar' ] ) ) {
	$buscar = $_GET [ 'buscar' ];
}

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );
switch ($buscar){
	case 'usuarios':
		$filtro_oficina = ' ';
		if ( $oficina != '' ) {
			//$filtro_oficina = " AND Oficina LIKE ('%$oficina%') ";
			$filtro_oficina = " AND '$oficina' IN (SELECT NOMBRE FROM [dbo].[StringSplit] (  (select  TOP 1 Oficina from Configuracion_Usuarios WHERE Usuario = @USUARIO  )  ,','  ,100)) ";
		}
		$query = "
			DECLARE @USUARIO VARCHAR(200);
			SET @USUARIO = (dbo.SF_OBTENER_USUARIO_DOMINIO('$nombre'));
			SELECT COUNT(Id) AS Encontrado FROM Configuracion_Usuarios AS USU WHERE Usuario = @USUARIO AND Baja = 0 $filtro_oficina;
		";
	break;
	case 'dominio':
		$query = "SELECT COUNT(sAMAccountName) AS Encontrado FROM TF_DATOS_DOMINIO() WHERE  Name = 	'$nombre'";
		break;
	default:die(0);
}


//die($query);
$query = DBSelect ( utf8_decode( $query ) );

for(; DBNext ( $query ) ;) {
	$Encontrado = utf8_encode ( DBCampo ( $query , "Encontrado" ) );
}
DBFree ( $query );
DBClose ();

echo $Encontrado;
?>