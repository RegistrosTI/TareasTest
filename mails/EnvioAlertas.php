<?php
// UTF8 ñáç
/**
 * ***********************************************************
 * AUTOR: Alberto Ruiz
 * FECHA: 25/05/2020
 * DESC: ENVIO DE ALERTAS POR CORREO
 * ***********************************************************
 */

// ****************************************************INCLUDES NECESARIOS
if ( 1 == 1 ) {
	$GLOBALS [ "cfg_HostDB" ] = 'SQL2016';
	//$GLOBALS [ "cfg_HostDB" ] = 'SQL2016-EXPRESS\SQL2016EXPRESS';
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
$ASUNTO = "";
$mensaje = "";
$ASIGNADOA = "";
$TO = "";
$fecha_hora = date ( "d-m-Y H:i:s" );
$queryProcesados = '';

// BUSCAMOS LOS USUARIO QUE LES TENGA QUE LLEGAR EL MENSAJE
$query = "
SELECT 
	A.Id
	,(select Name from GestionIDM.dbo.LDAP where sAMAccountName = A.UsuarioAlta) UsuarioAlta
	,D.Name UsuarioNombre
	,D.Mail
	,A.Tipo
	,A.Tarea
	,T.[Título] AS Titulo
	,T.Solicitado
	,ISNULL(CONVERT(VARCHAR(MAX),T.[Descripción]),'(En el momento de este correo la tarea no dispone de observaciones)') AS Descripcion
	,T.Oficina
FROM GestionPDT.dbo.Alertas A
INNER JOIN GestionIDM.DBO.LDAP D ON A.USUARIO = D.sAMAccountName
INNER JOIN GestionPDT.dbo.[Tareas y Proyectos] AS T ON a.Tarea = T.Id
WHERE Correo = 'SI' and enviado = 'NO'
";

$query = DBSelect ( utf8_decode ( $query ) );
for(; DBNext ( $query ) ;) {
	
	$ID = utf8_encode ( DBCampo ( $query , "Id" ) );
	$USUARIOALTA = utf8_encode ( DBCampo ( $query , "UsuarioAlta" ) );
	$USUARIONOMBRE = utf8_encode ( DBCampo ( $query , "UsuarioNombre" ) );
	$TIPO = utf8_encode ( DBCampo ( $query , "Tipo" ) );
	$TAREA = utf8_encode ( DBCampo ( $query , "Tarea" ) );
	$TO = utf8_encode ( DBCampo ( $query , "Mail" ) );
	
	$SOLICITADO = utf8_encode ( DBCampo ( $query , "Solicitado" ) );
	$TITULO = utf8_encode ( DBCampo ( $query , "Titulo" ) );
	$OFICINA = strip_tags(utf8_encode ( DBCampo ( $query , "Oficina" ) ) ) ;
	
	//++ descripcion
	$DESCRIPCION =  DBCampo ( $query , "Descripcion" )   ;
	
	if ((strpos($DESCRIPCION, 'Content-Type:') !== false) || (strpos($DESCRIPCION, 'PC9vOnA') !== false)) {
		$DESCRIPCION = '';
	}else{
		$DESCRIPCION = /*strip_tags*/(html_entity_decode(utf8_encode ( $DESCRIPCION ) ) ) ;
		$DESCRIPCION = "<p><b>Observaciones:</b><br>$DESCRIPCION";
	}
	// -- descripcion
	
	$queryProcesados .= " UPDATE Alertas SET Enviado = 'SI' WHERE Id = $ID; ";
	
	switch ($TIPO){
		case 'alerta_nueva_asignacion':
			$ASUNTO = "TAREA#$TAREA@$OFICINA NUEVA ASIGNACIÓN DE TAREA";
			$CUERPO_MENSAJE ="Se le comunica que el usuario <b>$USUARIOALTA</b> le ha asignado una nueva tarea en el portal de tareas, por favor diríjase al portal para analizar la tarea siguiente:";
			break;
		case 'alerta_fecha_objetivo':
			$ASUNTO = "TAREA#$TAREA@$OFICINA QUE VENCE O HA VENCIDO EN BREVE";
			$CUERPO_MENSAJE ="Se le comunica que la tarea que se indica a continuación se acerca a su fecha de vencimiento o ha vencido recientemente, por favor diríjase al portal para analizar la tarea:";
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
		<p>Estimado/a $USUARIONOMBRE ,</p>
		<p>$CUERPO_MENSAJE</p>
		<p><b>Tarea:</b> $TAREA</p>
		<p><b>Título:</b> $TITULO</p>
		$DESCRIPCION
		<br><br>
		<p><small>(correo enviado de forma automática, si lo desea puede darse de baja de este correo desde el menú de configuración del portal de tareas)</small></p><BR><BR>
	</body>
	</html>	
	";

	
	if ( $modo_de_pruebas ) {
		$TO = $correoAdjunto;
	}
	
	if(1==1){
		if ( iniciaWS () ) {
			$consultaWS = new consultaWS ( 'SP Berner' , 'InterfaceWS' );
			
			// Pasamos las variables para la ejecucion
			$params = array (
					'vRemitente' => $FROM,
					'vDestinatario' => $TO,
					'vAsunto' => $ASUNTO,
					'vCuerpo' => $mensaje,
					'vCC' => '',
					'vCO' => '',
			);
			
			// Ejecutamos la CodeUnit
			$result = $consultaWS -> MandarMensaje ( $params );
			$consultaWS -> ejecucionErronea ( $result );
			$respuesta = $result -> return_value;
			finalizaWS ();
	
			echo "<br>$fecha_hora: $ASUNTO de $USUARIONOMBRE a: $TO";
		}else{
			echo "<br><span style='color: RED;'>$fecha_hora: Error WS. $ASUNTO de $USUARIONOMBRE a: $TO</span>";
		}
	}
	
}

//echo $queryProcesados;

DBSelect ( utf8_decode ( $queryProcesados ) );

// ********************************************************************************************************CODIGO
  
// ****************************************************FINAL
DBFree ( $query );
DBClose ();
// ****************************************************FINAL


?>