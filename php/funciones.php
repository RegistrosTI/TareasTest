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
function MostrarOcultarTeletrabajo($campo, $usuario, &$nombre ){
	$nombre = /*utf8_decode*/ ( $campo );

   $query = " 
              SELECT COUNT(Usuario) AS USERCOUNT FROM [dbo].[Teletrabajo_Usuarios] AS TU
              LEFT JOIN [GestionIDM].[dbo].[LDAP] AS LDAP ON LDAP.sAMAccountName = TU.Usuario 
			  WHERE Usuario = '$usuario' GROUP BY LDAP.NIF
			  "; 
   $query = iconv('UTF-8', 'ISO-8859-1', $query); //decode
   $query = DBSelect ($query);
   $countUser = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "USERCOUNT")); //encode

   $query = " 
              SELECT LDAP.NIF AS NIF FROM [GestionIDM].[dbo].[LDAP] AS LDAP 
			  WHERE sAMAccountName = '$usuario' GROUP BY LDAP.NIF
			  "; 
   $query = iconv('UTF-8', 'ISO-8859-1', $query); //decode
   $query = DBSelect ($query);
   $nifUser =  iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "NIF"));
 
   $query = "
			SELECT COUNT(EP.NIF) AS USERCOUNTNIFDEPT FROM [GestionIDM].[dbo].[EPSILON] AS EP 
			INNER JOIN [GestionPDT].[dbo].[Teletrabajo_Departamentos] AS TD ON TD.Oficina = EP.Departamento
			WHERE EP.NIF = '$nifUser' 
            ";
	$query = DBSelect ($query);
	$countDpto = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "USERCOUNTNIFDEPT")); //encode		
    DBFree($query);

	if($countUser == 1 || $countDpto == 1){
		$valRet = true;
	}else{
		$valRet = false;
	}

   return $valRet;
}
?>