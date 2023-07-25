<?php
// UTF8 ñáç
/**
 * ***********************************************************
 * AUTOR: Alberto Ruiz
 * FECHA: 11/05/2016
 * DESC: CONTABILIDAD: ENVIA CORREOS A LOS RESPONSABLES CON FECHA CONTROL VENCIDA
 * ***********************************************************
 */

// ****************************************************INCLUDES NECESARIOS
if ( 1 == 1 ) {
// 	$GLOBALS["cfg_HostDB"]='SQLSERVER';
// 	$GLOBALS["cfg_UserDB"]='gestionti';
// 	$GLOBALS["cfg_PassDB"]='gestionti';
	$GLOBALS["cfg_DataBase"]='GestionCON';
	
	include "../conf/config.php";
	include "../../soporte/DB.php";
	include "../../soporte/funcionesgenerales.php";
	
	// incluimos configuracion de correo
	require_once "../conf/mail.php";
}
// ****************************************************INCLUDES NECESARIOS

// ****************************************************INICIO
DBConectar ( $GLOBALS [ "cfg_DataBase" ] );
// ****************************************************INICIO

// ******************************************************************************************************** VARIABLES
$asunto = "";
$mensaje = "";
$ASIGNADOA = "";
$TO = "";
$fecha_hora = date ( "d-m-Y H:i:s" );
$enviar = true;

$query = "
	SELECT [Id]
		,[Título] as Titulo
		,[Asignado a] as asignadoa
		,[Tipo]
		,[Estado]
		,[Solicitado]
		,IsNull(CONVERT(varchar(20), [Fecha Objetivo],105)	,'') AS Fecha_Objetivo
		,(SELECT Mail
			FROM OpenQuery(ADSI, 'SELECT Mail, Name, sAMAccountName FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user'' AND objectClass=''user''  ') 
			WHERE Name = [Asignado a]) AS Correo
	FROM [GestionCON].[dbo].[Tareas y Proyectos]
	WHERE (SELECT CONVERT(VARCHAR(10), GETDATE(),120)) = (SELECT CONVERT(VARCHAR(10), [Fecha Objetivo],120))
		AND [Tipo] = 'Expediente'
		AND [Estado] NOT IN ('Completado','Cancelado')" ;

//$query = utf8_decode ( $query );

$query = DBSelect ( utf8_decode ( $query ) );

for(; DBNext ( $query ) ;) {
	$ID = DBCampo ( $query , "Id" );
	$TITULO = DBCampo ( $query , "Titulo" );
	$FECHA_OBJETIVO = utf8_encode ( DBCampo ( $query , "Fecha_Objetivo" ) );
	$ASIGNADOA = utf8_encode ( DBCampo ( $query , "asignadoa" ) );
	$TO = utf8_encode ( DBCampo ( $query , "Correo" ) );
	$ESTADO = strtolower(utf8_encode ( DBCampo ( $query , "Estado" ) ));
	
	$enviar = true;
	$asunto = "Control de tarea ($ID) $TITULO";
	$mensaje = "Buenos días $ASIGNADOA,<br><br>Este es un aviso automático para recordar que hoy vence la fecha de control de la tarea <b><font color='red'>$ID</font> $TITULO</b> que se encuentra en estado $ESTADO.<br><br>";
	$mensaje .= "<small>(correo enviado de forma automática, por favor no responda a este mail)</small>";
	
	if ( $enviar ) {
		
		// envio mediante phpmailer
		$mail = new PHPMailer ();
		$mail -> CharSet = "UTF-8";
		$mail -> IsSMTP (); // telling the class to use SMTP
		$mail -> SMTPDebug = 1; // enables SMTP debug information (for testing) 2 = errors and messages 1 = error messages only
		$mail -> SMTPAuth = true; // enable SMTP authentication
		$mail -> Host = $smtp_server; // sets the SMTP server
		$mail -> Port = $smtp_port; // set the SMTP port
		$mail -> Username = $username; // SMTP account username
		$mail -> Password = $password; // SMTP account password
		$mail -> SetFrom ( $FROM , 'SP-Berner Portal SES' );
		//$mail -> AddReplyTo ( $mailResponsableSES , 'SP-Berner Portal TAREAS' );
		$mail -> Subject = $asunto;
		$mail -> MsgHTML ( $mensaje );
		
		if ( $modo_de_pruebas ) {
			$TO = $correoAdjunto;
		}
		
		$mail -> AddAddress ( $TO , $ASIGNADOA );
		
		if (  ! $mail -> Send () ) {
			echo "<br>$fecha_hora: Mailer Error a usuario: " . $mail -> ErrorInfo;
		} else {
			echo "<br>$fecha_hora: $asunto para: $ASIGNADOA, $TO";
		}
	}
} // FIN DEL BUCLE DE DOCUMENTOS

// ********************************************************************************************************CODIGO

// ****************************************************FINAL
DBFree ( $query );
DBClose ();
// ****************************************************FINAL

?>