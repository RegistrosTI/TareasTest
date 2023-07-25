<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id             = $_GET["Id"];
$Titulo         = $_GET["Titulo"];
$Predeterminado = $_GET["Predeterminado"];

DBConectar($GLOBALS["cfg_DataBase"]);
$q=DBSelect(utf8_decode("SELECT [Descripcion]  FROM [Colas] WHERE Numero = ".$id." "));
$Descripcion_Anterior=utf8_encode(DBCampo($q,"Descripcion"));
DBFree($q);	

$q=DBSelect(utf8_decode("SELECT COUNT(*) as resultado FROM [Colas] WHERE [Descripcion] like '".($Titulo)."' AND Numero <> ".$id.""));
$resultado=DBCampo($q,"resultado");
DBFree($q);	

if($resultado=='0')
{
	if ($Predeterminado=='1')
	{
		DBSelect(utf8_decode("UPDATE [Colas] SET [Predeterminado] = 0 WHERE [Predeterminado] = 1"));
	}
	if ($Titulo!=$Descripcion_Anterior)
	{
		if ($Titulo!='')
		{
			DBSelect(utf8_decode("UPDATE [Tipos] SET [Cola] = '".$Titulo."'	WHERE [Cola] like '".$Descripcion_Anterior."' AND Tipo=2"));
			DBSelect(utf8_decode("UPDATE [Tareas y Proyectos] SET [Cola] = '".$Titulo."' WHERE [Cola] like '".$Descripcion_Anterior."'"));			
		}
		else
		{
			DBSelect(utf8_decode("UPDATE [Tipos] SET [Cola] = null WHERE [Cola] like '".$Descripcion_Anterior."' AND Tipo=2"));
			DBSelect(utf8_decode("UPDATE [Tareas y Proyectos] SET [Cola] = null	WHERE [Cola] like '".$Descripcion_Anterior."'"));
		}
	}
	DBSelect(utf8_decode("UPDATE [Colas] SET [Descripcion] = '".$Titulo."',[Predeterminado] = ".$Predeterminado." WHERE Numero = ".$id." "));
}
DBClose();
?>
