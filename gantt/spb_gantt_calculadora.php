<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

if(isset($_POST['tarea']))
{	
	$datos_tarea = spb_gantt_datos_tarea($_POST['tarea']);
	
	if(isset($_POST['asignado']))
	{
		$datos_tarea['asignado']   = $_POST['asignado'];
	}
	if(isset($_POST['horas']))
	{
		$datos_tarea['horas']      = $_POST['horas'];
	}
	if(isset($_POST['inicio']))
	{
		$datos_tarea['inicio']     = $_POST['inicio'];
	}
	if(isset($_POST['fin']))
	{
		$datos_tarea['fin']        = $_POST['fin'];	
	}
	if(isset($_POST['guardar']))
	{
		spb_modificar_tarea($_POST['tarea'],$datos_tarea);
	}
	else
	{
		if(isset($_POST['funcion']))
		{
			echo spb_calcular($_POST['funcion'],$datos_tarea);
		}
		else
		{
			spb_usuarios_render($datos_tarea);
			spb_inicio_render($datos_tarea);
			spb_fin_render($datos_tarea);
			spb_horas_render($datos_tarea);
			echo '<div class="boton_lista_usuario" onClick="spb_acepta_calculadora();">Aceptar</div>';
		}
	}
}


function spb_horas_render($datos_tarea)
{
	echo '<div>';
	echo '<div>Horas</div>';
	echo '<input id="horas_calculadora" onChange="spb_cambio_horas();">';
	echo '</input><img onClick="spb_cambia_imagen_calculadora(this);" id="calculadora_imagen_horas" src="./gantt/image/nothing.png" value="'.$datos_tarea['horas'].'"/>';
	echo '</div>';
}
function spb_fin_render($datos_tarea)
{
	echo '<div>';
	echo '<div>Fin</div>';
	echo '<input id="fin_calculadora" onChange="spb_cambio_fin();">';
	echo '</input><img onClick="spb_cambia_imagen_calculadora(this);" id="calculadora_imagen_fin" src="./gantt/image/private.png"  value="'.$datos_tarea['fin'].'"/>';
	echo '</div>';
}
function spb_inicio_render($datos_tarea)
{
	echo '<div>';
	echo '<div>Inicio</div>';
	echo '<input id="inicio_calculadora" onChange="spb_cambio_inicio();">';
	echo '</input><img onClick="spb_cambia_imagen_calculadora(this);" id="calculadora_imagen_inicio" src="./gantt/image/nothing.png" value="'.$datos_tarea['inicio'].'"/>';
	echo '</div>';
}
function spb_usuarios_render($datos_tarea)
{
	$lista_usuarios  = spb_gantt_usuarios_select();	
	echo '<div>';
	echo '<div>Asignado</div>';
	echo '<select id="usuario_calculadora" onChange="spb_cambio_usuario();">';
	for($i=0; $i<count($lista_usuarios); $i++)
    {
		$selected = '';
		if($datos_tarea['asignado']==$lista_usuarios[$i]['Nombre'])
		{
			$selected = ' selected ';
		}
		echo '<option '.$selected.' value="'.$lista_usuarios[$i]['Numero'].'">'.$lista_usuarios[$i]['Nombre'].'</option>';
	}		
	echo '</select>';
	echo '</div>';
}

function spb_gantt_usuarios_select()
{	
	DBConectar($GLOBALS["cfg_DataBase"]);	
	$contador         = 0;	
	$usuarios         = array();
	$rows=DBSelect(utf8_decode(spb_gantt_get_query_usuarios()));
	for(;DBNext($rows);)
	{		
		$id_usuarios           = utf8_encode(DBCampo($rows,utf8_decode("valor")));
		$nombre_usuarios       = utf8_encode(DBCampo($rows,utf8_decode("nombre")));					
		$usuarios[$contador]   = array('Numero' => $id_usuarios,'Nombre' => $nombre_usuarios);
		$contador++;	
	}
	DBClose();
	return $usuarios;
}
function spb_gantt_get_query_usuarios()
{
	return
	"SELECT sAMAccountName as valor,Name as nombre
	FROM OpenQuery(ADSI,'SELECT sAMAccountName,Name,department FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user'' AND objectClass=''user''  and department=''Tecnologías de la Información''') Consulta ORDER BY Name";
}
function spb_gantt_datos_tarea($tarea)
{	
	DBConectar($GLOBALS["cfg_DataBase"]);	
	$contador         = 0;	
	$detalle          = array();
	$rows=DBSelect(utf8_decode("SELECT [Asignado a] as asignado,isnull([Horas estimadas],0) as horas,CONVERT(varchar(50),[Fecha objetivo inicio],105) as inicio ,CONVERT(varchar(50),[Fecha objetivo],105) as fin FROM [Tareas y Proyectos] where id = ".$tarea));
	for(;DBNext($rows);)
	{		
		$asignado              = utf8_encode(DBCampo($rows,utf8_decode("asignado")));
		$horas                 = utf8_encode(DBCampo($rows,utf8_decode("horas")));
		$inicio                = utf8_encode(DBCampo($rows,utf8_decode("inicio")));
		$fin                   = utf8_encode(DBCampo($rows,utf8_decode("fin")));
		
		$detalle['asignado']   = $asignado;
		$detalle['horas']      = $horas;
		$detalle['inicio']     = $inicio;
		$detalle['fin']        = $fin;					
		$contador++;	
	}
	DBClose();	
	return $detalle;
}
function spb_calcular($funcion,$datos_tarea)
{
	$resultado = '';
	$select    = '';
	if($funcion=='[CALCULADORA_OBJETIVOS_INICIO] ')
	{
		$select    =   "set nocount on; DECLARE @FECHA datetime
						EXECUTE [CALCULADORA_OBJETIVOS_INICIO] 
						".$datos_tarea['horas']."
						,'".$datos_tarea['asignado']."'
						,'".$datos_tarea['fin']."'
						,2
						,@FECHA output
						SELECT CONVERT(varchar(50),@FECHA,105)+' '+CONVERT(varchar(50),@FECHA,108) as resultado ";
	}
	if($funcion=='[CALCULADORA_OBJETIVOS_FIN] ')
	{
		$select    =   "set nocount on; DECLARE @FECHA datetime
						EXECUTE [CALCULADORA_OBJETIVOS_FIN] 
						".$datos_tarea['horas']."
						,'".$datos_tarea['asignado']."'
						,'".$datos_tarea['inicio']."'
						,2
						,@FECHA output
						SELECT CONVERT(varchar(50),@FECHA,105)+' '+CONVERT(varchar(50),@FECHA,108) as resultado ";
	}
	if($funcion=='[CALCULADORA_OBJETIVOS_HORAS] ')
	{
		$select    =   "set nocount on; EXECUTE [CALCULADORA_OBJETIVOS_HORAS] 
						'".$datos_tarea['asignado']."'
						,'".$datos_tarea['inicio']."'
						,'".$datos_tarea['fin']."' ";
	}
	DBConectar($GLOBALS["cfg_DataBase"]);	
	$contador         = 0;		
	$rows=DBSelect(utf8_decode($select));
	for(;DBNext($rows);)
	{		
		$resultado              = utf8_encode(DBCampo($rows,utf8_decode("resultado")));		
		$contador++;	
	}
	DBClose();
	return $resultado;
}
function spb_modificar_tarea($tarea,$datos_tarea)
{
	DBConectar($GLOBALS["cfg_DataBase"]);	
	$contador         = 0;		
	$rows=DBSelect(utf8_decode("UPDATE [Tareas y Proyectos]
		SET [Fecha objetivo] = '".$datos_tarea['fin']."'
			,[Asignado a] = '".$datos_tarea['asignado']."'
			,[Horas estimadas] = ".$datos_tarea['horas']."
			,[Fecha objetivo inicio] = '".$datos_tarea['inicio']."'
			WHERE Id = ".$tarea.""));	
	DBClose();
}