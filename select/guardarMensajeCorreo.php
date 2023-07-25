<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$mensajetiny=$_POST['mensajetiny'];
$idcorreo = $_POST['id'];


$mensajetiny = str_replace("'", "\"", $mensajetiny);  
//$mensajetiny = addslashes($mensajetiny);  

DBConectar($GLOBALS["cfg_DataBase"]);

		$query = "
				UPDATE [GestionPDT].[dbo].[Entrada_Correo]
				SET
					MensajeCorreo = '$mensajetiny'
				WHERE Id = $idcorreo
			";

//die($query);

DBSelect(utf8_decode($query));
DBClose();
?>