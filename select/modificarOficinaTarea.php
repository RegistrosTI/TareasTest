<?php
//include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

;

$tarea = $_GET [ "tarea" ];
$oficina = $_GET [ "oficina" ];
$Usuario = $_GET [ "usuario" ];
DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

// Hay que comprobar que el valor actual sea -1, de lo contrario no debe de poder modificar
$q = " 
BEGIN TRAN
	UPDATE [Tareas y Proyectos] 
	SET 
		Oficina 				= '$oficina' 
		,Tipo 					= isnull((SELECT TOP 1 Descripcion FROM Tipos WHERE Tipo = 0 AND Oficina = '$oficina' AND Predeterminado = 1),'')
		,Categoría 				= isnull((SELECT TOP 1 Descripcion FROM Tipos WHERE Tipo = 1 AND Oficina = '$oficina' AND Predeterminado = 1),'')
		,Subcategoría 			= isnull((SELECT TOP 1 Descripcion FROM Tipos WHERE Tipo = 2 AND Oficina = '$oficina' AND Predeterminado = 1),'')
		,AporteOrganizacion 	= 'N/A'
		,AporteEmpresa 			= 'N/A'
		,AporteJefe 			= 'N/A'
		,Prioridad 				= isnull((SELECT TOP 1 Descripcion FROM Tipos WHERE Tipo = 4 AND Oficina = '$oficina' AND Predeterminado = 1),'')
		,Estado 				= isnull((SELECT TOP 1 Descripcion FROM Tipos WHERE Tipo = 5 AND Oficina = '$oficina' AND Predeterminado = 1),'')
		,Evaluable 				= isnull((SELECT TOP 1 Descripcion FROM Tipos WHERE Tipo = 12 AND Oficina = '$oficina' AND Predeterminado = 1),'')
		,DocumentacionCompleta 	= isnull((SELECT TOP 1 Descripcion FROM Tipos WHERE Tipo = 19 AND Oficina = '$oficina' AND Predeterminado = 1),'')
		,Estrategico 			= isnull((SELECT TOP 1 Descripcion FROM Tipos WHERE Tipo = 18 AND Oficina = '$oficina' AND Predeterminado = 1),'')
	WHERE Id = $tarea
	UPDATE [Entrada_Correo] SET Oficina = '$oficina' WHERE IdTarea = $tarea
	DELETE FROM Feedback_Respuestas WHERE Tarea = $tarea
	DELETE FROM Evaluaciones WHERE Tarea = $tarea
	--DELETE FROM Colaboradores WHERE Id_Tarea = $tarea
	DELETE FROM Planificador WHERE Tarea = $tarea
COMMIT TRAN
";
DBSelect ( utf8_decode ( $q ) );

DBClose ();
?>
