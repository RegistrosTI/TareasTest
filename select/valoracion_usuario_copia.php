<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


$usuario   =$_GET['usuario'];
$tipo      =$_GET['tipo'];
$tarea     =$_GET['tarea'];
$id        =$_GET['id'];
$respuesta = '';

DBConectar($GLOBALS["cfg_DataBase"]);

if ($id == '1')
{	
	$g=DBSelect(utf8_decode("SELECT [Tipo],[SubTipo],[Count],[Grupo_Obligado],[Texto] FROM [Obligatoriedad]	where Texto = '".$tipo."'"));
	for(;DBNext($g);)
	{
		$TIPO            =(DBCampo($g,"Tipo"));		
		$TIPO_DESCRIPCION=(DBCampo($g,"SubTipo"));
		$COUNT           =(DBCampo($g,"Count"));
		$Grupo_Obligado  =(DBCampo($g,"Grupo_Obligado"));
		$Texto           =(DBCampo($g,"Texto"));
		if(utf8_encode($TIPO)=='Valoracion')
		{
			$m=DBSelect(utf8_decode(" SELECT COUNT(*) as RESULTADO FROM [Valoraciones] WHERE tarea = 9700 AND  Concepto IN  (SELECT [Numero] FROM [Concepto_Tipo_Valoraciones]  where grupo_obligado = ".$Grupo_Obligado." and activa = 1)"));
			$RESULTADO=(DBCampo($m,"RESULTADO"));	
			if($RESULTADO<$COUNT)
			{
				$respuesta = 'Valorar las tareas del tipo '.$tipo;
			}
			else
			{
				$respuesta = '';
			}			
			DBFree($m);	
		}
	}
}
DBClose();

echo $respuesta;
?>