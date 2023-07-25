<?php
	include_once "../conf/config_filtros.php";
	include_once "../php/funciones.php";
	include_once "../conf/config.php";
	include_once "../conf/config_MENU.php";
	include_once "../../soporte/DB.php";
	include_once "../../soporte/funcionesgenerales.php";
	
	require "../conf/mail.php";

	$usuario=$_GET['usuario'];
	$idcorreo = $_GET['idcorreo'];
	$correou = $_GET['correou'];
	
	DBConectar($GLOBALS["cfg_DataBase"]);
	$q=DBSelect(utf8_decode("EXECUTE [INICIAR_TAREA_CORREO] '$usuario',$idcorreo" ) );
	
	$Idinsertada=(DBCampo($q,"Id"));
	DBFree($q);
	
	// ++ Alberto 26/5/20 T#35637 - IDM#3159 DE RUBEN PARRA MATEO
	$q=DBSelect(utf8_decode(" EXECUTE [INSERTAR_ALERTA] @TAREA = $Idinsertada, @TIPO = N'alerta_nueva_asignacion', @USUARIO_ALTA = N'$usuario'" ) );
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
						      FROM [Entrada_Correo] WHERE Id =".$idcorreo));
	
	
	$Asignado = utf8_encode(DBCampo($q, "asignadonombre" ));
	$AsignadoUsuario = utf8_encode(DBCampo($q, "asignadousuario" ));
	$Asunto=DBCampo($q, "asunto" );
	$MotivoRechazoOAceptacion=utf8_encode(DBCampo($q, "motivorechazoaceptado" ));
	$SolicitadoCorreo=utf8_encode(DBCampo($q, "solicitadousuario" ));
	$NombreFichero=utf8_encode(DBCampo($q, "rutacorreo" ));
	$NoPlanificado = utf8_encode(DBCampo($q, "noplanificado" ));
	$Usuario = utf8_encode(DBCampo($q, "usuariocreacion" ));
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
		enviarmail($correou, "Su tarea ha sido aceptada [ID ".$Idinsertada."].", " Su tarea con asunto [ ".$Asuntotarea." ] ha sido aceptada. <br /> Su numero de tarea es: ".$Idinsertada."<br /> <b>Puedes hacer seguimiento del estado de tu tarea, así como de la fecha objetivo de la misma, a través del enlace <a href='https://tareas.sp-berner.com/?ext=TI'>Portal de Tareas</a></b>");	
	}else{
	    enviarmail($correou, "Su tarea ha sido aceptada[ID ".$Idinsertada."].", " Su tarea con asunto [ ".$Asuntotarea." ] ha sido aceptada.<br /> <b>Observaciones :</b><br /> ".$MotivoRechazoOAceptacion."<br /> Su numero de tarea es: ".$Idinsertada."<br /> <b>Puedes hacer seguimiento del estado de tu tarea, así como de la fecha objetivo de la misma, a través del enlace <a href='https://tareas.sp-berner.com/?ext=TI'>Portal de Tareas</a></b>");
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
											,'".utf8_decode($Usuario)."' --usuario creacion
											,''
											,''
											,''	
											,'SI'
											)
									 ";
		$crearplanificacion=DBSelect (( $crearplanificacion ));
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
 											,'".$Usuario."'
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

		echo 1;
	}else{
		die('ERROR ADJUNTO CORREO');
	}
	
	function enviarmail($to, $asunto, $mensaje){
		insertarCorreoMensajeriaTest($asunto,$mensaje,'soporte.ti@sp-berner.com',$to,'','',3,15);
	}
?>