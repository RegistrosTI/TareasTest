<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$tarea  =$_GET['tarea'];
$usuario=$_GET['usuario'];
$plan  =$_GET['plan'];
$origen  =$_GET['origen'];


DBConectar($GLOBALS["cfg_DataBase"]);
$q=DBSelect(utf8_decode("EXECUTE [INICIAR_TAREA_HORA] $tarea,'$usuario',$plan,$origen" ) );
$Resultado=(DBCampo($q,"Resultado"));		
DBFree($q);				

// T#28318 Alberto. Si la tarea se reabre hay que avisar al usuario
$q = "
  SELECT COUNT(*) AS ABIERTA 
  FROM Tipos 
  WHERE TIPO = 5 
	AND Predeterminado = 2 
	AND Oficina		= (SELECT Oficina FROM [Tareas y Proyectos] WHERE Id = (SELECT Tarea FROM Horas WHERE Numero = $Resultado) ) 
	AND Descripcion = (SELECT Estado  FROM [Tareas y Proyectos] WHERE Id = (SELECT Tarea FROM Horas WHERE Numero = $Resultado) ) 
";
$q = utf8_decode($q); 
$q = DBSelect($q);
$ABIERTA=(DBCampo($q,"ABIERTA"));
echo $ABIERTA;
// T#28318 Alberto. Si la tarea se reabre hay que avisar al usuario

DBClose();
?>