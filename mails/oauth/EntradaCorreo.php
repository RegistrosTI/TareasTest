<?php

ini_set('mssql.charset', 'UTF-8');

require __DIR__.'/vendor/autoload.php'; 
    
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\Query\WhereQuery;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Address;
use Webklex\PHPIMAP\Header;
use Webklex\PHPIMAP\Attribute;

    include "../../../soporte/DB.php";
    require "../../conf/config.php";
    include "../../../soporte/funcionesgenerales.php";
    //require "../../conf/mail.php";

    $CODE = isset($_GET ['code']) ? $_GET['code'] : null;
    $SESSEION_STATE = isset($_GET ['session_state']) ? $_GET['session_state'] : null;

   
    if (isset($_SERVER['HTTP_HOST']) === TRUE) {
		$actual_link = strtoupper("https://$_SERVER[HTTP_HOST]");
		
		//require_once "DB.php";
		if (strpos($actual_link, 'TEST') > 0 ){
			$GLOBALS["cfg_HostDB"]  = 'SQL2016-Express\SQL2016Express';
		}else{
			$GLOBALS["cfg_HostDB"]  = 'SQL2016';
		}
	}
	
	$GLOBALS["cfg_HostDB"]  = 'SQL2016';
	$GLOBALS["cfg_UserDB"]  = 'gestionti';
	$GLOBALS["cfg_PassDB"]  = 'gestionti';
	$GLOBALS["cfg_DataBase"] = 'GestionPDT';
	DBConectar ( $GLOBALS [ "cfg_DataBase" ] );	
	

	$fecha_hora = date ( "d-m-Y H:i:s" );

     if($CODE == NULL && $SESSEION_STATE == NULL){
        $URL_TO_CODE = 'https://login.microsoftonline.com/87375e02-5221-41ac-9db6-9949c490f072/oauth2/v2.0/authorize?client_id=4d719aac-7878-421d-bdef-520fae86499e&scope=https://outlook.office365.com/IMAP.AccessAsUser.All&redirect_uri=https%3A%2F%2Ftareas.sp-berner.com%2Fmails%2Foauth%2FEntradaCorreo.php&response_type=code&approval_prompt=auto';
        header('Location: ' . $URL_TO_CODE); 
        echo 'Esto lo hemos hecho nosotros:'.$URL_TO_CODE;
    } 


/**
 * @Autor Herman Martinez
 * 
 * @Description Credenciales creacion de aplicacion en portal Azure 
 * 
 * @date 28/12/2022
 */

if($CODE != null && $SESSEION_STATE != null){
$CLIENT_ID="4d719aac-7878-421d-bdef-520fae86499e";
$CLIENT_SECRET="HrS8Q~JTT4j8fhw3VrWOF0MhhQ4bQe2rdBpXsdiF";
//$ID_SECRETO = '5195255b-7c8b-42bf-9cb4-741dc241d801';
$TENANT="87375e02-5221-41ac-9db6-9949c490f072";
$SCOPE="https://outlook.office365.com/IMAP.AccessAsUser.All offline_access";
$REDIRECT_URI=urlencode("https://tareas.sp-berner.com/mails/oauth/EntradaCorreo.php");

//echo "Trying to authenticate the session..";

$url= "https://login.microsoftonline.com/$TENANT/oauth2/v2.0/token";

 $curl = curl_init();

 curl_setopt_array($curl, array(
   CURLOPT_URL => $url,
   CURLOPT_RETURNTRANSFER => true,
   CURLOPT_ENCODING => "",
   CURLOPT_MAXREDIRS => 10,
   CURLOPT_TIMEOUT => 30,
   CURLOPT_SSL_VERIFYHOST => false,
   CURLOPT_SSL_VERIFYPEER => false,
   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
   CURLOPT_CUSTOMREQUEST => "POST",
   CURLOPT_POSTFIELDS => "grant_type=authorization_code&code=$CODE&client_id=$CLIENT_ID&session_state=$SESSEION_STATE&client_secret=$CLIENT_SECRET&redirect_uri=$REDIRECT_URI&scope=$SCOPE",
   CURLOPT_HTTPHEADER => array(
     "accept: application/json",
     "cache-control: no-cache",
     "content-type: application/x-www-form-urlencoded"
   ),
 ));

 $response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  $array_php_resul =  json_decode($response, true);
  $access_token = $array_php_resul["access_token"];
}

}

if(!empty($access_token)){
    
//$cm = new ClientManager($options = ["options" => ["debug" => true]]);                     
$cm = new ClientManager();                      
$client = $cm->make([
    'host'          => 'outlook.office365.com',                
    'port'          => 993,
    'encryption'    => 'ssl',
    'validate_cert' => false,
    'username'      => 'soporte.ti@sp-berner.com',
    'password'      => $access_token,
    'protocol'      => 'imap',
    'authentication' => "oauth"
]);

try {
    //Connect to the IMAP Server
    $client->connect();
    $client->checkConnection();
    //$folders = $client->getFolders();
    $folders = $client->getFolder('INBOX');
    $emails = $folders->messages()->all()->get();



    /*$header = new Header($emails);
    print("<pre>".print_r($header,true)."</pre>");
    die();*/


    /**
     * array(6) { ["exists"]=> int(17) ["recent"]=> int(0) ["flags"]=> array(1) { [0]=> array(6) { [0]=> string(5) "\Seen" [1]=> string(9) "\Answered" [2]=> string(8) "\Flagged" [3]=> string(8) "\Deleted" [4]=> string(6) "\Draft" [5]=> string(8) "$MDNSent" } } ["unseen"]=> int(17) ["uidvalidity"]=> int(14) ["uidnext"]=> int(45659) }
     */
    //var_dump($folders->getStatus()); it work retrieve array of status

   /* foreach($folders as $folder){
        //$obj = (object) $email;
        //$obj = json_encode($obj);
        //var_dump($object);
         print("<pre>".print_r($folder,true)."</pre>");
    }
    die();*/

    //$emails = json_encode($emails);
   /* foreach($emails as $email){
        //$obj = (object) $email;
        //$obj = json_encode($obj);
        //$email = $email->getMessageByUid($uid = 42462);
        //var_dump($object);
        foreach($email as $key => $value) {
            print("<pre>".print_r($value,true)."</pre>");
        }
        die();
       
        $status = $email->hasTextBody();
        $body = $email->getTextBody();
        $header = $email->getHeader();
        $attributes = $email->getAttributes();
        foreach($header as $key => $value) {
            print("<pre>".print_r($value,true)."</pre>");
        }
      
        print("<pre>".print_r($attributes,true)."</pre>");


        $body = $email->getTextBody();
		$g = "$body";
		$cadena_buscada = 'high';
		$posicion_coincidencia = strrpos($g, $cadena_buscada);
		$prioridad = substr($g, $posicion_coincidencia , 4);
        $body = imap_utf8($body);
        print("<pre>".print_r($body,true)."</pre>");
    }
    die();*/
 
    $overviews = $folders->overview($sequence = "1:*");

 /*    foreach($overviews as $overview){
        $obj = (object) $overview;
        $obj = json_encode($obj);
        $object = json_decode(json_encode($overview), FALSE);
        //var_dump($object);
        foreach($overview as $key => $value) {
            print("<pre>".print_r($value,true)."</pre>");
        }
         if($overview['to']){
         $count = count($overview['to']);
         print("<pre>".print_r('this is count:'.$count,true)."</pre>");
         print("<pre>".print_r($overview,true)."</pre>");
        }
        print("<pre>".print_r($overview,true)."</pre>");
        
        
    }
    die(); */
/* 
       foreach($emails as $email){ 
        //$body = $email->getTextBody();
        //print("<pre>".print_r($body,true)."</pre>");

        $header = $email->getHeader();
        $attributes = $email->getAttributes();

 
        print("<pre>".print_r($email,true)."</pre>"); 
        //$header = "$headers";
        //$value = $header->toString();
        //$value = (string)$header;
        
      



    $body = $email->getTextBody();
    $attributes = $email->getAttributes()['content_transfer_encoding'][0];
    $bodies = $email->getBodies();
    //$econding = $email['content_transfer_encoding'];
    $headers = $email->getHeader();
    print("<pre>".print_r($headers,true)."</pre>"); 
        //var_dump($email);
        die(); 
        $pasebody = $body->parseBody();
        print("<pre>".print_r($body,true)."</pre>");

    $attributes = $email->getAttributes()['to'][0];
        print("<pre>".print_r($attributes,true)."</pre>");
        $count = count($email->getAttributes()['to']);
        print("<pre>".print_r($count,true)."</pre>"); 
        }
        die();   */
    

    if ($emails) {

            foreach ($emails as $email){
              

                $attributes = $email->getAttributes(); 
                $overviews = $email->getAttributes(); 
                /***
                 * @autor Herman Martinez
                 * @description cambios en los atributos respuesta de API oauth2
                 */
                $messageid = isset($overviews['message_id'][0]) ? $overviews['message_id'][0] : null; 	//15/01/2019
                $overview_time = isset($overview['x_ms_exchange_crosstenant_originalarrivaltime'][0]) ? $overview['x_ms_exchange_crosstenant_originalarrivaltime'][0] : '2000/01/01 00:00:00';
                $datetime = date_create($overview_time);
                $datetime = date_format($datetime,"Y/m/d H:i:s");
                
                //COMPROBACION//
         
                //$usuarioEnDominio = comprobarUsuarioDominio($header);
                
                $UsuarioPermitido = comprobacionDominiosPermitidos($attributes);

                if($UsuarioPermitido == true){
                    
                    $comprobarDestinatarios = false;
                    $comprobarCorreo = false;
                    
                    $comprobarDestinatarios = comprobacionDestinatarios($email);
                    //$comprobarCopiasOcultas = comprobacionCopiasOcultas($header); //SE PERMITE COPIAS/COPIAS CC
                    $comprobarCorreo = comprobarcionCorreo($attributes, $email);
                    
                    if(($comprobarDestinatarios == true) && ($comprobarCorreo == true)){
                        montarSQLCorreo($email, $datetime, $messageid);	
                        
                        moverCorreoCarpetaAceptado($email);
                    }else{
                        borrarCorreo($email);
                                            
                    }
                    
                }
            }			
		
		
	}else{
		echo '<br /><hr /><br />';
		echo 'No hay correos. ['.$fecha_hora.']';
		echo '<hr />';
	}
  
} catch (Exception $e) {
    echo 'Exception : ',  $e->getMessage(), "\n";
}
}

//FUNCIONES//
	
	function comprobacionCopiasOcultas($DestinatariosOcultos){
		if(property_exists($DestinatariosOcultos, 'cc')){
			$comprobaremailsocultas = count($DestinatariosOcultos -> cc);
			
			if ($comprobaremailsocultas <> 0 ){
				return false;
			}else{
				return true;
			}
		}
	}


	function comprobacionDestinatarios($email){
        $comprobarTo = $email->getAttributes()['to'];
		if($comprobarTo){
			$comprobaremailsto = count($email->getAttributes()['to']);
           
            $attributes = $email->getAttributes();
			$solicitantecorreo = $attributes['from'][0]->mail;
			
			$tocorreo = $attributes['to'][0]->mail;
            $tocorreo = trim($tocorreo);
						
			$msjCorreoAutomaticoInfo ='<br /><span style:"color:red"><b>Este correo se envía de forma automática, por favor no responda a este mensaje.</b></span>';
            /*var_dump('$comprobaremailsto:'.$comprobaremailsto);
            var_dump('$solicitantecorreo:'.$solicitantecorreo);
            var_dump('$tocorreo:'.$tocorreo);
            die();/*/

			if($comprobaremailsto > 1){
				enviarmail($solicitantecorreo , "Error en correo." , "Su tarea no ha sido creada porque ha incluido a varias personas en destinatarios.".$msjCorreoAutomaticoInfo, 0);
				return false;
			}else{
			    if($tocorreo != 'soporte.ti@sp-berner.com'){
                    
			        enviarmail($solicitantecorreo , "Error en correo." , "Su tarea no ha sido creada porque ha incluido a otra persona en destinatarios, solo tiene que ir esta cuenta en destinatarios. Si necesitas agregar a alguien asigna a la persona en copia.".$msjCorreoAutomaticoInfo, 0);
			        return false;
			    }else{
			        return true;
			    }
			}
		}else{
			return false;
		}
	}


	function comprobarUsuarioDominio($correo){
		
		//Comprobacion de que el usuario esta en el dominio.//
		
		$Usuario = $correo['from'][0]->mail;
		$Usuario = imap_utf8 ($Usuario); //CORREO PETICION	
		
		die($Usuario);
		$sqlbuscarnombre ="SELECT COUNT(*) as resultado FROM [GestionIDM].[dbo].[LDAP] Where Mail ='".$Usuario."'";
		$resultadoquery = DBSelect (utf8_decode( $sqlbuscarnombre ));
		$resultado=utf8_encode(DBCampo($resultadoquery, "resultado" ));
		
		if ($resultado == 1){
			return true;
		}
			return false;
	}


	function comprobarcionCorreo($correo, $body){
		
		//USUARIO PETICION//
		$from = $correo['from'][0]->mail;
		$from = imap_utf8 ($from); //CORREO PETICION
		$fromuser = $correo['from'][0]->mailbox;
		$fromuser = imap_utf8 ($fromuser); //NOMBRE CORREO PETICION

		//CORREO DESTINO (BOT - Se deja preparado para mas posibles cuentas de bots)//
		$emailto = $correo['to'][0]->mail;	//CORREO BOT		
		$toaddress = $correo['to'][0]->mail;
		$toaddress = imap_utf8 ( $toaddress ); //USUARIO BOT
		
		//ASUNTO DEL CORREO//
		$subject = $correo['subject'];
		$subject = imap_utf8 ($subject); //¿Haria falta? PRUEBA//
		// ++ Alberto 17/12/2020
		$basura = array("RV: ", "Rv: ", "RE: ","Re: ", "FW: ", "Fw: ");
		$subject = str_replace($basura, "", $subject);
		// -- Alberto 17/12/2020
		
		//MENSAJE CORREO//
        $message = $body->getBodies();
        $message = $message['html'];
		$message = trim ( utf8_encode ( $message ) );
        //$message = substr($message, 1750, 140);
		$message = str_replace ( "'" , "" , $message );
      
		/*
		echo '<p><b>Usuario peticion:</b> '.$fromuser.'</p>';
		echo '<p><b>Correo peticion:</b> '.$from.'</p>';
		echo '<p><b>Correo bot final:</b> '.$toaddress.'</p>';
		echo '<p><b>Para:</b> '.$emailto.'</p>';
		echo '<p><b>Asunto:</b> '.$subject.'</p>';
		*/
		//COMPROBACION ASUNTO DEL MENSAJE//
		$error = 0;
		$enviar = true;
		$msjCorreoAutomaticoInfo ='<br /><span style:"color:red"><b>Este correo se envía de forma automática, por favor no responda a este mensaje.</b></span>';
		
		//asunto vacio//
		if (str_replace(" ", "", $subject)=='' ){
			
			$error = 1;
			echo "Error el correo no contiene asunto";
			echo "<br/>";
			
			enviarmail ($from , "Error en correo." , "Su tarea no ha sido creada porque no contenía un asunto, escribe un asunto para poder crear la tarea.".$msjCorreoAutomaticoInfo, 0);
			$enviar = false;
		}
		
		//Asunto demasiado largo//
		if(strlen($subject) > 140){
			
			$error = 2;
			echo "El asunto tiene demasiados caracteres en el asunto.(<140) <br />";
			echo "<br/>";
			
			enviarmail ($from , "Error en correo." , "Su tarea no ha sido creada porque su asunto tiene más de 140 caracteres. ["+$subject+"]".$msjCorreoAutomaticoInfo , 0);
			$enviar = false;
		}
		
		
		//Mensaje vacio//
		if (str_replace(" ", "", $message)==''){
			
			$error = 3;
			echo "Error el correo no contiene mensaje.";
			echo "<br/>";

			enviarmail ($from , "Error en correo." , "Su tarea no ha sido creada porque no contenía un mensaje con la descripción de la tarea, escribe un asunto para poder crear la tarea." .$message .$msjCorreoAutomaticoInfo, 0);

			$enviar = false;
		}
		
		if($enviar == false){
			borrarCorreo($body);
		} 
		
		return $enviar;
	}

	function borrarCorreo($message){
        $message = $message->delete($expunge = true);
	}


	function montarSQLCorreo($email, $datetime, $messageid){
        $message = $email->getBodies();
        $message = $message['html'];
		$g = "$message";
		$cadena_buscada = 'high';
		$posicion_coincidencia = strrpos($g, $cadena_buscada);
		$prioridad = substr($g, $posicion_coincidencia , 4);
		
        $correo = $email->getAttributes();
		
		//USUARIO PETICION//
        $from = $correo['from'][0]->mail;
		$from = imap_utf8 ($from); //CORREO PETICION
		$fromuser = $correo['from'][0]->mailbox;
		$fromuser = imap_utf8 ($fromuser); //NOMBRE CORREO PETICION
   

		//CORREO DESTINO (BOT - Se deja preparado para mas posibles cuentas de bots)//
        $emailto = $correo['to'][0]->mail;	//CORREO BOT		
		$toaddress = $correo['to'][0]->mail;
		$toaddress = imap_utf8 ( $toaddress ); //USUARIO BOT
		
		//ASUNTO DEL CORREO//
        $attributes = $email->getAttributes(); 
		$subject = $attributes['subject'];
		// ++ Alberto 17/12/2020
		$basura = array("RV: ", "Rv: ", "RE: ","Re: ", "FW: ", "Fw: ");
		$subject = str_replace($basura, "", $subject);
		// -- Alberto 17/12/2020
		
		$mensajeid = substr($messageid, 1, strpos($messageid, '@')-1); 	//15/01/2019
		
		if($emailto == "soporte.ti@sp-berner.com"){ $dep ='TI';}
		$consultardep = "SELECT TOP 1 [Descripcion] FROM [GestionPDT].[dbo].[Tipos] WHERE Tipo = 4 AND Oficina = '".$dep."'";
		
		if($prioridad == "high"){
			$consultardep = $consultardep."AND PrioridadUrgente = 1"; 
		}else{
			$consultardep = $consultardep."AND Predeterminado = 1";
		}
		
		$consultardep = $consultardep." ORDER BY Numero";
		$consultardep = DBSelect (utf8_decode( $consultardep ));
		$resprio = DBCampo($consultardep, ("Descripcion"));
		
		$mensajeid = substr($messageid, 1, strpos($messageid, '@')-1);
		
		$solicitadoquery="SELECT [Name] as nombre FROM [GestionIDM].[dbo].[LDAP] Where Mail ='".$from."'";
		$consultasolicitado= DBSelect (utf8_decode( $solicitadoquery ));
		$nombresolicitante=DBCampo($consultasolicitado, ("nombre"));
		
		$subjectinsertar = mb_decode_mimeheader($subject);
		$subjectinsertar = str_replace ( "_" , " " , $subjectinsertar );
		$subjectinsertar = str_replace ( "'" , " " , $subjectinsertar );		
		
        $codificacion = isset($email->getAttributes()['content_transfer_encoding'][0]) ? $email->getAttributes()['content_transfer_encoding'][0] : null;
       
		//if ($codificacion -> encoding==0){ $message = imap_base64($message);}
		//if ($codificacion -> encoding==1){ $message = quoted_printable_decode(imap_8bit($message));}
		//if ($codificacion -> encoding==2){ $message = imap_binary($message);}
		if ($codificacion == 3 ){ $message = imap_base64($message);}
		//if ($codificacion -> encoding== 4){ $message = quoted_printable_decode($message);};
		
		// FILTRO DEL MENSAJE (29/03/2019)//
		$message = trim ( utf8_encode ( quoted_printable_decode ( $message ) ) );
		$message = str_replace ( "'" , "" , $message );
		$message = preg_replace('<hr([\w\W]+?)/>','', $message, -1);
		$message = str_replace ( "This email was scanned by Bitdefender" , " " , $message );	
		$message = preg_replace('<img([\w\W]+?)/>','', $message, -1);	
						

		$message = limpiaTextoConHTML($message);
		
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
									'".$nombresolicitante."'
									,'".$from."' 
									,'".utf8_decode($subjectinsertar)."' 
									,'".$datetime."' 
									,'".utf8_decode($message)."'
									,'Pendiente'
									,''
									,'".$resprio."'
									,'".$dep."'
									,''
									,''
									,''
									,0
									,''
									,''
									,''
									,'".$mensajeid."' 
									,''
									,''
									,(SELECT TOP 1 [Descripcion] FROM [GestionPDT].[dbo].[Tipos] WHERE Tipo = 14 AND Predeterminado = 1 AND Oficina = '$dep')
									,(SELECT TOP 1 [Descripcion] FROM [GestionPDT].[dbo].[Tipos] WHERE Tipo = 15 AND Predeterminado = 1 AND Oficina = '$dep')
									,0
									)";
						
		
		DBSelect ( $sqlinsertar );

        $header = $email->getHeader();
		$header_array = [];
        foreach($header as $key => $value) {
            array_push($header_array, $value);
        }
        $header_join = join(" ",$header_array);
        
		$body = $email->getBodies();
        $body= $body['html'];
		//TODO comprobar si se ha creado el fichero eml
		file_put_contents('../CorreosBOT/'.$mensajeid.'.eml', $header_join . "\n" . $body);

		enviarmail ( $from , "Se ha creado la solicitud para su valoración." , "Su solicitud de tarea con asunto : ". imap_utf8 ( $subject ) ."<br/><br/> Ha sido creada en el sistema para su valoración <br/><br/> <br/> <br/> <br/> <u><b> Este correo se envía de forma automática, por favor no responda a este mensaje. <br/> La creación de la solicitud es automática, pero la creación de tareas está bajo valoración.</b></u>", $mensajeid );
				
		
		if ($prioridad == "high"){
			echo "<br/>Importancia: ".$prioridad;
		}else{
			$prioridad = "normal";
			echo "<br/>Importancia: ".$prioridad;
		}
			
		
	}


	function moverCorreoCarpetaAceptado($email){
		
		//movemos el correo a la carpeta aceptados//
        $folder_path = 'CorreosAceptados';
        $email = $email->move($folder_path);

	}
	
	


	function comprobacionDominiosPermitidos($correo){
		$dominio = $correo['from'][0]->host;	
			
		//if(($dominio == 'sp-berner.com')||($dominio == 'miklimpieza.com')||($dominio == 'sp-bramli.com')||($dominio == 'spbernerplastic.onmicrosoft.com')){
		if(($dominio == 'sp-berner.com')||($dominio == 'miklimpieza.com')||($dominio == 'sp-bramli.com')||($dominio == 'spbernerplastic.onmicrosoft.com')||($dominio == 'bernerlogistic.com')){
			return true;
		}else{
			return false;
		}
		
	}

    function enviarmail($to, $asunto, $mensaje, $correoid){
		insertarCorreoMensajeria($asunto,$mensaje,'soporte.ti@sp-berner.com',$to,'','');
		echo "Correo de entrada: ".$to;
		echo "<br />";
		echo "Asunto: ".$mensaje;
		echo "<br />";
	}

   

