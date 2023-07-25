<?php
$cantidad_ocultar = 0;
$opciones_ocultar = array ();

$multiportal = "
	SELECT 
		isnull(disparador,1) 				as disparador
		,isnull(editable_consulta,'') 		as editable_consulta
		,isnull(visible_consulta,'') 		as visible_consulta
		,isnull(obligado_relacion,'') 		as obligado_relacion
		,isnull(obligado,0) 				as obligado
		,isnull(coletilla,'') 				as coletilla
		,fila								as fila
		,columna							as columna
		,[colspan]							as colspan
		,[maxlenth]							as maxlenth
		,[width]							as width
		,editable							as editable
		,ocultar							as ocultar
		,[campo]							as campo
		,[oficina]							as oficinas
		,CASE 	WHEN [nombre] IS NULL 
				THEN campo 
				ELSE nombre 
		END 								as nombre 
	FROM [ventana] 
	WHERE Ventana = '" . $multiportal_ventana . "' 
		AND Tipo = '" . $multiportal_tipo . "' 
		AND rol in(0," . $multiportal_rol . ") 
	ORDER BY fila, columna
";
//die($multiportal);
$multiportal = DBSelect ( ( $multiportal ) );

for(; DBNext ( $multiportal ) ;) {
	
	$ocultar = DBCampo ( $multiportal , "ocultar" );
	
	// OCULTAR TODOS AQUELLOS CAMPOS QUE NO ESTEN EN LA LISTA DE OFICINAS DEL CAMPO
	if ( $multiportal_tipo == 'FORMULARIO' ) {
		if ( $ocultar == 0 ) {
			$Oficinas = utf8_encode ( DBCampo ( $multiportal , "oficinas" ) );
			$Oficinas = explode ( ',' , $Oficinas );
			if ( !in_array ( $OFICINA , $Oficinas ) ) {
				$ocultar = 1;
			}
		}
	}
	// OCULTAR TODAS AQUELLAS SECCIONES QUE NO ESTEN EN LA LISTA DE OFICINAS DEL CAMPO
	if ( $multiportal_tipo == 'SECCIONES' ) {
		if ( $ocultar == 0 ) {
			$Oficinas = utf8_encode ( DBCampo ( $multiportal , "oficinas" ) );
			$Oficinas = explode ( ',' , $Oficinas );
			if ( !in_array ( $OFICINA , $Oficinas ) ) {
				$ocultar = 1;
			}
		}
	}
	// LA BARRA DE MENÚ SOLO MOSTRARÁ ELEMENTOS OCULTOS SI LA OFICINA ESTÁ INFORMADA
	if ( $multiportal_tipo == 'MENU' ) {
		if ( $ocultar == 1 ) {
			$Oficinas = utf8_encode ( DBCampo ( $multiportal , "oficinas" ) );
			$Oficinas = explode ( ',' , $Oficinas );
			if ( in_array ( $oficina_usuario_validado , $Oficinas ) ) {
				$ocultar = 0;
			}
		}
	}
	
	$opciones_ocultar [ $cantidad_ocultar ] = array (
			"Disparador" => DBCampo ( $multiportal , "disparador" ) , 
			"Editable Consulta" => DBCampo ( $multiportal , "editable_consulta" ) , 
			"Visible Consulta" => DBCampo ( $multiportal , "visible_consulta" ) , 
			"Obligado Relacion" => DBCampo ( $multiportal , "obligado_relacion" ) , 
			"Obligado" => DBCampo ( $multiportal , "obligado" ) , 
			"Coletilla" => DBCampo ( $multiportal , "coletilla" ) , 
			"Fila" => DBCampo ( $multiportal , "fila" ) , 
			"Columna" => DBCampo ( $multiportal , "columna" ) , 
			"Colspan" => DBCampo ( $multiportal , "colspan" ) , 
			"Maxlenth" => DBCampo ( $multiportal , "maxlenth" ) , 
			"Width" => DBCampo ( $multiportal , "width" ) , 
			"Campo" => DBCampo ( $multiportal , "campo" ) , 
			"Nombre" => utf8_encode ( DBCampo ( $multiportal , "nombre" ) ) , 
			"Ocultar" => $ocultar , 
			"Editar" => DBCampo ( $multiportal , "editable" ) 
	);
	$cantidad_ocultar = $cantidad_ocultar + 1;
}
DBFree ( $multiportal );

?>