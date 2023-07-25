<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);

$usuario   =$_GET["usuario"];
$tarea     =$_GET["tarea"];
$FechaVaciaPlanificador = 'NO';

$q = " SELECT FechaVaciaPlanificador FROM Configuracion_Oficinas AS OFI INNER JOIN [Tareas y Proyectos] AS TYP ON OFI.Oficina = TYP.Oficina and TYP.Id = $tarea ";
$q = DBSelect ( utf8_decode ( $q ) );

for(; DBNext ( $q ) ;) {
	$FechaVaciaPlanificador = utf8_encode ( DBCampo ( $q , "FechaVaciaPlanificador" ) );
}
DBFree($q);	

$fecha = 'GETDATE()';
$anyo = '(SELECT dbo.ISOyear(GETDATE()))';
$mes = 'DATEPART ( MONTH , GETDATE() )  ';
$semana = 'DATEPART ( ISO_WEEK , GETDATE() )  ';
if(strtoupper($FechaVaciaPlanificador) == 'SI'){
	$fecha = 'NULL';
	$anyo = 'NULL';
	$mes = 'NULL';
	$semana = 'NULL';
}

$insert ="
INSERT INTO [GestionPDT].[dbo].[Planificador](
	[Tarea]
	,[Fecha]
    ,[Anyo]
    ,[Mes]
    ,[Semana]
    ,[Observaciones]
    ,[Comentario_Adicional]
    ,[Estado]
    ,[Prioridad]
    ,[Asignado_Nombre]
    ,[Asignado_Usuario]
    ,[Fecha_Creacion]
    ,[Usuario_Crea]
    ,[Situacion])
SELECT
	T.Id
	,$fecha
	,$anyo
	,$mes
	,$semana
	,''
	,''
	,'Activo'
	,'Normal'
	,T.[Asignado a]
	,(select dbo.SF_OBTENER_USUARIO_DOMINIO(t.[Asignado a]))
	,GETDATE()
	,'$usuario'
	,(SELECT descripcion FROM TIPOS AS TIPO WHERE TIPO.OFICINA = T.OFICINA AND TIPO.TIPO = 13 AND TIPO.PREDETERMINADO = 1)
FROM [Tareas y Proyectos] AS T WHERE Id = $tarea	";

DBSelect(utf8_decode($insert));

	
DBClose();

?>
