<?php
if(file_exists("./conf/config.php"))
{		
	include_once "./php/funciones.php";
	include_once "./conf/config.php";
	include_once "./conf/config_".curPageName().".php";
	include_once "./../soporte/DB.php";
	include_once "./../soporte/funcionesgenerales.php";	
}
else
{
	include_once "../php/funciones.php";
	include_once "../conf/config.php";
	include_once "../conf/config_".curPageName().".php";
	include_once "../../soporte/DB.php";
	include_once "../../soporte/funcionesgenerales.php";	
}

$tarea      = $_GET['tarea'];
$usuario    = $_GET['usuario'];
$contador   = 0;
$DEVOLVER   = '-1';
$ENVIO      =    '-1';
$HTML_EXTRA = '';

DBConectar($GLOBALS["cfg_DataBase"]);

$insert=DBSelect(("SELECT COUNT(*)as Resultado  FROM [Valoraciones] Valoracion  INNER JOIN [Concepto_Tipo_Valoraciones] Concepto ON Concepto.Numero = Valoracion.Concepto where Tarea = ".$tarea." AND Valoracion is not null AND Concepto.Grupo = 0"));
$VALORADO = (DBCampo($insert,"Resultado"));

if ($VALORADO==0)
{
	//NO ESTA VALORADO
	DBFree($insert);
	$insert=DBSelect(("SELECT COUNT(*)  as Resultado  FROM [Mail] where Tipo = 0 AND Tarea =".$tarea." AND actor = 1	"));
	$VALORADO = (DBCampo($insert,"Resultado"));
	if ($VALORADO==0)
	{
		//NO ESTA ENVIADO
		$DEVOLVER = '0';
		DBFree($insert);
		$insert=DBSelect(("EXECUTE [INICIAR_MANDAR_CORREO] ".$tarea.",'".$usuario."',0,0"));
		for(;DBNext($insert);)
		{	
			$contador = $contador + 1;
			$ACTOR    = (DBCampo($insert,"ACTOR"));
			$MAIL     = utf8_encode(DBCampo($insert,"MAIL"));
			$NOMBRE   = utf8_encode(DBCampo($insert,"NOMBRE"));			
			$HTML_EXTRA=$HTML_EXTRA.'<div title="Mandar correo a la dirección '.$MAIL.'" style="correo-actores"><img style="width:20px;" src="./imagenes/ACTOR_'.$ACTOR.'.png">'.$NOMBRE.'</div>';
		}
	}
	else
	{
		//ESTA ENVIADO
		DBFree($insert);
		$insert=DBSelect(("SELECT CONVERT(varchar(250),[Fecha],109) as FECHA ,[Envio],[Usuario],[actor],[direccion],[Nombre] FROM [Mail] where Tipo = 0 AND Tarea =".$tarea." AND Envio = (SELECT MAX([Envio]) FROM [Mail] where Tipo = 0 AND Tarea =".$tarea.")		"));
		$DEVOLVER = '1';			
		for(;DBNext($insert);)
		{	
			$contador           = $contador + 1;
			$ENVIO              = (DBCampo($insert,"Envio"));
			$ACTOR              = (DBCampo($insert,"actor"));
			$MAIL               = utf8_encode(DBCampo($insert,"direccion"));
			$NOMBRE             = utf8_encode(DBCampo($insert,"Nombre"));			
			$FECHA              = utf8_encode(DBCampo($insert,"FECHA"));
			$USUARIO_SOLICITADO = utf8_encode(DBCampo($insert,"Usuario"));
			$HTML_EXTRA=$HTML_EXTRA.'<div title="Correo mandado a la dirección '.$MAIL.'" style="correo-actores"><img style="width:20px;" src="./imagenes/ACTOR_'.$ACTOR.'.png">'.$NOMBRE.'</div>';
		}
		$HTML_EXTRA=$HTML_EXTRA.'<div style="clear: both;" class="leyenda_valoracion">Valoración solicitada el '.$FECHA.' por el usuario '.$USUARIO_SOLICITADO .'</div>';
	}
}
else
{
	//ESTA VALORADO YA
	DBFree($insert);
	$insert=DBSelect(("SELECT TOP 1 [Envio] as Resultado FROM [Valoraciones] Valoracion INNER JOIN [Concepto_Tipo_Valoraciones] Concepto ON Concepto.Numero = Valoracion.Concepto
				where Tarea = ".$tarea." AND Valoracion is not null AND Concepto.Grupo = 0"));
	$DEVOLVER = '2';
	$ENVIO    = (DBCampo($insert,"Resultado"));		
}

echo $DEVOLVER.','.$ENVIO.','.$HTML_EXTRA;
DBClose();
?>