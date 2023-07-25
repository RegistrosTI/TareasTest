<?php
//include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$Id = $_GET [ "Id" ];
$Med_Calidad = $_GET [ "Med_Calidad" ];
$Med_Enfoque_Cliente = $_GET [ "Med_Enfoque_Cliente" ];
$Med_Evaluador = $_GET [ "Med_Evaluador" ];
$Comentario_Evaluador = '';
if (isset($_GET [ "Comentario_Evaluador" ])){
	$Comentario_Evaluador = $_GET [ "Comentario_Evaluador" ];
}
$Usuario = $_GET [ "usuario" ];
$campo_a_cambiar = $_GET [ "campo_a_cambiar" ];
$valor = '';
$Usuario_Valoracion = '';
$Evaluador = 0;
DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

// Hay que comprobar que el valor actual sea -1, de lo contrario no debe de poder modificar
$q = " SELECT [$campo_a_cambiar] as VALOR, USUARIO, AUTOMATICA FROM DBO.EVALUACIONES WHERE Id = $Id ";
$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$valor = ( DBCampo ( $q , utf8_decode ( "VALOR" ) ) );
	$Usuario_Valoracion = ( DBCampo ( $q , utf8_decode ( "USUARIO" ) ) );
	$Automatica = ( DBCampo ( $q , utf8_decode ( "AUTOMATICA" ) ) );
}
DBFree ( $q );

// Hay que comprobar que el usuario sea evaluador
$q = " SELECT coalesce([Evaluador],0) as Evaluador FROM [GestionPDT].[dbo].[Configuracion_Usuarios] where Usuario = '$Usuario' and (Baja = 0 or Baja is null) ";
$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$Evaluador = ( DBCampo ( $q , utf8_decode ( "Evaluador" ) ) );
}
DBFree ( $q );

// Si no tenemos el campo que hay que cambiar elevamos el error
if ( $campo_a_cambiar == '' ) {
	echo "No se ha seleccionado un campo para cambiar.";
	DBClose ();
	die ();
}

// Si se quiere cambiar la valoracion de jefe y no se es jefe, elevamos error
if ( $campo_a_cambiar == 'Med_Evaluador' && $Evaluador == 0 ) {
	echo "Este usuario no puede evaluar sobre este campo.";
	DBClose ();
	die ();
}

// Si se quiere cambiar el comentario del evaluador y no se es, elevamos error
if ( $campo_a_cambiar == 'Comentario_Evaluador' && $Evaluador == 0 ) {
	echo "Debe ser evaluador para cambiar este campo.";
	DBClose ();
	die ();
}

// Si el usuario asignado no es el mismo de la valoracion, excepto en la evaluacion del jefe que si puede, sino , elevamos error
if ( $Usuario != $Usuario_Valoracion ) {
	//Alberto 01/12/2017 Ahora los evaluadores deben cambiar la evaluacion de usuarios, por tanto aunque no sea el usuario de la valoración deben de poer cambiar
	if ( /*$campo_a_cambiar == 'Med_Evaluador' &&*/ $Evaluador == 1 ) { 
		// Se permite el cambio
	} else {
		echo "Este usuario no puede cambiar la valoracion.";
		DBClose ();
		die ();
	}
}

// Si la evaluación es automática solo la medicion del evaluador se puede cambiar
if ( $Automatica == '1' ) {
	if ( $campo_a_cambiar == 'Med_Evaluador' && $Evaluador == 1 ) {
		// Se permite el cambio
	} else {
		echo "Las evaluaciones finales son automáticas y no se pueden cambiar.";
		DBClose ();
		die ();
	}
}

// Si ya se ha efectuado la evaluación no se puede cambiar, solo se puede el cambiar la nota del evluador por el evaluador
if ( ($campo_a_cambiar == 'Med_Evaluador' && $Evaluador == 1) || ($campo_a_cambiar == 'Comentario_Evaluador' && $Evaluador == 1) || ($campo_a_cambiar == 'Med_Calidad' && $Evaluador == 1) ) {
	// Se permite el cambio
} else {
	if ( $valor != '-1' ) {
		echo "La valoración ya ha sido efectuada y no se puede cambiar.";
		DBClose ();
		die ();
	}
}

// Si el usuario ha evaluado los dos campos, se pone la fecha de evaluacion
// 26/07/2017 ALberto, enfoque cliente ya no se evalua
$fecha_usuario = '';
if ( $Med_Calidad != '-1' /*&& $Med_Enfoque_Cliente != '-1'*/ ) {
	$fecha_usuario = ' , Fecha_Usuario = GETDATE() ';
}
//Si lo que se ha cambiado es la evaluacion del jefe hay que poner la fecha del jefe y sus datos
if ( $campo_a_cambiar == 'Med_Evaluador') {
	
	// Obtenemos el nombre del evaluador
	$nomEvaluador = "";
	$q = "EXEC	[dbo].[SP_OBTENER_NOMBRE_DOMINIO] '$Usuario'";
	$q = DBSelect ( utf8_decode ( $q ) );
	for(; DBNext ( $q ) ;) {
		$nomEvaluador 	= utf8_encode ( DBCampo ( $q , utf8_decode ( "Name" ) ) );
	}
	DBFree ( $q );
	
	$q="
		UPDATE [Evaluaciones]
		SET 
			[Med_Evaluador] = $Med_Evaluador
			,[Fecha_Evaluador] = GETDATE()
			,[Evaluador] = '$Usuario'
			,[Evaluador_Nombre] = '$nomEvaluador'
		WHERE Id = $Id
	";
}else{
	if ($campo_a_cambiar == 'Comentario_Evaluador'){
		$q = "
			UPDATE [Evaluaciones]
			SET [Comentario_Evaluador] = '$Comentario_Evaluador'
			WHERE Id = $Id";
	}else{
		$q = "
			UPDATE [Evaluaciones]
			SET [Med_Calidad] = $Med_Calidad
				,[Med_Enfoque_Cliente] = $Med_Enfoque_Cliente
				$fecha_usuario
			WHERE Id = $Id";
	}
}





DBSelect ( utf8_decode ( $q ) );
//DBFree ( $q );
DBClose ();
?>
