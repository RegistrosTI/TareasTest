<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id     = $_GET["Id"];
$Titulo = $_GET["Titulo"];
$Usuario= $_GET["usuario"];
$Clase= $_GET["Clase"];
$tarea  = $_GET["tarea"];

$Titulo = strip_tags($Titulo);
$Titulo = str_replace("'", "''", ($Titulo));

DBConectar($GLOBALS["cfg_DataBase"]);

$query ="
		INSERT INTO [Log_Comentarios] (
			Numero
			,Fecha
			,Comentario
			,Usuario
			,Tarea
			,FechaAnterior
			,ComentarioAnterior
			,UsuarioAnterior
			,TareaAnterior
			,UsuarioCambia
			,FechaCambio)
		SELECT 
			Numero
			,Fecha
			,'$Titulo'
			,Usuario
			,Tarea
			,Fecha
			,Comentario
			,Usuario
			,Tarea
			,'$Usuario'
			,GETDATE()
		FROM [Comentarios]  
		WHERE  Numero = $id AND (
				('$Titulo' NOT LIKE [Comentario]) OR 
				('$Clase' NOT LIKE [Clase]))  
		
		UPDATE [Comentarios] 
		SET 
			[Comentario] = '$Titulo' 
			,[Clase] = '$Clase'
		WHERE Numero = $id ;

";
//die($query);
DBSelect(utf8_decode($query));

$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_HISTORICO] '".$Usuario."',".$tarea.",4"));
DBFree($insert);
$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_PALABRAS_NUMERO] ".$tarea.",4,".$id.",'".str_replace("'", "''", ($Titulo))."' "));
DBFree($insert);	
DBClose();
?>
