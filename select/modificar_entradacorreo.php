<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);

$id = $_GET["Id"];

if (isset($_GET["tipoajax"])){
	$tipoajax = $_GET["tipoajax"];
}

if (isset($_GET["Usuario"])){
	$Usuario = $_GET["Usuario"];
}

if (isset($_GET[ "Estado" ])){ 
	$Estado = $_GET ["Estado"];
}

if ($tipoajax == 'guardarCelda'){

	$Asignado = $_GET["Asignado"]; 
	$Prioridad = $_GET["Prioridad"];
	$MotivoRechazoOAceptacion = $_GET["MotivoRechazoOAceptacion"];
	$Tipo = $_GET["Tipo"];
	$Usuario = $_GET["Usuario"];
	$Horas = $_GET["Horas"];
	$Catalogacion = $_GET["Catalogacion"];
	$Planificado = $_GET["planificado"];
	$FechaObjetivo = $_GET["fechaobjetivo"];
	$AreaSolicitante = $_GET["areasolicitante"];
	$Planta = $_GET["planta"];
	$Solicitado = $_GET["solicitado"];
	$Asunto = $_GET["asunto"];
	$Actividad = $_GET["Actividad"];
	$FechaPlanificada = $_GET["FechaPlanificada"];

	$FechaObjetivo = limpiaTexto($FechaObjetivo);
	$FechaPlanificada = limpiaTexto($FechaPlanificada);
	
	if($FechaObjetivo  == 'NULL'  || $FechaObjetivo == NULL){
		$FechaObjetivo  = ",FechaObjetivo = NULL";
	}else{
		$FechaObjetivo = ",FechaObjetivo = '$FechaObjetivo'";
	}

	if($FechaPlanificada  == 'NULL'  || $FechaPlanificada == NULL){
		$FechaPlanificada  = ",FechaPlanificada = NULL";
	}else{
		$FechaPlanificada = ",FechaPlanificada = '$FechaPlanificada'";
	}

	// limpieza de textos
	$MotivoRechazoOAceptacion = strip_tags($MotivoRechazoOAceptacion);
	$MotivoRechazoOAceptacion = str_replace("\'", "", ($MotivoRechazoOAceptacion));
	$MotivoRechazoOAceptacion = str_replace('\"', "", ($MotivoRechazoOAceptacion));
	
	$Asunto = strip_tags($Asunto);
	$Asunto = str_replace("\'", "", ($Asunto));
	$Asunto = str_replace('\"', "", ($Asunto));
	
	$u=DBSelect(utf8_decode ("SELECT Mail as EmailSolicitado FROM [GestionIDM].[dbo].[LDAP] WHERE Name='$Solicitado'" ));
	$EmailSolicitado=utf8_encode(DBCampo($u, "EmailSolicitado" ));
	
	$q = "
		UPDATE [GestionPDT].[dbo].[Entrada_Correo]
		SET
			Estado = '$Estado'
			,Asignado = '$Asignado'
			,Prioridad = '$Prioridad'
			,MotivoRechazoOAceptacion = '$MotivoRechazoOAceptacion'
			,Tipo = '$Tipo'
			,Usuario = '$Usuario'
			,Horas = $Horas
			,Catalogacion = '$Catalogacion'
			,NoPlanificado = '$Planificado'
			$FechaObjetivo
			,AreaSolicitante = '$AreaSolicitante'
			,Planta='$Planta'
			,Solicitado='$Solicitado'
			,SolicitadoEmail='$EmailSolicitado'
			,Asunto='$Asunto'
			,Actividad = '$Actividad'
			$FechaPlanificada
		WHERE Id = $id
	";

	DBSelect(utf8_decode($q));
	DBFree ( $q );
	//DBClose ();

} else{
	// tipoajax -> comprobacionCorreo //
	$UsuarioV = $_GET["Usuario"];
	$estadocomprobacion = $_GET["estadocomprobacion"];
	
	$ResultadoComprobacion = TRUE;
	$lul = false;
	$mensajeError ='El campo ';

	$qe = "
		SELECT 
		    Id
			,Estado
			,Asignado
			,Tipo
			,Actividad
			,Catalogacion
			,Horas
			,FechaObjetivo
			,MotivoRechazoOAceptacion
			,HorasObligadas
			,AreaSolicitante
			,Planta
			,SolicitadoEmail
			,FechaPlanificada
			,[NoPlanificado] as NoPlanificada
			,Solicitado
		FROM Entrada_Correo
		WHERE 
			Id = $id";

	$q = DBSelect(utf8_decode($qe));
	for(; DBNext ( $q ) ;) {
		//obligatorios//
		$Id = DBCampo ( $q , "Id" );
		$Estado = utf8_encode ( DBCampo ( $q , utf8_decode ( "Estado" ) ) );
		$Asignado = utf8_encode ( DBCampo ( $q , utf8_decode ( "Asignado" ) ) );
		$Tipo = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tipo" ) ) );
		$Actividad = utf8_encode(DBCampo ( $q , utf8_decode ( "Actividad" )  ));
		$Catalogacion = utf8_encode ( DBCampo ( $q , utf8_decode ( "Catalogacion" ) ) );
		$Horas = utf8_encode ( DBCampo ( $q , utf8_decode ( "Horas" ) ) );
		$FechaObjetivo = DBCampo ( $q  , utf8_decode ( "FechaObjetivo" )  ) ;
		$MotivoRechazoOAceptacion = utf8_encode ( DBCampo ( $q , utf8_decode ( "MotivoRechazoOAceptacion" ) ) );
		$Obligadas = utf8_encode( DBCampo ( $q  , utf8_decode ( "HorasObligadas" )  ) );
		$AreaSolicitante = utf8_encode( DBCampo ( $q  , utf8_decode ( "AreaSolicitante" )  ) );
		$Planta = utf8_encode( DBCampo ( $q  , utf8_decode ( "Planta" )  ) );
		$SolicitadoEmail = utf8_encode( DBCampo ( $q  , utf8_decode ( "SolicitadoEmail" )  ) );
		$FechaPlan = DBCampo ( $q  , utf8_decode ( "FechaPlanificada" )  ) ;
		$NoPlan = utf8_encode( DBCampo ( $q  , utf8_decode ( "NoPlanificada" )  ) );
		$SolicitadoT = utf8_encode( DBCampo ( $q  , utf8_decode ( "Solicitado" )  ) );
	}
	DBFree ( $q );

	if ($FechaPlan == ''){
		$FechaPlan = 'NULL';
	}

	if(($Estado == "Aceptado")||($Estado == "Rechazado")){
		$ResultadoComprobacion = FALSE;
		$mensajeError = 'Esta tarea ya ha sido valorada y esta ['.$Estado.']';
	}else{
		//Pendiente
		if($estadocomprobacion == 'Aceptado'){
			//comprobaciones antes de aceptar la tarea//
			if(($Asignado == '')||(is_null($Asignado))){
				$ResultadoComprobacion = FALSE;
				$mensajeError .=  '[ASIGNADO],';
			}	
	
			if(($Tipo == '')||(is_null($Tipo))){
				$ResultadoComprobacion = FALSE;
				$mensajeError .=  '[TIPO],';
			}
			
			if(($Actividad == '')||(is_null($Actividad))){
				$ResultadoComprobacion = FALSE;
				$mensajeError .=  '[ACTIVIDAD],';
			}
	
			if(($Catalogacion == '')||(is_null($Catalogacion))){
				$ResultadoComprobacion = FALSE;
				$mensajeError .=  '[CATALOGACION],';
			}
	
			if(($Horas == '')||($Horas == 0)||(is_null($Horas))){
				$ResultadoComprobacion = FALSE;
				$mensajeError .=  '[HORAS],';
			}
	
			if(($FechaObjetivo == '')||(is_null($FechaObjetivo))){
				$ResultadoComprobacion = FALSE;
				$mensajeError .=  '[FECHA OBJETIVO],';
			}
	
			if(($Planta == '-')||(is_null($Planta))){
				$ResultadoComprobacion = FALSE;
				$mensajeError .=  '[PLANTA],';
			}
	
			if(($AreaSolicitante == '-')||(is_null($AreaSolicitante))){
				$ResultadoComprobacion = FALSE;
				$mensajeError .=  '[AREA SOLICITANTE],';
			}

			if(($FechaPlan != 'NULL')&&($NoPlan == 'SI')){	
				$ResultadoComprobacion = FALSE;
				$mensajeError .=  '[No puede asignar una fecha planificada y no planificada a la vez],';
				$lul = true;	
			}

			if(($SolicitadoT == '')||(is_null($SolicitadoT))){
				$ResultadoComprobacion = FALSE;
				$mensajeError .=  '[SOLICITANTE],';
			}		
			
		}else{
			//comprobaciones antes de rechazar la tarea//
			if(($MotivoRechazoOAceptacion == '')||(is_null($MotivoRechazoOAceptacion))){
				$ResultadoComprobacion = FALSE;
				$mensajeError .= '[MOTIVO RECHAZO O ACEPTACION], ';
			}

			if(($SolicitadoT == '')||is_null($SolicitadoT)){
				$ResultadoComprobacion = FALSE;
				$mensajeError .=  '[SOLICITANTE]';
			}
			
		}
	}

	if($ResultadoComprobacion) {
		if($estadocomprobacion == 'Aceptado'){
			//aceptada con exito//
			echo aceptarTarea($UsuarioV,$id, $SolicitadoEmail);
		}else{
			//rechazada con exito//
			echo rechazarTarea($estadocomprobacion, $UsuarioV, $id, $SolicitadoEmail);
		}
	}else{
		if($Estado == "Pendiente"){
			if ($lul){
				echo rtrim($mensajeError,',');
			}else{
				echo rtrim($mensajeError,','). ' esta vacio.';
			}
		}else{
			echo rtrim($mensajeError,',');
		}
	}
}

function rechazarTarea($estadoComp,$usuario,$id, $correoSolicitante){	
	$query = "
		UPDATE [GestionPDT].[dbo].[Entrada_Correo]
		SET
			Estado = 'Rechazado'
			,Usuario = '$usuario'
			,IdTarea = -1
		WHERE Id = $id
	";		

	DBSelect(utf8_decode($query));
	DBFree ( $query );

	// borramos el correo para que no se quede ahi //
	$q=DBSelect(utf8_decode ("SELECT 
									Asunto as t, 
									MotivoRechazoOAceptacion as o ,
									RutaCorreo as r,
									[Solicitado] as solicitadousuarioS
								FROM [Entrada_Correo] WHERE Id =".$id
	));
	
	$Asuntotarea1=utf8_encode(DBCampo($q, "t" ));
	$MotivoRechazoOAceptacion=utf8_encode(DBCampo($q, "o" ));
	$RutaCorreo=utf8_encode(DBCampo($q, "r" ));
	$solicitadousuarioS=utf8_encode(DBCampo($q, "solicitadousuarioS" ));
		
	$rutaPendiente = '//sp-berner.local/GestionDocumentalWeb/Tareas/CorreosBOT/';
	$rutaRechazados = '//sp-berner.local/GestionDocumentalWeb/Tareas/CorreosBOT/Rechazados/';
	
	copy($rutaPendiente.$RutaCorreo.'.eml', $rutaRechazados.$RutaCorreo.'.eml');
	unlink($rutaPendiente.$RutaCorreo.'.eml');
			
	if($MotivoRechazoOAceptacion == ' ' || empty($MotivoRechazoOAceptacion)){
		enviarmail($correoSolicitante, "Su tarea ha sido rechazada.", " Su tarea con asunto [ ".$Asuntotarea1." ] ha sido rechazada.", $solicitadousuarioS);
	}else{
		enviarmail($correoSolicitante, "Su tarea ha sido rechazada.", " Su tarea con asunto [ ".$Asuntotarea1." ] ha sido rechazada.<br /> <b>Observaciones :</b><br /> ".$MotivoRechazoOAceptacion,$solicitadousuarioS);
	}

	echo 2;
}


function aceptarTarea($usuarioAceptacion,$idcorreo,$correou){
	
	DBConectar($GLOBALS["cfg_DataBase"]);
	$q=DBSelect(utf8_decode("EXECUTE [INICIAR_TAREA_CORREO] '$usuarioAceptacion',$idcorreo" ) );
	
	$Idinsertada=(DBCampo($q,"Id"));
	DBFree($q);
	
	// ++ Alberto 26/5/20 T#35637 - IDM#3159 DE RUBEN PARRA MATEO
	$q=DBSelect(utf8_decode(" EXECUTE [INSERTAR_ALERTA] @TAREA = $Idinsertada, @TIPO = N'alerta_nueva_asignacion', @USUARIO_ALTA = N'$usuarioAceptacion'" ) );
	DBFree($q);
	// -- Alberto 26/5/20 T#35637 - IDM#3159 DE RUBEN PARRA MATEO
	
	$q=DBSelect(utf8_decode ("SELECT Título as t FROM [Tareas y Proyectos] WHERE Id =".$Idinsertada));	
	$Asuntotarea=utf8_encode(DBCampo($q, "t" ));
	DBFree($q);
	
	$q=DBSelect(utf8_decode ("SELECT
    								[Asignado] as asignadonombre
    								,[Usuario] as usuariocreacion
    								,[MotivoRechazoOAceptacion] as motivorechazoaceptado
    								,[Solicitado] as solicitadousuario
    								,[RutaCorreo] as rutacorreo
    								,[Asunto] as asunto
    								,[NoPlanificado] as noplanificado
    								,dbo.SF_OBTENER_USUARIO_DOMINIO([Asignado]) as asignadousuario
									--,[FechaPlanificada] as fechaPlan
									,CONVERT(VARCHAR(10),[FechaPlanificada],105)  as fechaPlan
						      FROM [Entrada_Correo] WHERE Id =".$idcorreo));
	
	
	$Asignado = utf8_encode(DBCampo($q, "asignadonombre" ));
	$AsignadoUsuario = utf8_encode(DBCampo($q, "asignadousuario" ));
	$Asunto=DBCampo($q, "asunto" );
	$MotivoRechazoOAceptacion=utf8_encode(DBCampo($q, "motivorechazoaceptado" ));
	$SolicitadoCorreo=utf8_encode(DBCampo($q, "solicitadousuario" ));
	$NombreFichero=utf8_encode(DBCampo($q, "rutacorreo" ));
	$NoPlanificado = utf8_encode(DBCampo($q, "noplanificado" ));
	$Usuario = utf8_encode(DBCampo($q, "usuariocreacion" ));
	$FechaPlan = DBCampo($q, "fechaPlan" );
	

	//$FechaPlan = new DateTime($FechaPlan);
	//die('fecha ->'.$FechaPlan);
	DBFree($q);
	
	$query = "
			UPDATE [GestionPDT].[dbo].[Entrada_Correo]
			SET
				IdTarea = $Idinsertada
			WHERE Id = $idcorreo
		";	
	
	$q=DBSelect($query);
	DBFree($q);
	
	$NombreFicheroDestino = 'CorreoOriginal';
	
	if($MotivoRechazoOAceptacion == ' ' || empty($MotivoRechazoOAceptacion)){
		enviarmail($correou, "Su tarea ha sido aceptada [ID ".$Idinsertada."].", " Su tarea con asunto [ ".$Asuntotarea." ] ha sido aceptada. <br /> Su numero de tarea es: ".$Idinsertada."<br /> <b>Puedes hacer seguimiento del estado de tu tarea, así como de la fecha objetivo de la misma, a través del enlace <a href='https://tareas.sp-berner.com/?ext=TI'>Portal de Tareas</a></b>",$SolicitadoCorreo);	
	}else{
	    enviarmail($correou, "Su tarea ha sido aceptada[ID ".$Idinsertada."].", " Su tarea con asunto [ ".$Asuntotarea." ] ha sido aceptada.<br /> <b>Observaciones :</b><br /> ".$MotivoRechazoOAceptacion."<br /> Su numero de tarea es: ".$Idinsertada."<br /> <b>Puedes hacer seguimiento del estado de tu tarea, así como de la fecha objetivo de la misma, a través del enlace <a href='https://tareas.sp-berner.com/?ext=TI'>Portal de Tareas</a></b>",$SolicitadoCorreo);
	}
	
	if ($NoPlanificado == 'SI'){
		$crearplanificacion = "INSERT INTO [Planificador](
											   [Tarea]
										      ,[Fecha]
										      ,[Anyo]
										      ,[Mes]
										      ,[Semana]
										      ,[Situacion]
										      ,[Observaciones]
										      ,[Comentario_Adicional]
										      ,[Estado]
										      ,[Prioridad]
										      ,[Asignado_Nombre]
										      ,[Asignado_Usuario]
										      ,[Fecha_Creacion]
										      ,[Usuario_Crea]
										      ,[Fecha_Cambio]
										      ,[Usuario_Cambio]
										      ,[Tiempo_Estimado]
										      ,[No_Planificado]
											)VALUES(
											$Idinsertada
											,Convert(date, getdate())
											,YEAR(getdate())
											,MONTH(GETDATE())
											,DATEPART(ISO_WEEK,GETDATE())
											,'Pendiente'
											,''
											,''
											,'Activo'
											,'Normal'
											,'".utf8_decode($Asignado)."' --asignado nombre
											,'".utf8_decode($AsignadoUsuario)."' --asignado usuario
											,GETDATE()
											,'".utf8_decode($usuarioAceptacion)."' --usuario creacion
											,''
											,''
											,''	
											,'SI'
											)";
		$crearplanificacion=DBSelect (( $crearplanificacion ));
	}

	//if ((!is_null($FechaPlan))&& ($FechaPlan != '')){
	if(($FechaPlan != 'NULL')&&($FechaPlan !='')){
			$crearPlanificacionNormal = "INSERT INTO [Planificador](
											[Tarea]
											,[Fecha]
											,[Anyo]
											,[Mes]
											,[Semana]
											,[Situacion]
											,[Observaciones]
											,[Comentario_Adicional]
											,[Estado]
											,[Prioridad]
											,[Asignado_Nombre]
											,[Asignado_Usuario]
											,[Fecha_Creacion]
											,[Usuario_Crea]
											,[Fecha_Cambio]
											,[Usuario_Cambio]
											,[Tiempo_Estimado]
											,[No_Planificado]
											)VALUES(
											$Idinsertada
											,'".$FechaPlan."'
											,YEAR('".$FechaPlan."')
											,MONTH('".$FechaPlan."')
											,DATEPART(ISO_WEEK,'".$FechaPlan."')
											,'Pendiente'
											,''
											,''
											,'Activo'
											,'Normal'
											,'".utf8_decode($Asignado)."' --asignado nombre
											,'".utf8_decode($AsignadoUsuario)."' --asignado usuario
											,GETDATE()
											,'".utf8_decode($usuarioAceptacion)."' --usuario creacion
											,''
											,''
											,''	
											,''
											)";
	//$crearPlanificacionNormal=DBSelect (( $crearPlanificacionNormal ));
	$q=DBSelect($crearPlanificacionNormal);
	DBFree($q);
	}
		
	$NUMEROADJUNTO = -1;
	//insertamos el adjunto a la tarea//
	$sqlinsertar = "INSERT INTO [Adjuntos](
											[Tarea]
											,[Fichero]
											,[Descripcion]
											,[Fecha]
											,[usuario]
											,[Publico]
											,[Tipo]
											)OUTPUT INSERTED.NUMERO as N
											VALUES(
											$Idinsertada
											,'db_adjuntos/MENU'
											--,'".$NombreFichero.".eml'
											,'".$NombreFicheroDestino.".eml'
											,GETDATE() 
 											,'".$usuarioAceptacion."'
											,1
											,3										
											)
									 ";
	$sqlinsertar=DBSelect (( $sqlinsertar ));
	$NUMEROADJUNTO=(DBCampo($sqlinsertar, "N" ));
	DBClose();
	
	if ($NUMEROADJUNTO != -1){
		copy('../mails/CorreosBOT/'.$NombreFichero.'.eml', '../db_adjuntos/MENU/'.$NUMEROADJUNTO.'_'.$NombreFicheroDestino.'.eml');
		unlink('../mails/CorreosBOT/'.$NombreFichero.'.eml');

		//echo 1;
	}else{
		die('ERROR ADJUNTO CORREO');
	}
	
	echo 1;
}


function enviarmail($to, $asunto, $mensaje, $usuarioNombre){
	$comprobacion = true;

	if(usuarioExcepcion($usuarioNombre)){
		$comprobacion = false;
	}

	if ($comprobacion){
		insertarCorreoMensajeriaTest($asunto,$mensaje,'soporte.ti@sp-berner.com',$to,'','',3,15);
	}

}
	
function usuarioExcepcion($nombreUsuario){
	// Se crea porque los terminales MAPEX crean las tareas directamente en la tabla y a la hora de notificar al estar vacio el correo lo manda '' a nav. 
	// Se evita la notificacion a la tabla de mensajeria.	

	if ($nombreUsuario == 'Terminales Mapex'){
		return true;
	}

}

?>
