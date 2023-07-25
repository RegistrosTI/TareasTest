<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id        = $_GET["id"];
$usuario   = $_GET["usuario"];
$resultado = '';

DBConectar($GLOBALS["cfg_DataBase"]);

$u=DBSelect(("SELECT [Numero] ,[Tarea],CAST(Descripcion as varchar(250)) as Descripcion,CAST(Fichero as varchar(250)) as Fichero FROM [Adjuntos]  WHERE Numero = ".$id));
$FicheroNumero     =(DBCampo($u,("Numero")));		
$FicheroTarea      =(DBCampo($u,"Tarea"));
$FicheroDescripcion=(DBCampo($u,"Descripcion"));			
$FicheroCarpeta    =(DBCampo($u,"Fichero"));
DBFree($u);		

$fichero_subido='../'.$FicheroCarpeta.'/'.$FicheroNumero.'_'.$FicheroDescripcion;
unlink($fichero_subido);
$insert=DBSelect(utf8_decode("DELETE FROM [Adjuntos] WHERE Numero = ".$id));
DBFree($insert);	
$insert=DBSelect(utf8_decode("DELETE FROM [Palabras] WHERE Tarea = ".$FicheroTarea." AND Campo = 5 AND Numero = ".$id." "));
DBFree($insert);	

$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_HISTORICO] '".$usuario."',".$FicheroTarea.",43"));
DBClose();
echo $resultado;
?>