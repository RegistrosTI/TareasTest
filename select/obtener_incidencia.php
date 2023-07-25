<?php
// UTF ñáç
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

// RECIBIMOS PARAMETROS
// 1 SI EXISTE EN LA TABLA USUARIOS
if ( isset ( $_GET [ 'id' ] ) ) {
	$id_pantalla = $_GET [ 'id' ];
} else {
	$id_pantalla = '-1';
}

if ( isset ( $_GET [ 'incidencia' ] ) ) {
	$incidencia = $_GET [ 'incidencia' ];
} else {
	$incidencia = '-1';
}

if ( isset ( $_GET [ 'usuario' ] ) ) {
	$usuario = $_GET [ 'usuario' ];
} else {
	$usuario = '';
}
// FIN RECIBIMOS PARAMETROS
//var_dump($id_pantalla, $usuario, $incidencia); string(1) "1" string(15) "herman.martinez" string(5) "42874"

$Registros = 0;
if ( $id_pantalla == 1 ) {
	$multiportal_rol = '1';
	$readonly = '';
	$disabled = '';
}
if ( $id_pantalla ==  - 1 ) {
	$multiportal_rol = '-1';
	$readonly = ' readonly="readonly" ';
	$disabled = ' disabled="disabled" ';
}

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

//$ESTRATEGICO_checked = '  ';
//$REQUIERE_DOCUMENTACION_checked = '  ';

$insert = "
	SELECT 	 
		(SELECT COUNT(*) FROM [tarea_pda] where tarea = $incidencia) as cantidad_pda
		,(SELECT COUNT(*) FROM [Feedback_Respuestas]  where tarea = $incidencia) as cantidad_feedback
		,(SELECT COUNT(*) FROM [Feedback_Respuestas]  where tarea = $incidencia and [UsuarioRespuesta] is not null) as cantidad_feedback_respondidas
		,(SELECT COUNT(*) FROM [folder]    where tarea = $incidencia) as cantidad_folder 
		,(SELECT COUNT(*) FROM [Planificador]    where tarea = $incidencia and estado = 'Activo' AND (No_Planificado = 'NO' OR No_Planificado IS NULL OR No_Planificado = '')) as cantidad_planificador
		,(SELECT COUNT(*) FROM [Planificador]    where tarea = $incidencia and estado = 'Activo' and [No_Planificado]= 'SI') as NOPLANIFICADAS
		,(SELECT COUNT(*) FROM [Colaboradores]   where Id_Tarea = $incidencia and estado = 'Activo') as cantidad_colaboradores
		,(SELECT VerHoraAltaTarea FROM Configuracion_Oficinas WHERE Oficina = TyP.Oficina) AS VER_HORA_ALTA
		,CAST(ISNULL((SELECT TOP 1 [Título] FROM [Tareas y Proyectos] where id = TyP.[Tarea / Proyecto]),'') as VARCHAR(MAX)) AS TAREA_PROYECTO_DESCRIPCION
		,ISNULL([Tarea / Proyecto],-1) AS TAREA_PROYECTO
		,CAST(Cola as varchar(255)) AS COLA
		,Control AS CONTROL
		,CASE WHEN [Fecha intermedio] IS NULL THEN 0 ELSE 1 END AS COLOR
		,CAST(Descripción as text) AS DESCRIPCION
		,CAST(Usuario as varchar(255)) AS USUARIO
		,CAST(Referencia as varchar(20)) AS REFERENCIA
		,[% Completado] AS PORCENTAJE
		,CAST([Departamento Solicitud] as varchar(100)) AS DEPARTAMENTO
		,CAST([Programa Solicitud] as varchar(100)) AS PROGRAMA
		,BAP AS BAP
		,[Horas Estimadas] AS HORASESTIMADAS
		,CONVERT(varchar(20), TyP.[Fecha objetivo],105) AS FECHAOBJETIVO
		,CONVERT(varchar(20), TyP.[Fecha Necesidad],105) AS FECHANECESIDAD
		,CONVERT(varchar(20), TyP.[Fecha Solicitud],105) AS FECHASOLICITUD
		,CAST(Título as varchar(255)) AS TITULO
		,CAST(Valoración as varchar(255)) AS VALORACION
		,CAST(TyP.Estado as varchar(255)) AS ESTADO
		,CAST(Prioridad as varchar(255)) AS PRIORIDAD
		,CAST(Solicitado as varchar(255)) AS SOLICITADO
		,CAST(Tipo as varchar(255)) AS TIPO
		,CAST(Categoría as varchar(255)) AS CATEGORIA
		,CAST([Motivo bap] as varchar(255)) AS MOTIVO_BAP
		,CAST([Subcategoría] as varchar(255)) AS SUBCATEGORIA
		,CAST(ISNULL([Criticidad],'') as varchar(255)) AS CRITICIDAD
		,CAST(ISNULL([Asignado A],'') as varchar(255)) AS ASIGNADO
		,CONVERT(varchar(20), TyP.[Fecha alta],105) AS FECHA_ALTA
		,CONVERT(varchar(20), TyP.[Fecha alta],108) AS HORA_ALTA
		--,ISNULL([Estrategico],0) as ESTRATEGICO
		,ISNULL([Estrategico],'NO') as ESTRATEGICO
		,ISNULL([Evaluable],'NO')   as EVALUABLE
		--,ISNULL([Requiere_Documentacion],0) as Requiere_Documentacion
		,ISNULL([Requiere_Documentacion],'NO') as Requiere_Documentacion
		,AporteOrganizacion AS APORTEORGANIZACION
		,AporteEmpresa AS APORTEEMPRESA
		,AporteJefe AS APORTEJEFE
		,Actividad AS ACTIVIDAD
		,Oficina AS OFICINA
		,Objetivo AS OBJETIVO
		,Cliente AS CLIENTE
		,Proyecto AS PROYECTO
		,Evaluador AS EVALUADOR
		,AreaSolicitante AS AREASOLICITANTE
		,Planta AS PLANTA
		,CosteTarea AS COSTETAREA
		,DocumentacionCompleta AS DOCUMENTACIONCOMPLETA
		,ConsiderarBI AS CONSIDERARBI
		,[Ahorro] AS AHORRO
		,[TipoAhorro] AS TIPOAHORRO
		,[Destinatario] AS DESTINATARIO
		,[Teletrabajo] AS TELETRABAJO
	FROM [Tareas y Proyectos] TyP 
	WHERE Id = " . $incidencia . " 
	EXECUTE  [INICIAR_HISTORICO]  '" . $usuario . "' ," . $incidencia . " , 1000";
//die($insert);
$insert = DBSelect ( utf8_decode ( $insert ) );

$CONTROL = DBCampo ( $insert , "CONTROL" );
$EVALUABLE   = DBCampo ( $insert , "EVALUABLE" );
$ESTRATEGICO = DBCampo ( $insert , "ESTRATEGICO" );
$REQUIERE_DOCUMENTACION = DBCampo ( $insert , "Requiere_Documentacion" );
$TAREA_PROYECTO = DBCampo ( $insert , "TAREA_PROYECTO" );
//$IMPORTE_ESTIMADO = DBCampo ( $insert , "IMPORTE_ESTIMADO" );
$TAREA_PROYECTO_DESCRIPCION = DBCampo ( $insert , "TAREA_PROYECTO_DESCRIPCION" );
$COLOR = DBCampo ( $insert , "COLOR" );
$DESCRIPCION = DBCampo ( $insert , "DESCRIPCION" );
$USUARIO = DBCampo ( $insert , "USUARIO" );
$REFERENCIA = DBCampo ( $insert , "REFERENCIA" );
$PORCENTAJE = DBCampo ( $insert , "PORCENTAJE" );
$PROGRAMA = DBCampo ( $insert , "PROGRAMA" );
$DEPARTAMENTO = DBCampo ( $insert , "DEPARTAMENTO" );
$BAP = DBCampo ( $insert , "BAP" );
$HORASESTIMADAS = DBCampo ( $insert , "HORASESTIMADAS" );
$TITULO = DBCampo ( $insert , "TITULO" );
$cantidad_pda = DBCampo ( $insert , "cantidad_pda" );
$cantidad_feedback = DBCampo ( $insert , "cantidad_feedback" );
$cantidad_feedback_respondidas = DBCampo ( $insert , "cantidad_feedback_respondidas" );
$cantidad_folder = DBCampo ( $insert , "cantidad_folder" );
$cantidad_planificador = DBCampo ( $insert , "cantidad_planificador" );
$cantidad_colaboradores = DBCampo ( $insert , "cantidad_colaboradores" );
$SOLICITADO = DBCampo ( $insert , "SOLICITADO" );
$DESTINATARIO = DBCampo ( $insert , "DESTINATARIO" );
$FECHA_ALTA = DBCampo ( $insert , "FECHA_ALTA" );
$HORA_ALTA = DBCampo ( $insert , "HORA_ALTA" );
$FECHANECESIDAD = DBCampo ( $insert , "FECHANECESIDAD" );
$MOTIVO_BAP = DBCampo ( $insert , "MOTIVO_BAP" );
$FECHAOBJETIVO = DBCampo ( $insert , "FECHAOBJETIVO" );
$FECHASOLICITUD = DBCampo ( $insert , "FECHASOLICITUD" );
$ASIGNADO = DBCampo ( $insert , "ASIGNADO" );
$TIPO = DBCampo ( $insert , "TIPO" );
$CATEGORIA = DBCampo ( $insert , "CATEGORIA" );
$SUBCATEGORIA = DBCampo ( $insert , "SUBCATEGORIA" );
$VALORACION = DBCampo ( $insert , "VALORACION" );
$PRIORIDAD = DBCampo ( $insert , "PRIORIDAD" );
$ESTADO = DBCampo ( $insert , "ESTADO" );
$COLA = DBCampo ( $insert , "COLA" );
$APORTEORGANIZACION = DBCampo ( $insert , "APORTEORGANIZACION" );
$APORTEEMPRESA = DBCampo ( $insert , "APORTEEMPRESA" );
$APORTEJEFE = DBCampo ( $insert , "APORTEJEFE" );
$OFICINA = DBCampo ( $insert , "OFICINA" );
$OBJETIVO = DBCampo ( $insert , "OBJETIVO" );
$CLIENTE = DBCampo ( $insert , "CLIENTE" );
$PROYECTO = DBCampo ( $insert , "PROYECTO" );
$EVALUADOR = DBCampo ( $insert , "EVALUADOR" );
$ACTIVIDAD = DBCampo ( $insert , "ACTIVIDAD" );
$CRITICIDAD = DBCampo ( $insert , "CRITICIDAD" );
$AREASOLICITANTE= DBCampo ( $insert , "AREASOLICITANTE" );
$PLANTA = DBCampo ( $insert , "PLANTA" );
$COSTETAREA = DBCampo ( $insert , "COSTETAREA" );
$NOPLANIFICADAS = DBCampo ( $insert, "NOPLANIFICADAS");
$VER_HORA_ALTA = DBCampo ( $insert, "VER_HORA_ALTA");
$DOCUMENTACIONCOMPLETA = DBCampo ( $insert, "DOCUMENTACIONCOMPLETA");
$CONSIDERARBI = DBCampo ( $insert, "CONSIDERARBI");
$AHORRO = DBCampo ( $insert , "AHORRO" );
$TIPOAHORRO = DBCampo ( $insert , "TIPOAHORRO" );
$TELETRABAJO = DBCampo ( $insert, "TELETRABAJO");

if( $cantidad_feedback != 0 && $cantidad_feedback_respondidas == $cantidad_feedback){
	$cantidad_feedback = -1;
}

if ( $COLOR == '1' ) {
	$IMAGEN_TAREA = 'red_circle.png';
} else {
	$IMAGEN_TAREA = 'green_circle.png';
}
if ( $TAREA_PROYECTO != "-1" ) {
	$TAREA_PROYECTO_DESCRIPCION = $TAREA_PROYECTO . ' - ' . $TAREA_PROYECTO_DESCRIPCION;
} else {
	$TAREA_PROYECTO_DESCRIPCION = '';
}

// ++ RECUPERAR CAMPOS DE TIPO FORMULARIO => $opciones_ocultar
$multiportal_tipo = 'FORMULARIO';
$multiportal_ventana = curPageName ();
include "../visibilidad/visibilidad_config.php";
// -- RECUPERAR CAMPOS DE TIPO FORMULARIO => $opciones_ocultar

// ++ OBTENER TODA LA LISTA DE TIPOS
$tipos_lista = Get_Lista_de_Tipos ( $OFICINA );
// -- OBTENER TODA LA LISTA DE TIPOS

$tabla = array ();
foreach ( $opciones_ocultar as $opcion ) {
	if ( $opcion [ 'Ocultar' ] == '0' ) {
		/*  var_dump($opciones_ocultar);
		 die(); */
		$readonly = ' readonly="readonly" ';
		$disabled = ' disabled="disabled" ';
		
		if ( $opcion [ 'Campo' ] == 'Botonera' ) {
			if ( BuscarEditarUTF8 ( ( 'Botonera' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Botonera' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Botonera' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td colspan="' . BuscarValor ( ( 'Botonera' ) , $opciones_ocultar , 'Colspan' ) . '">' . Get_Botonera ( $incidencia , $opciones_ocultar , $cantidad_pda , $cantidad_feedback , $cantidad_folder , $cantidad_planificador, $cantidad_colaboradores, $NOPLANIFICADAS ) . '</td>';
		}
		if ( $opcion [ 'Campo' ] == 'Porcentaje Completado' ) {
			if ( BuscarEditarUTF8 ( ( 'Porcentaje Completado' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Porcentaje Completado' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Porcentaje Completado' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td colspan="' . BuscarValor ( ( 'Porcentaje Completado' ) , $opciones_ocultar , 'Colspan' ) . '"><div id="porcentaje_visible_coletilla" class="porcentaje_visible_coletilla">' . BuscarValor ( ( 'Porcentaje Completado' ) , $opciones_ocultar , 'Coletilla' ) . '<div id="porcentaje_visible" class="porcentaje_visible_cantidad"></div><input type="hidden" name="porcentaje" align="center" maxlength="5" id="porcentaje" value="' . utf8_encode ( $PORCENTAJE ) . '"/></td>';
		}
		if ( $opcion [ 'Campo' ] == 'Slider' ) {
			if ( BuscarEditarUTF8 ( ( 'Slider' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Slider' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Slider' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td colspan="' . BuscarValor ( ( 'Slider' ) , $opciones_ocultar , 'Colspan' ) . '"><div id="slider"></div></td>';
		}
		
		if ( $opcion [ 'Campo' ] == 'Destinatario' ) { // dis
			if ( BuscarEditarUTF8 ( ( 'Destinatario' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Destinatario' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Destinatario' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Destinatario' ) , $opciones_ocultar , 'Colspan' ) . '"><input id="autocompletedestinatario" ' . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' ' . $readonly . $disabled . ' name="autocompletedestinatario" spb_submit="1" spb_disparador="' . BuscarValor ( ( 'Destinatario' ) , $opciones_ocultar , 'Disparador' ) . '" maxlength="' . BuscarValor ( ( 'Destinatario' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Destinatario' ) , $opciones_ocultar , 'Width' ) . 'px;" value="' . utf8_encode ( $DESTINATARIO ) . '">' . BuscarValor ( ( 'Destinatario' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
		
		if ( $opcion [ 'Campo' ] == 'Solicitado' ) { // dis
			if ( BuscarEditarUTF8 ( ( 'Solicitado' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Solicitado' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Solicitado' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Solicitado' ) , $opciones_ocultar , 'Colspan' ) . '"><input id="autocompletesolicitado" ' . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' ' . $readonly . $disabled . ' name="autocompletesolicitado" spb_submit="1" spb_disparador="' . BuscarValor ( ( 'Solicitado' ) , $opciones_ocultar , 'Disparador' ) . '" maxlength="' . BuscarValor ( ( 'Solicitado' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Solicitado' ) , $opciones_ocultar , 'Width' ) . 'px;" value="' . utf8_encode ( $SOLICITADO ) . '">' . BuscarValor ( ( 'Solicitado' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}

		if ( $opcion [ 'Campo' ] == 'Usuario' ) { // dis
			if ( BuscarEditarUTF8 ( ( 'Usuario' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Usuario' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Usuario' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Usuario' ) , $opciones_ocultar , 'Colspan' ) . '"><input id="autocompleteusuario" ' . $readonly . $disabled . ' spb_submit="1" name="autocompleteusuario" ' . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' maxlength="' . BuscarValor ( ( 'Usuario' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Usuario' ) , $opciones_ocultar , 'Width' ) . 'px;" value="' . utf8_encode ( $USUARIO ) . '">' . BuscarValor ( ( 'Usuario' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}

		if ( $opcion [ 'Campo' ] == 'Evaluador' ) { // dis
			if ( BuscarEditarUTF8 ( ( 'Evaluador' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$mensaje_ayuda = "<image src='imagenes/info.png' alt='Ayuda Aportes' width='19' height='19' style='cursor:pointer' onClick='mensaje(\"Información Evaluador\",\"Este campo ha de informarse solo en caso de que la persona que tenga que evaluar esta tarea sea externa al departamento. <br><br>En caso de informar un evaluador externo, normalmente este debería ser el solicitante.\",\"info.png\")' />";
			$tabla [ intval ( BuscarValor ( ( 'Evaluador' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Evaluador' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Evaluador' ) , $opciones_ocultar , 'Colspan' ) . '"><input id="autocompleteevaluador" ' . $readonly . $disabled . ' spb_submit="1" name="autocompleteevaluador" ' . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' maxlength="' . BuscarValor ( ( 'Evaluador' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Evaluador' ) , $opciones_ocultar , 'Width' ) . 'px;" value="' . utf8_encode ( $EVALUADOR ) . '">' . BuscarValor ( ( 'Evaluador' ) , $opciones_ocultar , 'Coletilla' ) . $mensaje_ayuda . '</td>';
		}
		
		if ( $opcion [ 'Campo' ] == 'Tarea/Proyecto' ) { // dis
			if ( BuscarEditarUTF8 ( ( 'Tarea/Proyecto' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Tarea/Proyecto' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Tarea/Proyecto' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Tarea/Proyecto' ) , $opciones_ocultar , 'Colspan' ) . '"><input id="autocomplete" ' . $readonly . $disabled . ' spb_submit="1" name="autocomplete" ' . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' maxlength="' . BuscarValor ( ( 'Tarea/Proyecto' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Tarea/Proyecto' ) , $opciones_ocultar , 'Width' ) . '%;" value="' . utf8_encode ( $TAREA_PROYECTO_DESCRIPCION ) . '">' . BuscarValor ( ( 'Tarea/Proyecto' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
		if ( $opcion [ 'Campo' ] == 'Departamento' ) {
			if ( BuscarEditarUTF8 ( ( 'Departamento' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Departamento' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Departamento' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Departamento ( $opciones_ocultar , $opcion [ 'Campo' ] , $DEPARTAMENTO , 'selectdepartamento' );
		}
		if ( $opcion [ 'Campo' ] == 'Programa' ) {
			if ( BuscarEditarUTF8 ( ( 'Programa' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Programa' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Programa' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Programa ( $opciones_ocultar , $opcion [ 'Campo' ] , $PROGRAMA , $DEPARTAMENTO , 'selectprograma' );
		}
		if ( $opcion [ 'Campo' ] == 'Cola' ) {
			if ( BuscarEditarUTF8 ( ( 'Cola' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Cola' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Cola' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Cola ( $opciones_ocultar , $opcion [ 'Campo' ] , $COLA , 'selectcola' );
		}
		
		if ( $opcion [ 'Campo' ] == 'Usuario Portal' ) {
			if ( BuscarEditarUTF8 ( ( 'Usuario Portal' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			// $tabla [ intval ( BuscarValor ( ( 'Usuario Portal' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Usuario Portal' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Lista_Usuarios_Departamento ( $opciones_ocultar , $opcion [ 'Campo' ] , $ASIGNADO , 'selectusuariosti' );
			   $tabla [ intval ( BuscarValor ( ( 'Usuario Portal' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Usuario Portal' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Usuario Portal' ) , $opciones_ocultar , 'Colspan' ) . '"><input id="autocompleteasignado" ' . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' ' . $readonly . $disabled . ' name="autocompleteasignado" spb_submit="1" spb_disparador="' . BuscarValor ( ( 'Usuario Portal' ) , $opciones_ocultar , 'Disparador' ) . '" maxlength="' . BuscarValor ( ( 'Usuario Portal' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Usuario Portal' ) , $opciones_ocultar , 'Width' ) . 'px;" value="' . utf8_encode ( $ASIGNADO ) . '">' . BuscarValor ( ( 'Usuario Portal' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
		if ( $opcion [ 'Campo' ] == 'Tipo' ) {
			if ( BuscarEditarUTF8 ( ( 'Tipo' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Tipo' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Tipo' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 0 ] , $opcion [ 'Campo' ] , $TIPO , 'selecttipo' );
		}
		if ( $opcion [ 'Campo' ] == 'Oficina' ) {
			if ( BuscarEditarUTF8 ( ( 'Oficina' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Oficina' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Oficina' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Oficinas ( $opciones_ocultar , $tipos_lista [ 0 ] , $opcion [ 'Campo' ] , $OFICINA , 'selectoficina' , $usuario );
		}
		if ( $opcion [ 'Campo' ] == 'Categoria' ) {
			if ( BuscarEditarUTF8 ( ( 'Categoria' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Categoria' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Categoria' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 1 ] , $opcion [ 'Campo' ] , $CATEGORIA , 'selectcategoria' );
		}

		if ( $opcion [ 'Campo' ] == 'AporteOrganizacion' ) {
			if ( BuscarEditarUTF8 ( ( 'AporteOrganizacion' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'AporteOrganizacion' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'AporteOrganizacion' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 102 ] , $opcion [ 'Campo' ] , $APORTEORGANIZACION , 'selectaporte' );
		}

		if ( $opcion [ 'Campo' ] == 'AporteEmpresa' ) {
			if ( BuscarEditarUTF8 ( ( 'AporteEmpresa' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'AporteEmpresa' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'AporteEmpresa' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 103 ] , $opcion [ 'Campo' ] , $APORTEEMPRESA , 'selectaporteempresa' );
		}

		if ( $opcion [ 'Campo' ] == 'AporteJefe' ) {
			if ( BuscarEditarUTF8 ( ( 'AporteJefe' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'AporteJefe' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'AporteJefe' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 104 ] , $opcion [ 'Campo' ] , $APORTEJEFE , 'selectaportejefe' );
		}
		
		if ( $opcion [ 'Campo' ] == 'Subcategoria' ) { // SERVICIO
			if ( BuscarEditarUTF8 ( ( 'Subcategoria' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Subcategoria' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Subcategoria' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 2 ] , $opcion [ 'Campo' ] , $SUBCATEGORIA , 'selectsubcategoria' );
		}
		
		if ( $opcion [ 'Campo' ] == 'Actividad' ) {
			if ( BuscarEditarUTF8 ( ( 'Actividad' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Actividad' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Actividad' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 106 ] , $opcion [ 'Campo' ] , $ACTIVIDAD , 'selectactividad' );
		}
		
		if ( $opcion [ 'Campo' ] == 'Valoracion' ) {
			if ( BuscarEditarUTF8 ( ( 'Valoracion' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Valoracion' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Valoracion' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 3 ] , $opcion [ 'Campo' ] , $VALORACION , 'selectvaloracion' );
		}
		if ( $opcion [ 'Campo' ] == 'Prioridad' ) {
			if ( BuscarEditarUTF8 ( ( 'Prioridad' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Prioridad' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Prioridad' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 4 ] , $opcion [ 'Campo' ] , $PRIORIDAD , 'selectprioridad' );
		}
		if ( $opcion [ 'Campo' ] == 'Estado' ) {
			$readonly = ' readonly="readonly" ';
			$disabled = ' disabled="disabled" ';
			if ( BuscarEditarUTF8 ( ( 'Estado' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Estado' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Estado' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 5 ] , $opcion [ 'Campo' ] , $ESTADO , 'selectestado' );
		}
		
		if ( $opcion [ 'Campo' ] == 'BAP' ) {
			if ( BuscarEditarUTF8 ( ( 'BAP' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'BAP' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'BAP' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 6 ] , $opcion [ 'Campo' ] , $BAP , 'selectbap' );
		}
		
		if ( $opcion [ 'Campo' ] == 'Criticidad' ) {
			if ( BuscarEditarUTF8 ( ( 'Criticidad' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			
			$tabla [ intval ( BuscarValor ( ( 'Criticidad' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Criticidad' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 107 ] , $opcion [ 'Campo' ] , $CRITICIDAD , 'selectcriticidad' );
		}
		
		/*
		if ( $opcion [ 'Campo' ] == 'Estrategico' ) { // dis
			$onclickfuncion = ' onclick="javascript: return false;" ';
			if ( BuscarEditarUTF8 ( ( 'Estrategico' ) , $opciones_ocultar , $nombre ) == true ) {
				$onclickfuncion = ' onClick="accede_estrategico(this);" ';
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Estrategico' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Estrategico' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Estrategico' ) , $opciones_ocultar , 'Colspan' ) . '"><input type="checkbox" ' . $readonly . $disabled . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' name="estrategico" value="' . $ESTRATEGICO . '" id="estrategico"  ' . $ESTRATEGICO_checked . ' ' . $onclickfuncion . '/>' . BuscarValor ( ( 'Estrategico' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
			if ( $opcion [ 'Campo' ] == 'Requiere Documentacion' ) { // dis
			$onclickfuncion = ' onclick="javascript: return false;" ';
			if ( BuscarEditarUTF8 ( ( 'Requiere Documentacion' ) , $opciones_ocultar , $nombre ) == true ) {
				$onclickfuncion = ' onClick="accede_Requiere_Documentacion(this);" ';
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Requiere Documentacion' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Requiere Documentacion' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Requiere Documentacion' ) , $opciones_ocultar , 'Colspan' ) . '"><input type="checkbox" ' . $readonly . $disabled . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' name="requiere_documentacion" value="' . $REQUIERE_DOCUMENTACION . '" id="requiere_documentacion" ' . $REQUIERE_DOCUMENTACION_checked . ' ' . $onclickfuncion . '/>' . BuscarValor ( ( 'Requiere Documentacion' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
		
		*/
		
		// T#26736 -- T#27305 (JUAN ANTONIO ABELLAN)  Cambio de varios tipos de campo + nuevo campo [30/10/2018]
		if ( $opcion [ 'Campo' ] == 'Estrategico' ) {
			if ( BuscarEditarUTF8 ( ( 'Estrategico' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Estrategico' ) , $opciones_ocultar , 'Fila' ) ) ] 
			[ intval ( BuscarValor ( ( 'Estrategico' ) , $opciones_ocultar , 'Columna' ) ) ]  
			= Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 12 ] , $opcion [ 'Campo' ] , $ESTRATEGICO , 'estrategico' );
		}
		
		if ( $opcion [ 'Campo' ] == 'Requiere Documentacion' ) {
			if ( BuscarEditarUTF8 ( ( 'Requiere Documentacion' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Requiere Documentacion' ) , $opciones_ocultar , 'Fila' ) ) ] 
			[ intval ( BuscarValor ( ( 'Requiere Documentacion' ) , $opciones_ocultar , 'Columna' ) ) ] 
			=Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 12 ] , $opcion [ 'Campo' ] , $REQUIERE_DOCUMENTACION , 'requiere_documentacion' );
		}
		
		if ( $opcion [ 'Campo' ] == 'AreaSolicitante' ) {
			if ( BuscarEditarUTF8 ( ( 'AreaSolicitante' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			
			$tabla [ intval ( BuscarValor ( ( 'AreaSolicitante' ) , $opciones_ocultar , 'Fila' ) ) ]
			[ intval ( BuscarValor ( ( 'AreaSolicitante' ) , $opciones_ocultar , 'Columna' ) ) ]
			= Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 14 ] , $opcion [ 'Campo' ] , $AREASOLICITANTE , 'selectareasolicitante' );
		}
		
		// T#28631 - JUAN ANTONIO ABELLAN -07/02/2019 (CCARRASCOSA)
		if ( $opcion [ 'Campo' ] == 'Planta' ) {
			if ( BuscarEditarUTF8 ( ( 'Planta' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			
			$tabla [ intval ( BuscarValor ( ( 'Planta' ) , $opciones_ocultar , 'Fila' ) ) ]
			[ intval ( BuscarValor ( ( 'Planta' ) , $opciones_ocultar , 'Columna' ) ) ]
			= Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 15 ] , $opcion [ 'Campo' ] , $PLANTA , 'selectplanta' );
		}
		
		if ( $opcion [ 'Campo' ] == 'CosteTarea'  && $ESTADO == 'Completado' ) { 
			if ( BuscarEditarUTF8 ( ( 'CosteTarea' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$COSTETAREA = str_replace ( "." , "," , $COSTETAREA );
			if($COSTETAREA == NULL){$COSTETAREA=0;}
			$tabla [ intval ( BuscarValor ( ( 'CosteTarea' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'CosteTarea' ) , $opciones_ocultar , 'Columna' ) ) ] 
			= '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'CosteTarea' ) , $opciones_ocultar , 'Colspan' ) . 
			'"><span class="importe_horas" id="costetarea" name="costetarea">' . utf8_encode ( $COSTETAREA )
			. '</span>' . BuscarValor ( ( 'CosteTarea' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
			
		}
		
		/*
		if ( ( $opcion [ 'Campo' ] == 'Importe Horas' ) && $ESTADO == 'Completado' ) {
			if ( BuscarEditarUTF8 ( ( 'Importe Horas' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$IMPORTE_ESTIMADO = str_replace ( "." , "," , $IMPORTE_ESTIMADO );
			$tabla [ intval ( BuscarValor ( ( 'Importe Horas' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Importe Horas' ) , $opciones_ocultar , 'Columna' ) ) ] = 
			'<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . 
			BuscarValor ( ( 'Importe Horas' ) , $opciones_ocultar , 'Colspan' ) . '"><span class="importe_horas" id="importe_horas" name="importe_horas">' . utf8_encode ( $IMPORTE_ESTIMADO ) 
			. '</span>' . BuscarValor ( ( 'Importe Horas' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}*/
		
		//////////////////////
		if ( $opcion [ 'Campo' ] == 'DocumentacionCompleta' ) {
			if ( BuscarEditarUTF8 ( ( 'DocumentacionCompleta' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'DocumentacionCompleta' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'DocumentacionCompleta' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 19 ] , $opcion [ 'Campo' ] , $DOCUMENTACIONCOMPLETA , 'selectcompletadatarea' );
		}
		if ( $opcion [ 'Campo' ] == 'Evaluable' ) {
			if ( BuscarEditarUTF8 ( ( 'Evaluable' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Evaluable' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Evaluable' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 12 ] , $opcion [ 'Campo' ] , $EVALUABLE , 'evaluable' );
		}
		if ( $opcion [ 'Campo' ] == 'ConsiderarBI' ) {
			if ( BuscarEditarUTF8 ( ( 'ConsiderarBI' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'ConsiderarBI' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'ConsiderarBI' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 22 ] , $opcion [ 'Campo' ] , $CONSIDERARBI , 'considerarbi' );
		}
		if ( $opcion [ 'Campo' ] == 'Teletrabajo' ) {
			if ( BuscarEditarUTF8 ( ( 'Teletrabajo' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Teletrabajo' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Teletrabajo' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 12 ] , $opcion [ 'Campo' ] , $TELETRABAJO , 'teletrabajo' );
		}

		if ( $opcion [ 'Campo' ] == 'Fecha Alta' ) { // dis
			if ( BuscarEditarUTF8 ( ( 'Fecha Alta' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$hora_alta_tarea = '';
			if($VER_HORA_ALTA == 1 && $HORA_ALTA != '00:00:00'){
				$hora_alta_tarea = "  <span style='color:red;'>$HORA_ALTA</span>";
			}
			$tabla [ intval ( BuscarValor ( ( 'Fecha Alta' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Fecha Alta' ) , $opciones_ocultar , 'Columna' ) ) ] =
			'<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Fecha Alta' ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" autocomplete="off" name="fechaalta" ' . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' align="center" maxlength="' . BuscarValor ( ( 'Fecha Alta' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Fecha Alta' ) , $opciones_ocultar , 'Width' ) . 'px;" id="fechaalta" ' . $readonly . $disabled . ' value="' . $FECHA_ALTA . '"/>' . BuscarValor ( ( 'Fecha Alta' ) , $opciones_ocultar , 'Coletilla' ) .$hora_alta_tarea. '</td>';
		}
		if ( $opcion [ 'Campo' ] == 'Fecha Solicitud' ) { // dis
			if ( BuscarEditarUTF8 ( ( 'Fecha Solicitud' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Fecha Solicitud' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Fecha Solicitud' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Fecha Solicitud' ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" autocomplete="off" name="fechasolicitud" ' . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' align="center" maxlength="' . BuscarValor ( ( 'Fecha Solicitud' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Fecha Solicitud' ) , $opciones_ocultar , 'Width' ) . 'px;" id="fechasolicitud" ' . $readonly . $disabled . ' value="' . $FECHASOLICITUD . '"/>' . BuscarValor ( ( 'Fecha Solicitud' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
		if ( $opcion [ 'Campo' ] == 'Fecha Necesidad' ) { // dis
			if ( BuscarEditarUTF8 ( ( 'Fecha Necesidad' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Fecha Necesidad' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Fecha Necesidad' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Fecha Necesidad' ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" autocomplete="off" ' . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' name="fechanecesidad" align="center" maxlength="' . BuscarValor ( ( 'Fecha Necesidad' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Fecha Necesidad' ) , $opciones_ocultar , 'Width' ) . 'px;" id="fechanecesidad" ' . $readonly . $disabled . ' value="' . $FECHANECESIDAD . '"/>' . BuscarValor ( ( 'Fecha Necesidad' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
		if ( $opcion [ 'Campo' ] == 'Fecha Objetivo' ) { // dis
			if ( BuscarEditarUTF8 ( ( 'Fecha Objetivo' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Fecha Objetivo' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Fecha Objetivo' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Fecha Objetivo' ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" autocomplete="off" ' . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' name="fechaobjetivo" align="center" maxlength="' . BuscarValor ( ( 'Fecha Objetivo' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Fecha Objetivo' ) , $opciones_ocultar , 'Width' ) . 'px;" id="fechaobjetivo" ' . $readonly . $disabled . ' value="' . $FECHAOBJETIVO . '"/>' . BuscarValor ( ( 'Fecha Objetivo' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
		// OBJETIVO
		if ( $opcion [ 'Campo' ] == 'Objetivo' ) { // dis
			if ( BuscarEditarUTF8 ( ( 'Objetivo' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Objetivo' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Objetivo' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" id="OBJETIVO_TAG" class="td_nombre">' . utf8_decode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Objetivo' ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" name="objetivo" ' . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' align="center" maxlength="' . BuscarValor ( ( 'Objetivo' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Objetivo' ) , $opciones_ocultar , 'Width' ) . '%;"  id="objetivo" ' . $readonly . $disabled . ' value="' . utf8_encode ( $OBJETIVO ) . '"/>' . BuscarValor ( ( 'Objetivo' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
		// CLIENTE
		if ( $opcion [ 'Campo' ] == 'Cliente' ) { // dis
			if ( BuscarEditarUTF8 ( ( 'Cliente' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			// $tabla [ intval ( BuscarValor ( ( 'Tarea/Proyecto' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Tarea/Proyecto' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Tarea/Proyecto' ) , $opciones_ocultar , 'Colspan' ) . '"><input id="autocomplete" ' . $readonly . $disabled . ' spb_submit="1" name="autocomplete" ' . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' maxlength="' . BuscarValor ( ( 'Tarea/Proyecto' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Tarea/Proyecto' ) , $opciones_ocultar , 'Width' ) . '%;" value="' . utf8_encode ( $TAREA_PROYECTO_DESCRIPCION ) . '">' . BuscarValor ( ( 'Tarea/Proyecto' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
			$tabla [ intval ( BuscarValor ( ( 'Cliente' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Cliente' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" id="CLIENTE_TAG" class="td_nombre">' . utf8_decode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Cliente' ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" name="cliente" ' . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' align="center" maxlength="' . BuscarValor ( ( 'Cliente' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Cliente' ) , $opciones_ocultar , 'Width' ) . '%;"  id="cliente" ' . $readonly . $disabled . ' value="' . utf8_encode ( $CLIENTE ) . '"/>' . BuscarValor ( ( 'Cliente' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
		// PROYECTO
		if ( $opcion [ 'Campo' ] == 'Proyecto' ) { // dis
			if ( BuscarEditarUTF8 ( ( 'Proyecto' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Proyecto' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Proyecto' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" id="PROYECTO_TAG" class="td_nombre">' . utf8_decode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Proyecto' ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" name="proyecto2" ' . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' align="center" maxlength="' . BuscarValor ( ( 'Proyecto' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Proyecto' ) , $opciones_ocultar , 'Width' ) . '%;"  id="proyecto2" ' . $readonly . $disabled . ' value="' . utf8_encode ( $PROYECTO ) . '"/>' . BuscarValor ( ( 'Proyecto' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
		// TITULO
		if ( $opcion [ 'Campo' ] == 'Titulo' ) { // dis
			if ( BuscarEditarUTF8 ( ( 'Titulo' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Titulo' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Titulo' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" id="TITULO_TAG" class="td_nombre">' . utf8_decode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Titulo' ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" name="titulo" ' . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' align="center" maxlength="' . BuscarValor ( ( 'Titulo' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Titulo' ) , $opciones_ocultar , 'Width' ) . '%;background-color: #F5ECCE;"  id="titulo" ' . $readonly . $disabled . ' value="' . utf8_encode ( $TITULO ) . '"/>' . BuscarValor ( ( 'Titulo' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
		// HORAS ESTIMADAS
		if ( $opcion [ 'Campo' ] == 'Horas Estimadas' ) { // dis
			if ( BuscarEditarUTF8 ( ( 'Horas Estimadas' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Horas Estimadas' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Horas Estimadas' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Horas Estimadas' ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" ' . $readonly . $disabled . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' name="horasestimadas" align="center" maxlength="' . BuscarValor ( ( 'Horas Estimadas' ) , $opciones_ocultar , 'Maxlenth' ) . '" spb_disparador="' . BuscarValor ( ( 'Horas Estimadas' ) , $opciones_ocultar , 'Disparador' ) . '" style="width:' . BuscarValor ( ( 'Horas Estimadas' ) , $opciones_ocultar , 'Width' ) . 'px" id="horasestimadas" ' . $readonly . ' value="' . utf8_encode ( $HORASESTIMADAS ) . '"/>' . BuscarValor ( ( 'Horas Estimadas' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
		// AHORRO
		if ( $opcion [ 'Campo' ] == 'Ahorro' ) { 
			if ( BuscarEditarUTF8 ( ( 'Ahorro' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Ahorro' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Ahorro' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Ahorro' ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" ' . $readonly . $disabled . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' name="ahorro" align="center" maxlength="' . BuscarValor ( ( 'Ahorro' ) , $opciones_ocultar , 'Maxlenth' ) . '" spb_disparador="' . BuscarValor ( ( 'Ahorro' ) , $opciones_ocultar , 'Disparador' ) . '" style="width:' . BuscarValor ( ( 'Ahorro' ) , $opciones_ocultar , 'Width' ) . 'px" id="ahorro" ' . $readonly . ' value="' . utf8_encode ( $AHORRO ) . '"/>' . BuscarValor ( ( 'Ahorro' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
		// TIPO AHORRO
		if ( $opcion [ 'Campo' ] == 'TipoAhorro' ) {
			if ( BuscarEditarUTF8 ( ( 'TipoAhorro' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'TipoAhorro' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'TipoAhorro' ) , $opciones_ocultar , 'Columna' ) ) ] = Get_Select_Tipos ( $opciones_ocultar , $tipos_lista [ 23 ] , $opcion [ 'Campo' ] , $TIPOAHORRO , 'selectahorro' );
		}
		// REFERENCIA
		if ( $opcion [ 'Campo' ] == 'Referencia' ) {
			if ( BuscarEditarUTF8 ( ( 'Referencia' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$boton_acceder_propuesta = '';
			if(intval ( BuscarValor ( ( 'Tipo' ) , $opciones_ocultar , 'Ocultar' ) ) == 0 && $TIPO == 'Idea mejora / antierror' && is_numeric($REFERENCIA)){
				$boton_acceder_propuesta = "<input  type='button' value='Acceder' onclick='alert($incidencia)' />";
				$boton_acceder_propuesta = "<a href='https://idm.sp-berner.com/menu.php?idm=$REFERENCIA' target='_new'>&nbsp;<image src='imagenes/external-link-24.png' title='Acceder a la idea de mejora $REFERENCIA' width='17' height='17' /></a>";
			}
			
			$tabla [ intval ( BuscarValor ( ( 'Referencia' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Referencia' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Referencia' ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" name="referencia" ' . $readonly . $disabled . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' align="center" maxlength="' . BuscarValor ( ( 'Referencia' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Referencia' ) , $opciones_ocultar , 'Width' ) . 'px;" id="referencia" ' . $readonly . ' value="' . utf8_encode ( $REFERENCIA ) . '"/>' . BuscarValor ( ( 'Referencia' ) , $opciones_ocultar , 'Coletilla' ) . $boton_acceder_propuesta . '</td>';
		}
		// MOTIVO BAP
		if ( $opcion [ 'Campo' ] == 'Motivo BAP' ) {
			if ( BuscarEditarUTF8 ( ( 'Motivo BAP' ) , $opciones_ocultar , $nombre ) == true ) {
				$readonly = '  ';
				$disabled = '  ';
			}
			$tabla [ intval ( BuscarValor ( ( 'Motivo BAP' ) , $opciones_ocultar , 'Fila' ) ) ] [ intval ( BuscarValor ( ( 'Motivo BAP' ) , $opciones_ocultar , 'Columna' ) ) ] = '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( 'Motivo BAP' ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" name="motivobap" ' . $readonly . $disabled . BuscarClase ( $opcion [ 'Campo' ] , $opciones_ocultar ) . ' align="center" maxlength="' . BuscarValor ( ( 'Motivo BAP' ) , $opciones_ocultar , 'Maxlenth' ) . '" style="width:' . BuscarValor ( ( 'Motivo BAP' ) , $opciones_ocultar , 'Width' ) . 'px;" id="motivobap" ' . $readonly . ' value="' . utf8_encode ( $MOTIVO_BAP ) . '"/>' . BuscarValor ( ( 'Motivo BAP' ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
	}
}

echo '<form method="post" id="formulario">';
// RECUPERAR CAMPOS DE TIPO SECCIONES
$multiportal_tipo = 'SECCIONES';
$multiportal_ventana = curPageName ();
include "../visibilidad/visibilidad_config.php";
// FIN RECUPERAR CAMPOS DE TIPO SECCIONES

foreach ( $opciones_ocultar as $opcion ) {
	if ( $opcion [ 'Campo' ] == 'Formulario' ) {
		if ( BuscarOcultarUTF8 ( ( $opcion [ 'Campo' ] ) , $opciones_ocultar , $nombre ) == true ) {
			echo "<!-- ******************* SECCION FORMULARIO ****************** -->";
			echo "<!-- ******************* SECCION FORMULARIO ****************** -->";
			echo '<div id="la_tarea" onMouseOut="cambiacolororiginal(this)" onMouseOver="cambiacolor(this)" onClick="plegardesplegar(this)" class="plegador"><img id="imagen_tarea" src="./imagenes/' . $IMAGEN_TAREA . '" width="15" height="15"> ' . $incidencia . ' - <span class="titulo_tarea">' . utf8_encode ( $TITULO ) . '</span><div id="la_tarea_tool" style="float:right;cursor:pointer;"></div></div>';
			echo '<div class="plegable" id="la_tarea_plegar" style="height:55%">';
			echo '		<div class="div-cuerpo">';
			echo '			<input type="hidden" name="programahidden" id="programahidden" value="' . utf8_encode ( $PROGRAMA ) . '"/>';
			echo '			<table class="tabla-normal" id="tarea">';
			foreach ( $tabla as $valor ) {
				echo '<tr>';
				foreach ( $valor as $valor_columna ) {
					echo $valor_columna;
				}
				echo '</tr>';
			}
			echo '			</table>';
			echo '		</div>';
			echo '</div>';
		}
	}
	if ( $opcion [ 'Campo' ] == 'Observaciones' ) {
		if ( BuscarOcultarUTF8 ( ( $opcion [ 'Campo' ] ) , $opciones_ocultar , $nombre ) == true ) {
			echo "<!-- ******************* SECCION OBSERVACIONES *************** -->";
			echo "<!-- ******************* SECCION OBSERVACIONES *************** -->";
			echo '<div id="las_observaciones" onMouseOut="cambiacolororiginal(this)" onMouseOver="cambiacolor(this)" onClick="plegardesplegar(this)" class="plegador">' . $nombre . '<div id="las_observaciones_tools" class="div-tools"></div></div>';
			echo '<div class="plegable" id="las_observaciones_plegar" >';
			echo '	<div style="height:75%;">';
			echo '		<textarea name="descripcion" id="descripcion" style="height:100%">' . utf8_encode ( $DESCRIPCION ) . '</textarea>';
			echo '	</div>';
			echo '</div>';
		}
	}
	if ( $opcion [ 'Campo' ] == 'Valoraciones' ) {
		if ( BuscarOcultarUTF8 ( ( $opcion [ 'Campo' ] ) , $opciones_ocultar , $nombre ) == true ) {
			echo "<!-- ******************* SECCION VALORACIONES **************** -->";
			echo "<!-- ******************* SECCION VALORACIONES **************** -->";
			echo '<div id="div_valorar_usuarios" onMouseOut="cambiacolororiginal(this)" onMouseOver="cambiacolor(this)" onClick="plegardesplegar(this)" class="plegador">' . $nombre . '<div id="div_valorar_usuarios_tools" class="div-tools"></div></div>';
			echo '<div class="plegable" id="div_valorar_usuarios_plegar" >';
			echo Get_Formulario_Valoraciones ( $incidencia );
			echo '</div>';
		}
	}
	if ( $opcion [ 'Campo' ] == 'Alta Usuario' ) {
		if ( BuscarOcultarUTF8 ( ( $opcion [ 'Campo' ] ) , $opciones_ocultar , $nombre ) == true ) {
			echo "<!-- ******************* SECCION ALTA USUARIO **************** -->";
			echo "<!-- ******************* SECCION ALTA USUARIO **************** -->";
			echo '<div id="div_alta_usuarios" onMouseOut="cambiacolororiginal(this)" onMouseOver="cambiacolor(this)" onClick="plegardesplegar(this)" class="plegador">' . $nombre . '<div id="div_valorar_usuarios_tools" class="div-tools"></div></div>';
			echo '<div class="plegable" id="div_alta_usuarios_plegar" >';
			echo Get_Formulario_Alta_Usuario ( $id_pantalla , $incidencia );
			echo '</div>';
		}
	}
	if ( $opcion [ 'Campo' ] == 'Archivos' ) {
		if ( BuscarOcultarUTF8 ( ( $opcion [ 'Campo' ] ) , $opciones_ocultar , $nombre ) == true ) {
			echo "<!-- ******************* SECCION ARCHIVOS ******************** -->";
			echo "<!-- ******************* SECCION ARCHIVOS ******************** -->";
			echo '<div id="div_ficheros_adjuntos" onMouseOut="cambiacolororiginal(this)" onMouseOver="cambiacolor(this)" onClick="plegardesplegar(this)" class="plegador">' . $nombre . '<div id="div_ficheros_adjuntos_tools" class="div-tools"></div></div>';
			echo '<div class="plegable" id="div_ficheros_adjuntos_plegar" >';
			echo Get_Adjuntos ( $id_pantalla , $incidencia );
			echo '</div>';
		}
	}
	if ( $opcion [ 'Campo' ] == 'Comentarios' ) {
		if ( BuscarOcultarUTF8 ( ( $opcion [ 'Campo' ] ) , $opciones_ocultar , $nombre ) == true ) {
			echo "<!-- ******************* SECCION COMENTARIOS ***************** -->";
			echo "<!-- ******************* SECCION COMENTARIOS ***************** -->";
			echo '<div id="grid_array_comentarios" onMouseOut="cambiacolororiginal(this)" onMouseOver="cambiacolor(this)" onClick="plegardesplegar(this)" class="plegador">' . $nombre . '<div id="grid_array_comentarios_tools" class="div-tools"></div></div>';
			echo '<div class="plegable" id="grid_array_comentarios_plegar" ></div>';
		}
	}
	if ( $opcion [ 'Campo' ] == 'Horas' ) {
		if ( BuscarOcultarUTF8 ( ( $opcion [ 'Campo' ] ) , $opciones_ocultar , $nombre ) == true ) {
			echo "<!-- ******************* SECCION GRID HORAS ****************** -->";
			echo "<!-- ******************* SECCION GRID HORAS ****************** -->";
			echo '<div id="grid_array_horas" onMouseOut="cambiacolororiginal(this)" onMouseOver="cambiacolor(this)" onClick="plegardesplegar(this)" class="plegador">' . $nombre . '<div id="grid_array_horas_tools" class="div-tools"></div></div>';
			echo '<div class="plegable" id="grid_array_horas_plegar" ></div>';
		}
	}
	if ( $opcion [ 'Campo' ] == 'Costes' ) {
		if ( BuscarOcultarUTF8 ( ( $opcion [ 'Campo' ] ) , $opciones_ocultar , $nombre ) == true ) {
			echo "<!-- ******************* SECCION GRID COSTES ***************** -->";
			echo "<!-- ******************* SECCION GRID COSTES ***************** -->";
			echo '<div id="grid_array_costes" onMouseOut="cambiacolororiginal(this)" onMouseOver="cambiacolor(this)" onClick="plegardesplegar(this)" class="plegador">' . $nombre . '<div id="grid_array_costes_tools" class="div-tools"></div></div>';
			echo '<div class="plegable" id="grid_array_costes_plegar" ></div>';
		}
	}
	if ( $opcion [ 'Campo' ] == 'Evaluaciones' ) {
		if ( BuscarOcultarUTF8 ( ( $opcion [ 'Campo' ] ) , $opciones_ocultar , $nombre ) == true ) {
			echo "<!-- ******************* SECCION EVALUACIONES **************** -->";
			echo "<!-- ******************* SECCION EVALUACIONES **************** -->";
			echo '<div id="grid_array_evaluaciones" onMouseOut="cambiacolororiginal(this)" onMouseOver="cambiacolor(this)" onClick="plegardesplegar(this)" class="plegador">' . $nombre . '</div>';
			echo '<div class="plegable" id="grid_array_evaluaciones_plegar">CAPA DE CONTENIDO<br>CAPA DE CONTENIDO<br>CAPA DE CONTENIDO<br>CAPA DE CONTENIDO<br></div>';
			//echo '<div class="plegable" >CAPA DE CONTENIDO</div>';
		}
	}
	if ( $opcion [ 'Campo' ] == 'Botonera' ) {
		if ( BuscarOcultarUTF8 ( ( $opcion [ 'Campo' ] ) , $opciones_ocultar , $nombre ) == true ) {
			echo "<!-- ******************* SECCION BOTONERA ******************** -->";
			echo "<!-- ******************* SECCION BOTONERA ******************** -->";
			echo '<hr>';
			echo Get_Botonera_Secciones ( $opciones_ocultar , $CONTROL );
		}
	}
}
echo '</form>';
echo '<div id="respuesta">';

DBClose ();
function Get_Botonera_Secciones( $opciones_ocultar , $CONTROL ) {
	$devolver = '';
	$cancelar = false;
	foreach ( $opciones_ocultar as $opcion ) {
		if ( $opcion [ 'Campo' ] == 'Botonera|Guardar' ) {
			if ( BuscarOcultarUTF8 ( ( $opcion [ 'Campo' ] ) , $opciones_ocultar , $nombre ) == true ) {
				$devolver = $devolver . '<input type="button" id="btn_enviar" class="botonAngular" style="width:' . BuscarValor ( ( 'Botonera|Guardar' ) , $opciones_ocultar , 'Width' ) . 'px;" value="' . $nombre . '">';
			}
		}
		if ( $opcion [ 'Campo' ] == 'Botonera|Salir' ) {
			if ( BuscarOcultarUTF8 ( ( $opcion [ 'Campo' ] ) , $opciones_ocultar , $nombre ) == true ) {
				$devolver = $devolver . '<input type="button" id="btn_salir" class="botonAngular" style="width:' . BuscarValor ( ( 'Botonera|Salir' ) , $opciones_ocultar , 'Width' ) . 'px;" value="' . $nombre . '">';
			}
		}
		if ( $opcion [ 'Campo' ] == 'Botonera|Cancelar' ) {
			if ( BuscarOcultarUTF8 ( ( $opcion [ 'Campo' ] ) , $opciones_ocultar , $nombre ) == true ) {
				$cancelar = true;
				if ( $CONTROL == '1' ) {
					$devolver = $devolver . '<input type="button" id="btn_cancelar" class="botonAngular" style="width:' . BuscarValor ( ( 'Botonera|Cancelar' ) , $opciones_ocultar , 'Width' ) . 'px;" value="' . $nombre . '">';
				} else {
					$devolver = $devolver . '<input type="hidden" id="btn_cancelar" class="botonAngular" style="width:' . BuscarValor ( ( 'Botonera|Cancelar' ) , $opciones_ocultar , 'Width' ) . 'px;" value="' . $nombre . '">';
				}
			}
		}
	}
	if ( $cancelar == false ) {
		if ( $CONTROL == '1' ) {
			$devolver = $devolver . '<input type="button" id="btn_cancelar" class="botonAngular" value="Cancelar">';
		} else {
			$devolver = $devolver . '<input type="hidden" id="btn_cancelar" class="botonAngular">';
		}
	}
	return $devolver;
}
function Get_Lista_de_Tipos( $OFICINA ) {
	$tipos_lista = array ();
	$query = "
			SELECT 
				CAST(Tipo as varchar(55)) as Tipo
				,CAST(Numero as varchar(55)) as Numero
				,CAST(Descripcion as varchar(55)) as Descripcion
				,Visible 
				,Oficina
				,CAST(COALESCE( [DescripcionVisible], '' ) as varchar(55) ) AS DescripcionVisible
			FROM Tipos 
			where visible = 1
			and Oficina = '$OFICINA' 
			ORDER BY Tipo, Descripcion
	";
	//die($query);
	$query = DBSelect ( utf8_decode ( $query ) );
	for(; DBNext ( $query ) ;) {
		$tipo_Tipos = ( DBCampo ( $query , "Tipo" ) );
		$Numero_Tipos = ( DBCampo ( $query , "Numero" ) );
		$Descripcion_Tipos = utf8_encode ( DBCampo ( $query , "Descripcion" ) );
		$Visible_Tipos = ( DBCampo ( $query , "Visible" ) );
		$Descripcion_Visible = utf8_encode ( DBCampo ( $query , "DescripcionVisible" ) );
		// echo "TIPO: " . $tipo_Tipos . " NUMERO: " . $Numero_Tipos . " DESCRIPCION: " . utf8_encode ($Descripcion_Tipos) . " VISIBLE: ". $Visible_Tipos . "<BR>";
		
		if($Descripcion_Visible != ''){
			$Descripcion_Visible = "$Descripcion_Tipos - $Descripcion_Visible";
		}else{
			$Descripcion_Visible = $Descripcion_Tipos;
		}
		
		$tipos_lista [ $tipo_Tipos ] [ $Numero_Tipos ] = array (
				"Descripcion" =>  $Descripcion_Tipos  , 
				"Descripcion_Visible" => $Descripcion_Visible,
				"Visible" => $Visible_Tipos 
				
		);
	}
	return $tipos_lista;
}
function Get_Select_Tipos( $opciones_ocultar , $tipo_deseado , $campo , $valor , $id ) {
	$select_montado = '';
	
	$mensaje_ayuda = '';
	
	if($id == 'selectaporte'){
		$mensaje_ayuda = "<image src='imagenes/info.png' alt='Ayuda Aportes' width='18' height='18' style='cursor:pointer' onClick='mensaje(\"Información Aporte Propio\",\"Se ha de informar este dato si esta tarea es uno de los objetivos marcados en tu cuadro de funciones.\",\"info.png\")' />";
	}
	if($id == 'selectaporteempresa'){
		$mensaje_ayuda = "<image src='imagenes/info.png' alt='Ayuda Aportes' width='18' height='18' style='cursor:pointer' onClick='mensaje(\"Información Aporte Empresa\",\"Se ha de informar este dato si esta tarea no forma parte de los objetivos marcados en tu cuadro de funciones.\",\"info.png\")' />";
	}
	//print_r($tipo_deseado);
	//echo "<br><br>";
	//echo "<h2>OP.OCUL: $opciones_ocultar TIPO: $tipo_deseado CAMPO: $campo VALOR: $valor ID: $id</h2>";
	//die('$opciones_ocultar' . ' ' . '$tipo_deseado' . ' ' . $campo . ' ' . $valor . ' ' . $id);
	
	if ( BuscarEditar (  $campo  , $opciones_ocultar , $nombre ) == true ) {
		
		$select_montado = $select_montado . '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Colspan' ) . '"><select id="' . $id . '" name="' . $id . '" style="width: ' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Width' ) . ';" >';
		foreach ( $tipo_deseado as $tipo_buscar ) {
			
			$select_selected = '';
			
			if ( strcmp ( strtoupper ( utf8_encode ( $valor ) ) , strtoupper ( $tipo_buscar [ 'Descripcion' ] ) ) == 0 ) {
				$select_selected = ' selected="selected" ';
			}
			$select_montado = $select_montado . '<option value="' . ( $tipo_buscar [ 'Descripcion' ] ) . '" ' . $select_selected . '>' . ( $tipo_buscar [ 'Descripcion_Visible' ] ) . '</option>';
		}
		$select_montado = $select_montado . '</select>' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Coletilla' ) . $mensaje_ayuda . '</td>';
	} else {
		$select_montado = $select_montado . '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" readonly="readonly" disabled="disabled" name="' . $id . '" value="' . utf8_encode ( $valor ) . '" id="' . $id . '" style="width: ' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Width' ) . ';""/>' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
	}
	
	return $select_montado;
}
function Get_Select_Oficinas( $opciones_ocultar , $tipo_deseado , $campo , $valor , $id , $usuario ) {
	// echo ' - '. $campo .' - '. $OFICINA .' - '. $id .' - '. $usuario;
	$oficinas = '';
	$query = "SELECT oficina FROM Configuracion_Usuarios WHERE usuario = '$usuario'";
	$query = DBSelect ( utf8_decode ( $query ) );
	for(; DBNext ( $query ) ;) {
		$oficinas = utf8_encode ( DBCampo ( $query , "oficina" ) );
	}
	DBFree ( $query );
	
	$oficinas = explode ( ',' , $oficinas );
	
	$select_montado = '';
	if ( BuscarEditar ( ( $campo ) , $opciones_ocultar , $nombre ) == true ) {
		$select_montado = $select_montado . '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Colspan' ) . '"><select id="' . $id . '" name="' . $id . '" style="width: ' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Width' ) . ';">';
		foreach ( $oficinas as $oficina ) {
			$select_selected = '';
			
			if ( strcmp ( strtoupper ( utf8_encode ( $valor ) ) , strtoupper ( $oficina ) ) == 0 ) {
				$select_selected = ' selected="selected" ';
			}
			
			$select_montado = $select_montado . "<option value='$oficina' $select_selected>$oficina</option>";
		}
		$select_montado = $select_montado . '</select>';
	} else {
		$select_montado = $select_montado . '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" readonly="readonly" disabled="disabled" name="' . $id . '" value="' . utf8_encode ( $valor ) . '" id="' . $id . '" style="width: ' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Width' ) . ';""/>' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
	}
	return $select_montado;
}
function Get_Lista_Usuarios_Departamento( $opciones_ocultar , $campo , $valor , $id ) {
	$select_montado = '';
	if ( BuscarEditar ( ( $campo ) , $opciones_ocultar , $nombre ) == true ) {
		$CodigoDep = '';
		$departamento = DBSelect ( utf8_decode ( "SELECT [Descripcion] FROM [Tipos] where Tipo = 100" ) );
		for(; DBNext ( $departamento ) ;) {
			$CodigoDep = $CodigoDep . 'or department = \'\'' . utf8_encode ( DBCampo ( $departamento , "Descripcion" ) ) . '\'\' ';
		}
		$CodigoDep = trim ( $CodigoDep , "or" );
		// echo $CodigoDep;
		$select_montado = $select_montado . '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Colspan' ) . '"><select id="' . $id . '" name="' . $id . '" style="width: ' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Width' ) . ';">';
		// $q = "SELECT department,sAMAccountName ,Name,mail,Activo FROM (SELECT department,sAMAccountName ,Name,mail,CASE WHEN ((2 & userAccountControl) = 2 ) THEN 0 ELSE 1 END AS Activo FROM OpenQuery(ADSI, 'SELECT department,sAMAccountName,Name,mail,userAccountControl FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user'' and (" . $CodigoDep . ")')) Consulta ORDER BY Name";
		$q = "SELECT department,sAMAccountName ,Name,mail,Activo FROM (SELECT department,sAMAccountName ,Name,mail,CASE WHEN ((2 & userAccountControl) = 2 ) THEN 0 ELSE 1 END AS Activo  FROM OpenQuery(ADSI, 'SELECT department,sAMAccountName,Name,mail,userAccountControl FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user''  AND objectClass=''user'' and (" . $CodigoDep . ") ')) Consulta	ORDER BY Name";
		// $q2 = "SELECT sAMAccountName ,Name FROM OpenQuery(ADSI, 'SELECT sAMAccountName,Name,department FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user'' ".$where_montado."') Consulta ORDER BY Name";
		$q = DBSelect ( utf8_decode ( $q ) );
		
		for(; DBNext ( $q ) ;) {
			$Name = ( DBCampo ( $q , "Name" ) );
			$Activo = ( DBCampo ( $q , "Activo" ) );
			$department = ( DBCampo ( $q , "department" ) );
			
			if ( utf8_encode ( $valor ) == utf8_encode ( $Name ) ) {
				$select_montado = $select_montado . '<option value="' . utf8_encode ( $Name ) . '" selected="selected">' . utf8_encode ( $Name ) . '</option>';
			} else {
				$select_montado = $select_montado . '<option value="' . utf8_encode ( $Name ) . '">' . utf8_encode ( $Name ) . '</option>';
				if ( utf8_encode ( $Activo ) == 1 ) {
					// esto excluye de la seleccion a usuarios no activos del dominio, quitado 10/11/2015 para que salgan todos los usuarios del depto
				}
			}
		}
		if ( utf8_encode ( $valor ) == '' ) {
			$select_montado = $select_montado . '<option value="" selected="selected"></option>';
		} else {
			$select_montado = $select_montado . '<option value=""></option>';
		}
		$select_montado = $select_montado . '</select>' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
	} else {
		$select_montado = $select_montado . '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" readonly="readonly" disabled="disabled" name="' . $id . '" value="' . utf8_encode ( $valor ) . '" id="' . $id . '" style="width: ' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Width' ) . ';""/>' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
	}
	return $select_montado;
}
function Get_Select_Cola( $opciones_ocultar , $campo , $valor , $id ) {
	$select_montado = '';
	if ( BuscarEditar ( ( $campo ) , $opciones_ocultar , $nombre ) == true ) {
		$select_montado = $select_montado . '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Colspan' ) . '"><select id="' . $id . '" name="' . $id . '" style="width: ' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Width' ) . ';">';
		$q = DBSelect ( utf8_decode ( "SELECT Colas.[Descripcion] , Predeterminado FROM [Colas] Colas ORDER BY Descripcion" ) );
		for(; DBNext ( $q ) ;) {
			$Name = ( DBCampo ( $q , "Descripcion" ) );
			if ( utf8_encode ( $valor ) == utf8_encode ( $Name ) ) {
				$select_montado = $select_montado . '<option value="' . utf8_encode ( $Name ) . '" selected="selected">' . utf8_encode ( $Name ) . '</option>';
			} else {
				$select_montado = $select_montado . '<option value="' . utf8_encode ( $Name ) . '">' . utf8_encode ( $Name ) . '</option>';
			}
		}
		$select_montado = $select_montado . '</select>' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
	} else {
		$select_montado = $select_montado . '<td align="left" class="td_nombre">' . ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" readonly="readonly" disabled="disabled" name="' . $id . '" value="' . $valor . '" id="' . $id . '" style="width: ' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Width' ) . ';""/>' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
	}
	return $select_montado;
}
function Get_Select_Departamento( $opciones_ocultar , $campo , $valor , $id ) {
	$select_montado = '';
	if ( BuscarEditar ( ( $campo ) , $opciones_ocultar , $nombre ) == true ) {
		$select_montado = $select_montado . '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Colspan' ) . '"><select onchange="javascript:cambia_departamento(this);" id="' . $id . '" name="' . $id . '" style="width: ' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Width' ) . ';">';
		$q = DBSelect ( utf8_decode ( "SELECT Código as Codigo,Nombre,Bloqueado FROM (SELECT * FROM OPENQUERY (NavisionSQL,'SELECT * FROM [NAVSQL].[dbo].[Departamento]')) Consulta	ORDER BY Nombre" ) );
		for(; DBNext ( $q ) ;) {
			$CodigoDepartamento = ( DBCampo ( $q , "Codigo" ) );
			$NombreDepartamento = ( DBCampo ( $q , "Nombre" ) );
			$BloqueadoDepartamento = ( DBCampo ( $q , "Bloqueado" ) );
			if ( utf8_encode ( $valor ) == utf8_encode ( $CodigoDepartamento ) ) {
				$select_montado = $select_montado . '<option value="' . utf8_encode ( $CodigoDepartamento ) . '" selected="selected">' . utf8_encode ( $CodigoDepartamento ) . ' (' . utf8_encode ( $NombreDepartamento ) . ')</option>';
			} else {
				if ( utf8_encode ( $BloqueadoDepartamento ) == 0 ) {
					$select_montado = $select_montado . '<option value="' . utf8_encode ( $CodigoDepartamento ) . '">' . utf8_encode ( $CodigoDepartamento ) . ' (' . utf8_encode ( $NombreDepartamento ) . ')</option>';
				}
			}
		}
		$select_montado = $select_montado . '</select>' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
	} else {
		$q = DBSelect ( utf8_decode ( "SELECT Código as Codigo,Nombre,Bloqueado FROM (SELECT * FROM OPENQUERY (NavisionSQL,'SELECT * FROM [NAVSQL].[dbo].[Departamento] where Código = ''" . utf8_encode ( $valor ) . "''')) Consulta	ORDER BY Nombre" ) );
		for(; DBNext ( $q ) ;) {
			$CodigoDepartamento = ( DBCampo ( $q , "Codigo" ) );
			$NombreDepartamento = ( DBCampo ( $q , "Nombre" ) );
			$select_montado = $select_montado . '<td align="left" class="td_nombre">' . ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Colspan' ) . '"><input type="text" readonly="readonly" disabled="disabled" name="' . $id . '" value="' . utf8_encode ( $CodigoDepartamento ) . ' (' . utf8_encode ( $NombreDepartamento ) . ')" id="' . $id . '" style="width: ' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Width' ) . ';""/>' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
	}
	return $select_montado;
}
function Get_Select_Programa( $opciones_ocultar , $campo , $valor , $valor_relacion , $id ) {
	$select_montado = '';
	if ( BuscarEditar ( ( $campo ) , $opciones_ocultar , $nombre ) == true ) {
		$select_montado = $select_montado . '<td align="left" class="td_nombre">' . utf8_encode ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Colspan' ) . '"><div id="div_programa"><select id="' . $id . '" name="' . $id . '" style="width: ' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Width' ) . ';">';
		$q = DBSelect ( utf8_decode ( "SELECT Departamento,Programa,Nombre_Departamento,Nombre_Programa,Bloqueado,Fila FROM (SELECT *,ROW_NUMBER() OVER(ORDER BY Departamento,Programa) Fila FROM OPENQUERY (NavisionSQL,'SELECT Relacion.Departamento,Relacion.Programa,	Departamento.Nombre AS Nombre_Departamento,		Programa.Nombre AS Nombre_Programa,		CASE Relacion.Bloqueado WHEN 1 THEN 1 ELSE CASE Programa.Bloqueado WHEN 1 THEN 1 ELSE 0 END END AS Bloqueado FROM [NAVSQL].[dbo].[Relación dpto_ y prog_] Relacion INNER JOIN Departamento Departamento ON Departamento.Código = Relacion.Departamento INNER JOIN 		Programa Programa ON Programa.Código = Relacion.Programa WHERE Relacion.Departamento = ''" . $valor_relacion . "'' ')) Consulta" ) );
		for(; DBNext ( $q ) ;) {
			$CodigoDepartamento = ( DBCampo ( $q , "Programa" ) );
			$NombreDepartamento = ( DBCampo ( $q , "Nombre_Programa" ) );
			$BloqueadoDepartamento = ( DBCampo ( $q , "Bloqueado" ) );
			if ( utf8_encode ( $valor ) == utf8_encode ( $CodigoDepartamento ) ) {
				$select_montado = $select_montado . '<option value="' . utf8_encode ( $CodigoDepartamento ) . '" selected="selected">' . utf8_encode ( $CodigoDepartamento ) . ' (' . utf8_encode ( $NombreDepartamento ) . ')</option>';
			} else {
				if ( utf8_encode ( $BloqueadoDepartamento ) == 0 ) {
					$select_montado = $select_montado . '<option value="' . utf8_encode ( $CodigoDepartamento ) . '">' . utf8_encode ( $CodigoDepartamento ) . ' (' . utf8_encode ( $NombreDepartamento ) . ')</option>';
				}
			}
		}
		$select_montado = $select_montado . '</select></div>' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
	} else {
		$q = DBSelect ( utf8_decode ( "SELECT Código as Codigo,Nombre FROM OPENQUERY (NavisionSQL,'SELECT Código,Nombre FROM Programa WHERE Programa.Código = ''" . $valor . "'' ') Consulta" ) );
		for(; DBNext ( $q ) ;) {
			$CodigoDepartamento = ( DBCampo ( $q , "Codigo" ) );
			$NombreDepartamento = ( DBCampo ( $q , "Nombre" ) );
			$select_montado = $select_montado . '<td align="left" class="td_nombre">' . ( $nombre ) . '</td><td class="td_campo" colspan="' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Colspan' ) . '"><div id="div_programa"><input type="text" readonly="readonly" disabled="disabled" name="' . $id . '" value="' . utf8_encode ( $CodigoDepartamento ) . '" id="' . $id . '" style="width: ' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Width' ) . ';""/></div>' . BuscarValor ( ( $campo ) , $opciones_ocultar , 'Coletilla' ) . '</td>';
		}
	}
	return $select_montado;
}
function Get_Botonera( $incidencia , $opciones_ocultar , $cantidad_pda , $cantidad_feedbaack , $cantidad_folder , $cantidad_planificador , $cantidad_colaboradores, $NOPLANIFICADAS ) {
	$devolver = '';
	foreach ( $opciones_ocultar as $opcion ) {
		
		if ( $opcion [ 'Campo' ] == 'Botonera|Mail' ) {
			if ( $opcion [ 'Ocultar' ] == '0' ) {
				$devolver = $devolver . '<div id="correos" class="imagenes_menu"><img class="correos" id="correo_informar" title="Enviar correo a solicitante" src="imagenes/correo_informar.png" onClick="manda_correo(this)"></div>';
			}
		}
		if ( $opcion [ 'Campo' ] == 'Botonera|PDA' ) {
			if ( $opcion [ 'Ocultar' ] == '0' ) {
				$imagen = 'pda.png';
				if ( $cantidad_pda == 0 ) {
					$imagen = 'pda_sin.png';
				}
				$devolver = $devolver . '<div id="pda" class="imagenes_menu"><img class="correos" id="pda_imagen" src="imagenes/' . $imagen . '"></div>';
				echo '<div id="tooltip2" class="tooltip_acceso"><div id="mi_pda" class="mi_pda"><input onChange="Asocia_PDA(this)" id="autocompletepda" name="autocompletepda" class="autocompletepda" value=""><img class="more" id="obtener_pda" src="imagenes/more.png" onClick="ObtenerPDA()"></div><div id="tooltip2sub" ></div></div>';
			}
		}
		if ( $opcion [ 'Campo' ] == 'Botonera|Feedback' ) {
			if ( $opcion [ 'Ocultar' ] == '0' ) {
				$imagen = 'feedback.png';
				if ( $cantidad_feedbaack == 0 ) {
					$imagen = 'feedback_sin.png';
				}
				if ( $cantidad_feedbaack == -1 ) {
					$imagen = 'feedback_con.png';
				}
				$devolver = $devolver . '<div id="feedback" class="imagenes_menu"><img class="correos" id="feedback_imagen" title="Enviar FeedBack" src="imagenes/' . $imagen . '" onclick="feedback_feedbacks(this,' . $incidencia . ');"></div>';
			}
		}
		if ( $opcion [ 'Campo' ] == 'Botonera|Carpetas' ) {
			if ( $opcion [ 'Ocultar' ] == '0' ) {
				$imagen = 'src="./treefolder/imagen/folder_vacio.png"';
				if ( $cantidad_folder > 0 ) {
					$imagen = 'src="./treefolder/imagen/folder.png"';
				}
				$devolver = $devolver . '<img class="folder_boton" id="folder_boton" ' . $imagen . ' onclick="treefolder_folders(' . $incidencia . ');">';
			}
		}
		if ( $opcion [ 'Campo' ] == 'Botonera|Planificador' ) {
			if ( $opcion [ 'Ocultar' ] == '0' ) {
				$imagen = ' src="imagenes/planificador.png" ';
				$ptitle = ' title="LA TAREA NO TIENE PLANIFICACIONES" ';
				if ( $cantidad_planificador > 0  ) {
					$imagen = ' src="imagenes/planificador2.png" ';
					$ptitle = ' title="PULSE PARA VER PLANIFICACIONES" ';
				}
				$devolver = $devolver . '<img class="folder_boton" id="planificador_boton" ' . $imagen . '  ' . $ptitle . ' onclick="pop_up_Planificador(' . $incidencia . ',1);">'; 
			}
		}
		
		if ( $opcion [ 'Campo' ] == 'Botonera|NoPlanificado' ) {
			if ( $opcion [ 'Ocultar' ] == '0' ) {
				$imagen = ' src="imagenes/planificadorapido.png" ';
				$ptitle = ' title="LA TAREA NO TIENE PLANIFICACIONES NO PLANIFICADAS" ';
				if ( $NOPLANIFICADAS > 0 ) {
					$imagen = ' src="imagenes/planificadorapido2.png" ';
					$ptitle = ' title="PULSE PARA CREAR PLANIFICACION NO PLANIFICADA" ';
				}
				$devolver = $devolver . '<img class="folder_boton" id="planificadorrapido_boton" ' . $imagen . '  ' . $ptitle . ' onclick="crearPlanificacionRapida(' . $incidencia . ');">';
			}
		}
		
		if ( $opcion [ 'Campo' ] == 'Botonera|Colaborador' ) {
			if ( $opcion [ 'Ocultar' ] == '0' ) {
				$imagen = ' src="imagenes/colaboradores.png" ';
				$ptitle = ' title="LA TAREA NO TIENE COLABORADORES" ';
				if ( $cantidad_colaboradores > 0 ) {
					$imagen = ' src="imagenes/colaboradores2.png" ';
					$ptitle = ' title="PULSE PARA VER COLABORADORES" ';
				}
				$devolver = $devolver . '<img class="folder_boton" id="colaborador_boton" ' . $imagen . '  ' . $ptitle . ' onclick="pop_up_Colaborador(' . $incidencia . ');">';
			}
		}
	}
	return $devolver;
}
function Get_Formulario_Valoraciones( $incidencia ) {
	$selectvaloracionusuario = '<table style="font-size:12px;"><tr><td style="padding:3px;">';
	$g = DBSelect ( utf8_decode ( "SELECT Tipo.[Numero] as TIPO ,Tipo.[Descripcion] AS TIPO_DESCRIPCION,Valoraciones.Descripcion as OPCION_DESCRIPCION,Valoraciones.[Numero] as OPCION_NUMERO
							,Valoraciones.Id as OPCION_NUMERO_ID,  ISNULL((SELECT [Numero] FROM [Valoraciones] Nota WHERE Nota.Tarea = " . $incidencia . "
							AND Nota.[Concepto] = Tipo.[Numero]),0) AS ID_VALORACION,(SELECT [Valoracion] FROM [Valoraciones] Nota  WHERE Nota.Tarea = " . $incidencia . " AND Nota.[Concepto] = Tipo.[Numero]) AS NOTA,
							(SELECT [Observacion] FROM [Valoraciones] Nota WHERE Nota.Tarea = " . $incidencia . " AND Nota.[Concepto] = Tipo.[Numero]) as OBSERVACION
							FROM [Concepto_Tipo_Valoraciones] Tipo INNER JOIN [Tipo_Valoraciones] Valoraciones ON Valoraciones.[Tipo] = Tipo.[Numero] 
							AND Valoraciones.Activa = 1 WHERE Tipo.Grupo = 1 AND Tipo.Activa = 1  order by Tipo.Numero,Valoraciones.id" ) );
	$TIPO =  - 1;
	$TIPO_anterior =  - 2;
	for(; DBNext ( $g ) ;) {
		$TIPO = ( DBCampo ( $g , "TIPO" ) );
		$TIPO_DESCRIPCION = ( DBCampo ( $g , "TIPO_DESCRIPCION" ) );
		$OPCION_DESCRIPCION = ( DBCampo ( $g , "OPCION_DESCRIPCION" ) );
		$OPCION_NUMERO_ID = ( DBCampo ( $g , "OPCION_NUMERO_ID" ) );
		$OPCION_NUMERO = ( DBCampo ( $g , "OPCION_NUMERO" ) );
		$ID_VALORACION = ( DBCampo ( $g , "ID_VALORACION" ) );
		$NOTA = ( DBCampo ( $g , "NOTA" ) );
		$OBSERVACION = ( DBCampo ( $g , "OBSERVACION" ) );
		$selec_opcion_val = '';
		if ( $NOTA == $OPCION_NUMERO ) {
			$selec_opcion_val = ' selected="selected" ';
		}
		if ( $TIPO == $TIPO_anterior ) {
			$selectvaloracionusuario = $selectvaloracionusuario . '<option value="' . $OPCION_NUMERO . '" ' . $selec_opcion_val . '>' . utf8_encode ( $OPCION_DESCRIPCION ) . '</option>';
		} else {
			$selectvaloracionusuario = $selectvaloracionusuario . '</td></tr>';
			$selectvaloracionusuario = $selectvaloracionusuario . '<tr><td style="padding:3px;">' . utf8_encode ( $TIPO_DESCRIPCION ) . '</td><td style="padding:3px;"><select id="selectvaloracionusuario' . $TIPO . '" name="selectvaloracionusuario' . $TIPO . '" onchange="javascript:valoracion_a_usuario(this,' . $TIPO . ',' . $incidencia . ');">';
			$selectvaloracionusuario = $selectvaloracionusuario . '<option value="' . $OPCION_NUMERO . '">' . utf8_encode ( $OPCION_DESCRIPCION ) . '</option>';
		}
		$TIPO_anterior = $TIPO;
	}
	$selectvaloracionusuario = $selectvaloracionusuario . '</td></tr>';
	$selectvaloracionusuario = $selectvaloracionusuario . '</table>';
	return $selectvaloracionusuario;
}
function Get_Formulario_Alta_Usuario( $id_pantalla , $incidencia ) {
	$contador_altas_usuario = 0;
	$select_alta_usuario = '';
	$select_alta_usuario = $select_alta_usuario . '<table class="tabla_datos_Alta">';
	$alu = DBSelect ( utf8_decode ( "
			SELECT  [Tarea]
				,[Usuario]
				,[Fecha]
				,[Nombre]
				,[Apellido_1]
				,[Apellido_2]
				,[Navision_Acceso]
				,[Navision_Usuario]
				,[Navision_Password]
				,[Navision_Espejo]
				,[Usuario_Incorporacion]
				,[Mail_Incorporacion]
				,[Internet_Acceso]
				,[Internet_Tipo]
				,[Remoto_Acceso]
				,[Reporta_a]
				,[Permisos_como]
				,[Crear_AD]
				,[Departamento_alta]
				,[Programa_alta]
				,[Telefono_Acceso]
				,[Telefono_Movil_Acceso]
				,[PC_Acceso]
				,[Portatil_Acceso]
				,[Telefono_Numero]
				,[Telefono_Movil_Numero]
				,[Estado]
				,[Mapex_Terminal_Acceso]
				,[Mapex_Aplicacion_Acceso]
				,[Mapex_Terminal_Tipo]
				,[Mapex_Terminal_Espejo]
				,[Mapex_Aplicacion_Tipo]
				,[Mapex_Aplicacion_Espejo]
				,[Contrasenya]
				,CONVERT(varchar(250),[Fecha_Incorporacion],105) as [Fecha_Incorporacion] 
			FROM [Altas_Usuario] WHERE Tarea = " . $incidencia . " " ) );
	for(; DBNext ( $alu ) ;) {
		$contador_altas_usuario = $contador_altas_usuario + 1;
		$Nombre = ( DBCampo ( $alu , "Nombre" ) );
		$Apellido_1 = ( DBCampo ( $alu , "Apellido_1" ) );
		$Apellido_2 = ( DBCampo ( $alu , "Apellido_2" ) );
		$Navision_Usuario_Espejo = ( DBCampo ( $alu , "Navision_Espejo" ) );
		$Navision_Acceso = ( DBCampo ( $alu , "Navision_Acceso" ) );
		$Crear_AD = ( DBCampo ( $alu , "Crear_AD" ) );
		$Navision_Usuario = ( DBCampo ( $alu , "Navision_Usuario" ) );
		$Navision_Password = ( DBCampo ( $alu , "Navision_Password" ) );
		$FECHA_INCORPORACION = ( DBCampo ( $alu , "Fecha_Incorporacion" ) );
		$Usuario_Incorporacion = ( DBCampo ( $alu , "Usuario_Incorporacion" ) );
		$Mail_Incorporacion = ( DBCampo ( $alu , "Mail_Incorporacion" ) );
		$Internet_Acceso = ( DBCampo ( $alu , "Internet_Acceso" ) );
		$Internet_Tipo = ( DBCampo ( $alu , "Internet_Tipo" ) );
		$Remoto_Acceso = ( DBCampo ( $alu , "Remoto_Acceso" ) );
		$Departamento_Alta = ( DBCampo ( $alu , "Departamento_alta" ) );
		$Programa_Alta = ( DBCampo ( $alu , "Programa_alta" ) );
		$Estado = ( DBCampo ( $alu , "Estado" ) );
		$Password = ( DBCampo ( $alu , "Contrasenya" ) );
		$Telefono_Acceso = ( DBCampo ( $alu , "Telefono_Acceso" ) );
		$Telefono_Movil_Acceso = ( DBCampo ( $alu , "Telefono_Movil_Acceso" ) );
		$PC_Acceso = ( DBCampo ( $alu , "PC_Acceso" ) );
		$Portatil_Acceso = ( DBCampo ( $alu , "Portatil_Acceso" ) );
		$Telefono_Numero = ( DBCampo ( $alu , "Telefono_Numero" ) );
		$Telefono_Movil_Numero = ( DBCampo ( $alu , "Telefono_Movil_Numero" ) );
		$Mapex_Terminal_Acceso = ( DBCampo ( $alu , "Mapex_Terminal_Acceso" ) );
		$Mapex_Aplicacion_Acceso = ( DBCampo ( $alu , "Mapex_Aplicacion_Acceso" ) );
		$Mapex_Terminal_Tipo = ( DBCampo ( $alu , "Mapex_Terminal_Tipo" ) );
		$Mapex_Terminal_Espejo = ( DBCampo ( $alu , "Mapex_Terminal_Espejo" ) );
		$Mapex_Aplicacion_Tipo = ( DBCampo ( $alu , "Mapex_Aplicacion_Tipo" ) );
		$Mapex_Aplicacion_Espejo = ( DBCampo ( $alu , "Mapex_Aplicacion_Espejo" ) );
		$Reporta_a = ( DBCampo ( $alu , "Reporta_a" ) );
		$Permisos_como = ( DBCampo ( $alu , "Permisos_como" ) );
		// *************************TIPO
		if ( 3 == 3 ) {
			
			$selecttipointernet = '<select id="selecttipointernet" name="selecttipointernet" style="width: 125;">';
			
			$t = DBSelect ( utf8_decode ( "SELECT CAST(Tipo as varchar(55)) as Tipo,CAST(Numero as varchar(55)) as Numero,CAST(Descripcion as varchar(55)) as Descripcion,visible FROM Tipos WHERE Tipo = 9 ORDER BY Descripcion" ) );
			
			for(; DBNext ( $t ) ;) {
				$Tipo_Tipos = ( DBCampo ( $t , "Tipo" ) );
				$Numero_Tipos = ( DBCampo ( $t , "Numero" ) );
				$Descripcion_Tipos = ( DBCampo ( $t , "Descripcion" ) );
				$Visible_Tipos = ( DBCampo ( $t , "visible" ) );
				
				if ( strcmp ( strtoupper ( $Internet_Tipo ) , strtoupper ( $Descripcion_Tipos ) ) == 0 ) {
					$selecttipointernet = $selecttipointernet . '<option value="' . utf8_encode ( $Descripcion_Tipos ) . '" selected="selected">' . utf8_encode ( $Descripcion_Tipos ) . '</option>';
				} else {
					$selecttipointernet = $selecttipointernet . '<option value="' . utf8_encode ( $Descripcion_Tipos ) . '">' . utf8_encode ( $Descripcion_Tipos ) . '</option>';
				}
			}
			DBFree ( $t );
			
			$selecttipointernet = $selecttipointernet . '</select>';
		}
		// *************************TIPO
		// *************************ESPEJO
		if ( 2 == 2 ) {
			$selectRegistrosEspejo = '<select id="selectRegistrosEspejo" name="selectRegistrosEspejo" style="width:100;">';
			$u = DBSelect ( utf8_decode ( "SELECT * FROM OPENQUERY (NavisionSQL,'SELECT [ID usuario] AS Resultado FROM Usuario WHERE [SQL Usuario] <> 1 order by [ID usuario]')" ) );
			
			for(; DBNext ( $u ) ;) {
				$CodigoRegistroEspejo = ( DBCampo ( $u , utf8_decode ( "Resultado" ) ) );
				
				if ( utf8_encode ( $Navision_Usuario_Espejo ) == utf8_encode ( $CodigoRegistroEspejo ) ) {
					$selectRegistrosEspejo = $selectRegistrosEspejo . '<option value="' . utf8_encode ( $CodigoRegistroEspejo ) . '" selected="selected">' . utf8_encode ( $CodigoRegistroEspejo ) . '</option>';
				} else {
					
					$selectRegistrosEspejo = $selectRegistrosEspejo . '<option value="' . utf8_encode ( $CodigoRegistroEspejo ) . '">' . utf8_encode ( $CodigoRegistroEspejo ) . '</option>';
				}
			}
		}
		$selectRegistrosEspejo = $selectRegistrosEspejo . '</select>';
		// *************************ESPEJO
		// *************************ALTA DEPARTAMENTO
		if ( 2 == 2 ) {
			
			$u = DBSelect ( utf8_decode ( "SELECT * FROM OPENQUERY (NavisionSQL,'SELECT COUNT(*) AS Resultado FROM [NAVSQL].[dbo].[Departamento]')" ) );
			$RegistrosDepartamento = DBCampo ( $u , "Resultado" );
			DBFree ( $u );
			
			$selectdepartamento_alta = '<select id="selectdepartamentoalta" name="selectdepartamentoalta" style="width:250;" onchange="javascript:cambia_departamento_alta(this);">';
			
			$i = 1;
			while ( $i <= $RegistrosDepartamento ) {
				$u = DBSelect ( utf8_decode ( "SELECT Código,Nombre,Bloqueado,Fila FROM (SELECT *,ROW_NUMBER() OVER(ORDER BY Código) Fila FROM OPENQUERY (NavisionSQL,'SELECT * FROM [NAVSQL].[dbo].[Departamento]')) Consulta	WHERE Fila BETWEEN " . $i . " AND " . ( ( $i + 200 ) - 1 ) . " ORDER BY Nombre" ) );
				for(; DBNext ( $u ) ;) {
					$CodigoDepartamento = ( DBCampo ( $u , utf8_decode ( "Código" ) ) );
					$NombreDepartamento = ( DBCampo ( $u , "Nombre" ) );
					$BloqueadoDepartamento = ( DBCampo ( $u , "Bloqueado" ) );
					if ( $Departamento_Alta == utf8_encode ( $CodigoDepartamento ) ) {
						$selectdepartamento_alta = $selectdepartamento_alta . '<option value="' . utf8_encode ( $CodigoDepartamento ) . '" selected="selected">' . utf8_encode ( $CodigoDepartamento ) . ' (' . utf8_encode ( $NombreDepartamento ) . ')</option>';
					} else {
						if ( utf8_encode ( $BloqueadoDepartamento ) == 0 ) {
							$selectdepartamento_alta = $selectdepartamento_alta . '<option value="' . utf8_encode ( $CodigoDepartamento ) . '">' . utf8_encode ( $CodigoDepartamento ) . ' (' . utf8_encode ( $NombreDepartamento ) . ')</option>';
						}
					}
				}
				DBFree ( $u );
				$i = $i + 200;
			}
			$selectdepartamento_alta = $selectdepartamento_alta . '</select>';
			if ( $Departamento_Alta == '' ) {
			}
		}
		// *************************ALTA DEPARTAMENTO
		// *************************TIPO OPERARIO MAPEX
		if ( 2 == 2 ) {
			$selectRegistrosTipoMapexTerminal = '<select id="selectRegistrosTipoMapexTermimal" name="selectRegistrosTipoMapexTermimal" style="width:100;">';
			$u = DBSelect ( utf8_decode ( "SELECT CAST(Desc_tipooperario as varchar(250)) as Desc_tipooperario FROM [mapex].[mapexbp].[dbo].[cfg_tipooperario] WHERE Activo = 1 order by Desc_tipooperario" ) );
			
			for(; DBNext ( $u ) ;) {
				$CodigoRegistroTipoMapexTerminal = ( DBCampo ( $u , utf8_decode ( "Desc_tipooperario" ) ) );
				
				if ( utf8_encode ( $Mapex_Terminal_Tipo ) == utf8_encode ( $CodigoRegistroTipoMapexTerminal ) ) {
					$selectRegistrosTipoMapexTerminal = $selectRegistrosTipoMapexTerminal . '<option value="' . utf8_encode ( $CodigoRegistroTipoMapexTerminal ) . '" selected="selected">' . utf8_encode ( $CodigoRegistroTipoMapexTerminal ) . '</option>';
				} else {
					
					$selectRegistrosTipoMapexTerminal = $selectRegistrosTipoMapexTerminal . '<option value="' . utf8_encode ( $CodigoRegistroTipoMapexTerminal ) . '">' . utf8_encode ( $CodigoRegistroTipoMapexTerminal ) . '</option>';
				}
			}
			$selectRegistrosTipoMapexTerminal = $selectRegistrosTipoMapexTerminal . '</select>';
		}
		
		// *************************TIPO OPERARIO MAPEX
		// *************************ESPEJO OPERARIO MAPEX
		if ( 2 == 2 ) {
			$selectRegistrosEspejoMapexTerminal = '<select id="selectRegistrosEspejoMapexTermimal" name="selectRegistrosEspejoMapexTermimal" style="width:100;">';
			$u = DBSelect ( utf8_decode ( "SELECT Id_tipooperario,CAST(Desc_operario as varchar(250)) as Desc_operario FROM [mapex].[mapexbp].[dbo].[cfg_operario] WHERE Activo = 1 order by Desc_operario" ) );
			
			for(; DBNext ( $u ) ;) {
				$CodigoRegistroTipoMapexTerminal = ( DBCampo ( $u , utf8_decode ( "Desc_operario" ) ) );
				$Id_tipooperario = ( DBCampo ( $u , ( "Id_tipooperario" ) ) );
				
				if ( utf8_encode ( $Mapex_Terminal_Espejo ) == utf8_encode ( $CodigoRegistroTipoMapexTerminal ) ) {
					$selectRegistrosEspejoMapexTerminal = $selectRegistrosEspejoMapexTerminal . '<option value="' . utf8_encode ( $CodigoRegistroTipoMapexTerminal ) . '" selected="selected">' . utf8_encode ( $CodigoRegistroTipoMapexTerminal ) . '</option>';
				} else {
					
					$selectRegistrosEspejoMapexTerminal = $selectRegistrosEspejoMapexTerminal . '<option value="' . utf8_encode ( $CodigoRegistroTipoMapexTerminal ) . '">' . utf8_encode ( $CodigoRegistroTipoMapexTerminal ) . '</option>';
				}
			}
			$selectRegistrosEspejoMapexTerminal = $selectRegistrosEspejoMapexTerminal . '</select>';
		}
		// *************************TIPO OPERARIO MAPEX
		if ( 2 == 2 ) {
			$selectRegistrosTipoMapexAplicacion = '<select id="selectRegistrosTipoMapexAplicacion" name="selectRegistrosTipoMapexAplicacion" style="width:100;">';
			$u = DBSelect ( utf8_decode ( "SELECT cast([desc_tipousuario] as varchar(250)) as desc_tipousuario FROM [MAPEX].[mapexbp].[dbo].[cfg_tipousuario] where [activo] = 1 order by [desc_tipousuario]" ) );
			
			for(; DBNext ( $u ) ;) {
				$CodigoRegistroTipoMapexTerminal = ( DBCampo ( $u , utf8_decode ( "desc_tipousuario" ) ) );
				
				if ( utf8_encode ( $Mapex_Aplicacion_Tipo ) == utf8_encode ( $CodigoRegistroTipoMapexTerminal ) ) {
					$selectRegistrosTipoMapexAplicacion = $selectRegistrosTipoMapexAplicacion . '<option value="' . utf8_encode ( $CodigoRegistroTipoMapexTerminal ) . '" selected="selected">' . utf8_encode ( $CodigoRegistroTipoMapexTerminal ) . '</option>';
				} else {
					
					$selectRegistrosTipoMapexAplicacion = $selectRegistrosTipoMapexAplicacion . '<option value="' . utf8_encode ( $CodigoRegistroTipoMapexTerminal ) . '">' . utf8_encode ( $CodigoRegistroTipoMapexTerminal ) . '</option>';
				}
			}
			$selectRegistrosTipoMapexAplicacion = $selectRegistrosTipoMapexAplicacion . '</select>';
		}
		
		// *************************TIPO OPERARIO MAPEX
		// *************************ESPEJO OPERARIO MAPEX
		if ( 2 == 2 ) {
			$selectRegistrosEspejoMapexAplicacion = '<select id="selectRegistrosEspejoMapexAplicacion" name="selectRegistrosEspejoMapexAplicacion" style="width:100;">';
			$u = DBSelect ( utf8_decode ( "SELECT CAST(Desc_usuario as varchar(250)) as Desc_usuario FROM [MAPEX].[mapexbp].[dbo].[sys_usuario] where [activo] = 1 order by Desc_usuario" ) );
			
			for(; DBNext ( $u ) ;) {
				$CodigoRegistroTipoMapexTerminal = ( DBCampo ( $u , utf8_decode ( "Desc_usuario" ) ) );
				
				if ( utf8_encode ( $Mapex_Aplicacion_Espejo ) == utf8_encode ( $CodigoRegistroTipoMapexTerminal ) ) {
					$selectRegistrosEspejoMapexAplicacion = $selectRegistrosEspejoMapexAplicacion . '<option value="' . utf8_encode ( $CodigoRegistroTipoMapexTerminal ) . '" selected="selected">' . utf8_encode ( $CodigoRegistroTipoMapexTerminal ) . '</option>';
				} else {
					
					$selectRegistrosEspejoMapexAplicacion = $selectRegistrosEspejoMapexAplicacion . '<option value="' . utf8_encode ( $CodigoRegistroTipoMapexTerminal ) . '">' . utf8_encode ( $CodigoRegistroTipoMapexTerminal ) . '</option>';
				}
			}
			
			$selectRegistrosEspejoMapexAplicacion = $selectRegistrosEspejoMapexAplicacion . '</select>';
		}
		// *************************ESPEJO OPERARIO MAPEX
		
		// MONTAMOS LA TABLA
		// $select_alta_usuario = $select_alta_usuario . '<tr><td colspan="6" align="center"><font color="red"><h1>NO DAR DE ALTA USUARIOS, CONSULTAR CON ALBERTO TI</h1></font></td></tr>';
		
		$select_alta_usuario = $select_alta_usuario . '<tr>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . 'Nombre';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . '<input type="text" name="alta_usuario_nombre" align="center" maxlength="250" style="width: 250;" onChange="refrescar_usuario_incorporacion();" id="alta_usuario_nombre" value="' . utf8_encode ( $Nombre ) . '"/>';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . 'Apellido 1';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . '<input type="text" name="alta_usuario_apellido_1" align="center" maxlength="250" style="width: 250;" id="alta_usuario_apellido_1" onChange="refrescar_usuario_incorporacion();" value="' . utf8_encode ( $Apellido_1 ) . '"/>';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . 'Apellido 2';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . '<input type="text" name="alta_usuario_apellido_2" align="center" maxlength="250" style="width: 250;" id="alta_usuario_apellido_2" onChange="refrescar_usuario_incorporacion();" value="' . utf8_encode ( $Apellido_2 ) . '"/>';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		$select_alta_usuario = $select_alta_usuario . '</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '<tr>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . 'Fecha Incorporacion';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . '<input type="text" name="fechaincorporacion" style="width:250px;" align="center" id="fechaincorporacion" value="' . $FECHA_INCORPORACION . '"/>';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . 'Usuario';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . '<input type="text" name="Usuario_Incorporacion" maxlength="20" align="center" style="width: 250;" id="Usuario_Incorporacion" value="' . utf8_encode ( $Usuario_Incorporacion ) . '"/>';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . 'Mail';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . '<input type="text" name="Mail_Incorporacion" align="center" style="width: 250;" id="Mail_Incorporacion" value="' . utf8_encode ( $Mail_Incorporacion ) . '"/>';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		$select_alta_usuario = $select_alta_usuario . '</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '<tr>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . 'Reporta a';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . '<input id="autocompletereportaa" spb_submit="1" name="autocompletereportaa" style="width:250px;" value="' . utf8_encode ( $Reporta_a ) . '">';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . 'Permisos como';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . '<input id="autocompletepermisoscomo" spb_submit="1" name="autocompletepermisoscomo" style="width:250px;" value="' . utf8_encode ( $Permisos_como ) . '">';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . 'Password';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . '<input type="text" name="alta_usuario_pass" align="center" maxlength="250" style="width: 250;" id="alta_usuario_pass" value="' . utf8_encode ( $Password ) . '"/>';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		
		$select_alta_usuario = $select_alta_usuario . '</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '<tr>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . 'Departamento';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . $selectdepartamento_alta;
		$select_alta_usuario = $select_alta_usuario . '</td>';
		
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$select_alta_usuario = $select_alta_usuario . 'Programa';
		$select_alta_usuario = $select_alta_usuario . '</td>';
		$select_alta_usuario = $select_alta_usuario . '<td>';
		$selectdepartamento_programa_buscado = '';
		include "obtener_relacion_alta.php";
		$select_alta_usuario = $select_alta_usuario . $selectdepartamento_programa_buscado;
		$select_alta_usuario = $select_alta_usuario . '</td>';
		
		$select_alta_usuario = $select_alta_usuario . '</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '<tr>';
		$select_alta_usuario = $select_alta_usuario . '	<td colspan="6">';
		$select_alta_usuario = $select_alta_usuario . '		<table class="tabla_datos_Alta_accesos" >';
		$select_alta_usuario = $select_alta_usuario . '			<tr>';
		$select_alta_usuario = $select_alta_usuario . '				<td style="padding: 10px;border:solid 1px grey;"><strong> Navision</strong>';
		$select_alta_usuario = $select_alta_usuario . '					<table class="tabla_datos_Alta_accesos_" >';
		$select_alta_usuario = $select_alta_usuario . '						<tr style="display:block;">';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								Accede ';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		if ( $Navision_Acceso == '1' ) {
			$checked = ' checked ';
			$display_acceso_navision = ' style="display:block;" ';
		} else {
			$checked = '  ';
			$display_acceso_navision = ' style="display:none;" ';
		}
		$imagen_refrescar_acceso_navision = '<img src="./imagenes/Very-Basic-Refresh-icon.png" id="imagen_refresco_acceso_navision" width="10" ' . $display_acceso_navision . ' onClick="refrescar_usuario_password_navision();"></img>';
		
		$select_alta_usuario = $select_alta_usuario . '								<input type="checkbox"  name="alta_usuario_acceso_navision" value="' . $Navision_Acceso . '" id="alta_usuario_acceso_navision" onClick="accede_navision(this);" ' . $checked . '/>';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '						<tr id="tr_dato_acceso_navision_1" ' . $display_acceso_navision . '>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								Usuario ';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								<input type="text" name="alta_usuario_acceso_navision_user" maxlength="250" style="width: 100;" id="alta_usuario_acceso_navision_user" value="' . utf8_encode ( $Navision_Usuario ) . '"/>' . $imagen_refrescar_acceso_navision;
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '						<tr id="tr_dato_acceso_navision_2" ' . $display_acceso_navision . '>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								Password ';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								<input type="text" name="alta_usuario_acceso_navision_pass" maxlength="250" style="width: 100;" id="alta_usuario_acceso_navision_pass" value="' . utf8_encode ( $Navision_Password ) . '"/>';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '						<tr id="tr_dato_acceso_navision_3" ' . $display_acceso_navision . '>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								Espejo ';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . $selectRegistrosEspejo;
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '					</table>';
		$select_alta_usuario = $select_alta_usuario . '				</td>';
		
		// ---------------MAPEX
		$select_alta_usuario = $select_alta_usuario . '				<td style="padding: 10px;border:solid 1px grey;"><strong> Mapex</strong>';
		$select_alta_usuario = $select_alta_usuario . '					<table class="tabla_datos_Alta_accesos_" >';
		$select_alta_usuario = $select_alta_usuario . '						<tr style="display:block;">';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								<strong>Accede Terminal</strong>';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		if ( $Mapex_Terminal_Acceso == '1' ) {
			$checked = ' checked ';
			$display_Mapex_Terminal_Acceso = ' style="display:block;" ';
		} else {
			$checked = '  ';
			$display_Mapex_Terminal_Acceso = ' style="display:none;" ';
		}
		
		$select_alta_usuario = $select_alta_usuario . '								<input type="checkbox"  name="alta_usuario_acceso_Mapex_Terminal" value="' . $Mapex_Terminal_Acceso . '" id="alta_usuario_acceso_Mapex_Terminal" onClick="accede_Mapex_Terminal(this);" ' . $checked . '/>';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '						<tr id="tr_dato_acceso_Mapex_Terminal_1" ' . $display_Mapex_Terminal_Acceso . '>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								Tipo ';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . $selectRegistrosTipoMapexTerminal;
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '						<tr id="tr_dato_acceso_Mapex_Terminal_2" ' . $display_Mapex_Terminal_Acceso . '>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								Espejo ';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . $selectRegistrosEspejoMapexTerminal;
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '						<tr style="display:block;">';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								<strong>Accede PC</strong>';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		if ( $Mapex_Aplicacion_Acceso == '1' ) {
			$checked = ' checked ';
			$display_Mapex_Aplicacion_Acceso = ' style="display:block;" ';
		} else {
			$checked = '  ';
			$display_Mapex_Aplicacion_Acceso = ' style="display:none;" ';
		}
		
		$select_alta_usuario = $select_alta_usuario . '								<input type="checkbox"  name="alta_usuario_acceso_Mapex_Aplicacion" value="' . $Mapex_Aplicacion_Acceso . '" id="alta_usuario_acceso_Mapex_Aplicacion" onClick="accede_Mapex_Aplicacion(this);" ' . $checked . '/>';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '						<tr id="tr_dato_acceso_Mapex_Aplicacion_1" ' . $display_Mapex_Aplicacion_Acceso . '>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								Tipo ';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . $selectRegistrosTipoMapexAplicacion;
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '						<tr id="tr_dato_acceso_Mapex_Aplicacion_2" ' . $display_Mapex_Aplicacion_Acceso . '>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								Espejo ';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . $selectRegistrosEspejoMapexAplicacion;
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '					</table>';
		$select_alta_usuario = $select_alta_usuario . '				</td>';
		// ---------------MAPEX
		
		$select_alta_usuario = $select_alta_usuario . '				<td style="padding: 10px;border:solid 1px grey;"> <strong>Internet</strong>';
		$select_alta_usuario = $select_alta_usuario . '					<table class="tabla_datos_Alta_accesos_" >';
		$select_alta_usuario = $select_alta_usuario . '						<tr style="display:block;">';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								Accede ';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		if ( $Internet_Acceso == '1' ) {
			$checked = ' checked ';
			$display_acceso_Internet_Acceso = ' style="display:block;" ';
		} else {
			$checked = '  ';
			$display_acceso_Internet_Acceso = ' style="display:none;" ';
		}
		
		$select_alta_usuario = $select_alta_usuario . '								<input type="checkbox"  name="alta_usuario_acceso_internet" value="' . $Internet_Acceso . '" id="alta_usuario_acceso_internet" onClick="accede_internet(this);" ' . $checked . '/>';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '						<tr id="tr_dato_acceso_internet_1" ' . $display_acceso_Internet_Acceso . '>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								Tipo ';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								' . $selecttipointernet;
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '					</table>';
		$select_alta_usuario = $select_alta_usuario . '				</td>';
		
		$select_alta_usuario = $select_alta_usuario . '				<td style="padding: 10px;border:solid 1px grey;"><strong> Remoto</strong>';
		$select_alta_usuario = $select_alta_usuario . '					<table class="tabla_datos_Alta_accesos_" >';
		$select_alta_usuario = $select_alta_usuario . '						<tr style="display:block;">';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								Accede ';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		if ( $Remoto_Acceso == '1' ) {
			$checked = ' checked ';
			$display_acceso_Remoto_Acceso = ' style="display:block;" ';
		} else {
			$checked = '  ';
			$display_acceso_Remoto_Acceso = ' style="display:none;" ';
		}
		
		$select_alta_usuario = $select_alta_usuario . '								<input type="checkbox"  name="alta_usuario_acceso_remoto" value="' . $Remoto_Acceso . '" id="alta_usuario_acceso_remoto" onClick="accede_remoto(this);" ' . $checked . '/>';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '					</table>';
		$select_alta_usuario = $select_alta_usuario . '				</td>';
		
		// --------------------
		$select_alta_usuario = $select_alta_usuario . '				<td style="padding: 10px;border:solid 1px grey;"><strong> Crear AD</strong>';
		$select_alta_usuario = $select_alta_usuario . '					<table class="tabla_datos_Alta_accesos_" >';
		$select_alta_usuario = $select_alta_usuario . '						<tr style="display:block;">';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								Crear Usuario AD ';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		if ( $Crear_AD == '1' ) {
			$checked = ' checked ';
		} else {
			$checked = '  ';
		}
		
		$select_alta_usuario = $select_alta_usuario . '								<input type="checkbox"  name="alta_usuario_crear_ad" value="' . $Crear_AD . '" id="alta_usuario_crear_ad" onClick="accede_crear_ad(this);" ' . $checked . '/>';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		// -----------------
		
		$select_alta_usuario = $select_alta_usuario . '					</table>';
		$select_alta_usuario = $select_alta_usuario . '				</td>';
		
		// ---------------herramientas
		$select_alta_usuario = $select_alta_usuario . '				<td style="padding: 10px;border:solid 1px grey;"> <strong>Herramientas</strong>';
		$select_alta_usuario = $select_alta_usuario . '					<table class="tabla_datos_Alta_accesos_" >';
		$select_alta_usuario = $select_alta_usuario . '						<tr style="display:block;">';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								PC ';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		if ( $PC_Acceso == '1' ) {
			$checked = ' checked ';
			$display_acceso_Remoto_Acceso = ' style="display:block;" ';
		} else {
			$checked = '  ';
			$display_acceso_Remoto_Acceso = ' style="display:none;" ';
		}
		$select_alta_usuario = $select_alta_usuario . '								<input type="checkbox"  name="alta_usuario_acceso_pc" value="' . $PC_Acceso . '" id="alta_usuario_acceso_pc" onClick="accede_pc(this);" ' . $checked . '/>';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		
		$select_alta_usuario = $select_alta_usuario . '						<tr style="display:block;">';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								Portátil ';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		if ( $Portatil_Acceso == '1' ) {
			$checked = ' checked ';
			$display_acceso_Remoto_Acceso = ' style="display:block;" ';
		} else {
			$checked = '  ';
			$display_acceso_Remoto_Acceso = ' style="display:none;" ';
		}
		$select_alta_usuario = $select_alta_usuario . '								<input type="checkbox"  name="alta_usuario_acceso_portatil" value="' . $Portatil_Acceso . '" id="alta_usuario_acceso_portatil" onClick="accede_portatil(this);" ' . $checked . '/>';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		$select_alta_usuario = $select_alta_usuario . '						<tr style="display:block;">';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								Teléfono ';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		if ( $Telefono_Acceso == '1' ) {
			$checked = ' checked ';
			$display_acceso_Remoto_Acceso = ' style="display:block;" ';
		} else {
			$checked = '  ';
			$display_acceso_Remoto_Acceso = ' style="display:none;" ';
		}
		$select_alta_usuario = $select_alta_usuario . '								<input type="checkbox"  name="alta_usuario_acceso_telefono" value="' . $Telefono_Acceso . '" id="alta_usuario_acceso_telefono" onClick="accede_telefono(this);" ' . $checked . '/>';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		$select_alta_usuario = $select_alta_usuario . '						<tr style="display:block;">';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		$select_alta_usuario = $select_alta_usuario . '								Móvil ';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '							<td>';
		if ( $Telefono_Movil_Acceso == '1' ) {
			$checked = ' checked ';
			$display_acceso_Remoto_Acceso = ' style="display:block;" ';
		} else {
			$checked = '  ';
			$display_acceso_Remoto_Acceso = ' style="display:none;" ';
		}
		$select_alta_usuario = $select_alta_usuario . '								<input type="checkbox"  name="alta_usuario_acceso_movil" value="' . $Telefono_Movil_Acceso . '" id="alta_usuario_acceso_movil" onClick="accede_movil(this);" ' . $checked . '/>';
		$select_alta_usuario = $select_alta_usuario . '							</td>';
		$select_alta_usuario = $select_alta_usuario . '						</tr>';
		$select_alta_usuario = $select_alta_usuario . '					</table>';
		$select_alta_usuario = $select_alta_usuario . '				</td>';
		// ---------------herramientas
		$select_alta_usuario = $select_alta_usuario . '			</tr>';
		$select_alta_usuario = $select_alta_usuario . '		</table>';
		$select_alta_usuario = $select_alta_usuario . '	</td>';
		$select_alta_usuario = $select_alta_usuario . '</tr>';
	}
	$select_alta_usuario = $select_alta_usuario . '</table>';
	if ( $contador_altas_usuario > 0 ) {
		if ( $Estado == 0 ) { // ¿YA ESTA CREADO?
			if ( $id_pantalla == 1 ) {
				$select_alta_usuario = $select_alta_usuario . '<div id="div_crear_alta_usuario"><img id="crear_usuario" style="cursor:pointer;" src="./imagenes/GUARDAR_USUARIO.png" title="Guardar y crear usuario" onClick="crear_alta_usuario(' . $id_pantalla . ',' . $incidencia . ',1);"></div>';
			} else {
				$select_alta_usuario = $select_alta_usuario . '<div id="div_crear_alta_usuario"><img id="crear_usuario" style="cursor:pointer;" src="./imagenes/GUARDAR_USUARIO.png" title="Guardar y crear usuario"></div>';
			}
		}
	}
	return $select_alta_usuario;
}
function Get_Adjuntos( $id_pantalla , $incidencia ) {
	$devolver = '';
	$maxfilesize_permitido = ini_get ( 'upload_max_filesize' );
	$contador_de_tablas = 1;
	$contador_de_filas = 1;
	$ExistenFicheros = 0;
	$tablaficheros = '';
	$tablaficheros1 = '';
	$tablaficheros2 = '';
	$tablaficheros3 = '';
	$tablaficheros4 = '';
	$automatico= '';
	
	if ( $id_pantalla == 1 ) {
		$u = DBSelect ( ( "SELECT [Tipo],[Numero],Publico ,[Tarea],CAST(Descripcion as varchar(250)) as Descripcion,CAST(Fichero as varchar(250)) as Fichero, usuario as Usuario, CONVERT(varchar(50),Fecha,103) as Fecha FROM [Adjuntos] where Tarea = " . $incidencia . "" ) );
		
		$e = DBSelect (("SELECT  COUNT(Numero) as c FROM [Adjuntos] where Tarea = " . $incidencia . "" ));
		$ExistenFicheros = ( DBCampo ( $e , "c" ) );
		
	} else {
		$u = DBSelect ( ( "SELECT [Tipo],[Numero],Publico ,[Tarea],CAST(Descripcion as varchar(250)) as Descripcion,CAST(Fichero as varchar(250)) as Fichero, usuario as Usuario, CONVERT(varchar(50),Fecha,103) as Fecha FROM [Adjuntos] where Tarea = " . $incidencia . " and Publico IN (1,2) " ) );
	   
		$e = DBSelect ( ( "SELECT  COUNT(Numero) as c FROM [Adjuntos] where Tarea = " . $incidencia . " and Publico IN (1,2) " ) );
		$ExistenFicheros = ( DBCampo ( $e , "c" ) );
		
	}
	
	if ($ExistenFicheros > 0 ){
		$tablaficheros1 = $tablaficheros1 . '<tr style="border: 2px solid #a59e9e; font-size: 15px;"><td style="width: 5%; padding-left:4px;"> Estado </td><td style="width: 60%;"> Archivo </td><td style="width: 25%;"> Subido </td><td style="width: 10%;"> Fecha Subida </td></tr>';
	}
	
	for(; DBNext ( $u ) ;) {
		$contador_de_filas = $contador_de_filas + 1;
		$Publico = ( DBCampo ( $u , ( "Publico" ) ) );
		$FicheroNumero = ( DBCampo ( $u , ( "Numero" ) ) );
		$Tipo = ( DBCampo ( $u , ( "Tipo" ) ) );
		$FicheroTarea = ( DBCampo ( $u , "Tarea" ) );
		$FicheroDescripcion = ( DBCampo ( $u , "Descripcion" ) );
		$FicheroCarpeta = ( DBCampo ( $u , "Fichero" ) );
		$UsuarioFichero = ( DBCampo ( $u , "Usuario" ) );
		$FechaSubida = ( DBCampo ( $u , "Fecha" ) );
		
		$r = DBSelect (("SELECT Name as Nombre FROM [GestionIDM].[dbo].[LDAP] WHERE sAMAccountName ='". $UsuarioFichero ."'"));
		$UsuarioNombre = ( DBCampo ( $r, "Nombre" ) );
		
		if ( $id_pantalla == 1 ) {
			if ( $Publico == '1' ) {
				$imagen_archivo = '<img src="./imagenes/transparent.png" width="20" name="IMAGEN_FICHERO_' . $FicheroNumero . '" id="IMAGEN_FICHERO_' . $FicheroNumero . '"></img>';
			} else {
				if ( $Publico == '2' ) {
					$imagen_archivo = '<img src="./imagenes/share.png" width="20" name="IMAGEN_FICHERO_' . $FicheroNumero . '" id="IMAGEN_FICHERO_' . $FicheroNumero . '"></img>';
				} else {
					$imagen_archivo = '<img src="./imagenes/private.png" width="20" name="IMAGEN_FICHERO_' . $FicheroNumero . '" id="IMAGEN_FICHERO_' . $FicheroNumero . '"></img>';
				}
			}
		} else {
			if ( $Publico == '2' ) {
				$imagen_archivo = '<img src="./imagenes/share.png" width="20" name="IMAGEN_FICHERO_' . $FicheroNumero . '" id="IMAGEN_FICHERO_' . $FicheroNumero . '"></img>';
			} else {
				$imagen_archivo = '<img src="./imagenes/transparent.png" width="20" name="IMAGEN_FICHERO_' . $FicheroNumero . '" id="IMAGEN_FICHERO_' . $FicheroNumero . '"></img>';
			}
		}
		
		$imagen_archivo_tipo = '<img src="./imagenes/' . $Tipo . '.png" width="20" name="IMAGEN_FICHERO_TIPO_' . $FicheroNumero . '" id="IMAGEN_FICHERO_TIPO_' . $FicheroNumero . '"></img>';
		/*
		if ( $contador_de_tablas == 1 ) {
			$tablaficheros1 = $tablaficheros1 . '<tr id="anadido_imagen_' . $contador_de_filas . '"><td style="cursor: pointer;">' . $imagen_archivo . $imagen_archivo_tipo . '</td><td style="cursor: pointer;" title="Descargar el fichero"><a class="ver_fichero" id="FICHERO_' . $FicheroNumero . '" href="select/archivo.php?id=' . $FicheroNumero . '">' . utf8_encode ( $FicheroDescripcion ) . '</td></tr>';
		}
		if ( $contador_de_tablas == 2 ) {
			$tablaficheros2 = $tablaficheros2 . '<tr id="anadido_imagen_' . $contador_de_filas . '"><td style="cursor: pointer;">' . $imagen_archivo . $imagen_archivo_tipo . '</td><td style="cursor: pointer;" title="Descargar el fichero"><a class="ver_fichero" id="FICHERO_' . $FicheroNumero . '"  href="select/archivo.php?id=' . $FicheroNumero . '">' . utf8_encode ( $FicheroDescripcion ) . '</td></tr>';
		}
		if ( $contador_de_tablas == 3 ) {
			$tablaficheros3 = $tablaficheros3 . '<tr id="anadido_imagen_' . $contador_de_filas . '"><td style="cursor: pointer;">' . $imagen_archivo . $imagen_archivo_tipo . '</td><td style="cursor: pointer;" title="Descargar el fichero"><a class="ver_fichero" id="FICHERO_' . $FicheroNumero . '"  href="select/archivo.php?id=' . $FicheroNumero . '">' . utf8_encode ( $FicheroDescripcion ) . '</td></tr>';
		}
		if ( $contador_de_tablas == 4 ) {
			$tablaficheros4 = $tablaficheros4 . '<tr id="anadido_imagen_' . $contador_de_filas . '"><td style="cursor: pointer;">' . $imagen_archivo . $imagen_archivo_tipo . '</td><td style="cursor: pointer;" title="Descargar el fichero"><a class="ver_fichero" id="FICHERO_' . $FicheroNumero . '"  href="select/archivo.php?id=' . $FicheroNumero . '">' . utf8_encode ( $FicheroDescripcion ) . '</td></tr>';
		}
		$contador_de_tablas = $contador_de_tablas + 1;
		if ( $contador_de_tablas > 5 ) {
			$contador_de_tablas = 1;
		}
		*/
		
		if ($FicheroDescripcion == 'CorreoOriginal.eml') {
			$automatico ='(Automático)';	
		}
		
		$tablaficheros1 = $tablaficheros1 . '<tr style="border: 1px solid #a59e9e; padding: 1px 2px;"  id="anadido_imagen_' . $contador_de_filas . '">
																			<td style="width: 5%;">' . $imagen_archivo . $imagen_archivo_tipo . '</td>
																			<td style="width: 60%;"><a style="cursor: pointer;" class="ver_fichero" id="FICHERO_' . $FicheroNumero . '" href="select/archivo.php?id=' . $FicheroNumero . '">' . utf8_encode ( $FicheroDescripcion ) . '</td>
																			<td style="width: 25%;">'.utf8_encode ( $UsuarioNombre ).' '.$automatico.'</td>
																			<td style="width: 10%;">'.$FechaSubida.'</td>
																	</tr>';
		/*
		$tablaficheros1 = $tablaficheros1 . '<tr style="wid;" id="anadido_imagen_' . $contador_de_filas . '">
																			<td style="cursor: pointer;">' . $imagen_archivo . $imagen_archivo_tipo . '</td>
																			<td style="cursor: pointer;" title="Descargar el fichero"><a class="ver_fichero" id="FICHERO_' . $FicheroNumero . '" href="select/archivo.php?id=' . $FicheroNumero . '">' . utf8_encode ( $FicheroDescripcion ) . '</td>
																	</tr>';
		*/
	}
	DBFree ( $u );
	$devolver = $devolver . '<br>';
	$devolver = $devolver . '<span class="btn btn-success fileinput-button"><i class="glyphicon glyphicon-plus"></i><span>Archivo...</span><input id="fileupload" type="file" name="files[]" multiple></span>    <br> ' . $maxfilesize_permitido . ' Máximo permitido.	<br>    <div id="progress" class="progress">        <div class="progress-bar progress-bar-success"></div>    </div>        <div id="files" class="files"></div>';
	$devolver = $devolver . '<div style="float:left; width: 100%;" id="adjuntosdiv">';
	$devolver = $devolver . '<table style ="width: 97%;margin: 0% 1.5%;" class="tabla-adjuntar" id="tablaadjuntos1">';
	$devolver = $devolver . $tablaficheros1;
	$devolver = $devolver . '</table>';
	$devolver = $devolver . '</div>';
	
	/*
	$devolver = $devolver . '<div style="float:left;width: 25%;">';
	$devolver = $devolver . '<table class="tabla-adjuntar" id="tablaadjuntos2">';
	$devolver = $devolver . $tablaficheros2;
	$devolver = $devolver . '</table>';
	$devolver = $devolver . '</div>';
	$devolver = $devolver . '<div style="float:left;width: 25%;">';
	$devolver = $devolver . '<table class="tabla-adjuntar" id="tablaadjuntos3">';
	$devolver = $devolver . $tablaficheros3;
	$devolver = $devolver . '</table>';
	$devolver = $devolver . '</div>';
	$devolver = $devolver . '<div style="float:left;width: 25%;">';
	$devolver = $devolver . '<table class="tabla-adjuntar" id="tablaadjuntos4">';
	$devolver = $devolver . $tablaficheros4;
	$devolver = $devolver . '</table>';
	$devolver = $devolver . '</div>';
	*/
	return $devolver;
}
?>
