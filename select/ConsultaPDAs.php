<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id             =$_GET['id'];
$usuario        =$_GET['usuario'];
$lista_archivos = '';
$permiso        = 1;
$select         ='';

DBConectar($GLOBALS["cfg_DataBase"]);

$u_programa = "
	SELECT 
		TAREA.[tarea] 
		,TAREA.[pda]
		,TAREA.[usuario]
		,TAREA.[fecha]
		,PDA.Titulo 
	FROM [GestionPDT].[dbo].[tarea_pda] AS TAREA 
	INNER JOIN [GestionPDA].[dbo].[Cabecera_PDA] AS PDA 
		ON TAREA.pda = PDA.Id 
	WHERE Tarea = ".$id." ";

$u_programa=DBSelect(utf8_decode($u_programa));
for(;DBNext($u_programa);)
{
	$Descripcion =(DBCampo($u_programa,("Titulo")));
	$Numero      =DBCampo($u_programa,("pda"));
	$tarea       =DBCampo($u_programa,("tarea"));
	$usuario_file=DBCampo($u_programa,("usuario"));
	if(utf8_encode($usuario_file)==$usuario)
	{
		$lista_archivos = $lista_archivos.'<div class="lista_subir_ficheros_div" id="fichero_num_'.$Numero.'_'.$tarea.'"><img class="lista_subir_ficheros_borrar" src="imagenes/erase.png" onclick="subir_pda_borrar_lista(\'fichero_num_'.$Numero.'_'.$tarea.'\','.$Numero.','.$tarea.');"><a href="https://pda.sp-berner.com/menu.php?pda='.$Numero.'" target="_blank"><img class="lista_subir_ficheros_fichero" src="imagenes/file.png">'.$Numero.' - '.utf8_encode($Descripcion).'</a></div>';
	}
	else
	{
		$lista_archivos = $lista_archivos.'<div class="lista_subir_ficheros_div" id="fichero_num_'.$Numero.'_'.$tarea.'"><a href="https://pda.sp-berner.com/menu.php?pda='.$Numero.'" target="_blank"><img class="lista_subir_ficheros_fichero" src="imagenes/file.png">'.$Numero.' - '.utf8_encode($Descripcion).'</a></div>';
	}
}
DBFree($u_programa);	
DBClose();

echo '<div id="lista_subir_ficheros_lista_adjuntos" class="lista_subir_ficheros">'.$lista_archivos.'</div>';
?>