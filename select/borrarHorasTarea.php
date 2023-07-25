<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id     = $_GET["Id"];
$usuario=$_GET["usuario"];
$tarea  =$_GET["tarea"];

DBConectar($GLOBALS["cfg_DataBase"]);

$query = "
		BEGIN TRANSACTION;
			INSERT INTO [Log_Horas] (
				Numero
				,Tarea
				,Inicio
				,Fin
				,Usuario
				,Minutos
				,Grupo
				,Comentario
				,TareaAnterior
				,InicioAnterior
				,FinAnterior
				,USuarioAnterior
				,MinutosAnterior
				,GrupoAnterior
				,ComentarioAnterior
				,UsuarioCambia
				,FechaCambio) 
			SELECT 
				Numero
				,Tarea
				,Inicio
				,Fin
				,USuario
				,0
				,Grupo
				,Comentario
				,Tarea
				,Inicio
				,Fin
				,USuario
				,Minutos
				,Grupo
				,Comentario
				,'$usuario'
				,GETDATE() 
			FROM [Horas]  
			WHERE  Numero = $id	;
					
			DELETE FROM [Horas]	WHERE Numero = $id ;
			DELETE FROM [Palabras] WHERE Tarea = $tarea AND Campo = 3 AND Numero = $id ;
		COMMIT TRANSACTION ; "; 

DBSelect(utf8_decode($query));

$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_HISTORICO] '".$usuario."',".$tarea.",8"));

DBClose();
?>
