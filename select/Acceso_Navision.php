<?php
include_once('../conf/config.php');

$alta_usuario_nombre              = sanear_string($_GET["alta_usuario_nombre"]);
$alta_usuario_apellido_1          = sanear_string($_GET["alta_usuario_apellido_1"]);
$alta_usuario_apellido_2          = sanear_string($_GET["alta_usuario_apellido_2"]);
$selectRegistrosEspejo            = $_GET["selectRegistrosEspejo"];
$alta_usuario_acceso_navision_user= $_GET["alta_usuario_acceso_navision_user"];
$alta_usuario_acceso_navision_pass= $_GET["alta_usuario_acceso_navision_pass"];

if(iniciaWS())
{
	$consultaWS=new consultaWS('SP Berner','InterfaceWS');
	$params = array('nombre' => $alta_usuario_nombre,'apellido1' => $alta_usuario_apellido_1,'apellido2' => $alta_usuario_apellido_2,'usuarioespejo' => $selectRegistrosEspejo,'usuario' => $alta_usuario_acceso_navision_user,'password' => $alta_usuario_acceso_navision_pass);       

	//Ejecutamos la CodeUnit
	$result = $consultaWS->UsuarioNavision($params); 

	$consultaWS->ejecucionErronea($result);       
	$id_resultado = $result->return_value;
	$id_usuario = $result->usuario;
	$id_password = $result->password;
	finalizaWS();

	//Devolvemos el resultado
	echo $id_usuario.'|'.$id_password;
}

/**
 * Reemplaza todos los acentos por sus equivalentes sin ellos
 *
 * @return $string string saneada
 *
 */
function sanear_string( $string ) {
	$string = trim ( $string );
	$string = strtoupper( $string );
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
