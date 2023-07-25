<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

/*
 * $alta_usuario_nombre = strtolower(str_replace ( " " , "" , $_GET [ "alta_usuario_nombre" ] ));
 * $alta_usuario_apellido_1 = strtolower(str_replace ( " " , "" , $_GET [ "alta_usuario_apellido_1" ] ));
 * $alta_usuario_apellido_2 = strtolower(str_replace ( " " , "" , $_GET [ "alta_usuario_apellido_2" ] ));
 */

$alta_usuario_nombre = sanear_string ( $_GET [ "alta_usuario_nombre" ] );
$alta_usuario_apellido_1 = sanear_string ( $_GET [ "alta_usuario_apellido_1" ] );
$alta_usuario_apellido_2 = sanear_string ( $_GET [ "alta_usuario_apellido_2" ] );

$Usuario_Incorporacion = $_GET [ "Usuario_Incorporacion" ];
$Mail_Incorporacion = $_GET [ "Mail_Incorporacion" ];
$usuario_buscar = '';
$libre = false;
$Registros = 0;
$contador = 0;
$usuario_buscar = $alta_usuario_nombre . '.' . $alta_usuario_apellido_1;

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

while ( $libre == false ) {
	$qry = DBSelect ( "SELECT CAST(COUNT(*) as int) as RESULTADO FROM (SELECT sAMAccountName ,Name, mail, ROW_NUMBER() OVER(ORDER BY sAMAccountName) Fila,CASE WHEN ((2 & userAccountControl) = 2 ) THEN 0 ELSE 1 END AS Activo  FROM OpenQuery(ADSI, 'SELECT sAMAccountName,Name,mail,userAccountControl FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user'' AND objectClass=''user''  and sAMAccountName = ''" . $usuario_buscar . "''')) Consulta" );
	$Registros = DBCampo ( $qry , "RESULTADO" );
	if ( $Registros == '0' ) {
		$libre = true;
	} else {
		$contador = $contador + 1;
		$usuario_buscar = $usuario_buscar . $contador;
	}
	DBFree ( $qry );
}

$libre = false;
$Registros = 0;
$contador = 0;
$usuario_buscar_mail = $usuario_buscar . '@sp-berner.com';
while ( $libre == false ) {
	$qry = DBSelect ( "SELECT COUNT(*) as RESULTADO FROM (SELECT sAMAccountName ,Name, mail, ROW_NUMBER() OVER(ORDER BY sAMAccountName) Fila,CASE WHEN ((2 & userAccountControl) = 2 ) THEN 0 ELSE 1 END AS Activo  FROM OpenQuery(ADSI, 'SELECT sAMAccountName,Name,mail,userAccountControl FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user'' AND objectClass=''user''  and mail = ''" . $usuario_buscar_mail . "''')) Consulta" );
	$Registros = DBCampo ( $qry , "RESULTADO" );
	if ( $Registros == '0' ) {
		$libre = true;
	} else {
		$contador = $contador + 1;
		$usuario_buscar_mail = $usuario_buscar_mail . $contador;
	}
	DBFree ( $qry );
}
DBClose ();

echo substr ( $usuario_buscar , 0 , 20 ) . '|' . $usuario_buscar_mail;
function limpiar_string( $s ) {
	$s = preg_replace ( "/[áàâãª]/" , "a" , $s );
	$s = preg_replace ( "/[ÁÀÂÃ]/" , "A" , $s );
	$s = preg_replace ( "/[ÍÌÎ]/" , "I" , $s );
	$s = preg_replace ( "/[íìî]/" , "i" , $s );
	$s = preg_replace ( "/[éèê]/" , "e" , $s );
	$s = preg_replace ( "/[ÉÈÊ]/" , "E" , $s );
	$s = preg_replace ( "/[óòôõº]/" , "o" , $s );
	$s = preg_replace ( "/[ÓÒÔÕ]/" , "O" , $s );
	$s = preg_replace ( "/[úùû]/" , "u" , $s );
	$s = preg_replace ( "/[ÚÙÛ]/" , "U" , $s );
	
	$s = str_replace ( "ç" , "c" , $s );
	$s = str_replace ( "Ç" , "C" , $s );
	$s = str_replace ( "ñ" , "n" , $s );
	$s = str_replace ( "Ñ" , "N" , $s );
	
	$s = str_replace ( " " , "" , $s );
	$s = strtolower ( $s );
	
	return $s;
}

/**
 * Reemplaza todos los acentos por sus equivalentes sin ellos
 *
 * @return $string string saneada
 *        
 */
function sanear_string( $string ) {
	$string = trim ( $string );
	$string = strtolower ( $string );
	$string = str_replace ( " " , "" , $string );
	$string = str_replace ( array (
			'á' , 
			'à' , 
			'ä' , 
			'â' , 
			'ª' , 
			'Á' , 
			'À' , 
			'Â' , 
			'Ä' 
	) , array (
			'a' , 
			'a' , 
			'a' , 
			'a' , 
			'a' , 
			'A' , 
			'A' , 
			'A' , 
			'A' 
	) , $string );
	
	$string = str_replace ( array (
			'é' , 
			'è' , 
			'ë' , 
			'ê' , 
			'É' , 
			'È' , 
			'Ê' , 
			'Ë' 
	) , array (
			'e' , 
			'e' , 
			'e' , 
			'e' , 
			'E' , 
			'E' , 
			'E' , 
			'E' 
	) , $string );
	
	$string = str_replace ( array (
			'í' , 
			'ì' , 
			'ï' , 
			'î' , 
			'Í' , 
			'Ì' , 
			'Ï' , 
			'Î' 
	) , array (
			'i' , 
			'i' , 
			'i' , 
			'i' , 
			'I' , 
			'I' , 
			'I' , 
			'I' 
	) , $string );
	
	$string = str_replace ( array (
			'ó' , 
			'ò' , 
			'ö' , 
			'ô' , 
			'Ó' , 
			'Ò' , 
			'Ö' , 
			'Ô' 
	) , array (
			'o' , 
			'o' , 
			'o' , 
			'o' , 
			'O' , 
			'O' , 
			'O' , 
			'O' 
	) , $string );
	
	$string = str_replace ( array (
			'ú' , 
			'ù' , 
			'ü' , 
			'û' , 
			'Ú' , 
			'Ù' , 
			'Û' , 
			'Ü' 
	) , array (
			'u' , 
			'u' , 
			'u' , 
			'u' , 
			'U' , 
			'U' , 
			'U' , 
			'U' 
	) , $string );
	
	$string = str_replace ( array (
			'ñ' , 
			'Ñ' , 
			'ç' , 
			'Ç' 
	) , array (
			'n' , 
			'N' , 
			'c' , 
			'C' 
	) , $string );
	
	return $string;
}
?>
