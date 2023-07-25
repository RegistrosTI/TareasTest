<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

// usuariovalidado + tarea + Id + Colaborador_Nombre + Funciones + Acceso
//sleep(2);
$Campo				= $_GET [ "Campo" ];
$Id					= $_GET [ "Id" ];
$Id_Tarea			= $_GET [ "Id_Tarea" ];
$Usuario			= $_GET [ "usuario" ];
$Colaborador_Nombre	= $_GET [ "Colaborador_Nombre" ];
$Acceso 			= $_GET [ "Acceso" ];
$Funciones 			= $_GET [ "Funciones" ];
$AporteOrganizacion = $_GET [ "AporteOrganizacion" ];
$AporteEmpresa 		= $_GET [ "AporteEmpresa" ];
$AporteJefe 		= $_GET [ "AporteJefe" ];

// limpieza de textos
$Funciones = strip_tags ( $Funciones );
$Funciones = str_replace ( "\'" , "" , ( $Funciones ) );
$Funciones = str_replace ( '\"' , "" , ( $Funciones ) );

if($AporteOrganizacion != '-' && $AporteEmpresa != '-'){
	$AporteOrganizacion = '-';
	$AporteEmpresa = '-';
} 

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );


if ( $Campo == 'Colaborador_Nombre' ) {
	// ++SI YA EXISTE UN COLABORADOR NO SE DEBE PERMITIR GUARDAR OTRO IGUAL
	$Encontrado = 0;
	$query = "SELECT COUNT(Id) AS Encontrado  FROM Colaboradores  WHERE Estado = 'Activo' AND Id_Tarea = $Id_Tarea	AND Colaborador_Nombre = '$Colaborador_Nombre' ";
	// die($query);
	$query = DBSelect ( utf8_decode ( $query ) );
	for(; DBNext ( $query ) ;) {
		$Encontrado = /*utf8_encode*/ ( DBCampo ( $query , "Encontrado" ) );
	}
	DBFree ( $query );
	if ( $Encontrado != 0 ) {
		die ( "El colaborador $Colaborador_Nombre ya existe en la tarea." );
	}
	// --SI YA EXISTE UN COLABORADOR NO SE DEBE PERMITIR GUARDAR OTRO IGUAL
	
	// ++CONSULTAMOS SU ACTIVIDAD EN EL DEPARTAMENTO DE LA TAREA PARA OBTENER EL APORTE ORGANIZACION
	$Oficina = "";
	$Actividad = "";
	$query = " SELECT  Oficina, Actividad FROM [Tareas y Proyectos] WHERE Id = $Id_Tarea";
	$query = DBSelect ( utf8_decode ( $query ) );
	for(; DBNext ( $query ) ;) {
		$Oficina = utf8_encode ( DBCampo ( $query , "Oficina" ) );
		$Actividad = utf8_encode ( DBCampo ( $query , "Actividad" ) );
	}
	DBFree ( $query );
	
	if($Actividad != ''){
		$query = "
			SELECT AporteOrganizacion, AporteEmpresa, AporteJefe 
			FROM ActividadesUsuario 
			WHERE IdUsuario = (SELECT dbo.SF_OBTENER_USUARIO_DOMINIO('$Colaborador_Nombre') )
				AND OficinaTipo = '$Oficina'
				AND NumTipo = (	SELECT NUMERO 
								FROM TIPOS 
								WHERE Tipo = 106 
									AND Descripcion = '$Actividad'
									 AND Oficina = '$Oficina')";
	}
	$query = DBSelect ( utf8_decode ( $query ) );
	for(; DBNext ( $query ) ;) {
		$AporteOrganizacion = utf8_encode ( DBCampo ( $query , "AporteOrganizacion" ) );
		$AporteEmpresa = utf8_encode ( DBCampo ( $query , "AporteEmpresa" ) );
		$AporteJefe = utf8_encode ( DBCampo ( $query , "AporteJefe" ) );
	}
	DBFree ( $query );
	// --CONSULTAMOS SU ACTIVIDAD EN EL DEPARTAMENTO DE LA TAREA PARA OBTENER EL APORTE ORGANIZACION
}
// ACTUALIZAR CAMPOS
$query = "
	UPDATE [GestionPDT].[dbo].[Colaboradores]
	SET
		Funciones = '$Funciones'
		,[Colaborador_Nombre] = '$Colaborador_Nombre'
		,[Acceso] = '$Acceso'
		,[Colaborador] = dbo.SF_OBTENER_USUARIO_DOMINIO('$Colaborador_Nombre')
		,FechaCambio = GETDATE()
		,UsuarioCambio = '$Usuario'
		,AporteOrganizacion = '$AporteOrganizacion'
		,AporteEmpresa = '$AporteEmpresa'
		,AporteJefe = '$AporteJefe'
	WHERE Id = $Id	and Id_Tarea = $Id_Tarea	;
";
//die($query);

$query = DBSelect ( utf8_decode ( $query ) );
DBFree ( $query );
DBClose ();
?>
