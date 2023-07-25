<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$tarea    =$_GET['tarea'];
$tipo     =$_GET['tipo'];
$usuario  =$_GET['usuario'];
$contador =0;
$res      ='';
$COPIAS   ='';
$ASUNTO   ='';

DBConectar($GLOBALS["cfg_DataBase"]);
	
$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_MANDAR_CORREO] ".$tarea.", '".$usuario."', 0,0"));
for(;DBNext($insert);)
{	
	$contador  = $contador+1;
	$ACTOR     = (DBCampo($insert,"ACTOR"));
	$DIRECCION = utf8_encode(DBCampo($insert,"MAIL"));					
	$USUARIO   =utf8_encode(DBCampo($insert,"USUARIO"));
	if ($ACTOR==1) 
	{
		if($DIRECCION !="")
		{
			$TO         = $DIRECCION;
			$To_Correcto=true;	
		}
	}
	if ($ACTOR!=1) 
	{
		if($DIRECCION !="")
		{
			if ($USUARIO!=$usuario)
			{
				if ($contador==1)
				{
					$COPIAS = $DIRECCION;	
				}
				else
				{
					$COPIAS = $COPIAS.';'.$DIRECCION;	
				}
			}
		}
	}	
}
$c=DBSelect(utf8_decode("SELECT CAST([Título] as varchar(MAX)) as TITULO FROM [Tareas y Proyectos] where Id = ".$tarea." "));
for(;DBNext($c);)
{
	if($tipo=='correo_informar')
	{
		$ASUNTO = 'Tarea '.$tarea.' - '.utf8_encode(DBCampo($c,"TITULO"));	
	}
}
$res='mailto:'.$TO.'?'.'subject='.$ASUNTO.'&cc='.$COPIAS;

DBClose();

echo $res;
?>