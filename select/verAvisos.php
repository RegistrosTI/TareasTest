<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$Usuario      = $_GET["usuario"];
$Menu         = $_GET["departamento"];
$contador     = 0;
$Mi_Menu_Toca = '-1';

DBConectar($GLOBALS["cfg_DataBase"]); 

$query=DBSelect(utf8_decode("SELECT Numero as Numero FROM Tipos WHERE Tipo = 100 and Descripcion = '".utf8_decode($Menu)."'"));
for(;DBNext($query);)
{		
	$Mi_Menu_Toca=(DBCampo($query,"Numero"));					
}
DBFree($query);	


if ($Mi_Menu_Toca == '1')
{
	$id_mh='1';
}
else
{
	$id_mh='2';
}	

$Descripcion    = '';
// Tarea 25128 - Dejar de mostrar avisos de cambio de tareas que no correspondan a la oficina actual del usuario
//$query= "
//	SELECT DISTINCT
//		CAST(Tareas.Título as varchar(250)) as TITULO
//		,Avisos.Aviso
//		,Avisos.Descripcion
//		,Avisos.Tarea
//		,Avisos.Tipo
//	FROM [Avisos] AS Avisos
//	INNER JOIN [Tareas y Proyectos] AS Tareas ON Tareas.Id = Avisos.Tarea AND Tareas.Control <> 1
//	WHERE Avisos.Usuario = '".$Usuario."' order by Tarea";
$query= "
	SELECT DISTINCT 
		CAST(Tareas.Título as varchar(250)) as TITULO
		,Avisos.Aviso
		,Avisos.Descripcion
		,Avisos.Tarea
		,Avisos.Tipo 
	FROM [Avisos] AS Avisos
	INNER JOIN [Tareas y Proyectos] AS Tareas ON Tareas.Id = Avisos.Tarea AND Tareas.Control <> 1 
    LEFT JOIN  [GestionPDT].[dbo].[Configuracion_Usuarios] AS USR on (USR.usuario='".$Usuario."' and USR.BAJA=0)
	WHERE Avisos.Usuario = '".$Usuario."' AND (Tareas.Oficina=USR.Oficina) order by Tarea";
$query=DBSelect(utf8_decode($query));
$Tarea_Anterior = -1;
for(;DBNext($query);)
{	
	$contador   = $contador + 1;
	$AVISO      = (DBCampo($query,"Aviso"));
	$TIPO       = (DBCampo($query,"Tipo"));
	$DESCRIPCION=utf8_encode(DBCampo($query,"Descripcion"));
	$TITULO     =utf8_encode(DBCampo($query,"TITULO"));
	$Tarea      =(DBCampo($query,"Tarea"));
	
	if ( $Tarea_Anterior != $Tarea ) {
		if ( $Tarea_Anterior !=  - 1 ) {
			$Descripcion = $Descripcion . '</div>';
		}
		if ( $AVISO == '11' || $AVISO == '12' || $AVISO == '13' ) {
			$alerta = '<img src="imagenes/user.png" style="width:20px"/>';
		} else {
			$alerta = '';
		}
		$Descripcion = $Descripcion . '<div style="cursor: pointer;clear: both;" onMouseOut="cambiacolororiginalaviso(this)" onMouseOver="cambiacolor(this)" onClick="plegardesplegaraviso(this)" class="div_aviso" id="PLEGADOR_AVISO_' . $Tarea . '">' . $alerta . $Tarea . ' - ' . $TITULO . '<div style="float: right;" onClick="selecciono_tarea_historial(' . ( DBCampo ( $query , "Tarea" ) ) . ',' . $id_mh . ');"><img src="imagenes/eye.png" style="width:20px"/></div></div><div class="PLEGAR_AVISO" id="PLEGADOR_AVISO_' . $Tarea . '_PLEGAR">';
		$Tarea_Anterior = $Tarea;
	}
	if ( $AVISO == '4' && $TIPO == '2' && $DESCRIPCION == 'Cambia el campo Asignado a' ) {
		$Descripcion = $Descripcion . '<div style="cursor: pointer;clear: both;" onClick="selecciono_tarea_historial(' . ( DBCampo ( $query , "Tarea" ) ) . ',' . $id_mh . ');" class="div_aviso"><img src="imagenes/Aviso_Asignada_Tarea.png" style="width:25px"/>Te han asignado una nueva Tarea</div>';
	} else {
		$Descripcion = $Descripcion . '<div style="cursor: pointer;clear: both;" onClick="selecciono_tarea_historial(' . ( DBCampo ( $query , "Tarea" ) ) . ',' . $id_mh . ');" class="div_aviso"><img src="imagenes/Aviso_' . $AVISO . '.png" style="width:25px"/>' . $DESCRIPCION . '</div>';
	}
}
if ($Tarea_Anterior!=-1)
{
	$Descripcion=$Descripcion.'</div>';
}
DBFree($query);	

if ($contador==0)
{
	echo "No tiene avisos pendientes";
}
else
{
	echo $Descripcion;
}	
DBClose();
?>