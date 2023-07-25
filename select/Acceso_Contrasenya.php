<?php
include_once('../conf/config.php');

$alta_usuario_apellido_1= sanear_string($_GET["alta_usuario_apellido_1"]);

if(iniciaWS())
{
	$consultaWS=new consultaWS('SP Berner','InterfaceWS');
	$params = array('apellido1' => $alta_usuario_apellido_1,'password' => '');       

	//Ejecutamos la CodeUnit
	$result = $consultaWS->UsuarioPassword($params); 

	$consultaWS->ejecucionErronea($result);       
	$id_password = $result->password;
	finalizaWS();

	//Devolvemos el resultado
	echo $id_password;
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
