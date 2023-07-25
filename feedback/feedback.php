<?php
if ( isset ( $_POST [ 'feedback_feedbacks' ] ) ) {
	feedback_feedbacks ();
	die ();
}
if ( isset ( $_POST [ 'feedback_send' ] ) ) {
	feedback_send ();
	die ();
}
function feedback_send() {
	require_once ( '../phpmailer/PHPMailer/class.phpmailer.php' );
	include_once "../conf/config_filtros.php";
	include_once "../php/funciones.php";
	include_once "../conf/config.php";
	include_once "../conf/config_" . curPageName () . ".php";
	include_once "../../soporte/DB.php";
	include_once "../../soporte/funcionesgenerales.php";

	$tipo_error = 0;
	$correos = array ();
	$colas = array ();
	$contador_correos = 0;
	$fechas_historicas = '';
	$tarea = $_POST [ 'tarea' ];
	$contador = 0;
	$usuario = $_POST [ 'usuario' ];
	$feedback_message = $_POST [ 'feedback_message' ];
	$To_Correcto = false;
	$TO = '';
	$COPIAS = '';
	$FROM = '';
	$FROM_NAME = '';
	$From_Correcto = false;
	$SQL_NOMBRES = '';
	$new_mail_B = ''; // NI SE USA...
	$correos_extras = $_POST [ 'correos_extras' ];
	$crear_respuestas = '';
	$marcar_EnviadoMail = '';

	DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

	// ++ Alberto 28719, Si existen correos extras los añadimos al array de $correos
	if ( $correos_extras != '' ) {

		$array_correos_extras = explode ( "|" , $correos_extras );
		foreach ( $array_correos_extras as $array_correo_extra ) {
			if ( $array_correo_extra != '' ) {
				$SQL_NOMBRES = $SQL_NOMBRES . ",'$array_correo_extra'";
			}
		}
		// var_dump($expression);
		$SQL_NOMBRES = trim ( $SQL_NOMBRES , "," );

		// $mail = "SELECT Mail FROM (SELECT Mail,sAMAccountName ,Name FROM OpenQuery(ADSI, 'SELECT Mail,sAMAccountName,Name FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user'' ')) Consulta WHERE Consulta.Name IN ($SQL_NOMBRES)";

		$mail = "SELECT Mail FROM GestionIDM.dbo.LDAP WHERE Name IN ($SQL_NOMBRES)";
		$mail = DBSelect ( utf8_decode ( $mail ) );
		for(; DBNext ( $mail ) ;) {
			$DIRECCION = utf8_encode ( DBCampo ( $mail , "Mail" ) );
			if ( $DIRECCION != "" ) {
				$crear_respuestas .= "EXEC [GestionPDT].[dbo].[INSERTAR_RESPUESTA_FEEDBACK] '$usuario', '$DIRECCION', $tarea,'$feedback_message' ; ";
				$correos [ $contador_correos ] = $DIRECCION;
				$contador_correos = $contador_correos + 1;
				$To_Correcto = true;
			}
		}
	}
	// -- Alberto 28719, Si existen correos extras los añadimos al array de $correos

	// ++ Alberto 28719, Metemos en el array $correos al asignado, el procedure nos va a crear una nueva tabla de respuestas con un UID unico
	$s = DBSelect ( utf8_decode ( "EXECUTE [INICIAR_MANDAR_FEEDBACK_NUEVO] $tarea, '$usuario', 0" ) );
	for(; DBNext ( $s ) ;) {
		$contador = $contador + 1;
		$ACTOR = ( DBCampo ( $s , "ACTOR" ) );
		$DIRECCION = utf8_encode ( DBCampo ( $s , "MAIL" ) );
		$USUARIO = utf8_encode ( DBCampo ( $s , "USUARIO" ) );
		$NOMBRE = utf8_encode ( DBCampo ( $s , "NOMBRE" ) );
		$UID = utf8_encode ( DBCampo ( $s , "UID" ) );
		$titulo = utf8_encode ( DBCampo ( $s , "titulo" ) );

		if ( $ACTOR == 1 ) {
			if ( $DIRECCION != "" ) {
				$crear_respuestas .= "EXEC [GestionPDT].[dbo].[INSERTAR_RESPUESTA_FEEDBACK] '$usuario', '$DIRECCION', $tarea, '$feedback_message' ; ";
				$correos [ $contador_correos ] = $DIRECCION;
				$contador_correos = $contador_correos + 1;
				$To_Correcto = true;
			}
		}
		
		// -- Alberto 28719, Metemos en el array $correos al asignado, el procedure nos va a crear una nueva tabla de respuestas con un UID unico

		// ++ Alberto 28719, 11/04/2019 de momento solo se va a enviar al asignado y los extras, solo viene ese dato del procedure
		$From_Correcto = true;
		/*
		* if ( $ACTOR != 1 && false) { //Pongo false para que no se entre por aquí
		* if ( $DIRECCION != "" ) {
		* if ( $USUARIO != $usuario ) {
		* $crear_respuestas .= "EXEC [GestionPDT].[dbo].[INSERTAR_RESPUESTA_FEEDBACK] '$usuario', '$DIRECCION', $tarea ; ";
		* $correos [ $contador_correos ] = $DIRECCION;
		* $contador_correos = $contador_correos + 1;
		* } else {
		* $FROM = $DIRECCION;
		* $FROM_NAME = $NOMBRE;
		* $From_Correcto = true;}}}
		*/
		// -- Alberto 28719, 11/04/2019 de momento solo se va a enviar al asignadoy los extras, solo viene ese dato del procedure
	}


	if ( $To_Correcto == true && $From_Correcto == true ) {

		// GENERAMOS LAS RESPUESTAS
		DBSelect ( utf8_decode ( $crear_respuestas ) );

		// PREPARAMOS LOS CORREOS
		$FROM = 'portal@sp-berner.com';
		$FROM_NAME = 'Solicitudes Feedback Tareas';
		$subject = "Solicitud de Feedback de la tarea: $titulo";

		// ++ PERO!! Si ya se ha creado un feedback anterior, no se debe enviar un nuevo correo
		/*
		$query = "SELECT count(*) as existe_feedback_anterior FROM Feedback_Respuestas WHERE Tarea = $tarea AND Solicitado = 1";
		$query = DBSelect(utf8_decode($query));
		DBNext($query);
		$existe_feedback_anterior = DBCampo ( $query , "existe_feedback_anterior" );
		$filtro_enviados = '';
		if($existe_feedback_anterior > 0){
			$tipo_error = 1;
			$txt_error = 'Correo <b>incorrecto</b> ya se ha enviado una solicitud anterior al solicitante de la tarea.';
			$colas [ 0 ] = array (
					"mensaje" => $txt_error
			);
		}
		*/
		// -- PERO!! Si ya se ha creado un feedback anterior, no se debe enviar un nuevo correo al solicitante

		$query_respuestas = "
			SELECT UID, Mail FROM [GestionPDT].[dbo].[Feedback_Respuestas]
			WHERE Tarea = $tarea AND EnviadoMail <> 1
			GROUP BY UID, MAIL ";
		$marcar_EnviadoMail = "";
		$query_respuestas = DBSelect ( utf8_decode ( $query_respuestas ) );
		for(; DBNext ( $query_respuestas ) ;) {
			$TO = ( DBCampo ( $query_respuestas , "Mail" ) );
			$UID = ( DBCampo ( $query_respuestas , "UID" ) );
			$marcar_EnviadoMail .= " UPDATE [GestionPDT].[dbo].[Feedback_Respuestas] SET EnviadoMail = 1 WHERE UID = '$UID'; ";
			$url = "https://tareas.sp-berner.com/feedback/index.php?test=" . $UID;
			$boton_enlace = "
			<BR><BR><a href='$url'  style='border: solid 4px red;font-weight:900;padding: 14px 28px;font-size: 20px;cursor: pointer;display: inline-block;color: red; text-decoration: none'>REALIZAR FEEDBACK</a>";
			$message = $feedback_message . $boton_enlace;

			ini_set ( 'SMTP' , "relay.sp-berner.com" );
			ini_set ( 'smtp_port' , "25" );
			ini_set ( 'sendmail_from' , $FROM );

			$email = new PHPMailer ();
			$email -> IsHTML ( true );
			$email -> From = utf8_decode ( $FROM );
			$email -> FromName = utf8_decode ( $FROM_NAME );
			$email -> Subject = utf8_decode ( $subject );
			$email -> Body = utf8_decode ( $message );
			//$email -> AddAddress ( $FROM );
			$email -> AddAddress ( $TO );

			/*
			 * if ( curPageName () == 'GESTIONPROYECTOSESTRUCTURAS' ) {
			 * if ( ! $encontrado_cristobal ) { $email -> AddBCC ( 'cristobal.seijo@sp-berner.com' );}
			 * if ( ! $encontrado_ana ) {$email -> AddBCC ( 'AnaCristobalXercavins@sp-berner.com' );}}
			 */
			$res = $email -> Send ();


			if ( $To_Correcto == false || $From_Correcto == false ) {
				$tipo_error =  - 1;
				$txt_error = 'Cuando se solicita un feedback se manda un correo a todos los participantes anunciándolo. El correo no se ha podido enviar. Alguno de los destinatarios no tiene correo o no es correcto.';
				$colas [ 0 ] = array (
						"mensaje" => $txt_error
				);
			} else {
				if ( $res == 1 ) {
					$tipo_error = 1;
					$txt_error = 'Correo <b>correcto</b> enviado a todos los participantes.';
					$colas [ 0 ] = array (
							"mensaje" => $txt_error
					);
					foreach ( $correos as $correo ) {
						// $q = DBSelect ( utf8_decode ( "INSERT INTO [feedback] ([Tarea],[fecha],[usuario],[destino],[feedback]) VALUES (" . $tarea . ",GETDATE() ,'" . $usuario . "','" . $correo . "','" . str_replace ( "'" , "''" , ( $message ) ) . "')" ) );
					}
				} else {
					$tipo_error =  - 1;
					$txt_error = 'Cuando se solicita un feedback se manda un correo a todos los participantes anunciándolo. El correo no se ha podido enviar. Intentelo de nuevo';
					$colas [ 0 ] = array (
							"mensaje" => $txt_error
					);
				}
			}
		}
	}

	DBFree ( $s );

	//echo "<h1>$marcar_EnviadoMail</h1>";

	if($marcar_EnviadoMail != ''){
		DBSelect ( utf8_decode ( $marcar_EnviadoMail ) );
	}else{
		$tipo_error = 1;
		$txt_error = 'Correo <b>incorrecto</b> ya se ha enviado una solicitud anterior al usuario.';
		$colas [ 0 ] = array (
				"mensaje" => $txt_error
		);
	}

	DBClose ();
	$json_arr = array (
			'tipo' => $tipo_error ,
			'tarea' => $tarea ,
			'data' => $colas
	);
	$php_json = json_encode ( $json_arr );

	echo $php_json;
}

function feedback_feedbacks() {
	include_once "../conf/config_filtros.php";
	include_once "../php/funciones.php";
	include_once "../conf/config.php";
	include_once "../conf/config_" . curPageName () . ".php";
	include_once "../../soporte/DB.php";
	include_once "../../soporte/funcionesgenerales.php";

	DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

	$tarea = $_POST [ 'tarea' ];

	// ++ OBTENEMOS DATOS DEL TEST
	$fechas_historicas = '';
	$existe_feedback_anterior = 0;
	$UID = '';
	$BOTONES = '';
	$MENSAJE = '';
	$query = "
		SELECT
			UID
			,Nombre
			,Mensaje
			,(select COUNT(Respuesta) - COUNT(UsuarioRespuesta)) AS PENDIENTES
		FROM Feedback_Respuestas
		WHERE Tarea = $tarea
		GROUP BY UID,Nombre,Mensaje";
	$query = DBSelect(utf8_decode($query));
	for (; DBNext($query);) {
		$UID = utf8_encode(DBCampo($query, "UID"));
		$Nombre = utf8_encode(DBCampo($query, "Nombre"));
		$PENDIENTES = utf8_encode(DBCampo($query, "PENDIENTES"));
		$MENSAJE = utf8_encode(DBCampo($query, "Mensaje"));
		$existe_feedback_anterior = 1;

		if($PENDIENTES != 0){
			$BOTONES .= "<a href='#' title='La solicitud de feedback está pendiente del usuario' style='border: solid 1px red;font-weight:900;margin:2px;padding: 5px 5px;font-size: 15px;cursor: pointer;display: inline-block;color: red; text-decoration: none'>$Nombre</a>";
		}else{
			$url = "https://tareas.sp-berner.com/feedback/index.php?test=" . $UID;
			$BOTONES .= "<a href='$url' target='blank' title='La solicitud de feedback ha sido completado por el usuario' style='border: solid 1px green;font-weight:900;margin:2px;padding: 5px 5px;font-size: 15px;cursor: pointer;display: inline-block;color: green; text-decoration: none'>$Nombre</a>";
		}
	}

	if($existe_feedback_anterior == 1){
		echo "<div style='color:red;font-weight: 900;text-align:center;font-size: 20px;'>Ya se ha solicitado el Feedback de esta tarea.</div>";
		echo "<div style='color:red;font-weight: 100;text-align:center;font-size: 16px;'>En la parte inferior de esta pantalla puede ver las solicitudes enviadas, donde verá en verde aquellas que ya hayan sido respondidas.<br>Tambien puede enviar nuevas solicitudes si lo cree conveniente desde el cuadro 'Incluir Destinatarios'.</div>";
	}else{
		echo "<div style='color:green;font-weight: 900;text-align:center;font-size: 20px;'>Formulario de solicitud de Feedback.</div>";
		echo "<div style='color:green;font-weight: 100;text-align:center;font-size: 16px;'>Se va a solicitar el feedback de esta tarea a la persona que figura como 'Solicitado' en la tarea, adicionalmente si lo desea, también puede solicitar feedback a otros usuarios desde el cuadro 'Incluir Destinatarios'.<br>Una vez enviadas las solicitudes, podrá ver la lista de las personas a quienes se ha solicitado el feedback, así como ver los cuestionarios que ya han sido respondidos por los mismos.</div>";
	}
	DBFree($query);
	// -- OBTENEMOS DATOS DEL TEST


	/*
	$q = "SELECT distinct convert(varchar(20),Fecha,105)+' '+convert(varchar(20),Fecha,108) as fecha  FROM [feedback] where [Tarea] = $tarea ";
	$q = DBSelect ( utf8_decode ( $q ) );
	for(; DBNext ( $q ) ;) {
		$fecha = DBCampo ( $q , "fecha" );
		$fechas_historicas = $fechas_historicas . '<div class="feedback_lista">' . utf8_encode ( $fecha ) . '</div>';
	}
	if ( $fechas_historicas != '' ) {
		echo "<div style='color:red;font-weight: 900;text-align:center;font-size: 20px;'>Ya se ha solicitado el Feedback de esta tarea</div>";
		echo '<div>Feedback solicitada en:</div>';
		echo '<div class="feedback_fechas">';
		echo $fechas_historicas;
		echo '</div>';
	}
	*/
	$contador = 0;
	$usuario = $_POST [ 'usuario' ];
	$query = "
			SELECT
				CAST([Título] as varchar(250)) as titulo
				,CONVERT(varchar(250),[Fecha alta],106) as fecha
				,[Categoría] as categoria
      			,[Tipo]	as tipo
			FROM [Tareas y Proyectos] where Id = " . $tarea;
	$typ = DBSelect ( utf8_decode ( $query ) );
	$typ_titulo = DBCampo ( $typ , "titulo" );
	$typ_fecha = DBCampo ( $typ , "fecha" );
	$typ_tipo = DBCampo ( $typ , "tipo" );
	$typ_categoria = DBCampo ( $typ , "categoria" );

	echo '<div class="feedback_bloque">Destinatarios de solicitud de feedback</div>';
	echo '<div class="feedback_destinatarios">';
	$s = DBSelect ( utf8_decode ( "EXECUTE [INICIAR_MANDAR_FEEDBACK_NUEVO] " . $tarea . ", '" . $usuario . "', 0" ) );
	// 08/02/2017 T#17145 - WEB TAREAS. Estructuras: modificar interlocutores de envío mensajes de feed back
	$encontrado_ana = false;
	$encontrado_cristobal = false;
	for(; DBNext ( $s ) ;) {
		$contador = $contador + 1;
		$ACTOR = ( DBCampo ( $s , "ACTOR" ) );
		$DIRECCION = utf8_encode ( DBCampo ( $s , "MAIL" ) );
		$USUARIO = utf8_encode ( DBCampo ( $s , "USUARIO" ) );
		if ( $USUARIO != $usuario ) {
			if ( $DIRECCION != "" ) {
				echo '<div>' . $DIRECCION . '</div>';
				//if ( strtolower ( $DIRECCION ) == 'anacristobalxercavins@sp-berner.com' ) $encontrado_ana = true;
				//if ( strtolower ( $DIRECCION ) == 'cristobal.seijo@sp-berner.com' ) $encontrado_cristobal = true;
			}
		}
	}
	DBFree ( $s );
	// 08/02/2017 T#17145 - WEB TAREAS. Estructuras: modificar interlocutores de envío mensajes de feed back
	//if ( curPageName () == 'GESTIONPROYECTOSESTRUCTURAS' ) {
	//	if (  ! $encontrado_cristobal ) echo '<div>CC: cristobal.seijo@sp-berner.com</div>';
	//	if (  ! $encontrado_ana ) echo '<div>CC: AnaCristobalXercavins@sp-berner.com</div>';
	//}

	echo '</div>';

	echo '<div class="feedback_bloque">Incluir Destinatarios:<input id="autocompletemascorreo" name="autocompletemascorreo"></input><img class="feedback_mas_imagen" id="feedback_mas_imagen" src="feedback/imagen/more.png" onclick="feedback_more(' . $tarea . ');"></div>';

	echo '<div class="feedback_destinatarios" id="feedback_destinatarios_mas"></div>';
	echo '<div class="feedback_bloque">El siguiente texto aparecerá en la solicitud, puede editarlo para incluir el mensaje que usted prefiera:</div>';

	if($MENSAJE == ''){
		echo "
		<div>
			<div id='feedback_textarea' contenteditable='true' style='height: 250px;overflow-y: scroll;border: solid 1px;background-color: rgba(255, 255, 255, 0.85);width: 100%;'>
				Buenos días: <br><br>

				A continuación se detallan los datos de la tarea <b>" . $tarea . " " . utf8_encode ( $typ_titulo ) . "</b><br>
				Fecha de alta: " . $typ_fecha . "<br>
				Tipo: " . utf8_encode ( $typ_tipo ) . "<br>
				Categoría: " . utf8_encode ( $typ_categoria ) . "<br>
				Deberá responder el cuestionario del enlace adjunto a este correo.<br>
				Gracias, saludos
			</div>
		</div>
		";
	}else{
		echo "
		<div>
			<div id='feedback_textarea' contenteditable='false' style='height: 250px;overflow-y: scroll;border: solid 1px;background-color: rgba(255, 255, 255, 0.85);width: 100%;'>
				$MENSAJE
			</div>
		</div>
		";
	}




	echo "<div id='feedback_confirmar' class='feedback_confirmar'><img class='feedback_confirmar_imagen' id='feedback_confirmar_imagen' src='imagenes/button_enviar.png' onclick='feedback_send($tarea);'></div>";
	echo $BOTONES;



	DBClose ();
}

?>
