<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$tarea    = $_GET['tarea'];
$usuario  = $_GET['usuario'];
$portal  =$_GET['portal']; 
$contador = 0;

switch($portal){
	case "CONTABILIDAD":
		$nombre_portal = "Gestión tareas área contable";
		break;
	case "gestioncalidad":
		$nombre_portal = "Gestión tareas calidad";
		break;
	case "gestionlean":
		$nombre_portal = "Gestión tareas lean";
		break;
	case "gestionproyectos":
		$nombre_portal = "Gestión tareas proyectos";
		break;
	case "gestionproyectosestructuras":
		$nombre_portal = "Gestión tareas proyectos-estructuras-packaging";
		break;
	case "GESTIONTI":
		$nombre_portal = "Gestión de proyectos, mejoras e incidencias TI";
		break;
	default:
		$nombre_portal = "Gestión de tareas";
}

DBConectar($GLOBALS["cfg_DataBase"]);

$To_Correcto  =false;	
$From_Correcto=false;	
$TO           = '';
$FROM         = '';
$COPIAS       = '';
$ENVIO        = -1;
$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_MANDAR_CORREO] ".$tarea.", '".$usuario."', 0,1"));
for(;DBNext($insert);)
{	
	$contador  = $contador + 1;
	$ACTOR     = (DBCampo($insert,"actor"));
	$DIRECCION = utf8_encode(DBCampo($insert,"direccion"));
	$TITULO    = utf8_encode(DBCampo($insert,"TITULO"));
	$ENVIO     = (DBCampo($insert,"Envio"));
	$NOMBRE    = utf8_encode(DBCampo($insert,"Nombre"));
	if ($ACTOR==1) 
	{
		if($DIRECCION !="")
		{
			$TO         = $DIRECCION;
			$To_Correcto= true;	
		}
	}
	if ($ACTOR==2) 
	{
		if($DIRECCION !="")
		{
			$FROM         = $DIRECCION;
			$From_Correcto= true;	
		}
	}
	if ($ACTOR!=1 && $ACTOR!=2) 
	{
		if($DIRECCION !="")
		{
			$COPIAS = $COPIAS.','.$DIRECCION;				
		}
	}
}

if ($To_Correcto==true && $From_Correcto==true)
{	
	$COPIAS=trim($COPIAS,",");
	ini_set('SMTP', "relay.sp-berner.com");
	ini_set('smtp_port', "25");
	ini_set('sendmail_from', $FROM);

	$subject = "Valoración tareas del portal $nombre_portal";
	$miruta  = rutaHTML()."/GestionTareas/menuvaloraciones.php?envio=$ENVIO&portal=".curPageName();
	$link    = '<div><a href="'.$miruta.'" style="color:#0B0B61;background:#FFFFFF;font-size:12px" align="center"><b>PULSE AQUÍ PARA ACCEDER</b></a></div>';

	 $message = "
	 <html>
	 <head>
	 <title>Valoración tareas</title>
	 </head>
	 <body>
	 
	 <p>Necesitamos que valore el trabajo realizado en la tarea : <b>".($TITULO)."</b> </p> ".$link."
	 <p>Gracias.</p>		
	 		
	 </body>
	 </html>
	 ";

	
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	$headers .= 'From: '.$FROM. "\r\n";
	if($COPIAS!='')
	{
		$headers .= 'BCC: '.$FROM.','.$COPIAS . "\r\n";
	}
	else
	{
		$headers .= 'BCC: '.$FROM. "\r\n";
	}

	$res = mail($TO,$subject,$message,$headers);
}	
	


if ($ENVIO!=-1)
{
	$q=DBSelect(utf8_decode("

	EXECUTE [INICIAR_VALORACION] '".utf8_encode($usuario)."',".$ENVIO.",-1,-1,'' "));
}

//****************************************************FINAL
DBClose();
//****************************************************FINAL

if ($To_Correcto==false || $From_Correcto==false)
{
	echo 'Error al enviar el mail. El destinatario no tiene correo o no es correcto. Consulte el envio '.$ENVIO;
}
else
{
	echo $res;
}


?>