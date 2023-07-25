<?php
// UTF8 ñáç
/**
 * ***********************************************************
 * AUTOR: Alberto Ruiz
 * FECHA: 22/09/2017
 * DESC: ENVÍA UN CORREO A RESPONSABLES CON LOS MINUTOS FICHADOS DE LOS USUARIOS DE SUS OFICINAS
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
$query = "
	SELECT 
		USU.Usuario AS Usuario
		,DOM.Mail 
		,USU.Oficina
		,DOM.Name	AS Name
	FROM Configuracion_Usuarios AS USU
	LEFT JOIN dbo.TF_DATOS_DOMINIO() AS DOM 
		ON USU.Usuario = DOM.sAMAccountName
	WHERE USU.Baja = 0 AND USU.InfMinutosFichados = 1
";

$query = DBSelect ( utf8_decode ( $query ) );
for(; DBNext ( $query ) ;) {
	$USUARIO = utf8_encode ( DBCampo ( $query , "Usuario" ) );
	$TO = utf8_encode ( DBCampo ( $query , "Mail" ) );
	$NAME = utf8_encode ( DBCampo ( $query , "Name" ) );
	$OFICINAS = utf8_encode ( DBCampo ( $query , "Oficina" ) );
	$OFICINAS = "('" . str_replace ( "," , "','" , $OFICINAS ) . "') ";
	
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
		<p>A continuación se muestran los totales de fichajes diarios de los usuarios, las líneas en rojo destacan aquellos con un fichaje inferior al 94% de su jornada teórica:</p>
		<div class='datagrid'>
		<table>
			<thead>
				<TR>
					<TH>Usuario</TH>
					<TH>Departamento</TH>
					<TH>Jornada Teórica</TH>
					<TH>Tiempo Fichado / Asignado</TH>
					<TH>% Fichado</TH>
				</TR>
			</thead>
			<tbody>
	";

	
	$query2 = "
		SELECT 
			USU.Usuario
			,DOM.Name
			,ISNULL( (SELECT VALOR FROM Configuracion WHERE Parametro = 'mi_jornada_teorica' AND Usuario = USU.Usuario) ,540) AS MinutosJornada
			,USU.Oficina
			,ISNULL( (SELECT SUM(Minutos) 
						FROM Horas AS HORAS
						WHERE HORAS.Usuario = USU.Usuario 
							AND Inicio >= CONVERT(date, GETDATE()) 
							AND Inicio <= CONVERT(date, GETDATE()+1)),0) AS MinutosFichados 
		FROM Configuracion_Usuarios AS USU 
		LEFT JOIN dbo.TF_DATOS_DOMINIO() AS DOM ON USU.Usuario = DOM.sAMAccountName
		WHERE USU.Baja = 0 AND USU.Oficina IN $OFICINAS
	";
	//die ($query2);
	$query2 = DBSelect ( utf8_decode ( $query2 ) );
	for(; DBNext ( $query2 ) ;) {
		$enviar = true;
				
		$USUARIONOMBRE   = utf8_encode ( DBCampo ( $query2 , "Name" ) );
		$MINUTOSJORNADA	 = utf8_encode ( DBCampo ( $query2 , "MinutosJornada" ) );
		$MINUTOSFICHADOS = utf8_encode ( DBCampo ( $query2 , "MinutosFichados" ) );
		$OFICINA         = utf8_encode ( DBCampo ( $query2 , "Oficina" ) );
		
		if(!is_numeric ($MINUTOSJORNADA) ){
			$MINUTOSJORNADA = 540;
		}
		
		$Desviacion = ($MINUTOSFICHADOS * 100) / $MINUTOSJORNADA;
		$Desviacion = round($Desviacion,2);
		
		$class = '';
		if($Desviacion < 94){
			$class = " class='rojo' " ;
		}
		
		$mensaje .= "<tr>";
		$mensaje .= "  <td $class >$USUARIONOMBRE</td>";
		$mensaje .= "  <td $class >$OFICINA</td>";
		$mensaje .= "  <td $class >$MINUTOSJORNADA</td>";
		$mensaje .= "  <td $class >$MINUTOSFICHADOS</td>";
		$mensaje .= "  <td $class >$Desviacion</td>";
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