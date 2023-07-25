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
	
	//imap//
	//$inbox = imap_open ( $hostname_recibir , $username_recibir , $password_recibir ) or die ( 'Cannot connect: ' . imap_last_error () );
	
	$con = '{outlook.office365.com:993/imap/ssl/novalidate-cert}INBOX';
	$usernamedominio= 'soporte.ti@sp-berner.com';
	$password='soporte70';
		
	//office 365//
	$inbox = imap_open( $con, $usernamedominio, $password, NULL, 1, array('DISABLE_AUTHENTICATOR' => 'GSSAPI'))or die ( 'No se ha podido conectar: ' . imap_last_error () );
	
	$emails = imap_search ( $inbox , 'ALL' );
	
	if ( $emails ) {
		$output = '';
		
		rsort ( $emails );
		
		foreach ( $emails as $email_number ) {
			$header = imap_headerinfo ( $inbox , $email_number );
				
			$from = $header -> from [ 0 ] -> mailbox . "@" . $header -> from [ 0 ] -> host;
			$toaddress = $header -> toaddress;
			$replyto = $header -> reply_to [ 0 ] -> mailbox . "@" . $header -> reply_to [ 0 ] -> host;
			$emailto = $header -> to [ 0 ] -> mailbox . "@" . $header -> to [ 0 ] -> host;
			$datetime = date ( "Ymd H:i:s" , $header -> udate );
			$subject = $header -> subject;
			$fromuser = $header -> from [0] -> mailbox;
			
			$toaddress = str_replace ( '"' , '' , $toaddress );

			$message = trim ( utf8_encode ( quoted_printable_decode ( imap_fetchbody ( $inbox , $email_number , 1 ) ) ) );
			$message = str_replace ( "'" , "" , $message );
			
			$messageid = $header -> message_id; 	//15/01/2019
			$toaddress = imap_utf8 ( $toaddress );
			$from = imap_utf8 ( $from );
			//$subject = imap_utf8 ( $subject ); 
			$fromuser = imap_utf8 ($fromuser);
			
			$sqlbuscarnombre ="SELECT COUNT(*) as resultado FROM [GestionIDM].[dbo].[LDAP] Where Mail ='".$from."'";
			$resultadoquery = DBSelect (utf8_decode( $sqlbuscarnombre ));
			$resultado=utf8_encode(DBCampo($resultadoquery, "resultado" ));
			
			//$message = imap_utf8($message); 		//JUAN ANTONIO ABELLAN 30/11/2018
			// Comprobamos primero si el remitente es del dominio. resultado == 1
			
				if(($resultado == 1)){
					if(array_key_exists('to', $header)){
						//Comprobamos destinatarios
						$comprobaremailsto = count($header -> to);
						
						if($comprobaremailsto > 1){
							//Varios destinatarios//
							echo " ENVIADO A DESTINATARIOS: ";
							echo $comprobaremailsto;
							echo "<br/>";
							enviarmail ( $from , "Error en correo." , "Su tarea no ha sido creada porque puso a mas de un destinatario, escribe un asunto y un mensaje para poder crear la tarea con unico destinatario soporte.ti@sp-berner.com.",  0 );
						}else{
							//Como solo hay 1 destinatario//
							$comprobaremailsto = 1;
						}
						
					}
					
					/*
					COMPROBACION  DE COPIAS 
					 if(array_key_exists('cc', $header)){
						//Comprobamos copias ocultas
						echo "COPIA OCULTAS: ";
						$comprobaremailsocultas = count($header -> cc);
						echo $comprobaremailsocultas;
						echo "<br/>";
						enviarmail ( $from , "Error en correo." , "Su tarea no ha sido creada porque puso de copia a otro destinatario, escribe un asunto y un mensaje para poder crear la tarea con unico destinatario soporte.ti@sp-berner.com y sin copia a nadie.", 0);
					}else{
						//No hay copias ocultas
						$comprobaremailsocultas = 0;
					}*/
					
					//Comprobamos si tiene copias ocultas o varios destinatarios
					//if($comprobaremailsto == 1 && ($comprobaremailsocultas == 0)){
					if($comprobaremailsto == 1 ){
						//No tiene copias ocultas y solo es un destinatario
						if ( $emailto == 'soporte.ti@sp-berner.com' ) {
							$enviar = true;
							
							$msjCorreoAutomaticoInfo ='<br /><span style:"color:red"><b> Este correo se envia de forma automatica, por favor no responda a este mensaje. </b></span>';
							
							//	COMPROBAMOS EL MENSAJE, ASUNTO	//
							if (str_replace(" ", "", $subject)=='' ){
								// Asunto vacio //
								enviarmail ( $from , "Error en correo." , "Su tarea no ha sido creada porque no contenia un asunto, escribe un asunto para poder crear la tarea.".$msjCorreoAutomaticoInfo, 0);
								echo "Error el correo no contiene asunto";
								echo "<br/>";
								$enviar = false;
							}
							
							if(strlen($subject) > 140){
								// Asunto demasiado largo //
								enviarmail ( $from , "Error en correo." , "Su tarea no ha sido creada porque su asunto tiene mas de 140 caracteres. ["+$subject+"]".$msjCorreoAutomaticoInfo , 0);
								echo "Error el correo, tiene demasiados caracteres en el asunto.(<140) <br /> Asunto("+strlen($subject)+"): <br />"+$subject;
								echo "<br/>";
								$enviar = false;
							}
							
							if ( str_replace(" ", "", $message)=='' && $enviar){
								// Mensaje vacio //
								enviarmail ( $from , "Error en correo." , "Su tarea no ha sido creada porque no contenia un mensaje con la descripción de la tarea, escribe un asunto para poder crear la tarea." .$message .$msjCorreoAutomaticoInfo, 0 );
								echo "Error el correo no contiene mensaje.";
								echo "<br/>";
								$enviar = false;
							}
							
							if ($enviar == false){
								//ELIMINAMOS EL CORREO SI NO PASA LAS COMPROBACIONES
								imap_delete ( $inbox , $email_number );
							}
							
							if ($enviar) {
								// COMPROBAMOS LA PRIORIDAD DEL MENSAJE //
								$g = imap_fetchheader( $inbox , $email_number );
								
								$cadena_buscada = 'high';
								$posicion_coincidencia = strrpos($g, $cadena_buscada);
								$prioridad = substr($g, $posicion_coincidencia , 4);
								
								if ($prioridad == "high"){
									echo "<br/>Importancia: ".$prioridad;
								}else{
									$prioridad = "normal";
									echo "<br/>Importancia: ".$prioridad;
								}
								
								////////////////////////////////////////////////
								if($emailto == "soporte.ti@sp-berner.com"){ $dep ='TI';}
								
								$consultardep = "SELECT TOP 1 [Descripcion] FROM [GestionPDT].[dbo].[Tipos] WHERE Tipo = 4 AND Oficina = '".$dep."' ";
								
								if($prioridad == "high"){
									$consultardep = $consultardep."AND PrioridadUrgente = 1"; 
								}else{
									$consultardep = $consultardep."AND Predeterminado = 1";
								}

								$consultardep = $consultardep." ORDER BY Numero";
	
								$consultardep = DBSelect (utf8_decode( $consultardep ));
								$resprio=DBCampo($consultardep, ("Descripcion"));
															
								////////////////////////////////////////////////
								$mensajeid=substr($messageid, 1, strpos($messageid, '@')-1);
								
								$solicitadoquery="SELECT [Name] as nombre FROM [GestionIDM].[dbo].[LDAP] Where Mail ='".$from."'";
								$consultasolicitado= DBSelect (utf8_decode( $solicitadoquery ));
								$nombresolicitante=DBCampo($consultasolicitado, ("nombre"));
								
								//mb_decode_mimeheader($subject)//
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
								$message = preg_replace('<hr([\w\W]+?)/>','', $message, -1);
								$message = str_replace ( "This email was scanned by Bitdefender" , " " , $message );
								//$message = str_replace ( "o:p" , " " , $message );
								//$message = preg_replace('<img([\w\W]+?)/>','[IMAGEN ADJUNTADA]', $message, -1);	
								$message = preg_replace('<img([\w\W]+?)/>','', $message, -1);	
								
								/*
								 $message = str_replace ( "<![if !vml]><![endif]>" , " " , $message );
								*/
								
								//$message = preg_replace('(?s)<!--\\[if(.*?)\\[endif\\] *-->','', $message, -1);
								//$message = preg_replace('/<!--[\s\S]*?-->/g','', $message, -1);					
								
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
											,'".$subjectinsertar."' 
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
								
								//die($sqlinsertar);
								DBSelect ( $sqlinsertar );
								
								$headers = imap_fetchheader($inbox, $email_number, FT_PREFETCHTEXT);
								$body = imap_body($inbox, $email_number);
								file_put_contents('CorreosBOT/'.$mensajeid.'.eml', $headers . "\n" . $body);
																								
								enviarmail ( $from , "Se ha creado la solicitud para su valoración." , "Su solicitud de tarea con asunto : ". imap_utf8 ( $subject ) ."<br/><br/> Ha sido creada en el sistema para su valoración <br/><br/> <br/> <br/> <br/> <u><b> Este correo se envia de forma automatica, por favor no responda a este mensaje. <br/> La creación de la solicitud es automatica, pero la creación de tareas esta bajo valoración.</b></u>", $mensajeid );
								
								echo "Perfecto, creado con exito.";
								echo "<br/>";
								
							}
						}
					}
				}
					
					imap_delete ( $inbox , $email_number );
					imap_expunge($inbox);
					$foo = imap_errors();
					imap_close($inbox);
		}
	}else{
		echo 'No hay correos.';
		imap_close($inbox);
	}
	
	
	function enviarmail($from, $asunto, $mensaje, $correoid){
		echo "PRUEBA DE CORREO";
		echo "<br />";
		echo "Correo de entrada: ".$from;
		echo "<br />";
		echo "Asunto: ".$asunto;
		echo "<br />";
		echo "mensaje: ".$mensaje;
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
		
	}
	
	
?>