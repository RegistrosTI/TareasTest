<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);

if(isset( $_GET["estadoTarea"] )){
	$estadoTarea = $_GET["estadoTarea"];
	$tarea     =$_GET["tarea"];

	//Para la oficina de la tarea los campos actividad, area y planta existen?
	//El nuevo estado es finalizado?
	$q = "
		SELECT 
			TI.Finalizado
			,TA.Oficina 
			,TA.AreaSolicitante
			,TA.Planta
			,TA.Actividad
			,ISNULL((SELECT OFICINA FROM ventana WHERE Ventana = 'MENU' AND TIPO = 'FORMULARIO' AND ROL = 1 AND ocultar = 0 AND CAMPO = 'AreaSolicitante'),'') AS OFICINAS_AREA 
			,ISNULL((SELECT OFICINA FROM ventana WHERE Ventana = 'MENU' AND TIPO = 'FORMULARIO' AND ROL = 1 AND ocultar = 0 AND CAMPO = 'PLANTA'),'') AS OFICINAS_PLANTA
			,ISNULL((SELECT OFICINA FROM ventana WHERE Ventana = 'MENU' AND TIPO = 'FORMULARIO' AND ROL = 1 AND ocultar = 0 AND CAMPO = 'Actividad'),'') AS OFICINAS_ACTIVIDAD
		FROM [GestionPDT].[dbo].[Tipos] TI INNER JOIN [Tareas y Proyectos] TA ON TI.OFICINA = TA.OFICINA
		WHERE TI.Tipo = 5 AND TI.Descripcion ='$estadoTarea' AND TA.Id = $tarea
	";
	
	$q = DBSelect ( utf8_decode ( $q ) );	
	
	$Finalizado = utf8_encode(DBCampo($q, "Finalizado" ));
	$Oficina = utf8_encode(DBCampo($q, "Oficina" ));
	$AreaSolicitante = utf8_encode(DBCampo($q, "AreaSolicitante" ));
	$Planta = utf8_encode(DBCampo($q, "Planta" ));
	$Actividad = utf8_encode(DBCampo($q, "Actividad" ));
	$OFICINAS_AREA = explode(',', utf8_encode(DBCampo($q, "OFICINAS_AREA" )));
	$OFICINAS_PLANTA = explode(',', utf8_encode(DBCampo($q, "OFICINAS_PLANTA" )));
	$OFICINAS_ACTIVIDAD = explode(',', utf8_encode(DBCampo($q, "OFICINAS_ACTIVIDAD" )));
	DBFree($q);
	
	$correcto = true;
	if($Finalizado == 1){
		
		//Añadir el campo actividad, que es obligatorio y puede no venir informado desde el bot
		if ($correcto && in_array($Oficina, $OFICINAS_ACTIVIDAD) && $Actividad == '-') {
			echo "Para cerrar la tarea debe informar la actividad.";
			$correcto = false;
		}
		
		if ($correcto && in_array($Oficina, $OFICINAS_AREA) && $AreaSolicitante == '-') {
			echo "Para cerrar la tarea debe informar el área del solicitante.";
			$correcto = false;
		}
		
		if ($correcto && in_array($Oficina, $OFICINAS_PLANTA) && $Planta == '-') {
			echo "Para cerrar la tarea debe informar la planta del solicitante.";
			$correcto = false;
		}
		
	}
	
	if($correcto){
		$q = " UPDATE [Tareas y Proyectos] SET Estado = '$estadoTarea' WHERE Id = $tarea AND Oficina = '$Oficina' ";
		DBSelect ( utf8_decode ( $q ) );
	}
	
} else{

	$Id        =$_GET["Id"];
	$Usuario   =$_GET["usuario"];
	$Fecha     =$_GET["Fecha"];
	$Asignado_Nombre     =$_GET["Asignado_Nombre"];
	$Observaciones     =$_GET["Observaciones"];
	$Comentario_Adicional     =$_GET["Comentario_Adicional"];
	$Situacion     =$_GET["Situacion"];
	$Tiempo_Estimado     =$_GET["Tiempo_Estimado"];
	$No_Planificado = $_GET["No_Planificado"];
	$Prioridad = $_GET["Prioridad"];
	
	// limpieza de textos
	$Observaciones = strip_tags($Observaciones);
	$Observaciones = str_replace("\'", "", ($Observaciones));
	$Observaciones = str_replace('\"', "", ($Observaciones));
	
	$Comentario_Adicional = strip_tags($Comentario_Adicional);
	$Comentario_Adicional = str_replace("\'", "", ($Comentario_Adicional));
	$Comentario_Adicional = str_replace('\"', "", ($Comentario_Adicional));
	
	
	
	$query = "
		SET DATEFORMAT dmy; 	
		UPDATE [GestionPDT].[dbo].[Planificador]
		SET
			Fecha = '$Fecha'
			,Anyo = dbo.ISOyear('$Fecha')
			,Mes = DATEPART(MONTH,'$Fecha')
			,Semana = DATEPART(ISO_WEEK,'$Fecha')
			,Observaciones = '$Observaciones'
			,[Comentario_Adicional] = '$Comentario_Adicional'
			,[Asignado_Nombre] = '$Asignado_Nombre'
			,[Asignado_Usuario] = dbo.SF_OBTENER_USUARIO_DOMINIO('$Asignado_Nombre')
			,Fecha_Cambio = GETDATE()
			,Usuario_Cambio = dbo.SF_OBTENER_USUARIO_DOMINIO('$Asignado_Nombre')
			,Situacion = '$Situacion'
			,Tiempo_Estimado = '$Tiempo_Estimado'
			,[No_Planificado] = '$No_Planificado'
			,[Prioridad] = '$Prioridad'
		WHERE Id = $Id		;
	";
	//die($query);
	
	/*$query = */DBSelect(utf8_decode($query));
	
	
}
DBClose();
?>
