<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id             = $_GET["id"];
$tipo           = $_GET["tipo"];
$descripcion    = $_GET["descripcion"];
$predeterminado = $_GET["predeterminado"];
$visible        = $_GET["visible"];
$cola           = $_GET["cola"];

DBConectar($GLOBALS["cfg_DataBase"]);
$q=DBSelect(utf8_decode("SELECT CAST([Descripcion] as varchar(250)) as Descripcion FROM [Tipos] where tipo=".$tipo." and numero = ".$id." "));
$Descripcion_Anterior=utf8_encode(DBCampo($q,"Descripcion"));
DBFree($q);	

$q=DBSelect(utf8_decode("SELECT COUNT(*) as resultado FROM [Tipos] WHERE [Descripcion] like '".utf8_decode($descripcion)."' AND Numero <> ".$id." AND tipo <> ".$tipo." "));
$resultado=DBCampo($q,"resultado");
DBFree($q);	

if($resultado=='0')
{
	if ($predeterminado=='1')
	{
		DBSelect(utf8_decode("UPDATE [Tipos] SET [Predeterminado] = 0 WHERE [Predeterminado] = 1 AND Tipo = ".$tipo." "));
	}
	if ($descripcion!=$Descripcion_Anterior)
	{
		if ($descripcion!='')
		{	
			DBSelect(utf8_decode("UPDATE [Tareas y Proyectos] SET [Subcategoría] = '".($descripcion)."'	WHERE [Subcategoría] like '".($Descripcion_Anterior)."'"));			
			if ($cola!='')
			{				
				DBSelect(utf8_decode("UPDATE [Tareas y Proyectos] SET [cola] = '".($cola)."' WHERE [Subcategoría] like '".($descripcion)."'"));
			}
			else
			{
				DBSelect(utf8_decode("UPDATE [Tareas y Proyectos] SET [cola] = null	WHERE [Subcategoría] like '".($descripcion)."'"));
			}
		}
		else
		{
			if ($cola!='')
			{
				
				DBSelect(utf8_decode("UPDATE [Tareas y Proyectos] SET [cola] = '".$cola."' WHERE [Subcategoría] like '".($Descripcion_Anterior)."'"));
			}
			else
			{
				DBSelect(utf8_decode("UPDATE [Tareas y Proyectos] SET [cola] = null	WHERE [Subcategoría] like '".($Descripcion_Anterior)."'"));
			}
			DBSelect(utf8_decode("UPDATE [Tareas y Proyectos] SET [Subcategoría] = null	WHERE [Subcategoría] like '".($Descripcion_Anterior)."'"));
		}
	}
	if ($cola!='')
	{
		DBSelect(utf8_decode("UPDATE [Tipos] SET [Descripcion] = '".($descripcion)."',[Predeterminado] = ".$predeterminado.",[visible] = ".$visible.",[cola] = '".($cola)."' WHERE tipo=".$tipo." and numero = ".$id." "));
	}
	else
	{
		DBSelect(utf8_decode("UPDATE [Tipos] SET [Descripcion] = '".($descripcion)."',[Predeterminado] = ".$predeterminado.",[visible] = ".$visible.",[cola] = null WHERE tipo=".$tipo." and numero = ".$id." "));
	}
}
DBClose();
?>
