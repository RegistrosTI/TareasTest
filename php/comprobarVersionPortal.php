<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

if ( isset ( $_POST [ 'setDate' ] ) ) {
	$usuario = $_POST [ 'usuario' ];
	$update = "
		UPDATE Configuracion_Usuarios
		SET UltimaConexion = GETDATE()
		WHERE Usuario = '$usuario' ";
	DBSelect ( utf8_decode ( $update ) );
	DBClose ();
	die ();
}

if ( isset ( $_POST [ 'nuevaVersion' ] ) ) {
	$nuevaVersion = $_POST [ 'nuevaVersion' ];
	$usuario = $_POST [ 'usuario' ];
	$update = "
		UPDATE Configuracion_Usuarios
		SET
			versionPortal = $nuevaVersion
			,FechaActualizacion = GETDATE()
		WHERE Usuario = '$usuario' ";
	DBSelect ( utf8_decode ( $update ) );
	DBClose ();
	die ();
}

if ( isset ( $_POST [ 'usuario' ] ) ) {
	
	$usuario = $_POST [ 'usuario' ];
	$versionPortalUsuario = $_POST [ 'versionPortalUsuario' ];
	$versionSiguiente = $versionPortalUsuario + 1;
	
	$q = "
		SELECT
			'$versionSiguiente' AS Version
			,COALESCE (CONVERT(VARCHAR(20), VER.Fecha,103),'') AS Fecha
			,COALESCE(VER.Cambios,'Pequeñas mejoras y correciones.') AS Cambios
		FROM Versiones AS VER
		WHERE Version = $versionSiguiente
	";
	
	$q = DBSelect ( utf8_decode ( $q ) );
	for(; DBNext ( $q ) ;) {
		$Version=/*utf8_encode*/( DBCampo ( $q , "Version" ) );
		$Fecha=/*utf8_encode*/( DBCampo ( $q , "Fecha" ) );
		$Cambios = utf8_encode ( DBCampo ( $q , "Cambios" ) );
	}
	
	echo "$Version;$Fecha;$Cambios";
	
	DBFree ( $q );
	DBClose ();
	die ();
}

// Si no hay parametro, solo se pide la versión actual
if (  ! isset ( $_POST [ 'setDate' ] ) &&  ! isset ( $_POST [ 'nuevaVersion' ] ) &&  ! isset ( $_POST [ 'usuario' ] ) ) {

	$q = "
		SELECT
			CONF.valor AS Version
			--,COALESCE (CONVERT(VARCHAR(20), VER.Fecha,103),'') AS Fecha
			--,COALESCE(VER.Cambios,'Pequeñas mejoras y correciones.') AS Cambios
		FROM [Configuracion] AS CONF
		--LEFT JOIN Versiones AS VER ON CONF.Valor = VER.Version
		WHERE CONF.USuario = '' and CONF.parametro = 'version'
	";
	$q = DBSelect ( utf8_decode ( $q ) );
	for(; DBNext ( $q ) ;) {
		$Version=/*utf8_encode*/( DBCampo ( $q , "Version" ) );
		// $Fecha=/*utf8_encode*/( DBCampo ( $q , "Fecha" ) );
		// $Cambios = utf8_encode ( DBCampo ( $q , "Cambios" ) );
	}
	
	echo "$Version;;";
	
	DBFree ( $q );
	DBClose ();
	die ();
}

?>