<?php
//UTF8 ÁÁ
function curPageName() {
	
	return "MENU";
	global $NOMBRE_PORTAL;
	
	if ( $NOMBRE_PORTAL == '' ) {
		$content = strtoupper ( getenv ( 'HTTP_REFERER' ) );
		if ( $content == '' ) {
			$content = strtoupper ( substr ( $_SERVER [ "SCRIPT_NAME" ] , strrpos ( $_SERVER [ "SCRIPT_NAME" ] , "/" ) + 1 ) );
		} else {
			$content = strtoupper ( substr ( $content , strrpos ( $content , "/" ) + 1 ) );
		}
		return substr ( $content , 0 , strrpos ( $content , '.' ) );
	} else {
		return $NOMBRE_PORTAL;
	}
}
function BuscarOcultar( $campo , $valores , &$nombre ) {
	$nombre = /*utf8_decode*/ ( $campo );
	foreach ( $valores as $valor ) {
		
		if ( $valor [ 'Campo' ] == /*utf8_decode*/ ( $campo ) ) {
			$nombre = /*utf8_decode*/ ( $valor [ 'Nombre' ] );
			if ( $valor [ 'Ocultar' ] == 1 ) {
				return false;
			} else {
				return true;
			}
		}
	}
	return true;
}
function BuscarOcultarUTF8( $campo , $valores , &$nombre ) {
	$nombre = utf8_decode ( $campo );
	foreach ( $valores as $valor ) {
		
		if ( $valor [ 'Campo' ] == utf8_decode ( $campo ) ) {
			$nombre = utf8_encode ( $valor [ 'Nombre' ] );
			if ( $valor [ 'Ocultar' ] == 1 ) {
				return false;
			} else {
				return true;
			}
		}
	}
	return true;
}
function BuscarEditar( $campo , $valores , &$nombre ) {
	$nombre = utf8_decode ( $campo );
	foreach ( $valores as $valor ) {
		
		if ( $valor [ 'Campo' ] == utf8_decode ( $campo ) ) {
			$nombre = utf8_decode ( $valor [ 'Nombre' ] );
			if ( $valor [ 'Editar' ] == 1 ) {
				return true;
			} else {
				return false;
			}
		}
	}
	return true;
}
function BuscarEditarUTF8( $campo , $valores , &$nombre ) {
	$nombre = utf8_decode ( $campo );
	foreach ( $valores as $valor ) {
		if ( $valor [ 'Campo' ] == utf8_decode ( $campo ) ) {
			$nombre = utf8_encode ( $valor [ 'Nombre' ] );
			if ( $valor [ 'Editar' ] == 1 ) {
				if ( $valor [ 'Editable Consulta' ] == '' ) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}
	return true;
}
function BuscarValor( $campo , $valores , $columna ) {
	foreach ( $valores as $valor ) {
		if ( $valor [ 'Campo' ] == utf8_decode ( $campo ) ) {
			return utf8_decode ( $valor [ $columna ] );
		}
	}
	if ( $columna == 'Colspan' ) {
		return '1';
	}
	if ( $columna == 'Maxlenth' ) {
		return '250';
	}
	if ( $columna == 'Width' ) {
		return '120';
	}
	return '';
}
function BuscarClase( $campo , $valores ) {
	foreach ( $valores as $valor ) {
		if ( $valor [ 'Campo' ] == utf8_decode ( $campo ) ) {
			if ( $valor [ 'Obligado' ] == 1 ) {
				return ' class="campo_obligado" spb_campo="' . ( $valor [ 'Nombre' ] ) . '" spb_obligado_relacion="' . ( $valor [ 'Obligado Relacion' ] ) . '"';
			}
			return '';
		}
	}
	return '';
}
?>