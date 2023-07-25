<?php
// UTF ñáç
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$usuario = 'alberto.ruiz';
if (isset($_POST["usuario"])) {
	$usuario = $_POST["usuario"];
}

// *** PLANIFICACIONES IMPORTANTES Y CUÑAS NUEVAS ***
//alberto. quito las planificaciones importantes 30/06/2020
$q = "
SELECT COUNT(Id) AS T FROM Planificador P WITH (NOLOCK)
WHERE Estado = 'Activo' 
	AND Asignado_Usuario = '$usuario'
	AND Situacion NOT IN ('Completado','Cancelado')
	AND Semana = DATEPART ( ISO_WEEK , GETDATE() ) 
	AND Anyo = (SELECT dbo.ISOyear(GETDATE()))
	AND (No_Planificado = 'SI' /*OR Prioridad = 'Alta'*/)
	AND 0 = (SELECT COUNT(Numero) FROM HORAS WITH (NOLOCK)
			 WHERE Tarea = P.Tarea 
				AND Semana_ISO =  DATEPART ( ISO_WEEK , GETDATE() ) 
				AND Anyo_ISO = (SELECT dbo.ISOyear(GETDATE())) 
			)
";
$q = DBSelect ( utf8_decode ( $q ) );
$NUEVAS_CUNYAS = utf8_encode(DBCampo($q, "T" ));
// *** PLANIFICACIONES IMPORTANTES Y CUÑAS NUEVAS ***

// *** BANNER DE TAREAS ***
$q = "
	DECLARE @DIAS_DIFF INT = (SELECT -1 * ISNULL(SUM(CONVERT(INT,ISNULL(Valor,0))),0) FROM [GestionPDT].[dbo].[Configuracion] WHERE USuario='$usuario' and parametro = 'mi_hist_ban_tareas')
	SELECT 
		 SUM(INCIDENCIAS) AS INCIDENCIAS
		,SUM(URGENTES) AS URGENTES
		,SUM(ENCURSO) AS ENCURSO
		,SUM(PENDIENTES) AS PENDIENTES
		,SUM(HOY) AS HOY
		,COUNT(*) AS TOTAL
		,@DIAS_DIFF AS DIAS_DIFF
	FROM(
		SELECT Id 
			,ISNULL((SELECT EsAsistencia FROM Tipos WITH (NOLOCK) WHERE T.Oficina = Oficina AND Tipo = 0 AND T.Tipo = Descripcion),0) AS INCIDENCIAS
			,ISNULL((SELECT PrioridadUrgente FROM Tipos WITH (NOLOCK) WHERE T.Oficina = Oficina AND Tipo = 4 AND T.Prioridad = Descripcion),0) AS URGENTES
			,ISNULL((SELECT EnCurso FROM Tipos WITH (NOLOCK) WHERE T.Oficina = Oficina AND Tipo = 5 AND T.Estado = Descripcion),0) AS ENCURSO
			,ISNULL((SELECT Pendiente FROM Tipos WITH (NOLOCK) WHERE T.Oficina = Oficina AND Tipo = 5 AND T.Estado = Descripcion),0) AS PENDIENTES
			,(CASE WHEN DATEDIFF(D, 0, GETDATE()) = DATEDIFF(D, 0, [Fecha alta]) THEN 1 ELSE 0 END) AS HOY
		FROM [Tareas y Proyectos] AS T WITH (NOLOCK)
		WHERE [Asignado a] = (SELECT TOP 1 Name FROM GestionIDM.dbo.LDAP WHERE sAMAccountName = '$usuario') AND Control = 0 AND [Fecha alta] >= DATEADD(DAY, @DIAS_DIFF, GETDATE())
	) AS TOTAL 
";

$q = DBSelect ( utf8_decode ( $q ) );
$HOY = utf8_encode(DBCampo($q, "HOY" ));
$URGENTES = utf8_encode(DBCampo($q, "URGENTES" ));
$INCIDENCIAS = utf8_encode(DBCampo($q, "INCIDENCIAS" ));
$ENCURSO = utf8_encode(DBCampo($q, "ENCURSO" ));
$PENDIENTES = utf8_encode(DBCampo($q, "PENDIENTES" ));
$TOTAL = utf8_encode(DBCampo($q, "TOTAL" ));
$DIAS_DIFF = utf8_encode(DBCampo($q, "DIAS_DIFF" ));

if($DIAS_DIFF == '0'){
	$respuesta = '0';
}else{
	if ($URGENTES > 0){
		$URGENTES = "<span style = 'color:red'>$URGENTES</span>";
	}
	
	$respuesta = '';
	$respuesta = $respuesta . "<div class='resumen_tareas_dia float_left ' title='Nuevas tareas en el día'><span style='font-size:xx-small'>HOY<br></span>$HOY</div>";
	$respuesta = $respuesta . "<div class='resumen_tareas_dia float_left ' title='Nuevas tareas en el día'><span style='font-size:xx-small'>URG<br></span>$URGENTES</div>";
	$respuesta = $respuesta . "<div class='resumen_tareas_dia float_left ' title='Nuevas tareas en el día'><span style='font-size:xx-small'>INC<br></span>$INCIDENCIAS</div>";
	$respuesta = $respuesta . "<div class='resumen_tareas_dia float_left ' title='Nuevas tareas en el día'><span style='font-size:xx-small'>CUR<br></span>$ENCURSO</div>";
	$respuesta = $respuesta . "<div class='resumen_tareas_dia float_left ' title='Nuevas tareas en el día'><span style='font-size:xx-small'>PEN<br></span>$PENDIENTES</div>";
	$respuesta = $respuesta . "<div class='resumen_tareas_dia float_left ' title='Nuevas tareas en el día'><span style='font-size:xx-small'>TOTAL<br></span>$TOTAL</div>";
}
// *** BANNER DE TAREAS ***

// *** TAREA ABIERTA
$query = "
	SELECT TOP 1 
		CAST(Id AS VARCHAR) + ' - ' + Título AS tarea_actual 
		,Id
	FROM GestionPDT.dbo.[Tareas y Proyectos] 
	WHERE Id = (SELECT TOP 1 Tarea FROM GestionPDT.dbo.Horas WHERE Fin IS NULL AND Usuario = '$usuario')";
$query = DBSelect ( utf8_decode ( $query ) );
$enlace_tarea_actual = "";
for(; DBNext ( $query ) ;) {
	$id_tarea_actual = utf8_encode(DBCampo($query, "Id" ));
	$tarea_actual = utf8_encode(DBCampo($query, "tarea_actual" ));
	$enlace_tarea_actual = "<element id='cabecera_titulo_tarea_inner' onclick='selecciono_tarea_historial(\"$id_tarea_actual\",1)'>$tarea_actual</element>";
}
// *** TAREA ABIERTA

// *** ALERTAS ***
$query = "
	SELECT TOP 1
		A.Id
		,(select Name from GestionIDM.dbo.LDAP where sAMAccountName = A.UsuarioAlta) UsuarioAlta
		,D.Name UsuarioNombre
		,A.Tipo
		,A.Tarea
		,T.[Título] AS Titulo
		,T.Solicitado
		,T.Id as Tarea
		,CONVERT(varchar(20), T.[Fecha objetivo],105) AS FechaObjetivo
		--,ISNULL(CONVERT(VARCHAR(MAX),T.[Descripción]),'(En el momento de este correo la tarea no dispone de observaciones)') AS Descripcion
		,T.Oficina
	FROM GestionPDT.dbo.Alertas A
	INNER JOIN GestionIDM.DBO.LDAP D ON A.USUARIO = D.sAMAccountName
	INNER JOIN GestionPDT.dbo.[Tareas y Proyectos] AS T ON a.Tarea = T.Id
	WHERE Pantalla = 'SI' 
		and Leido = 'NO' 
		and (getdate() > FechaPostponer OR FechaPostponer IS NULL)
		and a.Usuario = '$usuario'
        AND (SELECT Finalizado FROM Tipos WHERE Oficina = T.Oficina AND Tipo = 5 AND Descripcion = T.Estado) = 0
";
		
$query = DBSelect ( utf8_decode ( $query ) );
$aviso = "";
for(; DBNext ( $query ) ;) {
	$TAREA = utf8_encode(DBCampo($query, "Tarea" ));
	$TIPO = utf8_encode(DBCampo($query, "Tipo" ));
	$USUARIOALTA = utf8_encode ( DBCampo ( $query , "UsuarioAlta" ) );
	$USUARIONOMBRE = utf8_encode ( DBCampo ( $query , "UsuarioNombre" ) );
	$SOLICITADO = utf8_encode ( DBCampo ( $query , "Solicitado" ) );
	$TITULO = utf8_encode ( DBCampo ( $query , "Titulo" ) );
	$ID = utf8_encode(DBCampo($query, "Id" ));
	$OFICINA = utf8_encode(DBCampo($query, "Oficina" ));
	$FECHA_OBJETIVO = utf8_encode(DBCampo($query, "FechaObjetivo" ));
	
	switch($TIPO){
		case 'alerta_nueva_asignacion':
			$TITLE = "Nueva asignación de tarea #$TAREA@$OFICINA";
			$MENSAJE = "<p>Estimado/a $USUARIONOMBRE ,</p><p>Se le comunica que el usuario <b>$USUARIOALTA</b> le ha asignado una nueva tarea:</p><p><b>Tarea:</b> $TAREA</p><p><b>Título:</b> $TITULO</p><p><b>Solicitante:</b> $SOLICITADO</p>";
			break;
		case 'alerta_fecha_objetivo':
			$TITLE = "Aviso de vencimiento #$TAREA@$OFICINA";
			$MENSAJE = "<p>Estimado/a $USUARIONOMBRE ,</p><p>Se le comunica que la tarea que se detalla a continuación ha vencido o se acerca su fecha objetivo:</p><p><b>Tarea:</b> $TAREA</p><p><b>Título:</b> $TITULO</p><p><b>Solicitante:</b> $SOLICITADO</p><p><b>Fecha objetivo:</b> $FECHA_OBJETIVO</p>";
			break;
		default:
			$TITULO = "";
			$MENSAJE = "";
	}
	
	
	
	$aviso = "$TAREA###$TITLE###$MENSAJE###$ID";
}
// *** ALERTAS ***

echo "$NUEVAS_CUNYAS@@@$respuesta@@@$aviso@@@$enlace_tarea_actual";

DBClose ();
?>