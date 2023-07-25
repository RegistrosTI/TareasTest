<?php
$portal= $_GET['portal'];

include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_$portal.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


$usuario             = $_GET['usuario'];
$nota_1              = $_GET['nota_1'];
$id_nota_1           = $_GET['id_nota_1'];	
$nota_2              = $_GET['nota_2'];
$id_nota_2           = $_GET['id_nota_2'];	
$observaciones_nota_1=$_GET['observaciones_nota_1'];
$observaciones_nota_2=$_GET['observaciones_nota_2'];	


DBConectar($GLOBALS["cfg_DataBase"]);
$q=DBSelect(utf8_decode("EXECUTE [MODIFICAR_VALORACION] '".utf8_encode($usuario)."',".$nota_1.",".$id_nota_1.",'".str_replace("'", "''", ($observaciones_nota_1))."',".$nota_2.",".$id_nota_2.",'".str_replace("'", "''", ($observaciones_nota_2))."'    "));
DBFree($q);				
DBClose();

//echo $opcion;
?>