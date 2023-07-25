<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$tarea        = $_GET["tarea"];
$numero       = $_GET["numero"];
$usuario      = $_GET["usuario"];
$resultado = '';

DBConectar($GLOBALS["cfg_DataBase"]);

$insert=DBSelect(utf8_decode("SELECT [Titulo] FROM [GestionPDA].[dbo].[Cabecera_PDA] where id = ".$numero." "));
$Descripcion		= DBCampo($insert,utf8_encode("Titulo"));
$insert=DBSelect(utf8_decode("SELECT COUNT(*) AS RESULTADO FROM [tarea_pda] WHERE Tarea =".$tarea." and pda = ".$numero." "));
$cantidad		= DBCampo($insert,("RESULTADO"));
if($cantidad==0)
{
	$insert=DBSelect(utf8_decode("INSERT INTO [tarea_pda] ([tarea],[pda],[usuario],[fecha]) VALUES (".$tarea." ,".$numero." ,'".$usuario."' ,GETDATE())"));
	DBFree($insert);	
	$resultado = '<div class="lista_subir_ficheros_div" id="fichero_num_'.$numero.'_'.$tarea.'"><img class="lista_subir_ficheros_borrar" src="imagenes/erase.png" onclick="subir_pda_borrar_lista(\'fichero_num_'.$numero.'_'.$tarea.'\','.$numero.','.$tarea.');"><a href="https://pda.sp-berner.com/menu.php?pda='.$numero.'" target="_blank"><img class="lista_subir_ficheros_fichero" src="imagenes/file.png">'.$numero.' - '.utf8_encode($Descripcion).'</a></div>';
}
DBClose();
echo $resultado;
?>