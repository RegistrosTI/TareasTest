<?php
// UTF Á Ñ
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$tipo = $_GET [ 'tipo' ];

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );
if ( $tipo == 1 ) { // SE EJECUTA EN LA CARGA DEL GRID -> SEGUNDO AJAX -> INFORMACION DE CONFIG_ARRAYS
	get_jsonconfig ();
}
if ( $tipo == 2 ) { // SE EJECUTA SOLO PARA GUARDAR COLUMNAS DESDE EL MENU CONTEXTUAL Y EL EVENTO AL CAMBIAR COLUMNAS
	put_jsonconfig ();
}
if ( $tipo == 3 ) { // SE EJECUTA EN LA CARGA DEL GRID -> PRIMER AJAX -> INFORMACION TABLA VENTANA
	get_jsonconfigcolumnas ();
}
if ( $tipo == 4 ) { // SE BORRA LA CONFIGURACION DE CONFIG_ARRAYS DEL USUARIO
	del_jsonconfig ();
}
if ( $tipo == 5 ) { // SE DEVUELVE LA CONFIGURACION DEL DATAMODEL
	get_dataModel ();
}
DBClose ();
function get_dataModel() {
	// $tipo == 5 SE DEVUELVE LA CONFIGURACION DEL DATAMODEL
	$usuario = $_GET [ 'usuario' ];
	$select = "SELECT json FROM [Config_arrays] WHERE objeto = 'dataModel' AND usuario = '" . $usuario . "'";
	$select = DBSelect ( utf8_decode ( $select ) );
	$respuesta = utf8_encode ( DBCampo ( $select , "json" ) );
	DBFree ( $select );
	echo $respuesta;
}
function del_jsonconfig() {
	//$tipo == 4 SE BORRA LA CONFIGURACION DE CONFIG_ARRAYS DEL USUARIO
	$usuario = $_GET [ 'usuario' ];
	$delete = "DELETE FROM [Config_arrays] WHERE grid = 'grid_array' AND usuario = '" . $usuario . "'";
	$delete = DBSelect ( utf8_decode ( $delete ) );
}
function put_jsonconfig() {
	//$tipo == 2 SE EJECUTA SOLO PARA GUARDAR COLUMNAS DESDE EL MENU CONTEXTUAL Y EL EVENTO AL CAMBIAR COLUMNAS
	$myJsonString = htmlspecialchars ( $_POST [ 'myJsonString' ] );
	$usuario = $_POST [ 'usuario' ];
	$id = $_POST [ 'grid' ];
	$tipomodel = $_POST [ 'tipomodel' ];
	$insertar_myJsonString = utf8_decode ( "DELETE FROM [Config_arrays] WHERE grid = '" . $id . "' AND usuario = '" . $usuario . "' AND objeto = '" . $tipomodel . "'" );
	$result_insertar_myJsonString = DBSelect ( $insertar_myJsonString );
	$insertar_myJsonString = utf8_decode ( "INSERT INTO [Config_arrays] (usuario,grid,objeto,json) VALUES ('" . $usuario . "','" . $id . "','" . $tipomodel . "','" . $myJsonString . "')" );
	echo $insertar_myJsonString;
	$result_insertar_myJsonString = DBSelect ( $insertar_myJsonString );
}
function get_jsonconfigcolumnas() {
	// $tipo == 3 SE EJECUTA EN LA CARGA DEL GRID -> PRIMER AJAX -> INFORMACION TABLA VENTANA
	// del_jsonconfig();
	$usuario = $_GET [ 'usuario' ];
	$multiportal_ventana = $_GET [ 'titulo' ]; // NO HACE FALTA ES EL NOMBRE DEL PORTAL
	$multiportal_tipo = $_GET [ 'grid' ]; // NO HACE FALTA ES EL TIPO DE DATO VENTANA
	$multiportal_rol = $_GET [ 'rol' ];
	$json = '';
	$contador = 0;
	$opciones_ocultar = array ();
	
// 	$query="
// 	SELECT distinct
// 	imagen
// 	,[editable]
// 	,ocultar
// 	,[campo]
// 	,CASE WHEN [nombre] IS NULL THEN campo else nombre end as nombre
// 	FROM [ventana] where Ventana = '".$multiportal_ventana."' AND Tipo = '".$multiportal_tipo."' AND rol IN (0,".$multiportal_rol.") ";
	
	$query = "SELECT distinct imagen ,[editable] ,ocultar,[campo],CASE WHEN [nombre] IS NULL THEN campo else nombre end as nombre
			FROM [ventana] where Tipo = 'grid_array' AND rol IN (0,$multiportal_rol) ";
	
	// echo $query;
	$multiportal = DBSelect ( ( $query ) );
	for(; DBNext ( $multiportal ) ;) {
		$opciones_ocultar [ utf8_encode ( DBCampo ( $multiportal , "campo" ) ) ] = array (
				"Campo" => utf8_encode ( DBCampo ( $multiportal , "campo" ) ) , 
				"Nombre" => utf8_encode ( DBCampo ( $multiportal , "nombre" ) ) , 
				"Ocultar" => utf8_encode ( DBCampo ( $multiportal , "ocultar" ) ) , 
				"Editar" => utf8_encode ( DBCampo ( $multiportal , "editable" ) ) , 
				"Imagen" => utf8_encode ( DBCampo ( $multiportal , "imagen" ) ) 
		);
	}
	DBFree ( $multiportal );
	
	$php_json = json_encode ( $opciones_ocultar );
	echo $php_json;
}
function get_jsonconfig() {
	// $tipo == 1 SE EJECUTA EN LA CARGA DEL GRID -> SEGUNDO AJAX -> INFORMACION DE CONFIG_ARRAYS
	$usuario = $_GET [ 'usuario' ];
	$titulo = $_GET [ 'titulo' ];
	$id = $_GET [ 'grid' ];
	$json = '';
	$colas = array ();
	$datacolas = array ();
	
	$contadordata = 0;
	
	// OBTENEMOS DE LA BD LA CONFIGURACION DEL GRID DEL USUARIO
	$query = utf8_decode ( "SELECT json ,objeto 
							FROM [Config_arrays] 
							WHERE grid = '" . $id . "' AND usuario = '" . $usuario . "' " );
	
	$query = DBSelect ( $query );
	
	for(; DBNext ( $query ) ;) {
		
		// REGISTROS CON INFORMACION DE COLUMNAS SEPARADO POR BARRAS (NO ES UN JSON)
		$row_json = utf8_encode ( DBCampo ( $query , "json" ) );
		
		// NOMBRE DEL OBJETO (colModel, colModelWidth, pageModel)
		$row_objeto = utf8_encode ( DBCampo ( $query , "objeto" ) );
		
		// PREPARAMOS ARRAY CON COLUMNA Y VALOR DE HIDDEN
		$contador = 0;
		if ( $row_objeto == 'colModel' ) {
			if ( $row_json != "" ) {
				$json = explode ( "|" , ( $row_json ) );
				foreach ( $json as $colum ) {
					if ( rtrim ( $colum , " " ) != '' ) {
						$campos = explode ( "=" , $colum );
						$dataIndex = $campos [ 0 ];
						//$hidden = $campos [ 1 ];
						$width = $campos [ 2 ];
						$title = $campos [ 3 ];
						$hidden = false;
						
						if ( $campos [ 1 ] == "true" ) {$hidden = true;}
						
						$colas [ $contador ] = array (
								"columna" => $dataIndex , 
								"hidden" => $hidden , 
								"width" => $width , 
								"title" => $title 
						);
						
						$contador = $contador + 1;
					}
				}
			}
		}
		
		// AÑADIMOS AL ARRAY VALOR DE WIDTH
		// $contador = 0;
		// if ( $row_objeto == 'colModelWith_XXX' ) {
		// if ( $row_json != "" ) {
		// $json = explode ( "|" , ( $row_json ) );
		// foreach ( $json as $colum ) {
		// if ( rtrim ( $colum , " " ) != '' ) {
		// $campos = explode ( "=" , $colum );
		
		// $dataIndex = $campos [ 0 ];
		// $width = $campos [ 1 ];
		
		// $colas [ $contador ] = array (
		// "width" => $hidden
		// );
		// $contador = $contador + 1;
		// }
		// }
		// }
		// }
		
		$contador = 0;
		if ( $row_objeto == 'pageModel' ) {
			if ( $row_json != "" ) {
				$json = explode ( "|" , ( $row_json ) );
				foreach ( $json as $colum ) {
					if ( rtrim ( $colum , " " ) != '' ) {
						$campos = explode ( "=" , $colum );
						$datacolas [ $contadordata ] = array (
								"columna" => $campos [ 0 ] , 
								"valores" => $campos [ 1 ] 
						);
						$contadordata = $contadordata + 1;
					}
				}
			}
		}
	}
	$json_arr = array (
			'colModel' => $colas , 
			'pageModel' => $datacolas 
	);
	$php_json = json_encode ( $json_arr );
	echo $php_json;
}
?>