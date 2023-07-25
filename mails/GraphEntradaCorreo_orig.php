<?php

ini_set('mssql.charset', 'UTF-8');

include "../../soporte/DB.php";
require "../conf/config.php";
include "../../soporte/funcionesgenerales.php";
//require "../../conf/mail.php";
require "../conf/config_Logs.php";

if (isset($_SERVER['HTTP_HOST']) === TRUE) {
    $actual_link = strtoupper("https://$_SERVER[HTTP_HOST]");
    
    //require_once "DB.php";
    if (strpos($actual_link, 'tareastest') > 0 ){
        $GLOBALS["cfg_HostDB"]  = 'SQL2016-Express\SQL2016Express';
  
    }else{
        $GLOBALS["cfg_HostDB"]  = 'SQL2016-Express\SQL2016Express';
    
    }
}

//$GLOBALS["cfg_HostDB"]  = 'SQL2016';
$GLOBALS["cfg_UserDB"]  = 'gestionti';
$GLOBALS["cfg_PassDB"]  = 'gestionti';
$GLOBALS["cfg_DataBase"] = 'GestionPDT';
DBConectar ( $GLOBALS [ "cfg_DataBase" ] );	


$url = "https://graph.sp-berner.com";
$id_user = 'a0cec894-8f04-442c-ba5d-fc61a2683813';
$id_folder_acepted = "AAMkADllYTk0MGFhLWZmOGYtNDc0ZS04NDVhLWUxMGMxNmE0NTA1OAAuAAAAAABTaoF15fvNQrNBsYu9CRk4AQBYr38iOz4zTKVrD6-NHTzKAABp6WaJAAA=";
$id_folder = 'AAMkADllYTk0MGFhLWZmOGYtNDc0ZS04NDVhLWUxMGMxNmE0NTA1OAAuAAAAAABTaoF15fvNQrNBsYu9CRk4AQBYr38iOz4zTKVrD6-NHTzKAAAA0OkPAAA=';
$curl = curl_init();
$datetime = date("Ymd H:i:s");

curl_setopt_array($curl, array(
    CURLOPT_URL => $url . '?type=mailFolder&id_user=' . $id_user . '&id_folder=' . $id_folder . '&',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_POSTFIELDS => "type=mail",
    CURLOPT_HTTPHEADER => array(
        "accept: */*",
        "cache-control: no-cache",
        "Content-Type: application/json"
    ),
));

$response = curl_exec($curl);
//$response = iconv('UTF-8', 'ISO-8859-1', $response);
/* $response = preg_replace('/[[:^print:]]/', '', $response);
$response = json_decode(stripslashes($response)); */
for ($i = 0; $i <= 31; ++$i) {
    $response = str_replace(chr($i), "", $response);
}
$response = str_replace(chr(127), "", $response);

// This is the most common part
// Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
// here we detect it and we remove it, basically it's the first 3 characters 
if (0 === strpos(bin2hex($response), 'efbbbf')) {
    $response = substr($response, 3);
}
$response = json_decode($response);

/* switch(json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - Sin errores';
        break;
        case JSON_ERROR_DEPTH:
            echo ' - Excedido tamaño máximo de la pila';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Desbordamiento de buffer o los modos no coinciden';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Encontrado carácter de control no esperado';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Error de sintaxis, JSON mal formado';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Caracteres UTF-8 malformados, posiblemente codificados de forma incorrecta';
        break;
        default:
            echo ' - Error desconocido';
        break;
    }

    echo PHP_EOL; */
/* foreach($response as $email){
   echo $email;
   echo "////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////".PHP_EOL;
} */
/* for( $i = 0 ; $i < mb_strlen($response) ; $i++ ) {
    echo 'pos: ' . $i . ' | ord: ' . ord( $response[$i] ) . ' | char: ' . $response[$i] . '<br />';
  } */
$emails = $response->value;

if ($emails) {
    Logg::addLine();
    Logg::addInfo("Leer correos");

    foreach ($emails as $email) {

        /***
         * @autor Herman Martinez
         * @description cambios en los atributos respuesta de API oauth2
         */
        $datetime = date("Ymd H:i:s");


        //COMPROBACION//

        //$usuarioEnDominio = comprobarUsuarioDominio($header);

        $UsuarioPermitido = comprobacionDominiosPermitidos($email->sender->emailAddress->address);

        $comprobarDestinatarios = false;
        $comprobarCorreo = false;

        $comprobarDestinatarios = comprobacionDestinatarios($email);
        if ($UsuarioPermitido == true) {

            //$comprobarCopiasOcultas = comprobacionCopiasOcultas($header); //SE PERMITE COPIAS/COPIAS CC
            $comprobarCorreo = comprobarcionCorreo($email);


            if (($comprobarDestinatarios == true) && ($comprobarCorreo == true)) {

                montarSQLCorreo($email);
                moverCorreoCarpetaAceptado($email);

            } else {
                borrarCorreo($email);
            }
        }
    }
} else {
    echo '<br /><hr /><br />';
    echo 'No hay correos. [' . $datetime . ']';
    echo '<hr />';
}



//FUNCIONES//

function comprobacionCopiasOcultas($DestinatariosOcultos)
{
    if (property_exists($DestinatariosOcultos, 'cc')) {
        $comprobaremailsocultas = count($DestinatariosOcultos->cc);

        if ($comprobaremailsocultas <> 0) {
            return false;
        } else {
            return true;
        }
    }
}


function comprobacionDestinatarios($email)
{
    $comprobarTo = $email->toRecipients[0]->emailAddress->address;

    if ($comprobarTo) {
        $comprobaremailsto = count($email->toRecipients);

        $solicitantecorreo = $email->sender->emailAddress->address;

        $tocorreo = $comprobarTo;
        $tocorreo = trim($tocorreo);

        $msjCorreoAutomaticoInfo = '<br /><span style:"color:red"><b>Este correo se envía de forma automática, por favor no responda a este mensaje.</b></span>';
        /*var_dump('$comprobaremailsto:'.$comprobaremailsto);
            var_dump('$solicitantecorreo:'.$solicitantecorreo);
            var_dump('$tocorreo:'.$tocorreo);
            die();/*/

        if ($comprobaremailsto > 1) {
            enviarmail($solicitantecorreo, "Error en correo.", "Su tarea no ha sido creada porque ha incluido a varias personas en destinatarios." . $msjCorreoAutomaticoInfo, 0);
            return false;
        } else {
            if ($tocorreo != 'soporte.ti@sp-berner.com') {

                enviarmail($solicitantecorreo, "Error en correo.", "Su tarea no ha sido creada porque ha incluido a otra persona en destinatarios, solo tiene que ir esta cuenta en destinatarios. Si necesitas agregar a alguien asigna a la persona en copia." . $msjCorreoAutomaticoInfo, 0);
                return false;
            } else {
                return true;
            }
        }
    } else {
        return false;
    }
}


function comprobarUsuarioDominio($correo)
{

    //Comprobacion de que el usuario esta en el dominio.//

    $Usuario = $correo['from'][0]->mail;
    $Usuario = imap_utf8($Usuario); //CORREO PETICION	

    die($Usuario);
    $sqlbuscarnombre = "SELECT COUNT(*) as resultado FROM [GestionIDM].[dbo].[LDAP] Where Mail ='" . $Usuario . "'";
    $resultadoquery = DBSelect(utf8_decode($sqlbuscarnombre));
    $resultado = utf8_encode(DBCampo($resultadoquery, "resultado"));

    if ($resultado == 1) {
        return true;
    }
    return false;
}


function comprobarcionCorreo($email)
{

    //USUARIO PETICION//
    $from = $email->sender->emailAddress->address;
    $from = imap_utf8($from); //CORREO PETICION
    $fromuser = $email->sender->emailAddress->name;
    $fromuser = imap_utf8($fromuser); //NOMBRE CORREO PETICION

    //CORREO DESTINO (BOT - Se deja preparado para mas posibles cuentas de bots)//
    $emailto = $email->toRecipients[0]->emailAddress->address;    //CORREO BOT		
    $toaddress = $email->toRecipients[0]->emailAddress->address;
    $toaddress = imap_utf8($toaddress); //USUARIO BOT

    //ASUNTO DEL CORREO//
    $subject = $email->subject;
    $subject = imap_utf8($subject); //¿Haria falta? PRUEBA//
    // ++ Alberto 17/12/2020
    $basura = array("RV: ", "Rv: ", "RE: ", "Re: ", "FW: ", "Fw: ");
    $subject = str_replace($basura, "", $subject);
    // -- Alberto 17/12/2020

    //MENSAJE CORREO//
    $message = $email->body->content;
    $message = trim(iconv('ISO-8859-1', 'UTF-8', $message));
    //$message = substr($message, 1750, 140);
    $message = str_replace("'", "", $message);

    //COMPROBACION ASUNTO DEL MENSAJE//
    $error = 0;
    $enviar = true;
    $msjCorreoAutomaticoInfo = '<br /><span style:"color:red"><b>Este correo se envía de forma automática, por favor no responda a este mensaje.</b></span>';

    //asunto vacio//
    if (str_replace(" ", "", $subject) == '') {

        $error = 1;
        echo "Error el correo no contiene asunto";
        echo "<br/>";

        enviarmail($from, "Error en correo.", "Su tarea no ha sido creada porque no contenía un asunto, escribe un asunto para poder crear la tarea." . $msjCorreoAutomaticoInfo, 0);
        $enviar = false;
    }

    //Asunto demasiado largo//
    if (strlen($subject) > 140) {

        $error = 2;
        echo "El asunto tiene demasiados caracteres en el asunto.(<140) <br />";
        echo "<br/>";

        enviarmail($from, "Error en correo.", "Su tarea no ha sido creada porque su asunto tiene más de 140 caracteres. [" + $subject + "]" . $msjCorreoAutomaticoInfo, 0);
        $enviar = false;
    }


    //Mensaje vacio//
    if (str_replace(" ", "", $message) == '') {

        $error = 3;
        echo "Error el correo no contiene mensaje.";
        echo "<br/>";

        enviarmail($from, "Error en correo.", "Su tarea no ha sido creada porque no contenía un mensaje con la descripción de la tarea, escribe un asunto para poder crear la tarea." . $message . $msjCorreoAutomaticoInfo, 0);

        $enviar = false;
    }

    if ($enviar == false) {
        borrarCorreo($email);
    }

    return $enviar;
}

function borrarCorreo($message)
{
    // $message = $message->delete($expunge = true);
    //crear en la API para borrar
}


function montarSQLCorreo($email)
{
    $raw_email_all = getrawmessage($email);

    $message = $email->body->content;
    $g = "$message";
    $cadena_buscada = 'high';
    $posicion_coincidencia = strrpos($g, $cadena_buscada);
    $prioridad = substr($g, $posicion_coincidencia, 4);
    $datetime = date("Ymd H:i:s");

    //USUARIO PETICION//
    $from = $email->sender->emailAddress->address;
    $from = iconv('ISO-8859-1', 'UTF-8', $from); //CORREO PETICION
    $fromuser = $email->sender->emailAddress->name;
    $fromuser = iconv('ISO-8859-1', 'UTF-8', $fromuser); //NOMBRE CORREO PETICION


    //CORREO DESTINO (BOT - Se deja preparado para mas posibles cuentas de bots)//
    $emailto = $email->toRecipients[0]->emailAddress->address;    //CORREO BOT		
    $toaddress = $email->toRecipients[0]->emailAddress->address;
    $toaddress = iconv('ISO-8859-1', 'UTF-8', $toaddress); //USUARIO BOT

    //ASUNTO DEL CORREO//
    $subject = $email->subject;
    // ++ Alberto 17/12/2020
    $basura = array("RV: ", "Rv: ", "RE: ", "Re: ", "FW: ", "Fw: ");
    $subject = str_replace($basura, "", $subject);
    $subject = trim(iconv("ISO-8859-1", "UTF-8", $subject));

    // -- Alberto 17/12/2020

    $mensajeid = substr($email->internetMessageId, 1, strpos($email->internetMessageId, '@') - 1);     //15/01/2019

    if ($emailto == "soporte.ti@sp-berner.com") {
        $dep = 'TI';
    }
    $consultardep = "SELECT TOP 1 [Descripcion] FROM [GestionPDT].[dbo].[Tipos] WHERE Tipo = 4 AND Oficina = '" . $dep . "'";

    if ($prioridad == "high") {
        $consultardep = $consultardep . "AND PrioridadUrgente = 1";
    } else {
        $consultardep = $consultardep . "AND Predeterminado = 1";
    }

    $consultardep = $consultardep . " ORDER BY Numero";
    $consultardep = DBSelect(iconv('UTF-8', 'ISO-8859-1', $consultardep));
    $resprio = DBCampo($consultardep, ("Descripcion"));

    $mensajeid = substr($email->internetMessageId, 1, strpos($email->internetMessageId, '@') - 1);

    $solicitadoquery = "SELECT TOP 1 [Name] as nombre FROM [GestionIDM].[dbo].[LDAP] Where Mail ='" .$from. "'";
    $consultasolicitado = DBSelect(iconv('UTF-8', 'ISO-8859-1', $solicitadoquery));
    $nombresolicitante = DBCampo($consultasolicitado, ("nombre"));

    //$subjectinsertar = mb_decode_mimeheader($subject);
    $subjectinsertar = $subject;
    $subjectinsertar = str_replace("_", " ", $subjectinsertar);
    $subjectinsertar = str_replace("'", " ", $subjectinsertar);
    
    //if ($codificacion -> encoding== 4){ $message = quoted_printable_decode($message);};

    // FILTRO DEL MENSAJE (29/03/2019)//
    $message = trim(iconv("ISO-8859-1", "UTF-8", $message));
    $message = str_replace("'", "", $message);
    $message = preg_replace('<hr([\w\W]+?)/>', '', $message, -1);
    $message = str_replace("This email was scanned by Bitdefender", " ", $message);
    $message = preg_replace('<img([\w\W]+?)/>', '', $message, -1);


    $message = limpiaTextoConHTML($message);

    /*  if($solicitantecorreo == 'herman.martinez@sp-berner.com'){

        print("<pre>".print_r(iconv("UTF-8", "ISO-8859-1", $subjectinsertar),true)."</pre>"); 
        //echo gettype($message);
        print("<pre>".print_r(iconv("UTF-8", "ISO-8859-1", $message),true)."</pre>"); 
       
        die(); 
        }   */


    if ((strpos($message, 'Content-Type:') !== false) || (strpos($message, 'PC9vOnA') !== false)) {
        $message = '';
    }

    $sqlinsertar = "INSERT INTO [Entrada_Correo]( 
									[Solicitado]
									,[SolicitadoEmail]
									,[Asunto]
									,[FechaSolicitud]
									,[MensajeCorreo]
									,[Estado]
									,[Asignado]
									,[Prioridad]
									,[Oficina]
									,[MotivoRechazoOAceptacion]
									,[Tipo]
									,[Usuario]
									,[Horas]
									,[Catalogacion]
									,[HorasObligadas]
									,[Criticidad]
									,[RutaCorreo]
									,[NoPlanificado]
									,[FechaObjetivo]
									,[AreaSolicitante]
									,[Planta]
									,[IdTarea]
									)VALUES( 
									'" . $nombresolicitante . "'
									,'" . $from . "' 
									,'" . utf8_decode(utf8_decode($subjectinsertar)) . "' 
									,'" . $datetime . "' 
									,'" . utf8_decode(utf8_decode($message)) . "'
									,'Pendiente'
									,''
									,'" . $resprio . "'
									,'" . $dep . "'
									,''
									,''
									,''
									,0
									,''
									,''
									,''
									,'" . $mensajeid . "' 
									,''
									,''
									,(SELECT TOP 1 [Descripcion] FROM [GestionPDT].[dbo].[Tipos] WHERE Tipo = 14 AND Predeterminado = 1 AND Oficina = '$dep')
									,(SELECT TOP 1 [Descripcion] FROM [GestionPDT].[dbo].[Tipos] WHERE Tipo = 15 AND Predeterminado = 1 AND Oficina = '$dep')
									,0
									)";


    DBSelect($sqlinsertar);

    $raw_email_all = getrawmessage($email);

    $raw_email_all = str_replace('v:ext="edit"', 'v:ext=3D"edit"', $raw_email_all);
    $raw_email_all = str_replace('data="1"', 'data=3D"1"', $raw_email_all); 
    //TODO comprobar si se ha creado el fichero eml
    file_put_contents('CorreosBOT/' . $mensajeid . '.eml', $raw_email_all);

    enviarmail($from, "Se ha creado la solicitud para su valoración.", "Su solicitud de tarea con asunto : " . imap_utf8($subject) . "<br/><br/> Ha sido creada en el sistema para su valoración <br/><br/> <br/> <br/> <br/> <u><b> Este correo se envía de forma automática, por favor no responda a este mensaje. <br/> La creación de la solicitud es automática, pero la creación de tareas está bajo valoración.</b></u>", $mensajeid);

    Logg::addLine();
    Logg::addInfo("Ha sido creada en el sistema para su valoración");

    if ($prioridad == "high") {
        echo "<br/>Importancia: " . $prioridad;
    } else {
        $prioridad = "normal";
        echo "<br/>Importancia: " . $prioridad;
    }
}


function moverCorreoCarpetaAceptado($email)
{

    $url = "https://graph.sp-berner.com";
    $id_user = 'a0cec894-8f04-442c-ba5d-fc61a2683813';
    $id_folder_acepted = "AAMkADllYTk0MGFhLWZmOGYtNDc0ZS04NDVhLWUxMGMxNmE0NTA1OAAuAAAAAABTaoF15fvNQrNBsYu9CRk4AQBYr38iOz4zTKVrD6-NHTzKAABp6WaJAAA=";
    $id_folder = 'AAMkADllYTk0MGFhLWZmOGYtNDc0ZS04NDVhLWUxMGMxNmE0NTA1OAAuAAAAAABTaoF15fvNQrNBsYu9CRk4AQBYr38iOz4zTKVrD6-NHTzKAAAA0OkPAAA=';
    $curl = curl_init();

    //movemos el correo a la carpeta aceptados//
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url . '?type=move&id_user=' . $id_user . '&id_folder=' . $id_folder . '&id_message=' . $email->id . '&id_folder_move=' .$id_folder_acepted. '&',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "type=move",
        CURLOPT_HTTPHEADER => array(
            "accept: */*",
            "cache-control: no-cache",
            "Content-Type: application/json"
        ),
    ));

    $response = curl_exec($curl);

    return true;
}

function getrawmessage($email)
{

    $url = "https://graph.sp-berner.com";
    $id_user = 'a0cec894-8f04-442c-ba5d-fc61a2683813';
    $id_folder = 'AAMkADllYTk0MGFhLWZmOGYtNDc0ZS04NDVhLWUxMGMxNmE0NTA1OAAuAAAAAABTaoF15fvNQrNBsYu9CRk4AQBYr38iOz4zTKVrD6-NHTzKAAAA0OkPAAA=';
    $value = '$value';
    $curl = curl_init();

    //movemos el correo a la carpeta aceptados//
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url . '?type=mailraw&id_user=' . $id_user . '&id_folder=' . $id_folder . '&id_message=' . $email->id . '&raw_message='.$value.'&',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
       // CURLOPT_POSTFIELDS => "type=mailraw",
        CURLOPT_HTTPHEADER => array(
            "accept: */*",
            "cache-control: no-cache",
            "Content-Type: application/json"
        ),
    ));

    $response = curl_exec($curl);


    return $response;
}




function comprobacionDominiosPermitidos($address)
{
    $dominio = explode('@', $address);

    //if(($dominio == 'sp-berner.com')||($dominio == 'miklimpieza.com')||($dominio == 'sp-bramli.com')||($dominio == 'spbernerplastic.onmicrosoft.com')){
    if (($dominio[1] == 'sp-berner.com') || ($dominio[1] == 'miklimpieza.com') || ($dominio[1] == 'sp-bramli.com') || ($dominio[1] == 'spbernerplastic.onmicrosoft.com') || ($dominio[1] == 'bernerlogistic.com')) {
        Logg::addLine();
        Logg::addInfo("Cumple con el criterio de dominios");
        return true;
    } else {
        return false;
    }
}

function enviarmail($to, $asunto, $mensaje, $correoid)
{
    insertarCorreoMensajeria($asunto, $mensaje, 'soporte.ti@sp-berner.com', $to, '', '');
    echo "Correo de entrada: " . $to;
    echo "<br />";
    echo "Asunto: " . $mensaje;
    echo "<br />";
}
