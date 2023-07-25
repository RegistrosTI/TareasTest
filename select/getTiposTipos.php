<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

/**
 * Función que devuelve valores de la tabla tipos, admite parámetros:
 * tipo: (obligado) es el tipo de tipo
 * id: (opcional) el número de tarea, para filtrar por los tipos de oficina que coincidan con la oficina de la tarea
 * oficina: (opcional) para filtrar por los tipos de la oficina pasada por parametro
 * orden: (opcional) el ordenamiento de los resultados, por defecto por descripción, con parámetro C, ordena por codigo en la tabla
 */

if (  ! isset ( $_GET [ 'tipo' ] ) ) {
	throw new Exception ( 'El tipo no existe.' );
} else {
	$TIPO = $_GET [ 'tipo' ];
	
	DBConectar ( $GLOBALS [ "cfg_DataBase" ] );
	
	$filtro_adicional = " ";
	
	if ( isset ( $_GET [ 'id' ] ) ) {
		$ID_SELECCIONADA = $_GET [ 'id' ];
		
		$query = "SELECT [Oficina] FROM [GestionPDT].[dbo].[Tareas y Proyectos] WHERE ID = $ID_SELECCIONADA";
		$query = DBSelect ( ( $query ) );
		for(; DBNext ( $query ) ;) {
			$OFICINA = utf8_encode ( DBCampo ( $query , "Oficina" ) );
		}
		$filtro_adicional .= " AND oficina = '$OFICINA' ";
	}
	
	if ( isset ( $_GET [ 'oficina' ] ) ) {
		$OFICINA = $_GET [ 'oficina' ];
		$filtro_adicional .= " AND oficina = '$OFICINA' ";
	}
	
	$order_by = ' ORDER BY Descripcion ';
	if ( isset ( $_GET [ 'orden' ] ) ) {
		if ( $_GET [ 'orden' ] == 'C' ) {
			$order_by = ' ORDER BY Numero ';
		}
	}
	
	$Descripcion = '';
	
	$query = "
		SELECT 
			CAST(Descripcion as varchar(MAX)) as Descripcion
			,Numero 
		FROM Tipos 
		WHERE Tipo = $TIPO 
			AND visible = 1 
			$filtro_adicional $order_by ";
	// die($query);
	$query = DBSelect ( ( $query ) );
	$Descripcion = utf8_encode ( DBCampo ( $query , "Descripcion" ) );
	for(; DBNext ( $query ) ;) {
		$Descripcion = $Descripcion . ',' . utf8_encode ( DBCampo ( $query , "Descripcion" ) );
	}
	
	DBFree ( $query );
	DBClose ();
	
	echo $Descripcion;
}

?>