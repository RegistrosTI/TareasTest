<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$tarea 				= $_GET [ "tarea" ];
$alta_nombre 		= $_GET [ "alta_nombre" ];
$alta_ape1 			= $_GET [ "alta_ape1" ];
$alta_ape2 			= $_GET [ "alta_ape2" ];
$alta_fecha 		= $_GET [ "alta_fecha" ];
$alta_mail 			= $_GET [ "alta_mail" ];
$alta_reporta 		= $_GET [ "alta_reporta" ];
$alta_permisoscomo 	= $_GET [ "alta_permisoscomo" ];
$alta_usuario 		= $_GET [ "alta_usuario" ];
$alta_password 		= $_GET [ "alta_password" ];
$alta_departamento	= $_GET [ "alta_departamento" ];
$alta_programa		= $_GET [ "alta_programa" ];

$alta_accede_nav	= $_GET [ "alta_accede_nav" ];
$alta_usuario_nav 	= $_GET [ "alta_usuario_nav" ];
$alta_password_nav 	= $_GET [ "alta_password_nav" ];
$alta_espejo_nav 	= $_GET [ "alta_espejo_nav" ];

$alta_accede_mapex_apl	= $_GET [ "alta_accede_mapex_apl" ];
$alta_tipo_mapex_apl	= $_GET [ "alta_tipo_mapex_apl" ];
$alta_espejo_mapex_apl	= $_GET [ "alta_espejo_mapex_apl" ];

$alta_accede_mapex_ter	= $_GET [ "alta_accede_mapex_ter" ];
$alta_tipo_mapex_ter	= $_GET [ "alta_tipo_mapex_ter" ];
$alta_espejo_mapex_ter	= $_GET [ "alta_espejo_mapex_ter" ];

$alta_internet			= $_GET [ "alta_internet" ];
$alta_internet_tipo		= $_GET [ "alta_internet_tipo" ];

$alta_remoto		= $_GET [ "alta_remoto" ];

$alta_crear_ad		= $_GET [ "alta_crear_ad" ];

$alta_usuario_pc	= $_GET [ "alta_usuario_pc" ];
$alta_usuario_porta	= $_GET [ "alta_usuario_porta" ];
$alta_usuario_tel	= $_GET [ "alta_usuario_tel" ];
$alta_usuario_movil		= $_GET [ "alta_usuario_movil" ];



DBConectar($GLOBALS["cfg_DataBase"]);

$update ="
		UPDATE [GestionPDT].[dbo].[Altas_Usuario] 
		SET 
			[Nombre] = '$alta_nombre'
			,[Apellido_1] = '$alta_ape1'
			,[Apellido_2] = '$alta_ape2'
			,[Fecha_Incorporacion] = '$alta_fecha'
			,[Mail_Incorporacion] = '$alta_mail'
			,[Reporta_a] = '$alta_reporta'
			,[Permisos_como] = '$alta_permisoscomo'
			,[Usuario_Incorporacion] = '$alta_usuario'
			,[Contrasenya] = '$alta_password'
			,[Departamento_alta] = '$alta_departamento'
			,[Programa_alta] = '$alta_programa'
			
			,[Navision_Acceso] = '$alta_accede_nav'
			,[Navision_Usuario] = '$alta_usuario_nav'
			,[Navision_Password] = '$alta_password'
			,[Navision_Espejo] = '$alta_espejo_nav'
			
			,[Mapex_Aplicacion_Acceso] = '$alta_accede_mapex_apl'
			,[Mapex_Aplicacion_Tipo] = '$alta_tipo_mapex_apl'
			,[Mapex_Aplicacion_Espejo] = '$alta_espejo_mapex_apl'
			
			,[Mapex_Terminal_Acceso] = '$alta_accede_mapex_ter'
			,[Mapex_Terminal_Tipo] = '$alta_tipo_mapex_ter'
			,[Mapex_Terminal_Espejo] = '$alta_espejo_mapex_ter'
			
			,[Internet_Acceso] = '$alta_internet'
			,[Internet_Tipo] = '$alta_internet_tipo'
			
			,[Remoto_Acceso] = '$alta_remoto'
			
			,[Crear_AD] = '$alta_crear_ad'
			
			,[Telefono_Acceso] = '$alta_usuario_tel'
			,[Telefono_Movil_Acceso] = '$alta_usuario_movil'
			,[PC_Acceso] = '$alta_usuario_pc'
			,[Portatil_Acceso] = '$alta_usuario_porta'
			
		WHERE tarea = $tarea
";
echo $update;
$update = DBSelect(utf8_decode($update));

DBFree($update);	
DBClose();

?>
