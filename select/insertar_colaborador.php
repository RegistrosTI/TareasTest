<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$usuario   =$_GET["usuario"];
$tarea     =$_GET["tarea"];


DBConectar($GLOBALS["cfg_DataBase"]);

$insert ="
		
INSERT INTO [GestionPDT].[dbo].[Colaboradores](
	[Id_Tarea]
	,[Estado]
	,[FechaAlta]
	,[UsuarioCrea]
	,[Funciones]
	,[Externo]
	,[Acceso]
      ,[AporteOrganizacion]
      ,[AporteEmpresa]
      ,[AporteJefe]
	)
values(
	$tarea
	,'Activo'
	,GETDATE()
	,'$usuario'
	,''
	,'NO'
	,'Permitido'
	,'-'
	,'-'
	,'-'
)
";

//die($insert);

DBSelect(utf8_decode($insert));

DBFree($insert);	
DBClose();
echo $Titulo;
?>
