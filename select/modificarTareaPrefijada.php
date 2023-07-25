<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$nombreusuario = $_GET [ 'nombreusuario' ];
$usuario = $_GET [ 'usuario' ];
$numero = $_GET [ 'numero' ];
$tarea = 0;
if ( isset ( $_GET [ 'tarea' ] ) ) {
	$tarea = $_GET [ 'tarea' ];
}
// $alias = '';  Delete Tarea 17957
$alias = 'x'; // Insert Tarea 17957
if ( isset ( $_GET [ 'alias' ] ) ) {
	$alias = $_GET [ 'alias' ];
}

$borrar = $_GET [ 'borrar' ];

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

// SI HAY QUE BORRAR LA TAREA O EL ALIAS DE LA TAREA
if ( $borrar == 1 ) {
	
	// Borramos solo el alias
	//	if ( $tarea == 0 ) { Delete Tarea 17957
	if ( $alias != 'x' ){ // Insert Tarea 17957
		$update = "
			update [Tareas_Predefinidas]
			set alias = ''
			where usuario = '$usuario' and numero = $numero ";
		$update = DBSelect ( utf8_decode ( $update ) );
		DBClose ();
		die ( "-3" );
	}
		
	// Borramos tarea predefinida
	//	if ( $tarea != 0 ) { Delete Tarea 17957
	if ( empty ( $_GET [ 'tarea' ] )) { // Insert Tarea 17957
		$update = "
		update [Tareas_Predefinidas]
		set tarea = NULL, titulo = '', alias = ''
		where usuario = '$usuario' and numero = $numero ";
		$update = DBSelect ( utf8_decode ( $update ) );
		DBClose ();
		die ( "-2" );
	}
}

// CAMBIAMOS EL ALIAS
// if ( $alias != '' ) {  Delete Tarea 17957
if ( $alias != ''  &&  $alias != 'x' ) {  // Insert Tarea 17957
	$update = "
		update [Tareas_Predefinidas]
		set alias = '$alias'
		where usuario = '$usuario' and numero = $numero ";
	$update = DBSelect ( utf8_decode ( $update ) );
	DBClose ();
	die ( "-4" );
}

// Tarea 17957: Si el usuario tiene definido el ámbito global en su oficina, puede darse de alta cualquier tarea como frecuente
//              Si no, sólo se la podrá dar de alta si la tiene asignada o actua como colaborador en ella 
// Inicio Insert Tarea 17957
$select = " SELECT count(*) as ambito FROM [Configuracion_Usuarios] where Usuario = '$usuario' and [Ambito] = 0";
$select = DBSelect ( utf8_decode ( $select ) );
$tmp = utf8_encode ( DBCampo ( $select , "ambito" ) );
DBFree ( $select );
if ($tmp == 0) {
	// COMPROBAR SI EXISTE LA TAREA Y LA TENEMOS ASIGNADA
	$select = " SELECT count(*) as tareas FROM [Tareas y Proyectos] where id = $tarea and [asignado a] = '$nombreusuario'";
	$select = DBSelect ( utf8_decode ( $select ) );
	$tmp = utf8_encode ( DBCampo ( $select , "tareas" ) );
	DBFree ( $select );

	$select1 = " SELECT count(*) as colaboradores FROM [Colaboradores] where Id_Tarea = $tarea and [Colaborador_Nombre] = '$nombreusuario'";
	$select1 = DBSelect ( utf8_decode ( $select1 ) );
	$tmp1 = utf8_encode ( DBCampo ( $select1 , "colaboradores" ) );
	DBFree ( $select1 );

	if   (( $tmp  == 0 ) && ( $tmp1 == 0 )){ 
		DBClose ();
		die ( "-1" );
	}
}
// Fin Insert Tarea 17957

// Inicio Delete Tarea 17957
// COMPROBAR SI EXISTE LA TAREA Y LA TENEMOS ASIGNADA
// $select = " SELECT count(*) as tareas FROM [Tareas y Proyectos] where id = $tarea and [asignado a] = '$nombreusuario'";
// $select = DBSelect ( utf8_decode ( $select ) );
// $tmp = utf8_encode ( DBCampo ( $select , "tareas" ) );
// DBFree ( $select );
// if   (( $tmp  == 0 ) {
//	DBClose ();
//	die ( "-1" );
// }
// Fin Delete Tarea 17957

// OBTENER DESCRIPCION DE TAREA
$select = " SELECT [Título] AS Titulo FROM [Tareas y Proyectos] where id = $tarea";
$select = DBSelect ( utf8_decode ( $select ) );
$titulo = utf8_encode ( DBCampo ( $select , "Titulo" ) );
DBFree ( $select );

// GUARDAMOS LOS CAMBIOS
$update = "UPDATE Tareas_Predefinidas SET tarea = $tarea, titulo = '$titulo', fecha = GETDATE(), alias = '' WHERE usuario = '$usuario' and numero = $numero";

$update = DBSelect ( utf8_decode ( $update ) );
DBFree ( $update );

DBClose ();

echo '';
?>