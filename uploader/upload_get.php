<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id        = $_GET["id"];
$file      = $_GET["file"];
$usuario   = $_GET["usuario"];
$resultado = '';

DBConectar($GLOBALS["cfg_DataBase"]);

$name  = $file;
$fecha = date("d/m/Y"); 
$insert=DBSelect(utf8_decode("INSERT INTO [Adjuntos] ([Tarea],[Fichero],[Descripcion],[Fecha],[usuario],[Publico],[Tipo]) OUTPUT Inserted.Numero VALUES (".$id.",'db_adjuntos/".curPageName()."','".str_replace("'", "''", ($name))."','".$fecha."','".$usuario."',1,0)"));
$id_insertado =DBCampo($insert,"Numero");
 DBFree($insert);	   

rename('../files/server/php/files/'.$file , '../db_adjuntos/'.curPageName().'/' .$id_insertado.'_'.utf8_decode(($name)));
$resultado = $id.'|'.'db_adjuntos/'.curPageName().'|'.($name).'|'.$id_insertado;

$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_PALABRAS_NUMERO] ".$id.",5,".$id_insertado.",'".str_replace("'", "''", ($name))."' "));
echo $resultado;

DBClose();
?>