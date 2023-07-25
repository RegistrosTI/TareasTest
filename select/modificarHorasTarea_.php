<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id        =$_GET["Id"];
$Titulo    =$_GET["Titulo"];
$diainicio =$_GET["diainicio"];
$diafin    =$_GET["diafin"];
$horainicio=$_GET["horainicio"];
$horafin   =$_GET["horafin"];
$usuario   =$_GET["usuario"];
$tarea     =$_GET["tarea"];
$tipo      =$_GET["Tipo"];
if(strlen($diafin)<=10) 
{
	$diayhorafin=$diafin.' '.$horafin;
}
else
{
	$diayhorafin=$diafin;
}
if(strlen($diainicio)<=10) 
{
	$diayhorainicio=$diainicio.' '.$horainicio;
}
else
{
	$diayhorainicio=$diainicio;
}

DBConectar($GLOBALS["cfg_DataBase"]);
if($diafin=='')
{
	DBSelect(utf8_decode("insert into [Log_Horas] (	Numero,	Tarea,	Inicio,	Fin,Usuario,Minutos,Grupo,Comentario,Tipo,TareaAnterior,InicioAnterior,FinAnterior,USuarioAnterior,MinutosAnterior,
						GrupoAnterior,ComentarioAnterior,TipoAnterior,UsuarioCambia,FechaCambio) SELECT Numero,Tarea,'".$diayhorainicio."',NULL,USuario,0,Grupo,'".str_replace("'", "''", ($Titulo))."',
						'".str_replace("'", "''", ($tipo))."',Tarea,Inicio,Fin,USuario,Minutos,Grupo,Comentario,Tipo,'".($usuario)."',GETDATE()	FROM [Horas]  WHERE  
						(('".str_replace("'", "''", ($Titulo))."' NOT LIKE [Comentario]) OR	('".str_replace("'", "''", ($tipo))."' NOT LIKE [Tipo]) OR	('".$diayhorainicio."' <> [Inicio]) OR
						([Fin] is not null )) AND Numero = ".$id."	
						UPDATE [Horas] SET [Comentario] = '".str_replace("'", "''", ($Titulo))."',[Inicio] = '".$diayhorainicio."',[Fin] = null,[Minutos] = 0
						,[Tipo] = '".str_replace("'", "''", ($tipo))."' WHERE Numero = ".$id." "));
}
else
{
	DBSelect(utf8_decode("insert into [Log_Horas] (	Numero,	Tarea,	Inicio,	Fin,Usuario,Minutos,Grupo,Comentario,Tipo,TareaAnterior,InicioAnterior,FinAnterior,USuarioAnterior,MinutosAnterior,
						GrupoAnterior,ComentarioAnterior,TipoAnterior,UsuarioCambia,FechaCambio) SELECT Numero,Tarea,'".$diayhorainicio."',	'".$diayhorafin."',	USuario,
						DATEDIFF(mi,'".$diayhorainicio."', '".$diayhorafin."'),Grupo,'".str_replace("'", "''", ($Titulo))."','".str_replace("'", "''", ($tipo))."',
						Tarea,Inicio,Fin,USuario,Minutos,Grupo,Comentario,Tipo,'".($usuario)."',GETDATE() FROM [Horas]  WHERE  
						(('".str_replace("'", "''", ($Titulo))."' NOT LIKE [Comentario]) OR
						('".str_replace("'", "''", ($tipo))."' NOT LIKE [Tipo]) OR
						('".$diayhorainicio."' <> [Inicio]) OR	('".$diayhorafin."' <> [Fin]))	AND Numero = ".$id."
						UPDATE [Horas] SET [Comentario] = '".str_replace("'", "''", ($Titulo))."',[Tipo] = '".str_replace("'", "''", ($tipo))."'
						,[Inicio] = '".$diayhorainicio."',[Fin] = '".$diayhorafin."',[Minutos] =  DATEDIFF(mi,'".$diayhorainicio."', '".$diayhorafin."') WHERE Numero = ".$id."	"));
}

$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_HISTORICO] '".$usuario."',".$tarea.",2"));
DBFree($insert);
$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_PALABRAS_NUMERO] ".$tarea.",3,".$id.",'".str_replace("'", "''", ($Titulo))."' "));
DBFree($insert);	
DBClose();

echo $Titulo;
?>
