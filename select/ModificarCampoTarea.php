<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id          =$_GET['id'];
$dato        =$_GET['dato'];
$usuario     =$_GET['usuario'];
$campo       =$_GET['campo'];
$guardar_log ='';

DBConectar($GLOBALS["cfg_DataBase"]);

//SIN TABULAR, NO SE USA, RESPALDO
$guardar_log_OLD = "INSERT INTO [Log_Tareas_y_Proyectos] ([Id],[Título],[Categoría],[Tipo],[Descripción],[Solicitado] ,[Fecha Solicitud],[Departamento Solicitud],[Programa Solicitud]
				,[Departamento Origen Fallo],[Programa Origen Fallo],[Fecha alta],[Fecha inicio],[Fecha fin],[Prioridad],[Fecha objetivo],[Horas reales],[Estado],[Valoración],[% completado]
				,[Asignado a],[Tarea / Proyecto],[Referencia],[Resultado final],[Usuario],[Fecha intermedio],[Horas estimadas],[Subcategoría],[BAP],[Motivo bap],[Control],[Fecha Necesidad]
				,[Cola],[TítuloAnterior],[CategoríaAnterior],[TipoAnterior],[DescripciónAnterior],[SolicitadoAnterior],[Fecha SolicitudAnterior],[Departamento SolicitudAnterior],[Programa SolicitudAnterior]
				,[Departamento Origen FalloAnterior],[Programa Origen FalloAnterior],[Fecha altaAnterior],[Fecha inicioAnterior],[Fecha finAnterior],[PrioridadAnterior],[Fecha objetivoAnterior],[Horas realesAnterior]
				,[EstadoAnterior],[ValoraciónAnterior],[% completadoAnterior],[Asignado aAnterior],[Tarea / ProyectoAnterior],[ReferenciaAnterior],[Resultado finalAnterior],[UsuarioAnterior],[Fecha intermedioAnterior]
				,[Horas estimadasAnterior],[SubcategoríaAnterior]           ,[BAPAnterior],[Motivo bapAnterior],[ControlAnterior],[Fecha NecesidadAnterior],[ColaAnterior],[AporteOrganizacion],[UsuarioCambia],[FechaCambio])
				SELECT [Id],[Título],[Categoría],[Tipo],[Descripción],[Solicitado],[Fecha Solicitud],[Departamento Solicitud],[Programa Solicitud],[Departamento Origen Fallo],[Programa Origen Fallo]
				,[Fecha alta],[Fecha inicio],[Fecha fin],[Prioridad],[Fecha objetivo],[Horas reales],[Estado],[Valoración],[% completado],[Asignado a],[Tarea / Proyecto],[Referencia],[Resultado final]
				,[Usuario],[Fecha intermedio],[Horas estimadas],[Subcategoría],[BAP],[Motivo bap],[Control],[Fecha Necesidad],[Cola]      ,[Título],[Categoría],[Tipo],[Descripción]      ,[Solicitado]
				,[Fecha Solicitud]      ,[Departamento Solicitud]      ,[Programa Solicitud]      ,[Departamento Origen Fallo]      ,[Programa Origen Fallo]      ,[Fecha alta]
				,[Fecha inicio]      ,[Fecha fin]      ,[Prioridad]      ,[Fecha objetivo]      ,[Horas reales]      ,[Estado]      ,[Valoración]      ,[% completado]      ,[Asignado a]
				,[Tarea / Proyecto]      ,[Referencia]      ,[Resultado final]      ,[Usuario]      ,[Fecha intermedio]      ,[Horas estimadas]      ,[Subcategoría]            ,[BAP]
				,[Motivo bap]      ,[Control]      ,[Fecha Necesidad]      ,[Cola]      ,[AporteOrganizacion],'".$usuario."'      ,GETDATE()  FROM [Tareas y Proyectos]
				where id = ".$id." ";
				
$guardar_log ="
		INSERT INTO [Log_Tareas_y_Proyectos] (
			[Id]
			,[Título]
			,[Categoría]
			,[Tipo]
			,[Descripción]
			,[Solicitado]
			,[Fecha Solicitud]
			,[Departamento Solicitud]
			,[Programa Solicitud]
			,[Departamento Origen Fallo]
			,[Programa Origen Fallo]
			,[Fecha alta]
			,[Fecha inicio]
			,[Fecha fin]
			,[Prioridad]
			,[Fecha objetivo]
			,[Horas reales]
			,[Estado]
			,[Valoración]
			,[% completado]
			,[Asignado a]
			,[Tarea / Proyecto]
			,[Referencia]
			,[Resultado final]
			,[Usuario]
			,[Fecha intermedio]
			,[Horas estimadas]
			,[Subcategoría]
			,[BAP]
			,[Motivo bap]
			,[Control]
			,[Fecha Necesidad]
			,[Cola]
			,[TítuloAnterior]
			,[CategoríaAnterior]
			,[TipoAnterior]
			,[DescripciónAnterior]
			,[SolicitadoAnterior]
			,[Fecha SolicitudAnterior]
			,[Departamento SolicitudAnterior]
			,[Programa SolicitudAnterior]
			,[Departamento Origen FalloAnterior]
			,[Programa Origen FalloAnterior]
			,[Fecha altaAnterior]
			,[Fecha inicioAnterior]
			,[Fecha finAnterior]
			,[PrioridadAnterior]
			,[Fecha objetivoAnterior]
			,[Horas realesAnterior]
			,[EstadoAnterior]
			,[ValoraciónAnterior]
			,[% completadoAnterior]
			,[Asignado aAnterior]
			,[Tarea / ProyectoAnterior]
			,[ReferenciaAnterior]
			,[Resultado finalAnterior]
			,[UsuarioAnterior]
			,[Fecha intermedioAnterior]
			,[Horas estimadasAnterior]
			,[SubcategoríaAnterior]
			,[BAPAnterior]
			,[Motivo bapAnterior]
			,[ControlAnterior]
			,[Fecha NecesidadAnterior]
			,[ColaAnterior]
			,[AporteOrganizacion]
			,[UsuarioCambia]
			,[FechaCambio])
		SELECT 
			[Id]
			,[Título]
			,[Categoría]
			,[Tipo]
			,[Descripción]
			,[Solicitado]
			,[Fecha Solicitud]
			,[Departamento Solicitud]
			,[Programa Solicitud]
			,[Departamento Origen Fallo]
			,[Programa Origen Fallo]
			,[Fecha alta]
			,[Fecha inicio]
			,[Fecha fin]
			,[Prioridad]
			,[Fecha objetivo]
			,[Horas reales]
			,[Estado]
			,[Valoración]
			,[% completado]
			,[Asignado a]
			,[Tarea / Proyecto]
			,[Referencia]
			,[Resultado final]
			,[Usuario]
			,[Fecha intermedio]
			,[Horas estimadas]
			,[Subcategoría]
			,[BAP]
			,[Motivo bap]
			,[Control]
			,[Fecha Necesidad]
			,[Cola],[Título]
			,[Categoría]
			,[Tipo]
			,[Descripción]
			,[Solicitado]
			,[Fecha Solicitud]      
			,[Departamento Solicitud]      
			,[Programa Solicitud]      
			,[Departamento Origen Fallo]      
			,[Programa Origen Fallo]      
			,[Fecha alta]
			,[Fecha inicio]      
			,[Fecha fin]      
			,[Prioridad]      
			,[Fecha objetivo]      
			,[Horas reales]      
			,[Estado]      
			,[Valoración]      
			,[% completado]      
			,[Asignado a]
			,[Tarea / Proyecto]      
			,[Referencia]      
			,[Resultado final]      
			,[Usuario]      
			,[Fecha intermedio]      
			,[Horas estimadas]      
			,[Subcategoría]            
			,[BAP]
			,[Motivo bap]      
			,[Control]      
			,[Fecha Necesidad]      
			,[Cola]      
			,[AporteOrganizacion]
			,'".$usuario."'      
			,GETDATE()  
		FROM [Tareas y Proyectos]
		WHERE id = ".$id." ";

if ($campo=='Fecha objetivo')
{
	if($dato=='')
	{
		$guardar_log=$guardar_log." UPDATE [Log_Tareas_y_Proyectos] 
									SET [".$campo."] = null WHERE Id = ".$id." AND UsuarioCambia = '".$usuario."' AND FechaCambio = (SELECT TOP 1 FechaCambio
									FROM [Log_Tareas_y_Proyectos] where UsuarioCambia = '".$usuario."' AND Id = ".$id." ORDER BY FechaCambio desc) ";
		$insert=DBSelect(utf8_decode($guardar_log." UPDATE [Tareas y Proyectos] SET [".$campo."] = null WHERE Id = ".$id." "));	
	}
	else
	{
		$guardar_log=$guardar_log." UPDATE [Log_Tareas_y_Proyectos] SET [".$campo."] = '".$dato."' WHERE Id = ".$id." AND UsuarioCambia = '".$usuario."' AND FechaCambio = (SELECT TOP 1 FechaCambio
									FROM [Log_Tareas_y_Proyectos] where UsuarioCambia = '".$usuario."' AND Id = ".$id." ORDER BY FechaCambio desc) ";
		$insert=DBSelect(utf8_decode($guardar_log." UPDATE [Tareas y Proyectos] SET [".$campo."] = '".str_replace("'", "''", ($dato))."'
		WHERE Id = ".$id." "));	
	}
}
else
{	
	if ($campo=='AporteOrganizacionasasasas')
	{
		//$Aportes=array(0=>"N/A",1=>"Bajo",2=>"Medio",3=>"Alto");
		//$dato=array_search($dato,$Aportes);
		switch ( $dato ) {
			case "Bajo" :
				$dato = 1;
				break;
			case "Medio" :
				$dato = 2;
				break;
			case "Alto" :
				$dato = 3;
				break;
			default :
				$dato = 0;
		}
	}
	if ($campo=='Tarea / Proyecto')
	{
		$pieces = explode(" ", $dato);
		$dato   = $pieces[0]; 
	}
	if ($campo=='Estrategico')
	{
		if($dato=='Si')
		{
			$dato = '1';
		}
		else
		{
			$dato = '0';
		}		
	}	
		
	if($dato=='NULL')
		$valor_dato=" NULL ";
	else
		$valor_dato=" '".str_replace("'", "''", ($dato))."'";

	$guardar_log=$guardar_log."UPDATE [Log_Tareas_y_Proyectos] SET [".$campo."] =".$valor_dato." WHERE Id = ".$id." AND UsuarioCambia = '".$usuario."' AND FechaCambio = (SELECT TOP 1 FechaCambio
								FROM [Log_Tareas_y_Proyectos] where UsuarioCambia = '".$usuario."' AND Id = ".$id." ORDER BY FechaCambio desc) ";
	$insert=DBSelect(utf8_decode($guardar_log." UPDATE [Tareas y Proyectos] SET [".$campo."] = ".$valor_dato."	WHERE Id = ".$id." "));
}
$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_HISTORICO] '".$usuario."',".$id.",5"));
if($campo=='Título')
{
	$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_PALABRAS] ".$id.",1,'".str_replace("'", "''", ($dato))."' "));
}
DBClose();
echo '';
?>