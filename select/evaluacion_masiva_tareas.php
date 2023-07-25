<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);

$femEvaluador	= $_POST["femUsuario"];
$femDesde		= $_POST["femDesde"];
$femHasta		= $_POST["femHasta"];
$femUsuario 	= $_POST["femAutocompleteUsuario"];
$femValoracion 	= $_POST["femValoracion"];

// Obtenemos el nombre del evaluador
$nomEvaluador = "";
$q = "EXEC	[dbo].[SP_OBTENER_NOMBRE_DOMINIO] '$femEvaluador'";
$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$nomEvaluador 	= utf8_encode ( DBCampo ( $q , utf8_decode ( "Name" ) ) );
}	

// Obtenemos el usuario del usuario a evaluar
$q = "EXEC	[dbo].[SP_OBTENER_USUARIO_DOMINIO] '$femUsuario'";
$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$femUsuario 	= utf8_encode ( DBCampo ( $q , utf8_decode ( "sAMAccountName" ) ) );
}

// Preparo las oficinas y el área del evaluador para la select
$oficinas = '';
$area = '';
$q = "SELECT Oficina, Area FROM Configuracion_Usuarios WHERE Usuario = '$femEvaluador' ";
$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$oficinas 	= utf8_encode ( DBCampo ( $q , utf8_decode ( "Oficina" ) ) );
	$area   	= utf8_encode ( DBCampo ( $q , utf8_decode ( "Area" ) ) );
}
$oficinas = "'" . str_replace(",", "','", $oficinas) . "'";

// Si tiene area solo puede evaluar las suyas, si no tiene es que puede evaluarlas todas y no filtramos
if ( $area != ""){
	$filtro_adicional = " AND EVA.usuario in (select usuario from Configuracion_Usuarios where area = '$area') ";
}

// Realizamos la actualización
$UPDATE = "
	UPDATE EVA
	SET 
		Fecha_Evaluador = GETDATE(),
		Evaluador = '$femEvaluador',
		Evaluador_Nombre = '$nomEvaluador',
		Med_Evaluador = $femValoracion
	FROM Evaluaciones  AS EVA
		INNER JOIN [Tareas y Proyectos] AS TAR 
			ON EVA.Tarea = TAR.Id
		INNER JOIN Configuracion_Oficinas AS OFI 
			ON TAR.Oficina = OFI.Oficina
	WHERE fechaCreacion between '$femDesde' and DATEADD(day,1,'$femHasta') --añado un dia para sumar las 24 horas al dia actual
		AND EVA.usuario = '$femUsuario'
		AND Med_Evaluador = -1
		AND Automatica = 0
		AND EVA.Periodo = OFI.Evaluacion
		--AND TAR.Oficina IN (SELECT Oficina FROM Configuracion_Usuarios AS USU WHERE USU.Usuario = '$femEvaluador')
		AND TAR.Oficina IN ($oficinas) 
		AND TAR.Evaluable = 'SI'
		$filtro_adicional
";
//die($UPDATE);
DBSelect(utf8_decode($UPDATE));

	
DBClose();
?>