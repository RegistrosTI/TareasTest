<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$tarea_coste=$_GET['tarea_coste'];
$usuario    =$_GET['usuario'];
DBConectar($GLOBALS["cfg_DataBase"]);


$q=DBSelect(utf8_decode("INSERT INTO [Log_Costes] ([Numero],[Tarea],[Inicio],[Fin],[Usuario],[Importe],[Grupo],[Comentario],[Tipo],[TareaAnterior],[InicioAnterior],[FinAnterior],[UsuarioAnterior]
						,[ImporteAnterior],[GrupoAnterior],[ComentarioAnterior],[TipoAnterior],[UsuarioCambia],[FechaCambio]) SELECT [Numero],[Tarea],[Inicio],[Fin],[Usuario],[Importe]
						,[Grupo],[Comentario],[Tipo],[Tarea],[Inicio],[Fin],[Usuario],[Importe],[Grupo],[Comentario],[Tipo],'".$usuario ."',GETDATE() FROM [Costes] where Numero = ".$tarea_coste));
DBFree($q);	

$q=DBSelect(utf8_decode("DELETE FROM [Costes] WHERE Numero = ".$tarea_coste));
DBFree($q);	
$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_HISTORICO] '".$usuario."',".$tarea_coste.",33002"));
DBFree($insert);			
DBClose();
echo $Resultado;
?>