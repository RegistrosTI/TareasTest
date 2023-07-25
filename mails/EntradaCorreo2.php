<?php
	
	ini_set('mssql.charset', 'UTF-8');

	include "../../soporte/DB.php";
	require "../conf/config.php";
	include "../../soporte/funcionesgenerales.php";
	require "../conf/mail.php";

	//$GLOBALS["cfg_HostDB"]  = 'SQL2016-Express\SQL2016Express';
	$GLOBALS["cfg_HostDB"]  = 'SQL2016';
	$GLOBALS["cfg_UserDB"]  = 'gestionti';
	$GLOBALS["cfg_PassDB"]  = 'gestionti';
	$GLOBALS["cfg_DataBase"] = 'GestionPDT';
	DBConectar ( $GLOBALS [ "cfg_DataBase" ] );	
	

	$fecha_hora = date ( "d-m-Y H:i:s" );

	$con = '{outlook.office365.com:993/imap/ssl/novalidate-cert}INBOX';
	//$con = '{outlook.office365.com:993/imap/ssl/novalidate-cert}prueba'; //CARPETA PRUEBAS//
	$usernamedominio= 'soporte.ti@sp-berner.com';
	$password='soporte70';

	$inbox = imap_open($con, $usernamedominio, $password, NULL, 1, array('DISABLE_AUTHENTICATOR' => 'GSSAPI'))or die ( 'No se ha podido conectar: ' . imap_last_error());
	$emails = imap_search ($inbox , 'ALL');
	
	echo '<br /><hr /><br />';
	echo 'PASADA -> ['.$fecha_hora.']';
	echo '<br />';

	if ($emails) {
		rsort ($emails);	
		
		foreach ( $emails as $email_number ) {
		
			$header = imap_headerinfo($inbox , $email_number);			
			

			$messageid = $header -> message_id; 	//15/01/2019
			$datetime = date ( "Ymd H:i:s" , $header -> udate );
			
			//COMPROBACION//
			
			//$usuarioEnDominio = comprobarUsuarioDominio($header);
			
			$UsuarioPermitido = comprobacionDominiosPermitidos($header);					
			
			if($UsuarioPermitido == true){
				
				$comprobarDestinatarios = false;
				$comprobarCorreo = false;
				
				$comprobarDestinatarios = comprobacionDestinatarios($header);
				//$comprobarCopiasOcultas = comprobacionCopiasOcultas($header); //SE PERMITE COPIAS/COPIAS CC
				$comprobarCorreo = comprobarcionCorreo($header,$inbox,$email_number);
				
				
				if(($comprobarDestinatarios == true) && ($comprobarCorreo == true)){
					montarSQLCorreo($inbox,$email_number);	
					
					moverCorreoCarpetaAceptado($inbox,$email_number);
				}else{
					borrarCorreo($inbox,$email_number);
										
				}
				
			}			
		}
		
		imap_expunge($inbox);
		imap_close($inbox);
		
	}else{
		echo '<br /><hr /><br />';
		echo 'No hay correos. ['.$fecha_hora.']';
		echo '<hr />';
		imap_close($inbox);
	}


	//FUNCIONES//
	
	function comprobacionCopiasOcultas($DestinatariosOcultos){
		if(property_exists($DestinatariosOcultos, 'cc')){
			$comprobaremailsocultas = count($DestinatariosOcultos -> cc);
			
			if ($comprobaremailsocultas <> 0 ){
				return false;
			}else{
				return true;
			}
		}
	}


	function comprobacionDestinatarios($Destinatarios){
		if(property_exists($Destinatarios,'to')){
			$comprobaremailsto = count($Destinatarios -> to);
			$solicitantecorreo = $Destinatarios -> from [ 0 ] -> mailbox . "@" . $Destinatarios -> from [ 0 ] -> host;
			
			if($comprobaremailsto > 1){
				$msjCorreoAutomaticoInfo ='<br /><span style:"color:red"><b>Este correo se envía de forma automática, por favor no responda a este mensaje.</b></span>';
				
				enviarmail($solicitantecorreo , "Error en correo." , "Su tarea no ha sido creada porque ha incluido a varias personas en destinatarios.".$msjCorreoAutomaticoInfo, 0);
				return false;
				
			}else{
				
				return true;
			}
			
		}else{
			return false;
		}
	}


	function comprobarUsuarioDominio($correo){
		
		//Comprobacion de que el usuario esta en el dominio.//
		
		$Usuario = $correo -> from [ 0 ] -> mailbox . "@" . $correo -> from [ 0 ] -> host;
		$Usuario = imap_utf8 ($Usuario); //CORREO PETICION	
		
		die($Usuario);
		$sqlbuscarnombre ="SELECT COUNT(*) as resultado FROM [GestionIDM].[dbo].[LDAP] Where Mail ='".$Usuario."'";
		$resultadoquery = DBSelect (utf8_decode( $sqlbuscarnombre ));
		$resultado=utf8_encode(DBCampo($resultadoquery, "resultado" ));
		
		if ($resultado == 1){
			return true;
		}
			return false;
	}


	function comprobarcionCorreo($correo,$inbox,$email_number){
		
		//USUARIO PETICION//
		$from = $correo -> from [ 0 ] -> mailbox . "@" . $correo -> from [ 0 ] -> host;
		$from = imap_utf8 ($from); //CORREO PETICION
		$fromuser = $correo -> from [0] -> mailbox;
		$fromuser = imap_utf8 ($fromuser); //NOMBRE CORREO PETICION

		//CORREO DESTINO (BOT - Se deja preparado para mas posibles cuentas de bots)//
		$emailto = $correo -> to [ 0 ] -> mailbox . "@" . $correo -> to [ 0 ] -> host;	//CORREO BOT		
		$toaddress = $correo -> toaddress;
		$toaddress = imap_utf8 ( $toaddress ); //USUARIO BOT
		
		//ASUNTO DEL CORREO//
		$subject = $correo -> subject;
		$subject = imap_utf8 ($subject); //¿Haria falta? PRUEBA//
		
		//MENSAJE CORREO//
		$message = trim ( utf8_encode ( quoted_printable_decode (imap_fetchbody ( $inbox , $email_number , 1) ) ) );
		$message = str_replace ( "'" , "" , $message );
		/*
		echo '<p><b>Usuario peticion:</b> '.$fromuser.'</p>';
		echo '<p><b>Correo peticion:</b> '.$from.'</p>';
		echo '<p><b>Correo bot final:</b> '.$toaddress.'</p>';
		echo '<p><b>Para:</b> '.$emailto.'</p>';
		echo '<p><b>Asunto:</b> '.$subject.'</p>';
		*/
		//COMPROBACION ASUNTO DEL MENSAJE//
		$error = 0;
		$enviar = true;
		$msjCorreoAutomaticoInfo ='<br /><span style:"color:red"><b>Este correo se envía de forma automática, por favor no responda a este mensaje.</b></span>';
		
		//asunto vacio//
		if (str_replace(" ", "", $subject)=='' ){
			
			$error = 1;
			echo "Error el correo no contiene asunto";
			echo "<br/>";
			
			enviarmail ($from , "Error en correo." , "Su tarea no ha sido creada porque no contenía un asunto, escribe un asunto para poder crear la tarea.".$msjCorreoAutomaticoInfo, 0);
			$enviar = false;
		}
		
		//Asunto demasiado largo//
		if(strlen($subject) > 140){
			
			$error = 2;
			echo "El asunto tiene demasiados caracteres en el asunto.(<140) <br />";
			echo "<br/>";
			
			enviarmail ($from , "Error en correo." , "Su tarea no ha sido creada porque su asunto tiene más de 140 caracteres. ["+$subject+"]".$msjCorreoAutomaticoInfo , 0);
			$enviar = false;
		}
		
		
		//Mensaje vacio//
		if (str_replace(" ", "", $message)==''){
			
			$error = 3;
			echo "Error el correo no contiene mensaje.";
			echo "<br/>";

			enviarmail ($from , "Error en correo." , "Su tarea no ha sido creada porque no contenía un mensaje con la descripción de la tarea, escribe un asunto para poder crear la tarea." .$message .$msjCorreoAutomaticoInfo, 0);

			$enviar = false;
		}
		
		if($enviar == false){
			borrarCorreo($inbox,$email_number);
		} 
		
		return $enviar;
	}

	function borrarCorreo($inbox, $email_number){
		imap_delete ($inbox , $email_number);
	}

	function enviarmail($from, $asunto, $mensaje, $correoid){
		echo "Correo de entrada: ".$from;
		echo "<br />";
		echo "Asunto: ".$asunto;
		echo "<br />";
		
		// incluimos configuracion de correo
		require "../conf/mail.php";
		$fecha_hora = date ( "d-m-Y H:i:s" );
		
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
		// $mail -> AddReplyTo ( $mailResponsableSES , 'SP-Berner Portal TAREAS' );
		$mail -> Subject = $asunto;
		$mail -> MsgHTML ( $mensaje );
		
		$mail -> AddAddress ( $from );
		
		if($correoid != 0){
			$file = 'CorreosBOT/'.$correoid.'.eml';
			$mail->AddAttachment( $file, $correoid.'.eml' );
		}
		
		if (  ! $mail -> Send () ) {
			echo "<br>$fecha_hora: Mailer Error a usuario: " . $mail -> ErrorInfo;
		} else {
			echo "<br>$fecha_hora: $asunto para: $from <br>";
		}
		echo "<br />";
		echo "<hr />";
	}


	function montarSQLCorreo($inbox,$email_number){
		
		$g = imap_fetchheader( $inbox , $email_number );	
		$cadena_buscada = 'high';
		$posicion_coincidencia = strrpos($g, $cadena_buscada);
		$prioridad = substr($g, $posicion_coincidencia , 4);
		
		
		$correo = imap_headerinfo($inbox , $email_number);	
		
		//USUARIO PETICION//
		$from = $correo -> from [ 0 ] -> mailbox . "@" . $correo -> from [ 0 ] -> host;
		$from = imap_utf8 ($from); //CORREO PETICION
		$fromuser = $correo -> from [0] -> mailbox;
		$fromuser = imap_utf8 ($fromuser); //NOMBRE CORREO PETICION

		//CORREO DESTINO (BOT - Se deja preparado para mas posibles cuentas de bots)//
		$emailto = $correo -> to [ 0 ] -> mailbox . "@" . $correo -> to [ 0 ] -> host;	//CORREO BOT		
		$toaddress = $correo -> toaddress;
		$toaddress = imap_utf8 ( $toaddress ); //USUARIO BOT
		
		//ASUNTO DEL CORREO//
		$subject = $correo -> subject;
		
		$messageid = $correo -> message_id; 	//15/01/2019
		$datetime = date ( "Ymd H:i:s" , $correo -> udate );
		
		if($emailto == "soporte.ti@sp-berner.com"){ $dep ='TI';}
		$consultardep = "SELECT TOP 1 [Descripcion] FROM [GestionPDT].[dbo].[Tipos] WHERE Tipo = 4 AND Oficina = '".$dep."'";
		
		if($prioridad == "high"){
			$consultardep = $consultardep."AND PrioridadUrgente = 1"; 
		}else{
			$consultardep = $consultardep."AND Predeterminado = 1";
		}
		
		$consultardep = $consultardep." ORDER BY Numero";
		$consultardep = DBSelect (utf8_decode( $consultardep ));
		$resprio = DBCampo($consultardep, ("Descripcion"));
		
		$mensajeid = substr($messageid, 1, strpos($messageid, '@')-1);
		
		$solicitadoquery="SELECT [Name] as nombre FROM [GestionIDM].[dbo].[LDAP] Where Mail ='".$from."'";
		$consultasolicitado= DBSelect (utf8_decode( $solicitadoquery ));
		$nombresolicitante=DBCampo($consultasolicitado, ("nombre"));
		
		$subjectinsertar = mb_decode_mimeheader($subject);
		$subjectinsertar = str_replace ( "_" , " " , $subjectinsertar );
		$subjectinsertar = str_replace ( "'" , " " , $subjectinsertar );		
		
		$codificacion =  imap_fetchstructure($inbox, $email_number);
		//if ($codificacion -> encoding==0){ $message = imap_base64($message);}
		//if ($codificacion -> encoding==1){ $message = quoted_printable_decode(imap_8bit($message));}
		//if ($codificacion -> encoding==2){ $message = imap_binary($message);}
		if ($codificacion -> encoding==3 ){ $message = imap_base64($message);}
		//if ($codificacion -> encoding== 4){ $message = quoted_printable_decode($message);};
		
		// FILTRO DEL MENSAJE (29/03/2019)//
		$message = trim ( utf8_encode ( quoted_printable_decode ( imap_fetchbody ( $inbox , $email_number , 1 ) ) ) );
		$message = str_replace ( "'" , "" , $message );
		$message = preg_replace('<hr([\w\W]+?)/>','', $message, -1);
		$message = str_replace ( "This email was scanned by Bitdefender" , " " , $message );	
		$message = preg_replace('<img([\w\W]+?)/>','', $message, -1);	
						

		$message = limpiaTextoConHTML($message);
		
		if ((strpos($message, 'Content-Type:') !== false) || (strpos($message, 'PC9vOnA') !== false)) {
			$message = '';
		}
		
		$sqlinsertar = "INSERT INTO [Entrada_Correo]( 
									[Solicitado]
									,[SolicitadoEmail]
									,[Asunto]
									,[FechaSolicitud]
									,[MensajeCorreo]
									,[Estado]
									,[Asignado]
									,[Prioridad]
									,[Oficina]
									,[MotivoRechazoOAceptacion]
									,[Tipo]
									,[Usuario]
									,[Horas]
									,[Catalogacion]
									,[HorasObligadas]
									,[Criticidad]
									,[RutaCorreo]
									,[NoPlanificado]
									,[FechaObjetivo]
									,[AreaSolicitante]
									,[Planta]
									,[IdTarea]
									)VALUES( 
									'".$nombresolicitante."'
									,'".$from."' 
									,'".utf8_decode($subjectinsertar)."' 
									,'".$datetime."' 
									,'".utf8_decode($message)."'
									,'Pendiente'
									,''
									,'".$resprio."'
									,'".$dep."'
									,''
									,''
									,''
									,0
									,''
									,''
									,''
									,'".$mensajeid."' 
									,''
									,''
									,(SELECT TOP 1 [Descripcion] FROM [GestionPDT].[dbo].[Tipos] WHERE Tipo = 14 AND Predeterminado = 1 AND Oficina = '$dep')
									,(SELECT TOP 1 [Descripcion] FROM [GestionPDT].[dbo].[Tipos] WHERE Tipo = 15 AND Predeterminado = 1 AND Oficina = '$dep')
									,0
									)";
						
		
		DBSelect ( $sqlinsertar );
		
		$headers = imap_fetchheader($inbox, $email_number, FT_PREFETCHTEXT);
		$body = imap_body($inbox, $email_number);
		file_put_contents('CorreosBOT/'.$mensajeid.'.eml', $headers . "\n" . $body);

		enviarmail ( $from , "Se ha creado la solicitud para su valoración." , "Su solicitud de tarea con asunto : ". imap_utf8 ( $subject ) ."<br/><br/> Ha sido creada en el sistema para su valoración <br/><br/> <br/> <br/> <br/> <u><b> Este correo se envía de forma automática, por favor no responda a este mensaje. <br/> La creación de la solicitud es automática, pero la creación de tareas está bajo valoración.</b></u>", $mensajeid );
				
		
		if ($prioridad == "high"){
			echo "<br/>Importancia: ".$prioridad;
		}else{
			$prioridad = "normal";
			echo "<br/>Importancia: ".$prioridad;
		}
			
		
	}


	function moverCorreoCarpetaAceptado($inbox, $email_number){
		
		//movemos el correo a la carpeta aceptados//
		imap_mail_copy($inbox, $email_number ,'CorreosAceptados');
		
		// borramos el correo//
		borrarCorreo($inbox,$email_number);
	}


	function comprobacionDominiosPermitidos($correo){
		$dominio = $correo -> from [ 0 ] -> host;	
			
		if(($dominio == 'sp-berner.com')||($dominio == 'miklimpieza.com') || ($dominio == 'sp-bramli.com')){
			return true;
		}else{
			return false;
		}
		
	}



?>