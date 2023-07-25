<?php
// UTF8 ñáç
/**
 * ***********************************************************
 * AUTOR: Alberto Ruiz
 * FECHA: 19/06/2018
 * DESC: ENVÍA UN CORREO A RESPONSABLES CON LOS MINUTOS FICHADOS Y PRESENCIA DE LOS USUARIOS DE SUS OFICINAS
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
// recojo los parametros de linea de comandos y los asigno al get
parse_str ( implode ( '&' , array_slice ( $argv , 1 ) ) , $_GET );

// recuperamos el tipo de la ficha
if (!isset($_GET [ 'tipo' ])){
	die('NO HAY TIPO');
}
$tipo = $_GET [ 'tipo' ];

// ++ comprobamos si es viernes para preparar tipo de informe
$timestamp = time ();
if ( $tipo == 'SEMANAL' && date ( 'D' , $timestamp ) != 'Fri' ) {
	die('<BR>**** SE OMITE EL CORREO DEL VIERNES ****<BR>');
}
// -- comprobamos si es viernes para preparar tipo de informe

$mensaje = "";
$ASIGNADOA = "";
$TO = "";
$asunto = "";

switch($tipo){
	case 'DIARIO':
		$asunto = "EFICIENCIA. Reporte diario de tiempo fichado.";
		$where = " AND USU.InfPresenciaDiario = 1 ";
		break;
	case 'SEMANAL':
		$asunto = "EFICIENCIA. Reporte semanal de tiempo fichado.";
		$where = " AND USU.InfPresenciaSemana = 1 ";
		break;
}
/*
$query = "
	SELECT
		USU.Usuario AS Usuario
		,DOM.Mail
		,USU.Oficina
		,DOM.Name	AS Name
	FROM Configuracion_Usuarios AS USU
	LEFT JOIN dbo.TF_DATOS_DOMINIO() AS DOM
		ON USU.Usuario = DOM.sAMAccountName
	WHERE USU.Baja = 0 $where
	ORDER BY USU.Usuario
";
*/
$query = "
	SELECT
		USU.Usuario AS Usuario
		,LDAP.Mail
		,USU.Oficina
		,LDAP.Name	AS Name
	FROM Configuracion_Usuarios AS USU
	LEFT JOIN GestionIDM.dbo.LDAP AS LDAP
		ON USU.Usuario = LDAP.sAMAccountName
	WHERE USU.Baja = 0 $where
		AND (LDAP.Mail <> '' OR LDAP.Mail IS NOT NULL)
		AND BajaDominio IS NULL
		AND BajaEpsilon IS NULL
		--AND sAMAccountName = 'ALBERTO.RUIZ'
	ORDER BY USU.Usuario
";


$fecha_hora = date ( "d-m-Y H:i:s" );
$estilos2 = "
		.rojo{background-color: #F5A9A9; color: #000}
		.datagrid table { border-collapse: collapse; text-align: left; width: 100%; padding: 100px;}
		.datagrid {font: normal 12px/150% Arial, Helvetica, sans-serif; background: #fff; overflow: hidden; border: 2px solid #006699; }
		.datagrid table td, .datagrid table th { padding: 3px 10px; }
		.datagrid table thead th {background-color:#006699; color:#FFFFFF; font-size: 15px; font-weight: bold; border-left: 1px solid #0070A8; }
		.datagrid table thead th:first-child { border: none; }
		.datagrid table tbody td { color: #00557F; border-left: 1px solid #E1EEF4;font-size: 12px;font-weight: normal; }
		.datagrid table tbody td:first-child { border-left: none; }
		.datagrid table tbody tr:last-child td { border-bottom: none; }";

// BUSCAMOS LOS USUARIO QUE LES TENGA QUE LLEGAR EL MENSAJE

$query = DBSelect ( utf8_decode ( $query ) );
for(; DBNext ( $query ) ;) {
	$USUARIO = utf8_encode ( DBCampo ( $query , "Usuario" ) );
	$TO = utf8_encode ( DBCampo ( $query , "Mail" ) );
	$NAME = utf8_encode ( DBCampo ( $query , "Name" ) );
	$OFICINAS = utf8_encode ( DBCampo ( $query , "Oficina" ) );
	//$OFICINAS = "('" . str_replace ( "," , "','" , $OFICINAS ) . "') ";
	
	// echo $NAME .' ' . $USUARIO .' ' . $TO .' ' . $OFICINAS."<br><br>";
	$enviar = false;
	$mensaje = "
	<html>
	<head>
		<meta charset='UTF-8'>
		<title>Solicitud de documentación</title>
		<style type='text/css'>$estilos2</style
	</head>
	<body>
		<p>Buenos días $NAME,</p>
		<p>A continuación se muestran los totales de fichajes de los usuarios de su/s departamento/s, las líneas en rojo destacan aquellos con un fichaje inferior al 95% o superior al 100% de sus horas de presencia:</p>
		<div class='datagrid'>
		<table>
			<thead>
				<TR>
					<TH>Usuario</TH>
					<TH>Departamento</TH>
					<TH>Horas Fichadas</TH>
					<TH>Horas Presencia</TH>
					<TH>% Fichado</TH>
				</TR>
			</thead>
			<tbody>
	";

	if($tipo == 'DIARIO'){
		$query2 = "SELECT * FROM [dbo].[COMPARATIVA_HORAS_FICHADAS_PRESENCIA] (GETDATE(),GETDATE(),'$OFICINAS') ORDER BY USUARIO ";
	}
	if($tipo == 'SEMANAL'){
		$query2 = "SELECT * FROM [dbo].[COMPARATIVA_HORAS_FICHADAS_PRESENCIA] (DATEADD(DAY,-5,GETDATE()),GETDATE(),'$OFICINAS') ORDER BY USUARIO ";
	}
	
	//die ($query2);
	$query2 = DBSelect ( utf8_decode ( $query2 ) );
	for(; DBNext ( $query2 ) ;) {
		$enviar = true;
				
		$USUARIONOMBRE		= utf8_encode ( DBCampo ( $query2 , "USUARIO" ) );
		$HORAS_PRESENCIA	= utf8_encode ( DBCampo ( $query2 , "HORAS_PRESENCIA" ) );
		$TIEMPO_FICHADO		= utf8_encode ( DBCampo ( $query2 , "TIEMPO_FICHADO" ) );
		$OFICINA			= utf8_encode ( DBCampo ( $query2 , "OFICINA" ) );
		$RENDIMIENTO		= utf8_encode ( DBCampo ( $query2 , "RENDIMIENTO" ) );
		
		$HORAS_PRESENCIA	= number_format($HORAS_PRESENCIA, 2);
		$TIEMPO_FICHADO		= number_format($TIEMPO_FICHADO, 2);
		$RENDIMIENTO		= number_format($RENDIMIENTO, 2);

		
		$class = '';
		if($RENDIMIENTO < 95 || $RENDIMIENTO >= 100){
			$class = " class='rojo' " ;
		}
		
		$mensaje .= "<tr>";
		$mensaje .= "  <td $class >$USUARIONOMBRE</td>";
		$mensaje .= "  <td $class align='center'>$OFICINA</td>";
		$mensaje .= "  <td $class align='center'>$TIEMPO_FICHADO</td>";
		$mensaje .= "  <td $class align='center'>$HORAS_PRESENCIA</td>";
		$mensaje .= "  <td $class align='center'>$RENDIMIENTO</td>";
		$mensaje .= "</tr>";
	}

	$mensaje .= "</tbody></table></div><br>";
	$mensaje .= "<small>(correo enviado de forma automática, por favor no responda a este mail)</small><BR><BR></body></html>";
	
	//echo $mensaje;$enviar = false;
	
	if ($enviar){
		
		// envio mediante phpmailer
		if ( $modo_de_pruebas ) {
			$TO = $correoAdjunto;
		}
		
		//$TO = $correoAdjunto;
		
		//echo "<font color='green'><b>$fecha_hora: RECORDATORIO A EVALUADORES DE TAREAS SIN APORTE JEFE. $NAME. $TO</b></font><br>";
		
		$mail = new PHPMailer ();
		$mail -> CharSet = "UTF-8";
		$mail -> IsSMTP (); // telling the class to use SMTP
		$mail -> SMTPDebug = 1; // enables SMTP debug information (for testing) 2 = errors and messages 1 = error messages only
		$mail -> SMTPAuth = false; // enable SMTP authentication
		$mail -> Host = $smtp_server; // sets the SMTP server
		$mail -> Port = $smtp_port; // set the SMTP port
		//$mail -> Username = $username; // SMTP account username
		//$mail -> Password = $password; // SMTP account password
		$mail -> SetFrom ( $FROM , 'SP-Berner Portal TAREAS' );
		$mail -> Subject = $asunto;
		$mail -> MsgHTML ( $mensaje );
		

		
		$mail -> AddAddress ( $TO , $NAME );
		
		if (  ! $mail -> Send () ) {
			echo "<br>$fecha_hora: Mailer Error a usuario: " . $mail -> ErrorInfo;
			echo "<br><span style='color: green;'>$fecha_hora: REINTENTANDO EN 10 SEG. PARA: $NAME, $TO </span>";
			sleep(10);
			if (  ! $mail -> Send () ) {
				echo "<br><span style='color: RED;'>$fecha_hora: Mailer Error a usuario: " . $mail -> ErrorInfo . "</span>";
			}else{
				echo "<br><span style='color: green;'>$fecha_hora: $asunto para: $NAME, $TO </span>";
			}
		} else {
			echo "<br>$fecha_hora: $asunto para: $NAME, $TO";
		}
		
	}
}


// ********************************************************************************************************CODIGO
  
// ****************************************************FINAL
DBFree ( $query );
DBClose ();
// ****************************************************FINAL

?>