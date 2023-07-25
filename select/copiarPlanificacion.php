<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$oficina			= $_POST["oficina"];
$semanaOrigen		= $_POST["semanaOrigen"];
$semanaDestino		= $_POST["semanaDestino"];
$usuarioOrigen		= $_POST["usuarioOrigen"];
$usuarioDestino		= $_POST["usuarioDestino"];
if ($usuarioDestino == '') {
	$usuarioDestino = $usuarioOrigen;
}
$tareasPendientes	= $_POST["tareasPendientes"];
$planifPendientes	= $_POST["planifPendientes"];
$todosUsuarios		= $_POST["todosUsuarios"];
$diaSeleccionado	= $_POST["diaSeleccionado"];
$mantenerSituacion	= $_POST["mantenerSituacion"];
$copiarTiempos		= $_POST["copiarTiempos"];
$soloNuevas			= $_POST["soloNuevas"];
$noCopiarObservaciones = $_POST["noCopiarObservaciones"];

$usuario = $_POST["usuario"];

$semanaOrigen = explode(" ", $semanaOrigen);
$semanaDestino = explode(" ", $semanaDestino);

$fechaIni = explode("-",$semanaOrigen[0]);
$fechaFin = explode("-",$semanaDestino[0]);

$semanaOrigen = $semanaOrigen[1];
$semanaDestino = $semanaDestino[1];

$fechaIni = $fechaIni[2] . "-" . $fechaIni[1] . "-" . $fechaIni[0];
$fechaFin = $fechaFin[2] . "-" . $fechaFin[1] . "-" . $fechaFin[0];

$WHERE1 = "WHERE Estado = 'Activo' AND (SELECT Oficina FROM [Tareas y Proyectos] WHERE Id = P.Tarea) = @OFICINA
	";
$WHERE2 = "WHERE Estado = 'Activo' AND (SELECT Oficina FROM [Tareas y Proyectos] WHERE Id = P.Tarea) = @OFICINA AND Tarea = P.Tarea AND Asignado_Nombre = @USUARIO_DESTINO ";

if ($diaSeleccionado == 'true') {
	$WHERE1 .= "		AND FECHA = @FECHA_INI
	";
	$WHERE2 .= " AND FECHA = @FECHA_FIN ";
}else{
	$WHERE1 .= "		AND SEMANA = DATEPART(ISOWK,@FECHA_INI) AND Anyo = dbo.ISOyear(@FECHA_INI)
	";
	$WHERE2 .= " AND SEMANA = DATEPART(ISOWK,@FECHA_FIN) AND Anyo = dbo.ISOyear(@FECHA_FIN) ";
}


if ($todosUsuarios != 'true') {
	$WHERE1 .= "		AND Asignado_Nombre = '$usuarioOrigen'
	";
}

if ($tareasPendientes == 'true') {
	$WHERE1 .= "		AND (SELECT Pendiente FROM Tipos WHERE Oficina = 'TI' AND Tipo = 5 AND Descripcion = (SELECT estado FROM [Tareas y Proyectos] WHERE Id = P.Tarea)) = 1  
	";
}

if ($planifPendientes == 'true') {
	$WHERE1 .= "		AND (SELECT Pendiente FROM Tipos WHERE Oficina = 'TI' AND Tipo = 13 AND Descripcion = P.Situacion) = 1 
	";
}

if ($soloNuevas == 'true') {
	$WHERE1 .= "		AND 0 = (SELECT COUNT(*) FROM [GestionPDT].[dbo].[Planificador] $WHERE2)
	";
}

//FECHA sumamos los semanas entre fechaOrigen y fechaDestino, si es por días solo ponemos la fecha destino
$fecha = " DATEADD(WK, @SEMANAS , Fecha) ";
if ($diaSeleccionado == 'true') {
	$fecha = " @FECHA_FIN ";
}

$Tiempo_Estimado = "0.00";
if($copiarTiempos == 'true'){
	$Tiempo_Estimado == "Tiempo_Estimado";
}

$Observaciones = "Observaciones";
if($noCopiarObservaciones == 'true'){
	$Observaciones == "''";
}

$Situacion = " (SELECT TOP 1 Descripcion FROM Tipos WHERE Tipo = 13 AND Predeterminado = 1 AND Oficina = @OFICINA) ";
if($mantenerSituacion == 'true'){
	$Situacion = "Situacion";
}

DBConectar($GLOBALS["cfg_DataBase"]);

$DECLARE = "
		DECLARE @OFICINA AS VARCHAR(MAX) = '$oficina'
		DECLARE @FECHA_INI AS DATE = '$fechaIni'
		DECLARE @FECHA_FIN AS DATE = '$fechaFin'
		DECLARE @USUARIO_DESTINO AS VARCHAR(MAX) = '$usuarioDestino';
		DECLARE @SEMANAS AS INT = DATEDIFF(WK,@FECHA_INI,@FECHA_FIN);
";

//Obtenemos las filas
$query = "$DECLARE SELECT COUNT(*) AS ROWS FROM [GestionPDT].[dbo].[Planificador] AS P $WHERE1";
$query = utf8_decode ( $query );
// die ( $query );
$query = DBSelect ( $query );
$ROWS = 0;
for(; DBNext ( $query ) ;) {
	$ROWS = utf8_encode ( DBCampo ( $query , ( "ROWS" ) ) ) ;
}

DBFree($query);	


$query = "	
	BEGIN TRAN
		$DECLARE

		INSERT INTO [GestionPDT].[dbo].[Planificador]
		SELECT
           Tarea
           ,$fecha
           ,dbo.ISOyear(DATEADD(WK, @SEMANAS  , Fecha))
           ,DATEPART(MONTH,DATEADD(WK, @SEMANAS  , Fecha))
           ,DATEPART(ISOWK,DATEADD(WK, @SEMANAS  , Fecha))
           ,$Situacion
           ,$Observaciones
           ,Comentario_Adicional
           ,Estado
           ,@USUARIO_DESTINO
           ,(select sAMAccountName from GestionIDM.dbo.LDAP where Name = @USUARIO_DESTINO)
           ,GETDATE()
           ,'$usuario'
           ,NULL
           ,NULL
           ,$Tiempo_Estimado
           ,''
           ,Prioridad
		FROM [GestionPDT].[dbo].[Planificador] AS P
		$WHERE1

	COMMIT TRAN
";
//die($query);
$query = utf8_decode ( $query );
// die ( $query );
$query = DBSelect ( $query );



DBFree($query);	
DBClose();

echo $ROWS;
?>

