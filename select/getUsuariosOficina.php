<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

//$Id_Pda = $_GET [ 'id_pda' ];
if ( isset ( $_GET [ 'id_pda' ] ) ) {
	$Id_Pda = $_GET [ 'id_pda' ];
}

$todos = 'no';
$filtro_adicional = '';

if ( isset ( $_GET [ 'todos' ] ) ) {
	$todos = $_GET [ 'todos' ];
}

if ( isset ( $_GET [ 'idbotcorreo' ] ) ) {
	$idbotcorreo = $_GET [ 'idbotcorreo' ];
	$filtro_adicional .= " AND U.Oficina = (SELECT OFICINA FROM [Entrada_Correo] WHERE Id = $idbotcorreo ) ";
}

// SI EL PARAMETRO TODOS ES NO, SOLO SE MUESTRAN LOS USUARIO DE LA MISMA OFICINA DE LA TAREA
if ( $todos == 'no' ) {
	$filtro_adicional .= " AND U.Oficina = (SELECT OFICINA FROM [Tareas y Proyectos] WHERE Id = $Id_Pda ) ";
}

$query = "
SELECT D.Name
  FROM [GestionPDT].[dbo].[Configuracion_Usuarios] AS U
  INNER JOIN  [GestionIDM].[dbo].[LDAP] AS D ON U.Usuario = D.sAMAccountName
  WHERE U.Baja <> 1 $filtro_adicional  
  ORDER BY D.name
";
// die ( $query );
$query = DBSelect ( utf8_decode ( $query ) );
$Descripcion = '';
for(; DBNext ( $query ) ;) {
	if ( $Descripcion == '' ) {
		$Descripcion = utf8_encode ( DBCampo ( $query , "Name" ) );
	} else {
		$Descripcion = $Descripcion . "|" . utf8_encode ( DBCampo ( $query , "Name" ) );
	}
}
DBFree ( $query );
DBClose ();

echo $Descripcion;
?>