<?php
include "DBAdv.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$contador=0;

DBConectar($GLOBALS["cfg_DataBase"]);

$insert=DBSelect(utf8_decode("SELECT Mail.Numero,Mail.Tipo,Mail.Tarea,Mail.Fecha,Mail.Usuario,Mail.actor,Mail.direccion,	Mail.Envio,Nombre,CAST(Tareas.Título as varchar(250)) as TITULO,
						(SELECT Mail_2.direccion FROM [Mail] Mail_2	WHERE Mail_2.Envio = Mail.Envio AND Mail_2.Actor = 2) as direccion_copia,(SELECT Mail_2.Nombre FROM 
						[Mail] Mail_2 WHERE Mail_2.Envio = Mail.Envio AND Mail_2.Actor = 2) as Nombre_copia	FROM [Mail] Mail INNER JOIN	[Tareas y Proyectos] Tareas ON Tareas.Id = Mail.Tarea
						WHERE Mail.actor = 1 AND DATEDIFF(day,Mail.Fecha,GETDATE()) <= 30 AND DATEDIFF(day,Mail.Fecha,GETDATE()) > 7 AND Mail.Tarea IN (select DISTINCT(Valoraciones.Tarea) FROM
						[Valoraciones] Valoraciones	WHERE Valoraciones.Valoracion IS NULL )"));
for(;DBNext($insert);)
{
	set_time_limit(50);
	$To_Correcto  = false;	
	$From_Correcto= false;	
	$TO           = '';
	$FROM         = '';
	$COPIAS       = '';
	$ENVIO        =-1;
	$contador     = $contador + 1;
	$DIRECCION    = utf8_encode(DBCampo($insert,"direccion"));
	$DIRECCION2   = utf8_encode(DBCampo($insert,"direccion_copia"));		
	$TITULO       = utf8_encode(DBCampo($insert,"TITULO"));
	$ENVIO        =(DBCampo($insert,"Envio"));
	$NOMBRE       =utf8_encode(DBCampo($insert,"Nombre"));
	$NOMBRE2      =utf8_encode(DBCampo($insert,"Nombre_copia"));
	if($DIRECCION !="")
	{
		$TO         = $DIRECCION;
		$To_Correcto=true;	
	}
	if($DIRECCION2 !="")
	{
		$FROM         = $DIRECCION2;
		$From_Correcto=true;	
	}	
	if ($To_Correcto==true && $From_Correcto==true)
	{		 
		ini_set('SMTP', "relay.sp-berner.com");
		ini_set('smtp_port', "25");
		ini_set('sendmail_from', $FROM);
		
		$subject = 'Recordatorio Valoración tareas TI';
		$miruta="https://tareas.sp-berner.com/menuvaloraciones.php?envio=".$ENVIO;
		$link='<div><a href="'.$miruta.'" style="color:#0B0B61;background:#FFFFFF;font-size:12px" align="center"><b>PULSE AQUÍ PARA ACCEDER</b></a></div>';
	
	 
		 
		 $message = "
		 <html>
		 <head>
		 <title>Recordatorio Valoración tareas TI</title>
		 </head>
		 <body>
		 <p>Le recordamos que necesitamos valores el trabajo realizado en la tarea : <b>".($TITULO)."</b> </p> ".$link."
		 </body>
		 </html>
		 ";

		 
		 $headers = "MIME-Version: 1.0" . "\r\n";
		 $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		 
		 $headers .= 'From: '.$FROM. "\r\n";
		 $headers .= 'BCC: '.$FROM. "\r\n";
		 
			
		 $res = mail($TO,$subject,$message,$headers);
		 echo $res.' '.$TO.' '.$subject.' '.$message.' '.$headers;
	}
}
DBClose();
?>