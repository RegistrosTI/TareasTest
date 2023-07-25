<?php
include_once "./php/funciones.php";
include_once "./conf/config.php";
include_once "./conf/config_".curPageName().".php";
include_once "../soporte/DB.php";
include_once "../soporte/funcionesgenerales.php";

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );
$acceso_permitido = '0';
$ambito = 0;
$ambito_global = 0;
$versionPortal = 0;
$OcultarHoraGrids = 0;
$Area = '';

$q = "SELECT Acceso FROM [Accesos]  WHERE Tipo = 'Departamento' AND Descripcion = 'Todos'";
$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$acceso_permitido = utf8_encode ( DBCampo ( $q , "Acceso" ) );
}

$q = "SELECT Acceso FROM [Accesos]  WHERE Tipo = 'Departamento' AND Descripcion = '" . $departamento_usuario_validado . "'";
$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$acceso_permitido = utf8_encode ( DBCampo ( $q , "Acceso" ) );
}
DBFree ( $q );

$q = " SELECT [ambito],[ambito_global],[OcultarHoraGrids],[Area] FROM [Configuracion_Usuarios] WHERE usuario = '$usuario' ";
$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$ambito = utf8_encode ( DBCampo ( $q , "ambito" ) );
	$ambito_global = utf8_encode ( DBCampo ( $q , "ambito_global" ) );
	$OcultarHoraGrids = utf8_encode ( DBCampo ( $q , "OcultarHoraGrids" ) );
	$Area = utf8_encode ( DBCampo ( $q , "Area" ) );
}


DBFree ( $q );

$q="
BEGIN TRAN
	SELECT 
		valor AS versionPortal,
		isnull((SELECT VersionPortal FROM Configuracion_Usuarios where usuario = '$usuario'),-1) as versionPortalUsuario
	FROM [Configuracion] Config 
	WHERE Config.USuario='' and Config.parametro = 'version'

	UPDATE Configuracion_Usuarios SET UltimaConexion = GETDATE() where usuario = '$usuario'
COMMIT TRAN
";
$q=DBSelect(utf8_decode($q));
for(;DBNext($q);)
{
	$versionPortal=/*utf8_encode*/(DBCampo($q,"versionPortal")); // Tambien se actualiza en al ajax comprobarVersionPortal.php
	$versionPortalUsuario=/*utf8_encode*/(DBCampo($q,"versionPortalUsuario"));
}
DBFree($q);
DBClose ();
if ( $acceso_permitido == '0' ) {
	die ( '	<h2 align="center"><font color="red">ERROR ACCESO DENEGADO</font></h2><p align="center">No tiene permisos para entrar en este portal.<br>Consulte con el administrador.</p>' );
}
?>