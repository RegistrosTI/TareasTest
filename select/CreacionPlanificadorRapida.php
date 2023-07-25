<?php
	include_once "../conf/config_filtros.php";
	include_once "../php/funciones.php";
	include_once "../conf/config.php";
	include_once "../conf/config_MENU.php";
	include_once "../../soporte/DB.php";
	include_once "../../soporte/funcionesgenerales.php";

	DBConectar($GLOBALS["cfg_DataBase"]);

	
	$idtarea = $_POST['idtarea'];
    $q=DBSelect(utf8_decode ("SELECT 
								[Asignado a] as asignadonombre
								,dbo.SF_OBTENER_USUARIO_DOMINIO([Asignado a]) as asignadousuario
								,Usuario as usuariocreacion
								 FROM [Tareas y Proyectos] 
							 WHERE Id =".$idtarea));	
	
	$Asignado = utf8_encode(DBCampo($q, "asignadonombre" ));
	$AsignadoUsuario = utf8_encode(DBCampo($q, "asignadousuario" ));
	$Usuario = utf8_encode(DBCampo($q, "usuariocreacion" ));
	DBFree($q);
	
	
	$q=DBSelect(utf8_decode ("SELECT COUNT(*) as encontrado FROM [GestionPDT].[dbo].[Planificador] WHERE Tarea =".$idtarea." AND estado = 'Activo' AND No_Planificado = 'SI'"));	
	$planificacionencontrada = DBCampo($q, "encontrado" );
	DBFree($q);
	
	$comprobacion =1;
	
	
	if($Asignado ==  '' || $Asignado ==  ' '){$comprobacion = 0;  $motivo = 1;}
	if($planificacionencontrada > 0){$comprobacion =  0;  $motivo = 2;}
	
	if($comprobacion == 1){
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
											$idtarea
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
			$motivo = 3;
	}
	
	echo $motivo;
?>