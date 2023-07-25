<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$Id_Tarea				= $_GET [ "Id_Tarea" ];
$autocompleteasignado	= $_GET [ "autocompleteasignado" ];
$selectactividad		= $_GET [ "selectactividad" ];
$selectoficina			= $_GET [ "selectoficina" ];

DBConectar($GLOBALS["cfg_DataBase"]);

$AporteOrganizacion = '-';
$AporteEmpresa = '-';
$AporteJefe = '-';
$query = "
	SELECT AporteOrganizacion, AporteEmpresa , AporteJefe  
	FROM ActividadesUsuario 
	WHERE IdUsuario = (SELECT dbo.SF_OBTENER_USUARIO_DOMINIO('$autocompleteasignado') )
		AND OficinaTipo = '$selectoficina'
		AND NumTipo = (SELECT NUMERO FROM TIPOS WHERE Tipo = 106 AND Descripcion = '$selectactividad'  AND Oficina = '$selectoficina')
";
//die($query);
$query = DBSelect ( utf8_decode ( $query ) );
for(; DBNext ( $query ) ;) {
	$AporteOrganizacion = utf8_encode ( DBCampo ( $query , "AporteOrganizacion" ) );
	$AporteEmpresa = utf8_encode ( DBCampo ( $query , "AporteEmpresa" ) );
	$AporteJefe = utf8_encode ( DBCampo ( $query , "AporteJefe" ) );
}

DBFree($query);
DBClose();

echo $AporteOrganizacion . ";" . $AporteEmpresa . ";" . $AporteJefe;
?>