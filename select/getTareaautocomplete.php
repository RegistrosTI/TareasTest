<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

// ESTE SCRIPT SE EJECUTA DESDE EDICION DE TAREAS, DESDE FICHAJES RAPIDOS Y DESDE FUSIONAR

$buscar = $_GET [ 'buscar' ];
$filtro_adicional = "";
$nombreusuario = "";

// SI SE EJECUTA DESDE FICHAJES RAPIDOS
if(isset($_GET [ 'nombreusuario' ])){ 
	$nombreusuario = $_GET [ 'nombreusuario' ];	
	// Tarea 25127
	//$filtro_adicional = " AND ( [Asignado a] = '$nombreusuario' OR (COL.Colaborador_Nombre = '$nombreusuario' AND COL.acceso='Permitido' ) ) ";
	$filtro_adicional = " 
		AND (TAR.[Estado] not in ('Completado','Cancelado','Anulado')) 
		AND ((  ([Asignado a] = '$nombreusuario' OR (COL.Colaborador_Nombre = '$nombreusuario' AND COL.acceso='Permitido' AND COL.Estado='Activo')) 
				AND 0=(select COUNT (*) from Configuracion_Usuarios as usr1 where usr1.usuario=(SELECT GestionPDT.dbo.SF_OBTENER_USUARIO_DOMINIO ('$nombreusuario')) and usr1.Baja=1) ) OR (([Asignado a] = '$nombreusuario' OR (COL.Colaborador_Nombre = '$nombreusuario' AND COL.acceso='Permitido' AND COL.Estado='Activo')) 
		AND (TAR.[Oficina]=USR.[Oficina]))) ";
}

// SI SE EJECUTA DESDE EDICION DE TAREAS O DE FUSIONAR
if(isset($_REQUEST [ 'tarea' ])){ 
	$filtro_adicional = " and tar.Oficina = (select oficina from [Tareas y Proyectos] where id = " . $_REQUEST [ 'tarea' ] . ")  ";
}

$Descripcion = '';

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );
// Tarea 25127 - Cambiamos select para que no muestre las tareas finalizadas ni las tareas en las que participaba cuando estaba en otra oficina
//$query = "
//		SELECT DISTINCT TOP 20 
//			TAR.Id
//			,CAST(TAR.Id as varchar(10)) + ' - ' + CAST([Oficina] as varchar(MAX)) + ' - ' + CAST([Título] as varchar(MAX)) as RESULTADO 
//		FROM [Tareas y Proyectos] AS TAR
//		LEFT join Colaboradores AS COL on TAR.Id = col.Id_Tarea
//		WHERE (CAST(TAR.id as varchar(10)) LIKE '%" . $buscar . "%' OR CAST([Título] as varchar(MAX)) like '%" . $buscar . "%') 
//			AND ([Control] = 0 OR [Control] is null) $filtro_adicional order by TAR.Id desc";
$query = "
		SELECT DISTINCT TOP 20
			TAR.Id
			,CAST(TAR.Id as varchar(10)) + ' - ' + CAST(TAR.[Oficina] as varchar(MAX)) + ' - ' + CAST([Título] as varchar(MAX)) as RESULTADO
		FROM [Tareas y Proyectos] AS TAR
		LEFT join Colaboradores AS COL on TAR.Id = col.Id_Tarea
		LEFT join Configuracion_Usuarios AS USR on (USR.usuario=(SELECT GestionPDT.dbo.SF_OBTENER_USUARIO_DOMINIO ('$nombreusuario')) and USR.Baja<>1)
		WHERE (CAST(TAR.id as varchar(10)) LIKE '%" . $buscar . "%' OR CAST([Título] as varchar(MAX)) like '%" . $buscar . "%')
			AND ([Control] = 0 OR [Control] is null) 
            $filtro_adicional order by TAR.Id desc";
//die($query);
$query = DBSelect ( utf8_decode ( $query ) );
$Descripcion = utf8_encode ( DBCampo ( $query , "RESULTADO" ) );
for(; DBNext ( $query ) ;) {
	$Descripcion = $Descripcion . '|' . utf8_encode ( DBCampo ( $query , "RESULTADO" ) );
}
DBFree ( $query );
DBClose ();

$Descripcion = trim ( $Descripcion , '|' );
echo $Descripcion;
?>