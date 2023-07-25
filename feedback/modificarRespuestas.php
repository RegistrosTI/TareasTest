<?php
require_once '../phpmailer/PHPMailer/class.phpmailer.php';
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$JSON_RESPUESTAS = $_GET["respuestas"];
$JSON_RESPUESTAS = html_entity_decode($JSON_RESPUESTAS);
$JSON_RESPUESTAS = json_decode($JSON_RESPUESTAS, true);

//var_dump($JSON_RESPUESTAS); die();
DBConectar($GLOBALS["cfg_DataBase"]);

$update = "";
$Id = 0;
foreach ($JSON_RESPUESTAS as $RESPUESTA) {
    //$key = str_replace ( '_' , '' , $key );
    //$value = str_replace ( "'" , "''" , $value );

    $update .= " UPDATE Feedback_Respuestas SET ";
    $final = "";
    foreach ($RESPUESTA as $key => $value) {
        $value = str_replace("'", "''", $value);
        switch ($key) {
            case 'Id':
                $final = " WHERE $key = $value ;";
                $Id = $value;
                break;
            case 'UsuarioObservaciones':
            case 'Respuesta':
                $update .= " $key = '$value',  ";
                break;
        }
    }

    $update .= "
		FechaRespuesta = GETDATE(),
		UsuarioRespuesta = '" . $_SERVER['REMOTE_USER'] . "' ";

    $update .= $final;

}

echo $update;

DBSelect(utf8_decode($update));

// OBTENEMOS DATOS DE TEST
$UID = '';
$Tarea = 0;
$query = "
	SELECT
		Tarea
		,UID
		,UsuarioAlta
		,(SELECT CAST([Título] AS VARCHAR(200))  FROM [Tareas y Proyectos] WHERE Id = Tarea) as TITULO
		,(SELECT Mail FROM GestionIDM.DBO.LDAP WHERE sAMAccountName = UsuarioAlta ) as DESTINO_MAIL
		,(SELECT Name FROM GestionIDM.DBO.LDAP WHERE sAMAccountName = UsuarioAlta) as DESTINO_NAME
	FROM Feedback_Respuestas WHERE Id = $Id ";
$query = DBSelect(utf8_decode($query));
for (; DBNext($query);) {
    $UID = utf8_encode(DBCampo($query, "UID"));
    $Tarea = utf8_encode(DBCampo($query, "Tarea"));
    $TITULO = utf8_encode(DBCampo($query, "TITULO"));
    $TO = utf8_encode(DBCampo($query, "DESTINO_MAIL"));
    $TO_NAME = utf8_encode(DBCampo($query, "DESTINO_NAME"));
}
DBFree($query);

// PREPARAMOS LOS CORREOS
$FROM = 'portal@sp-berner.com';
$FROM_NAME = 'Solicitudes Feedback Tareas';
$query = "SELECT Mail, Name FROM GestionIDM.DBO.LDAP WHERE sAMAccountName = '" . $_SERVER['REMOTE_USER'] . "' and (Mail IS NOT NULL OR Mail <> '') ";
$query = DBSelect(utf8_decode($query));
for (; DBNext($query);) {
    $FROM = utf8_encode(DBCampo($query, "Mail"));
    $FROM_NAME = utf8_encode(DBCampo($query, "Name"));
}

$subject = "Feedback realizado de la tarea: $TITULO";
$url = "https://tareas.sp-berner.com/feedback/index.php?test=" . $UID;
$boton_enlace = "
	<BR><BR>
	<a href='$url'  style='border: solid 4px red;font-weight:900;padding: 14px 28px;font-size: 20px;cursor: pointer;display: inline-block;color: red; text-decoration: none'>
		VER FEEDBACK
	</a>";
$message = "El usuario $FROM_NAME ha cumplimentado el feedback según su solicitud, puede ver el resultado en el siguiente enlace:" . $boton_enlace;

ini_set('SMTP', "relay.sp-berner.com");
ini_set('smtp_port', "25");
ini_set('sendmail_from', $FROM);

$email = new PHPMailer();
$email->IsHTML(true);
$email->From = utf8_decode($FROM);
$email->FromName = utf8_decode($FROM_NAME);
$email->Subject = utf8_decode($subject);
$email->Body = utf8_decode($message);
//$email->AddCC($FROM, $FROM_NAME);
$email->AddAddress($TO, $TO_NAME);

$res = $email->Send();

DBClose();
