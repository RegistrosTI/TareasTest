<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$dia = $_GET [ "dia" ];
$usuario = $_GET [ "usuario" ];
$tarea = $_GET [ "tarea" ];
$minutos = $_GET [ "minutos" ];

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

// COMPROBAR QUE LA FECHA SE IGUAL A LA DE HOY O ANTERIOR

$dia_fichada = date("Y-m-d", strtotime($dia));
$dia_hoy     = date("Y-m-d");
if($dia_fichada > $dia_hoy){
	die ( "La fichada no puede ser posterior al día actual." );
}

// QUITO GUIONES PARA DEJAR FORMATO FECHA SQL YYYYMMDD
$dia = str_replace('-', '', $dia);

// obtener las oficinas del usuario
$oficinas = '';
$ambito = 1;
$q = "SELECT Oficina, Ambito FROM Configuracion_Usuarios WHERE Usuario = '$usuario' ";
$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$oficinas = utf8_encode ( DBCampo ( $q , utf8_decode ( "Oficina" ) ) );
	$ambito = utf8_encode ( DBCampo ( $q , utf8_decode ( "Ambito" ) ) );
}
$oficinas = "'" . str_replace ( "," , "','" , $oficinas ) . "'";
DBFree ( $q );

// si el ambito es 1, solo puede añadir la fichada si el es el asignado a la tarea
$filtro_adicional = '';
if ( $ambito == 1 ) {
	$filtro_adicional = " AND [Asignado a] = dbo.SF_OBTENER_NOMBRE_DOMINIO('$usuario') ";
}
// comprobar que la fichada es de una tarea de la oficina o una tarea que colaboro
$filtro_colaboro = "
	(SELECT COUNT(*) 
		FROM Colaboradores AS COL 
		WHERE COL.Estado = 'Activo' 
			AND COL.Id_Tarea = TYP.Id 
			AND COL.Colaborador = '$usuario' 
			AND COL.acceso='Permitido' )";

$encontrado = 0;
$q = "
	SELECT count(Id) as encontrado
	FROM [GestionPDT].[dbo].[Tareas y Proyectos] AS TYP
	WHERE Id = $tarea AND ( ( Oficina IN ($oficinas) $filtro_adicional) OR 0 < $filtro_colaboro )";
//die($q);
$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$encontrado = utf8_encode ( DBCampo ( $q , utf8_decode ( "encontrado" ) ) );
}
DBFree ( $q );

// Si no encontramos la tarea, salimos he indicamos el error
if ( $encontrado == 0 ) {
	die ( 'No tiene permisos para fichar en esta tarea o la tarea no existe.' );
}

// COMPROBAR SI EN EL DÍA YA HAY UNA TAREA, SI NO HAY TAREA LA HORA INICIO SERÁ LA HORA DE INICIO JORNADA EN CONFIGURACION O EN SU DEFECTO A LAS 08:00
$hay_fichadas = 0;
$q = "SELECT COUNT(Numero) AS hay_fichadas FROM Horas WHERE Usuario = '$usuario'  AND Inicio BETWEEN '$dia' AND DATEADD(DD,1,'$dia')";
$q = "SELECT COUNT(Numero) AS hay_fichadas FROM Horas WHERE Usuario = '$usuario'  AND Inicio >= '$dia' AND Inicio < DATEADD(DD,1,'$dia')";

$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$hay_fichadas = utf8_encode ( DBCampo ( $q , utf8_decode ( "hay_fichadas" ) ) );
}
DBFree ( $q );

// SI HAY FICHADAS COMPROBAMOS QUE TODAS TENGAN FECHAS DE FIN, DE LO CONTRARIO CANCELAMOS Y AVISAMOS
$hay_fichadas_sin_finalizar = 0;
if ( $hay_fichadas > 0 ) {
	$q = " SELECT COUNT(Numero) as hay_fichadas_sin_finalizar FROM Horas WHERE Usuario = '$usuario'  AND Inicio >= '$dia' AND Inicio < DATEADD(DD,1,'$dia') and Fin is null ";
	$q = DBSelect ( utf8_decode ( $q ) );
	for(; DBNext ( $q ) ;) {
		$hay_fichadas_sin_finalizar = utf8_encode ( DBCampo ( $q , utf8_decode ( "hay_fichadas_sin_finalizar" ) ) );
	}
	DBFree ( $q );
	
	if ( $hay_fichadas_sin_finalizar != 0 ) {
		die ( 'No puede crear una nueva fichada si tiene fichadas abiertas. Cierre la fichada que tiene abierta y vuelva a intentarlo.' );
	}
}

// CALCULAMOS LA HORA DE INICIO Y DE FIN PARA LA NUEVA FICHADA, TENIENDO EN CUENTA SI ES LA PRIMERA FICHADA DEL DIA,
// ESTA SE DEBE OBTENER DE LA CONFIGURACION O EN SU DEFECTO A LAS 08:00
$hora_inicio = "";
$hora_fin = "";
if ( $hay_fichadas > 0 ) {
	// Buscamos el cierre de la ultima fichada
	$q = "
		SELECT 
			 CONVERT(varchar(30), MAX(Fin),20) AS hora_inicio
		FROM Horas 
		WHERE Usuario = '$usuario' 
			AND Inicio >= '$dia' AND Inicio < DATEADD(DD,1,'$dia')
	";
	$q = DBSelect ( utf8_decode ( $q ) );
	for(; DBNext ( $q ) ;) {
		$hora_inicio = utf8_encode ( DBCampo ( $q , utf8_decode ( "hora_inicio" ) ) );
	}
	DBFree ( $q );
	$hora_inicio = str_replace('-','',$hora_inicio);

} else {
	//OBTENER HORA DESDE CONFIGURACIÓN PARA LA PRIMERA FICHADA
	$hora_inicio = "$dia 08:00:00";
	$q = "SELECT Valor AS hora_inicio FROM Configuracion WHERE Usuario = '$usuario' AND Parametro = 'mi_hora_inicio'";
	$q = DBSelect ( utf8_decode ( $q ) );
	for(; DBNext ( $q ) ;) {
		$hora_inicio = $dia . " " .utf8_encode ( DBCampo ( $q , utf8_decode ( "hora_inicio" ) ) );
	}
	DBFree ( $q );
}

//VERIFICAMOS SI LA TEREA FUE CREADA COMO TELETRABAJO
$q = "
SELECT 
	 Teletrabajo
FROM [Tareas y Proyectos]
WHERE Id = $tarea
";
$q = DBSelect ( utf8_decode ( $q ) );
$teletrabajo = (DBCampo($q, "Teletrabajo"));
if($teletrabajo != 'SI'){
	$teletrabajo = 'NO';
}
// SI ESTA TODO CORRECTO REALIZAMOS LA INSERCIÓN DE LA FICHADA
$insert = "
BEGIN TRANSACTION 		
	INSERT INTO [dbo].[Horas]
           ([Tarea]
           ,[Inicio]
           ,[Fin]
           ,[Usuario]
           ,[Minutos]
           ,[Grupo]
           ,[Comentario]
           ,[Tipo]
           ,[Gasto]
           ,[Aporte]
           ,[AporteEmpresa]
			,[Anyo_ISO]
			,[Semana_ISO]
			,[Anyo]
			,[Mes]
			,InicioReal
			,[Teletrabajo])
     VALUES
           ($tarea
           ,'$hora_inicio'
			,(DATEADD(MINUTE,$minutos, ( '$hora_inicio' ) ) )
           ,'$usuario'
           ,$minutos
           ,NULL
           ,''
           ,(SELECT Valor FROM Configuracion WHERE Usuario = '$usuario' and Parametro = 'mi_horas') 
           ,''
           ,''
           ,''
			,DBO.ISOYear('$hora_inicio')
			,DATEPART(ISO_WEEK,'$hora_inicio')
			,DATEPART(YEAR,'$hora_inicio')
			,DATEPART(MONTH,'$hora_inicio')
			,GETDATE() 
			,'$teletrabajo')
           
	UPDATE TAREAS
	SET 
		Estado = (	SELECT TOP 1 [Descripcion] 
					FROM [GestionPDT].[dbo].[Tipos] as T
					WHERE Tipo = 5 AND predeterminado = 2
						AND T.Oficina = TAREAS.Oficina)
	FROM [GestionPDT].[dbo].[Tareas y Proyectos] AS TAREAS
	WHERE Id = $tarea ;	
	
	EXECUTE [GestionPDT].[dbo].[INICIAR_EVALUACIONES] $tarea ;
	
COMMIT TRANSACTION ;
		";
//die($insert);

$insert = DBSelect ( utf8_decode ( $insert ) );

$insert = DBSelect ( utf8_decode ( "EXECUTE [INICIAR_HISTORICO] '$usuario',$tarea,8" ) );

DBClose ();
?>
