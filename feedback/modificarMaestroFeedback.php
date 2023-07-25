<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_" . curPageName () . ".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$Id = $_GET [ "Id" ];
$Tipo = $_GET [ "Tipo" ];
$Pregunta = $_GET [ "Pregunta" ];
$TipoFeedback = $_GET [ "TipoFeedback" ];
$Usuario = $_GET [ "Usuario" ];
$Oficina = $_GET [ "Oficina" ];

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$query = "
	UPDATE [GestionPDT].[dbo].[Feedback_Preguntas] SET
		Oficina = '$Oficina'
		,Tipo = '$Tipo'
		,Pregunta = '$Pregunta'
		,TipoFeedback = '$TipoFeedback'
		,UsuarioModifica = '$Usuario'
		,FechaModifica = GETDATE()
	WHERE Id = $Id
";
$query = DBSelect ( utf8_decode ( $query ) );
DBFree ( $query );
DBClose ();

?>
