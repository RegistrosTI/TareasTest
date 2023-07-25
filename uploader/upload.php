<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id        = $_POST["id"];
$file      = $_POST["file"];
$usuario   = $_POST["usuario"];
$resultado = '';


DBConectar($GLOBALS["cfg_DataBase"]);
	
$name  = $file;
$fecha = date("d/m/Y"); 
$insert=DBSelect(utf8_decode("INSERT INTO [Adjuntos] ([Tarea],[Fichero],[Descripcion],[Fecha],[usuario],[Publico],[Tipo]) OUTPUT Inserted.Numero VALUES (".$id.",'db_adjuntos/".curPageName()."','".str_replace("'", "''", ($name))."','".$fecha."','".$usuario."',1,0)"));
$id_insertado =DBCampo($insert,"Numero");
DBFree($insert);	

$ruta_completa = "../files/server/php/files/$file" ;
rename('../files/server/php/files/'.$file , '../db_adjuntos/'.curPageName().'/' .$id_insertado.'_'.utf8_decode(($name)));
//rename($ruta_completa , "../db_adjuntos/MENU/$id_insertado_".utf8_decode(($name)) );
$resultado = $id.'|'.'db_adjuntos/'.curPageName().'|'.($name).'|'.$id_insertado;
$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_PALABRAS_NUMERO] ".$id.",5,".$id_insertado.",'".str_replace("'", "''", ($name))."' "));

echo $resultado;

DBClose();
?>