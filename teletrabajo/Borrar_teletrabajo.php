<?php
include_once "../conf/config.php";
include_once "../conf/config_menu.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$data = json_decode(file_get_contents('php://input'), true);

$id                   = $data['id'];
$usuario              = $_SERVER['REMOTE_USER'];

$query = "
UPDATE [dbo].[Teletrabajo_Imputado]
   SET [Eliminado] = 'SI'
 WHERE Id = $id AND Usuario = '$usuario'
 ";
 $query = iconv('UTF-8', 'ISO-8859-1', $query); 
 $query = DBSelect ($query);
 DBFree($query);

$response = ['status' => 'success', 'message' => 'Data delete successfully'];
echo json_encode($response);