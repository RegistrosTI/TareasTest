<?php
// UTF8 ñáç
/**
 * ***********************************************************
 * AUTOR: Alberto Ruiz
 * FECHA: 22/09/2017
 * DESC: ENVIO DE CORREOS NO PROCESADOS DE LA TABLA MAIL
 * ***********************************************************
 */

// ****************************************************INCLUDES NECESARIOS
if ( 1 == 1 ) {
	$GLOBALS [ "cfg_HostDB" ] = 'SQL2016';
	$GLOBALS [ "cfg_UserDB" ] = 'gestionti';
	$GLOBALS [ "cfg_PassDB" ] = 'gestionti';
	$GLOBALS [ "cfg_DataBase" ] = 'GestionPDT';
	
	
	include "../../soporte/DB.php";
	require "../conf/config.php";
	include "../../soporte/funcionesgenerales.php";
	
	// incluimos configuracion de correo
	require "../conf/mail.php";
	

}
// ****************************************************INCLUDES NECESARIOS

// ****************************************************INICIO
DBConectar ( $GLOBALS [ "cfg_DataBase" ] );
// ****************************************************INICIO

// ******************************************************************************************************** VARIABLES
$asunto = "EFICIENCIA. Reporte diario de tiempo fichado.";
$mensaje = "";
$ASIGNADOA = "";
$TO = "";
$fecha_hora = date ( "d-m-Y H:i:s" );
$queryProcesados = '';

// BUSCAMOS LOS USUARIO QUE LES TENGA QUE LLEGAR EL MENSAJE
$query = "
	SELECT  
		 M.Numero
		,M.direccion
		,M.Tipo
		,M.Tarea
		,M.Usuario
		,D.Name AS UsuarioNombre
		,T.[Título] AS Titulo
		,T.Solicitado
		,T.[Descripción] AS Descripcion
	FROM Mail AS M
	INNER JOIN [Tareas y Proyectos] AS T
		ON M.Tarea = T.Id
	INNER JOIN dbo.TF_DATOS_DOMINIO() AS D
		ON M.Usuario = D.sAMAccountName
	WHERE M.Procesado = 0 AND M.Tipo IN (1)
";

$query = DBSelect ( utf8_decode ( $query ) );
for(; DBNext ( $query ) ;) {
	
	$NUMERO = utf8_encode ( DBCampo ( $query , "Numero" ) );
	$USUARIO = utf8_encode ( DBCampo ( $query , "Usuario" ) );
	$USUARIONOMBRE = utf8_encode ( DBCampo ( $query , "UsuarioNombre" ) );
	$SOLICITADO = utf8_encode ( DBCampo ( $query , "Solicitado" ) );
	$TIPO = utf8_encode ( DBCampo ( $query , "Tipo" ) );
	$TAREA = utf8_encode ( DBCampo ( $query , "Tarea" ) );
	$TO = utf8_encode ( DBCampo ( $query , "direccion" ) );
	$TITULO = utf8_encode ( DBCampo ( $query , "Titulo" ) );
	$DESCRIPCION = strip_tags(utf8_encode ( DBCampo ( $query , "Descripcion" ) ) ) ;
	
	$queryProcesados .= " UPDATE Mail SET Procesado = 1 WHERE Numero = $NUMERO; ";
	
	$prioridad = 2;
	switch ($TIPO){
		case '1':
			$prioridad = 1;
			$ASUNTO = "NUEVA TAREA DE TIPO INCIDENCIA SEGURIDAD";
			$CUERPO_MENSAJE ="El usuario <b>$USUARIONOMBRE</b>, ha creado una nueva tarea en el portal de tareas de tipo <span style='color:red;'>Incidencia de Seguridad</span>, por favor diríjase al portal para analizar la tarea siguiente:";
			break;
	}
	
	$enviar = false;
	$mensaje = "
	<html>
	<head>
		<meta charset='UTF-8'>
		<title>Portal de Tareas</title>
	</head>
	<body>
		<p>Buenos días,</p>
		<p>$CUERPO_MENSAJE</p>
		<p><b>Tarea:</b> $TAREA<p>
		<p><b>Título:</b> $TITULO<p>
		<p><b>Observaciones:</b><br>$DESCRIPCION
		<br><br>
		<p><small>(correo enviado de forma automática, por favor no responda a este mail)</small></p><BR><BR>
	</body>
	</html>	
	";

	
	
	// envio mediante phpmailer
	if ( $modo_de_pruebas ) {
		$TO = $correoAdjunto;
	}
	
		
	$mail = new PHPMailer ();
	$mail -> CharSet = "UTF-8";
	$mail -> Priority = $prioridad; // 1 = High, 2 = Medium, 3 = Low
	$mail -> IsSMTP (); // telling the class to use SMTP
	$mail -> SMTPDebug = 1; // enables SMTP debug information (for testing) 2 = errors and messages 1 = error messages only
	$mail -> SMTPAuth = false; // enable SMTP authentication
	$mail -> Host = $smtp_server; // sets the SMTP server
	$mail -> Port = $smtp_port; // set the SMTP port
	//$mail -> Username = $username; // SMTP account username
	//$mail -> Password = $password; // SMTP account password
	$mail -> SetFrom ( $FROM , 'SP-Berner Portal TAREAS' );
	$mail -> Subject = $ASUNTO;
	$mail -> MsgHTML ( $mensaje );
	
	$mail -> AddAddress ( $TO );
	
	if (  ! $mail -> Send () ) {
		echo "<br>$fecha_hora: Mailer Error a usuario: " . $mail -> ErrorInfo;
		echo "<br><span style='color: green;'>$fecha_hora: REINTENTANDO EN 10 SEG. PARA: $USUARIONOMBRE, $TO </span>";
		sleep(10);
		if (  ! $mail -> Send () ) {
			echo "<br><span style='color: RED;'>$fecha_hora: Mailer Error a usuario: " . $mail -> ErrorInfo . "</span>";
		}else{
			echo "<br><span style='color: green;'>$fecha_hora: $ASUNTO de: $USUARIONOMBRE, $TO </span>";
		}
	} else {
		echo "<br>$fecha_hora: $ASUNTO de $USUARIONOMBRE a: $TO";
	}
		
	
}


DBSelect ( utf8_decode ( $queryProcesados ) );

// ********************************************************************************************************CODIGO
  
// ****************************************************FINAL
DBFree ( $query );
DBClose ();
// ****************************************************FINAL


?>