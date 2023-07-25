<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

if(isset($_POST['lista_usuarios']))
{
	spb_usuarios_render();
	die;
}
if(isset($_POST['ajax']))
{
	spb_gantt_render_ajax($_POST['factor'],$_POST['inicio'],$_POST['fin']);
	die;
}
if(isset($_POST['detalle_dia']))
{
	spb_detalle_dia_render();
	die;
}
spb_gantt_render();


function spb_gantt_render()
{

	$gantt                      = array();
	include('spb_gantt_config.php');
		
	$calendario                 = spb_gantt_crea_calendario($gantt);
	$usuarios                   = spb_gantt_crea_usuarios($gantt);
		
	$gantt['calendario']        = $calendario;
	$gantt['usuarios']          = $usuarios;
	
	echo spb_gantt_pinta_gantt($gantt);

	
	//echo '<pre>'.print_r($gantt,1).'</pre>';
}
function spb_gantt_render_ajax($factor_pasado,$inicio_pasado,$fin_pasado)
{	
	$lista_usuarios             = json_decode(stripslashes($_POST['usuarios']));
	$gantt                      = array();
	include('spb_gantt_config.php');
	$gantt['factor']['valor']   = $factor_pasado;
	$gantt['fechas']['Fin']     = spb_gantt_strtotime($fin_pasado);
	$gantt['fechas']['Inicio']  = spb_gantt_strtotime($inicio_pasado);
	$gantt['lista_usuarios']    = $lista_usuarios;
	$calendario                 = spb_gantt_crea_calendario($gantt);
	$usuarios                   = spb_gantt_crea_usuarios($gantt);
		
	$gantt['calendario']        = $calendario;
	$gantt['usuarios']          = $usuarios;
    //echo '<pre>'.print_r($gantt,1).'</pre>';	
	echo spb_gantt_pinta_gantt($gantt);
}
function spb_gantt_crea_usuarios($gantt)
{
	$usuarios = array();
	spb_gantt_usuarios($usuarios,$gantt);
	return $usuarios;
}
function spb_gantt_crea_calendario($gantt)
{
	$calendario = array();
	spb_gantt_anyo($calendario,$gantt['fechas']['Inicio'],$gantt['fechas']['Fin']);	
	spb_gantt_mes($calendario);
	spb_gantt_dias($calendario,$gantt);
	spb_gantt_carga_unidades($calendario);
	return $calendario;
}
function spb_gantt_pinta_gantt($gantt)
{
	$spb_gantt = '';
	$usuarios        = $gantt['usuarios'];
	$altura          = 0;
	for ($i = 0; $i < count($usuarios['usuario']); $i++)
	{	
		$altura = $altura+$usuarios['usuario'][$i]['Lineas'];
	}
	$spb_gantt = $spb_gantt.'<div id="spb_gantt_contenedor">';
	$spb_gantt = $spb_gantt.'<div class="spb_gantt" hora_inicio="'.$gantt['horas']['Inicio'].'" hora_fin="'.$gantt['horas']['Fin'].'" inicio="'.date('d/m/Y',$gantt['fechas']['Inicio']).'" fin="'.date('d/m/Y',$gantt['fechas']['Fin']).'" ruta="./gantt" id="spb_gantt" style="height:'.(($gantt['altura lineas']*($altura+1))+$gantt['inicio gantt']).'px;">';
	$spb_gantt = $spb_gantt.spb_gantt_pinta_usuarios($gantt);	
	$spb_gantt = $spb_gantt.spb_gantt_pinta_calendario($gantt);	
	$spb_gantt = $spb_gantt.'</div>';	
	$spb_gantt = $spb_gantt.'</div>';
	return $spb_gantt;
}
function spb_gantt_pinta_usuarios($gantt)
{
	$spb_gantt_div = '';
	$spb_gantt_div = $spb_gantt_div.'<div class="Usuarios" id="Usuarios">';
	$spb_gantt_div = $spb_gantt_div.'<div class="Usuarios_Hueco" id="Usuarios_Hueco" style="height: '.$gantt['inicio gantt'].'px;"></div>';
	$spb_gantt_div = $spb_gantt_div.'<div class="Usuarios_Nombres" id="Usuarios_Nombres">';
	
	$usuarios = $gantt['usuarios'];
	for ($i = 0; $i < count($usuarios['usuario']); $i++)
	{	
	
		$spb_gantt_div = $spb_gantt_div.'<div class="Usuario" id="'.str_replace(".", "_",$usuarios['usuario'][$i]['Numero']).'" >'.$usuarios['usuario'][$i]['Nombre'].'</div>';
		if($usuarios['usuario'][$i]['Lineas']>1)
		{
			for ($j = 1; $j < ($usuarios['usuario'][$i]['Lineas']); $j++)
			{
				$spb_gantt_div = $spb_gantt_div.'<div class="Servicio" id="Servicio_'.$usuarios['usuario'][$i]['Lineas'].'_'.$j.'" ></div>';
			}
		}
	}
	$spb_gantt_div = $spb_gantt_div.'<div><input style="width: 100%;" id="spb_gantt_slider" factor_anterior="'.$gantt['factor']['valor'].'" type="range" min="'.$gantt['factor']['min'].'" max="'.$gantt['factor']['max'].'" value="'.$gantt['factor']['valor'].'" onChange="spb_gantt_cambia_factor_slide()"/></div>';
	$spb_gantt_div = $spb_gantt_div.'</div>';	
	$spb_gantt_div = $spb_gantt_div.'</div>';
	return $spb_gantt_div;
}
function spb_gantt_pinta_tareas($gantt)
{
    $clase               = '';
	$altura              = 0;
	$usuarios            = $gantt['usuarios'];
	$spb_gantt_div = '';
	for ($i = 0; $i < count($usuarios['usuario']); $i++)
	{	
		if($gantt['grid']==true)
		{
			if ($i%2==0)
			{
				 $clase = 'grid par';
			}else
			{
				 $clase = 'grid impar';
			}	
			$spb_gantt_div = $spb_gantt_div.'<div class="'.$clase.'" style="height:'.(($usuarios['usuario'][$i]['Lineas'])*$gantt['altura lineas']).'px;top:'.((($altura)*$gantt['altura lineas'])+$gantt['inicio gantt']).'px;width:'.$gantt['ancho'].'px;"></div>';
		}

		for ($j = 0; $j < count($usuarios['usuario'][$i]['Ausencia']); $j++)
		{
			$apariciones = ((int)$usuarios['usuario'][$i]['Ausencia'][$j]['Fila'])+1;
			$extrai      = '';
			$extrad      = '';
			if($usuarios['usuario'][$i]['Ausencia'][$j]['Extra-d']==true)
			{
				$extrad = '<div class="Extra-d"></div>';	
			}
			if($usuarios['usuario'][$i]['Ausencia'][$j]['Extra-i']==true)
			{
				$extrai = '<div class="Extra-i"></div>';
			}
			
			$id_servicio = $usuarios['usuario'][$i]['Ausencia'][$j]['Numero'];			
			$clase       = $usuarios['usuario'][$i]['Ausencia'][$j]['clase'];
			$tipo        = $usuarios['usuario'][$i]['Ausencia'][$j]['Nombre'];
			
			$spb_gantt_div = $spb_gantt_div.'<div class="Ausencia '.$clase.'" title="'.$tipo.'" style="top:'.((($altura)*$gantt['altura lineas'])+$gantt['inicio gantt']).'px;left:'.$usuarios['usuario'][$i]['Ausencia'][$j]['Posicion']*$gantt['factor']['valor'].'px;width:'.$usuarios['usuario'][$i]['Ausencia'][$j]['Unidades']['Horas']*$gantt['factor']['valor'].'px;height:'.(($usuarios['usuario'][$i]['Lineas'])*$gantt['altura lineas']).'px;">'.$extrai.$extrad.'</div>';						

		}
		

		for ($j = 0; $j < count($usuarios['usuario'][$i]['Servicio']); $j++)
		{			
			$apariciones = ((int)$usuarios['usuario'][$i]['Servicio'][$j]['Fila']);

			$extrai      = '';
			$extrad      = '';
			if($usuarios['usuario'][$i]['Servicio'][$j]['Extra-d']==true)
			{
				$extrad = '<div class="Extra-d"></div>';	
			}
			if($usuarios['usuario'][$i]['Servicio'][$j]['Extra-i']==true)
			{
				$extrai = '<div class="Extra-i"></div>';
			}
			
			$id_servicio = $usuarios['usuario'][$i]['Servicio'][$j]['Numero'];						
			$clase       = $usuarios['usuario'][$i]['Servicio'][$j]['clase'];
			
			$hsp           = $usuarios['usuario'][$i]['Servicio'][$j]['Numero'];						
			$id_trat       = $usuarios['usuario'][$i]['Servicio'][$j]['Nombre'];						
			$span_hsp_min  = '<span class="detalle_servicio">'.$hsp.'-'.$id_trat.'</span>';
            
			
			$spb_gantt_div = $spb_gantt_div.'<div class="Tarea '.$clase.'" title="" onClick="kalvinnet_gantt_mostrar_servicio('.$id_servicio.','.'6'.');" style="top:'.((($altura+$apariciones)*$gantt['altura lineas'])+$gantt['inicio gantt']).'px;left:'.$usuarios['usuario'][$i]['Servicio'][$j]['Posicion']*$gantt['factor']['valor'].'px;width:'.$usuarios['usuario'][$i]['Servicio'][$j]['Unidades']['Horas']*$gantt['factor']['valor'].'px;">'.$extrai.$span_hsp_min.$extrad.'</div>';						
		}

		$altura=$altura+$usuarios['usuario'][$i]['Lineas'];
	}
	return $spb_gantt_div;
}
function spb_gantt_pinta_calendario($gantt)
{
	$usuarios = $gantt['usuarios'];
	$altura   = 0;
	for ($i = 0; $i < count($usuarios['usuario']); $i++)
	{
		$altura = $altura+($usuarios['usuario'][$i]['Lineas']);
	}
	$spb_gantt_div = '';
	$spb_gantt_div = $spb_gantt_div.'<div class="spb_gantt_calendario" id="spb_gantt_calendario">';
	$spb_gantt_div = $spb_gantt_div.spb_gantt_pinta_hora($gantt['calendario'],$altura,$gantt['factor']['valor'],$gantt['altura lineas'],$gantt);	
	$spb_gantt_div = $spb_gantt_div.spb_gantt_pinta_tareas($gantt);
	$spb_gantt_div = $spb_gantt_div.'</div>';
	return $spb_gantt_div;
}
function spb_gantt_pinta_hora($calendario,$altura,$factor,$factor_altura,&$gantt)
{
	$anchura_maxima = 0;
	$div_anyo = '';
	$div_anyo = $div_anyo.'<div class="Anyos" id="Anyos">';
	$div_mes = '';
	$div_mes = $div_mes.'<div class="Meses" id="Meses">';
	$div_semana = '';
	$div_semana = $div_semana.'<div class="Semanas" id="Semanas">';
	$div_dia = '';
	$div_dia = $div_dia.'<div class="Dias" id="Dias">';
	$div_hora = '';
	$div_hora = $div_hora.'<div class="Horas" id="Horas">';
	for ($i = 0; $i < count($calendario['anyo']); $i++)
	{
		$anchura_maxima = $anchura_maxima + ($calendario['anyo'][$i]['Unidades']['Horas']*$factor);
		$div_anyo = $div_anyo.'<div class="Anyo" title="'.$calendario['anyo'][$i]['Nombre'].'" id="Anyo_'.$calendario['anyo'][$i]['Numero'].'" style="left: '.$calendario['anyo'][$i]['Posicion']*$factor.'px;width: '.$calendario['anyo'][$i]['Unidades']['Horas']*$factor.'px;" >'.$calendario['anyo'][$i]['Nombre'].'</div>';
		for ($j = 0; $j < count($calendario['anyo'][$i]['Mes']); $j++)
		{
			$div_mes = $div_mes.'<div class="Mes" title="'.$calendario['anyo'][$i]['Mes'][$j]['Nombre'].'" id="Mes_'.$calendario['anyo'][$i]['Numero'].'_'.$calendario['anyo'][$i]['Mes'][$j]['Numero'].'" style="left: '.$calendario['anyo'][$i]['Mes'][$j]['Posicion']*$factor.'px;width: '.$calendario['anyo'][$i]['Mes'][$j]['Unidades']['Horas']*$factor.'px;" >'.$calendario['anyo'][$i]['Mes'][$j]['Nombre'].'</div>';
			for ($k = 0; $k < count($calendario['anyo'][$i]['Mes'][$j]['Semana']); $k++)
			{
				if ($k%2==0)
				{
					 $clase = 'semana_par';
				}else
				{
					 $clase = 'semana_impar';
				}
				$div_semana = $div_semana.'<div class="Semana '.$clase.'" title="'.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Numero'].'" id="Semana_'.$calendario['anyo'][$i]['Numero'].'_'.$calendario['anyo'][$i]['Mes'][$j]['Numero'].'_'.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Numero'].'" style="left: '.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Posicion']*$factor.'px;width: '.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Unidades']['Horas']*$factor.'px;" ></div>';

				for ($l = 0; $l < count($calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia']); $l++)
				{
					if($calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Dia de semana']==6 || $calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Dia de semana'] == 0)
					{						
						$clase         = 'Fiesta ';
						$clase_Hora    = 'Hora_Fiesta ';						
						$tamano_altura = 'height:'.(($altura*$factor_altura)+6).'px;';
					}
					else
					{
						$clase         = '';
						$clase_Hora    = '';						
						$tamano_altura = '';
					}
					
				    $Extra_Dia        = '<div onClick="spb_selecciono_fecha_dia(this,'.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Numero'].','.$calendario['anyo'][$i]['Mes'][$j]['Numero']. ','.$calendario['anyo'][$i]['Numero'].');" class="Id_Dia" id="Id_Dia_'.$calendario['anyo'][$i]['Numero'].'_'.$calendario['anyo'][$i]['Mes'][$j]['Numero'].'_'.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Numero'].'">'.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Numero'].'</div>';
					$Extra_Dia_Nombre = '<div onClick="spb_selecciono_detalle_dia(this,'.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Numero'].','.$calendario['anyo'][$i]['Mes'][$j]['Numero']. ','.$calendario['anyo'][$i]['Numero'].');" class="Nombre_Dia" id="Nombre_Dia_'.$calendario['anyo'][$i]['Numero'].'_'.$calendario['anyo'][$i]['Mes'][$j]['Numero'].'_'.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Numero'].'">'.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Nombre'][0].'</div>';
					$div_dia          = $div_dia.'<div class="'.$clase.'Dia" title="'.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Nombre'][0].' '.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Numero'].' de '.$calendario['anyo'][$i]['Mes'][$j]['Nombre']. ' de '.$calendario['anyo'][$i]['Nombre'].' (Semana '.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Numero'].')'.'" id="Dia_'.$calendario['anyo'][$i]['Numero'].'_'.$calendario['anyo'][$i]['Mes'][$j]['Numero'].'_'.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Numero'].'" style="left: '.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Posicion']*$factor.'px;width: '.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Unidades']['Horas']*$factor.'px;" >'.$Extra_Dia_Nombre.$Extra_Dia.'</div>';
										
					$div_hora   = $div_hora.'<div class="'.$clase_Hora.'Hora" title="'.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Nombre'][0].' '.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Numero'].' de '.$calendario['anyo'][$i]['Mes'][$j]['Nombre']. ' de '.$calendario['anyo'][$i]['Nombre'].' (Semana '.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Numero'].')'.'" id="Hora_'.$calendario['anyo'][$i]['Numero'].'_'.$calendario['anyo'][$i]['Mes'][$j]['Numero'].'_'.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Numero'].'" style="'.$tamano_altura.'left: '.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Posicion']*$factor.'px;width: '.$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Unidades']['Horas']*$factor.'px;" ></div>';					
				}	
			}
		}	
	}
	$gantt['ancho']  = $anchura_maxima;
	$div_hora        = $div_hora.'</div>';
	$div_dia         = $div_dia.'</div>';
	$div_semana      = $div_semana.'</div>';
	$div_mes         = $div_mes.'</div>';
	$div_anyo        = $div_anyo.'</div>';
	return $div_anyo.$div_mes.$div_semana.$div_dia.$div_hora;
}
function spb_gantt_carga_unidades(&$calendario)
{
	$posicion = 0;
	for ($i = 0; $i < count($calendario['anyo']); $i++)
	{	
		$calendario['anyo'][$i]['Posicion'] = $posicion;
		$semanas  =0;
		$dias     =0;
		$dias_mes =0;
		$horas_mes = 0;
		$dias_anyo=0;
		$horas_anyo=0;
		for ($j = 0; $j < count($calendario['anyo'][$i]['Mes']); $j++)
		{
			$calendario['anyo'][$i]['Mes'][$j]['Posicion'] = $posicion;
			$dias_mes=0;
			$horas_mes=0;
			$semanas=$semanas+count($calendario['anyo'][$i]['Mes'][$j]['Semana']);			
			for ($k = 0; $k < count($calendario['anyo'][$i]['Mes'][$j]['Semana']); $k++)
			{	
				$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Posicion'] = $posicion;
				
				
				$horas_semana = 0;
				$dias_semana  = count($calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia']);
				for ($l = 0; $l < count($calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia']); $l++)
				{
					$horas_dia = 0;
					$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Posicion'] = $posicion;
					for ($m = 0; $m < count($calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Hora']); $m++)
					{
						$horas_dia = $horas_dia + $calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Hora'][$m]['Unidades']['Horas'];
						$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Hora'][$m]['Posicion'] = $posicion;
						$posicion=$posicion+$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Hora'][$m]['Unidades']['Horas'];
					}					
					$horas_semana = $horas_semana + $horas_dia;
					$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Dia'][$l]['Unidades']['Horas'] = $horas_dia;
					
				}
				$horas_mes   = $horas_mes+$horas_semana;
				$dias_mes    = $dias_mes+$dias_semana;
				$dias_anyo   = $dias_anyo+$dias_semana;
				$horas_anyo  = $horas_anyo+$horas_semana;
				$dias        = $dias+$dias_mes;

				$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Unidades']['Dias']   =$dias_semana;
				$calendario['anyo'][$i]['Mes'][$j]['Semana'][$k]['Unidades']['Horas']  =$horas_semana;
			}
			$calendario['anyo'][$i]['Mes'][$j]['Unidades']['Semanas']=count($calendario['anyo'][$i]['Mes'][$j]['Semana']);
			$calendario['anyo'][$i]['Mes'][$j]['Unidades']['Dias']   =$dias_mes;
			$calendario['anyo'][$i]['Mes'][$j]['Unidades']['Horas']  =$horas_mes;
		}		
		$calendario['anyo'][$i]['Unidades']['Meses']  =count($calendario['anyo'][$i]['Mes']);
		$calendario['anyo'][$i]['Unidades']['Semanas']=$semanas;
		$calendario['anyo'][$i]['Unidades']['Dias']   =$dias_anyo;
		$calendario['anyo'][$i]['Unidades']['Horas']  =$horas_anyo;
	}
}
function spb_gantt_usuarios(&$usuarios,$gantt)
{
	DBConectar($GLOBALS["cfg_DataBase"]);	
	$contador         = 0;
	$Unidades         = $gantt['horas']['Fin']-$gantt['horas']['Inicio'] + 1;
	$where_usuarios   = '';
	if($gantt['lista_usuarios']!=null)
	{	
		$where_usuarios = " where sAMAccountName in ('".implode("','",$gantt['lista_usuarios'])."')";
	}
	$rows=DBSelect(utf8_decode(spb_gantt_get_query_usuarios(date('d/m/Y',$gantt['fechas']['Inicio']),date('d/m/Y',$gantt['fechas']['Fin']),$where_usuarios)));
	for(;DBNext($rows);)
	{		
		$controlador_posiciones = array();
		
		$id_usuarios           = utf8_encode(DBCampo($rows,utf8_decode("valor")));
		$nombre_usuarios       = utf8_encode(DBCampo($rows,utf8_decode("nombre")));				
		$Lineas            = 1;
		$Contador_Lineas   = 0;	
		$usuarios['usuario'][$contador] = array('Numero' => $id_usuarios,'Nombre' => $nombre_usuarios, 'Lineas' => $Lineas , 'Servicio' => null,'Ausencia' => null);

		//______________SERVICIOS DE UN USUARIO
		$services = DBSelect(utf8_decode(spb_gantt_get_query_servicios($id_usuarios,date('d/m/Y',$gantt['fechas']['Inicio']),date('d/m/Y',$gantt['fechas']['Fin']))));
		for(;DBNext($services);)
		{
		
			$clase                  = '';	
			$id_tarea               = utf8_encode(DBCampo($services,utf8_decode("tarea")));			
			$fecha_inicio_tarea     = utf8_encode(DBCampo($services,utf8_decode("fecha_inicio"))); 	
			$fecha_fin_tarea        = utf8_encode(DBCampo($services,utf8_decode("fecha_fin")));
			$titulo                 = utf8_encode(DBCampo($services,utf8_decode("titulo")));
			$estado                 = utf8_encode(DBCampo($services,utf8_decode("estado")));			
			if($estado=="festivo global")
			{
				$clase = 'global';
			}
			if($estado=="festivo particular")
			{
				$clase = 'particular';
			}
			
			$date1         = date_create(date("Y-m-d",spb_gantt_strtotime($fecha_inicio_tarea)));
			$date2         = date_create(date("Y-m-d",spb_gantt_strtotime($fecha_fin_tarea)));
			$tamanyo       = date_diff($date1,$date2);
			$tamanyo_tarea = ((int)$tamanyo->format("%r%a")+ 1)*$Unidades ;	
			

			
			$date1         = date_create(date("Y-m-d",$gantt['fechas']['Inicio']));
			$date2         = date_create(date("Y-m-d",spb_gantt_strtotime($fecha_inicio_tarea )));
			$posicion      = date_diff($date1,$date2);
			$calculo_tarea = (int)$posicion->format("%r%a");
			
			$date1             = date_create(date("Y-m-d",spb_gantt_strtotime($fecha_fin_tarea )));
			$date2             = date_create(date("Y-m-d",$gantt['fechas']['Fin']));
			$posicion_ext      = date_diff($date1,$date2);
			$calculo_tarea_ext = (int)$posicion_ext->format("%r%a");
			$extrad		       = false;	
			$extrai            = false;
			if($calculo_tarea_ext<0)
			{
				$extrad         = true;
				$tamanyo_tarea  = $tamanyo_tarea + ($calculo_tarea_ext*$Unidades);
			}
			if($calculo_tarea<0)
			{
				$extrai         = true;
				$posicion_tarea = 0;
				$tamanyo_tarea  = $tamanyo_tarea + ($calculo_tarea*$Unidades);
			}
			else
			{
				$posicion_tarea = $calculo_tarea*$Unidades;
			}
			$controlador_posiciones[$Contador_Lineas] = array("INICIO" => $posicion_tarea, "FIN" => $posicion_tarea+$tamanyo_tarea, "SERVICIO" => $Contador_Lineas);
						
			$usuarios['usuario'][$contador]['Servicio'][$Contador_Lineas] = array('Numero' => $id_tarea, 'Usuario' => $id_usuarios, 'Nombre' => $titulo,'Inicio' => spb_gantt_strtotime($fecha_inicio_tarea),'Fin' => spb_gantt_strtotime($fecha_fin_tarea),'Posicion' => $posicion_tarea,'Fecha Inicio'=>date("d/m/Y",spb_gantt_strtotime($fecha_inicio_tarea)),'Fecha Fin' =>date("d/m/Y",spb_gantt_strtotime($fecha_fin_tarea)),'Unidades' => array('Horas' => $tamanyo_tarea),"Fila" => 1, "Extra-d" => $extrad, "Extra-i" => $extrai);				
			$Contador_Lineas++;
		}	
		//______________SERVICIOS DE UN USUARIO	
		
		$Lineas            = 1;
		$Contador_Lineas   = 0;	
		//______________AUSENCIAS DE UN USUARIO		
		$ausencias = DBSelect(utf8_decode(spb_gantt_get_query_ausencia($id_usuarios,date('d/m/Y',$gantt['fechas']['Inicio']),date('d/m/Y',$gantt['fechas']['Fin']))));
		for(;DBNext($ausencias);)
		{
			$clase                  = '';				
			$fecha_inicio_tarea     = utf8_encode(DBCampo($ausencias,utf8_decode("fecha_inicio"))); 	
			$fecha_fin_tarea        = utf8_encode(DBCampo($ausencias,utf8_decode("fecha_fin")));
			$tipo                   = utf8_encode(DBCampo($ausencias,utf8_decode("tipo")));
			if($tipo=="festivo global")
			{
				$clase = 'global';
			}
			if($tipo=="festivo particular")
			{
				$clase = 'particular';
			}
			
			
			
			$date1         = date_create(date("Y-m-d",spb_gantt_strtotime($fecha_inicio_tarea)));
			$date2         = date_create(date("Y-m-d",spb_gantt_strtotime($fecha_fin_tarea)));
			$tamanyo       = date_diff($date1,$date2);
			$tamanyo_tarea = ((int)$tamanyo->format("%r%a")+ 1)*$Unidades ;	
			

			
			$date1         = date_create(date("Y-m-d",$gantt['fechas']['Inicio']));
			$date2         = date_create(date("Y-m-d",spb_gantt_strtotime($fecha_inicio_tarea )));
			$posicion      = date_diff($date1,$date2);
			$calculo_tarea = (int)$posicion->format("%r%a");
			
			$date1             = date_create(date("Y-m-d",spb_gantt_strtotime($fecha_fin_tarea )));
			$date2             = date_create(date("Y-m-d",$gantt['fechas']['Fin']));
			$posicion_ext      = date_diff($date1,$date2);
			$calculo_tarea_ext = (int)$posicion_ext->format("%r%a");
			$extrad		       = false;	
			$extrai            = false;
			if($calculo_tarea_ext<0)
			{
				$extrad         = true;
				$tamanyo_tarea  = $tamanyo_tarea + ($calculo_tarea_ext*$Unidades);
			}
			if($calculo_tarea<0)
			{
				$extrai         = true;
				$posicion_tarea = 0;
				$tamanyo_tarea  = $tamanyo_tarea + ($calculo_tarea*$Unidades);
			}
			else
			{
				$posicion_tarea = $calculo_tarea*$Unidades;
			}
			
						
			$usuarios['usuario'][$contador]['Ausencia'][$Contador_Lineas] = array('Numero' => $id_usuarios,'clase' => $clase, 'Nombre' => $tipo,'Inicio' => spb_gantt_strtotime($fecha_inicio_tarea),'Fin' => spb_gantt_strtotime($fecha_fin_tarea),'Posicion' => $posicion_tarea,'Fecha Inicio'=>date("d/m/Y",spb_gantt_strtotime($fecha_inicio_tarea)),'Fecha Fin' =>date("d/m/Y",spb_gantt_strtotime($fecha_fin_tarea)),'Unidades' => array('Horas' => $tamanyo_tarea),"Fila" => 1, "Extra-d" => $extrad, "Extra-i" => $extrai, "Informacion" => '');				
			$Contador_Lineas++;
		}		
		//______________AUSENCIAS DE UN USUARIO
		
		$existe = false;
		$filas  = array();
		for($l=0;$l<count($controlador_posiciones);$l++)
		{			
			for($n=0;$n<count($filas);$n++)
			{								
				for($o=0;$o<count($filas[$n]);$o++)
				{					
					if($controlador_posiciones[$l]['FIN']>	$filas[$n][$o]['INICIO'] && $controlador_posiciones[$l]['INICIO']<	$filas[$n][$o]['FIN'])
					{						
						$existe = true;
						break;
					}
					else
					{
						$existe = false;
					}
				}
				if($existe == false)
				{
					$filas[$n][count($filas[$n])] = array('INICIO' => $controlador_posiciones[$l]['INICIO'], 'FIN' => $controlador_posiciones[$l]['FIN'], "SERVICIO" => $controlador_posiciones[$l]['SERVICIO']);					
					break;
				}				
			}
			if($existe == true || count($filas) == 0)
			{
				$filas[count($filas)][0] = array('INICIO' => $controlador_posiciones[$l]['INICIO'], 'FIN' => $controlador_posiciones[$l]['FIN'], "SERVICIO" => $controlador_posiciones[$l]['SERVICIO']);
			}			
			$existe = false;
		}	

		$filas_calculadas = intval(count($filas));
		if($filas_calculadas==0)
		{
			$filas_calculadas = 1;
		}
		$usuarios['usuario'][$contador]['Lineas'] = $filas_calculadas;
		for($n=0;$n<count($filas);$n++)
		{								
			for($o=0;$o<count($filas[$n]);$o++)
			{
				$usuarios['usuario'][$contador]['Servicio'][$filas[$n][$o]['SERVICIO']]['Fila'] = $n;
			}
		}

		$contador++;	
	}
	DBClose();
}
function spb_gantt_anyo(&$calendario,$fecha_inicio,$fecha_fin)
{
	$inicio   = date("Y", $fecha_inicio);
	$fin      = date("Y",$fecha_fin);
	$calendario['inicio'] = $fecha_inicio;
	$calendario['fin']    = $fecha_fin;
	$contador = 0;
	for ($i = $inicio; $i <= $fin; $i++)
	{	
		if(date("Y", $fecha_inicio)==$i)
		{
			$inicio_anyo = $fecha_inicio;			
		}
		else
		{
			$inicio_anyo = strtotime(strval($i)."-01-01");			
		}
		if(date("Y", $fecha_fin)==$i)
		{
			$fin_anyo = $fecha_fin;
		}
		else
		{		
			$fin_anyo = strtotime(strval($i)."-12-31");
		}
		$calendario['anyo'][$contador] = array('Numero' => $i,'Nombre' => strval($i), 'Nombre Min' => strval($i),'Inicio' => $inicio_anyo,'Fin' => $fin_anyo,'Posicion' => null, 'Unidades' => array('Meses' => null, 'Semanas' => null, 'Dias' => null, 'Horas' => null), 'Mes' => null);		
		$contador++;
	}
}
function spb_gantt_mes(&$calendario)
{	
	for ($i = 0; $i < count($calendario['anyo']); $i++)
	{	
		$calendario['anyo'][$i]['Mes'] = spb_gantt_mes_anyo($calendario['anyo'][$i]['Numero'],intval(date('m',$calendario['anyo'][$i]['Inicio'])),intval(date('m',$calendario['anyo'][$i]['Fin'])));
	}
}
function spb_gantt_dias(&$calendario,$gantt)
{
	$fecha_inicio = date('d/m/Y',$calendario['inicio']);
	$fecha_fin    = date('d/m/Y',$calendario['fin']);
	$datePeriod   = spb_gantt_returnDates($fecha_inicio, $fecha_fin);
	foreach($datePeriod as $date) 
	{		
		$dia_calendario = $date->format('d/m/Y');
		$day            = date('d',spb_gantt_strtotime($dia_calendario));
		$month          = date('m',spb_gantt_strtotime($dia_calendario));
		$year           = date('Y',spb_gantt_strtotime($dia_calendario));		
		$day_week       = date('w',spb_gantt_strtotime($dia_calendario));
		$year_week      = date('W',spb_gantt_strtotime($dia_calendario));
		$Nombre_Dia     = spb_gantt_nameDia($year,$month,$day);
		for ($i = 0; $i < count($calendario['anyo']); $i++)
		{	
			if($calendario['anyo'][$i]['Numero']==$year)
			{
				for ($j = 0; $j < count($calendario['anyo'][$i]['Mes']); $j++)
				{	
					if($calendario['anyo'][$i]['Mes'][$j]['Numero']==$month)
					{
						if(count($calendario['anyo'][$i]['Mes'][$j]['Semana'])==0)
						{
							$calendario['anyo'][$i]['Mes'][$j]['Semana'][0] = array('Numero' => $year_week,'Posicion' => null,'Unidades' => array('Dias' => null, 'Horas' => null), 'Dia' => null);
							$calendario['anyo'][$i]['Mes'][$j]['Semana'][0]['Dia'][count($calendario['anyo'][$i]['Mes'][$j]['Semana'][0]['Dia'])] = array('Numero' => $day,'Mes' => $month,'Anyo' => $year,'Dia de semana' => $day_week, 'Nombre' => 'Lunes', 'Fecha' => spb_gantt_strtotime($dia_calendario),'Nombre' => $Nombre_Dia, 'Posicion' => null,'Hora' => spb_gantt_horas($gantt), 'Unidades' => array('Horas' => null));
						}
						else
						{
							if($calendario['anyo'][$i]['Mes'][$j]['Semana'][count($calendario['anyo'][$i]['Mes'][$j]['Semana'])-1]['Numero']==$year_week)
							{
								$calendario['anyo'][$i]['Mes'][$j]['Semana'][count($calendario['anyo'][$i]['Mes'][$j]['Semana'])-1]['Dia'][count($calendario['anyo'][$i]['Mes'][$j]['Semana'][count($calendario['anyo'][$i]['Mes'][$j]['Semana'])-1]['Dia'])] = array('Numero' => $day,'Mes' => $month,'Anyo' => $year,'Dia de semana' => $day_week, 'Nombre' => 'Lunes', 'Fecha' => spb_gantt_strtotime($dia_calendario),'Nombre' => $Nombre_Dia, 'Posicion' => null,'Hora' => spb_gantt_horas($gantt), 'Unidades' => array('Horas' => null));	
							}
							else
							{
								$calendario['anyo'][$i]['Mes'][$j]['Semana'][count($calendario['anyo'][$i]['Mes'][$j]['Semana'])] = array('Numero' => $year_week, 'Posicion' => null,'Unidades' => array('Dias' => null, 'Horas' => null),'Dia' => null);
								$calendario['anyo'][$i]['Mes'][$j]['Semana'][count($calendario['anyo'][$i]['Mes'][$j]['Semana'])-1]['Dia'][count($calendario['anyo'][$i]['Mes'][$j]['Semana'][count($calendario['anyo'][$i]['Mes'][$j]['Semana'])-1]['Dia'])] = array('Numero' => $day,'Mes' => $month,'Anyo' => $year,'Dia de semana' => $day_week, 'Nombre' => 'Lunes', 'Fecha' => spb_gantt_strtotime($dia_calendario),'Nombre' => $Nombre_Dia, 'Posicion' => null,'Hora' => spb_gantt_horas($gantt), 'Unidades' => array('Horas' => null));	
							}
						}
					}
				}
			}
		}		
	}	
}
function spb_gantt_horas($gantt)
{
	$contador = 0;
	for($i=$gantt['horas']['Inicio'] ;$i <= $gantt['horas']['Fin']; $i++) 
	{
		$nameHoras[$contador] = array("Numero" => $i, 'Nombre' => strval($i), 'Posicion' => null,'Unidades' => array('Horas' => 1) );
		$contador++;
	}
	return $nameHoras;
}
function spb_gantt_mes_anyo($anyo,$inicio,$fin)
{
	/*Funcion que crea el contenedor de los meses de un año*/
	$mes      = array();
	$contador = 0;
	if($inicio<=1 && $fin>=1)
	{
		$mes[$contador] = array('Numero' => 1,'Nombre' => 'Enero', 'Nombre Min' => 'Ene','Cantidad Dias' => cal_days_in_month(CAL_GREGORIAN, 1, $anyo),'Posicion' => null, 'Unidades' => array('Semanas' => null, 'Dias' => null, 'Horas' => null), 'Semana' => null); 
		$contador++;
	}
	if($inicio<=2 && $fin>=2)
	{
		$mes[$contador] = array('Numero' => 2,'Nombre' => 'Febrero', 'Nombre Min' => 'Feb','Cantidad Dias' => cal_days_in_month(CAL_GREGORIAN, 2, $anyo),'Posicion' => null, 'Unidades' => array('Semanas' => null, 'Dias' => null, 'Horas' => null), 'Semana' => null); 
		$contador++;
	}
	if($inicio<=3 && $fin>=3)
	{
		$mes[$contador] = array('Numero' => 3,'Nombre' => 'Marzo', 'Nombre Min' => 'Mar','Cantidad Dias' => cal_days_in_month(CAL_GREGORIAN, 3, $anyo),'Posicion' => null, 'Unidades' => array('Semanas' => null, 'Dias' => null, 'Horas' => null), 'Semana' => null); 
		$contador++;
	}
	if($inicio<=4 && $fin>=4)
	{
		$mes[$contador] = array('Numero' => 4,'Nombre' => 'Abril', 'Nombre Min' => 'Abr','Cantidad Dias' => cal_days_in_month(CAL_GREGORIAN, 4, $anyo),'Posicion' => null, 'Unidades' => array('Semanas' => null, 'Dias' => null, 'Horas' => null), 'Semana' => null); 
		$contador++;
	}
	if($inicio<=5 && $fin>=5)
	{
		$mes[$contador] = array('Numero' => 5,'Nombre' => 'Mayo', 'Nombre Min' => 'May','Cantidad Dias' => cal_days_in_month(CAL_GREGORIAN, 5, $anyo),'Posicion' => null, 'Unidades' => array('Semanas' => null, 'Dias' => null, 'Horas' => null), 'Semana' => null); 
		$contador++;
	}
	if($inicio<=6 && $fin>=6)
	{
		$mes[$contador] = array('Numero' => 6,'Nombre' => 'Junio', 'Nombre Min' => 'Jun','Cantidad Dias' => cal_days_in_month(CAL_GREGORIAN, 6, $anyo),'Posicion' => null, 'Unidades' => array('Semanas' => null, 'Dias' => null, 'Horas' => null), 'Semana' => null); 
		$contador++;
	}
	if($inicio<=7 && $fin>=7)
	{
		$mes[$contador] = array('Numero' => 7,'Nombre' => 'Julio', 'Nombre Min' => 'Jul','Cantidad Dias' => cal_days_in_month(CAL_GREGORIAN, 7, $anyo),'Posicion' => null, 'Unidades' => array('Semanas' => null, 'Dias' => null, 'Horas' => null), 'Semana' => null); 
		$contador++;
	}
	if($inicio<=8 && $fin>=8)
	{
		$mes[$contador] = array('Numero' => 8,'Nombre' => 'Agosto', 'Nombre Min' => 'Ago','Cantidad Dias' => cal_days_in_month(CAL_GREGORIAN, 8, $anyo),'Posicion' => null, 'Unidades' => array('Semanas' => null, 'Dias' => null, 'Horas' => null), 'Semana' => null); 
		$contador++;
	}
	if($inicio<=9 && $fin>=9)
	{
		$mes[$contador] = array('Numero' => 9,'Nombre' => 'Septiembre', 'Nombre Min' => 'Sep','Cantidad Dias' => cal_days_in_month(CAL_GREGORIAN, 9, $anyo),'Posicion' => null, 'Unidades' => array('Semanas' => null, 'Dias' => null, 'Horas' => null), 'Semana' => null); 
		$contador++;
	}
	if($inicio<=10 && $fin>=10)
	{
		$mes[$contador] = array('Numero' => 10,'Nombre' => 'Octubre', 'Nombre Min' => 'Oct','Cantidad Dias' => cal_days_in_month(CAL_GREGORIAN, 10, $anyo),'Posicion' => null, 'Unidades' => array('Semanas' => null, 'Dias' => null, 'Horas' => null), 'Semana' => null); 
		$contador++;
	}
	if($inicio<=11 && $fin>=11)
	{
		$mes[$contador] = array('Numero' => 11,'Nombre' => 'Noviembre', 'Nombre Min' => 'Nov','Cantidad Dias' => cal_days_in_month(CAL_GREGORIAN, 11, $anyo),'Posicion' => null, 'Unidades' => array('Semanas' => null, 'Dias' => null, 'Horas' => null), 'Semana' => null); 
		$contador++;
	}
	if($inicio<=12 && $fin>=12)
	{
		$mes[$contador] = array('Numero' => 12,'Nombre' => 'Diciembre', 'Nombre Min' => 'Dic','Cantidad Dias' => cal_days_in_month(CAL_GREGORIAN, 12, $anyo),'Posicion' => null, 'Unidades' => array('Semanas' => null, 'Dias' => null, 'Horas' => null), 'Semana' => null); 
		$contador++;
	}
	return $mes;
}
function spb_gantt_getStartAndEndDate($week, $year)
{
    //$week      = $week-1;
    $time      = strtotime("1 January $year", time());
    $day       = date('w', $time);
    $time     += ((7*$week)+1-$day)*24*3600;		
    $return[0] = strtotime(date('Y-n-j', $time));
    $time     += 6*24*3600;
    $return[1] = strtotime(date('Y-n-j', $time));
    return $return;	
}
function spb_gantt_nameDia($ano,$mes,$dia)
{
	$nameDias[] = array('Domingo','D');
	$nameDias[] = array('Lunes','L');
	$nameDias[] = array('Martes','M');
	$nameDias[] = array('Miercoles','X');
	$nameDias[] = array('Jueves','J');
	$nameDias[] = array('Viernes','V');
	$nameDias[] = array('Sabado','S');
	// 0->domingo	 | 6->sabado
	$dia= date("w",mktime(0, 0, 0, $mes, $dia, $ano));
	return $nameDias[$dia];
} 
function spb_gantt_returnDates($fromdate, $todate) 
{
    $fromdate = \DateTime::createFromFormat('d/m/Y', $fromdate);
    $todate   = \DateTime::createFromFormat('d/m/Y', $todate);
    return new \DatePeriod($fromdate,new \DateInterval('P1D'),$todate->modify('+1 day'));
}
function spb_gantt_strtotime($fecha)
{

	$fecha       = str_replace("-", "/",$fecha);	
	$array_fecha = explode("/",$fecha);
	return strtotime($array_fecha[2].'-'.$array_fecha[1].'-'.$array_fecha[0]);
}
function spb_gantt_get_informacion_episodio($id_episodio)
{
	if($id_episodio!=0)
	{
		//$nodo_episodio = node_load($id_episodio);
	    
	}
}
function spb_gantt_get_query_usuarios($inicio,$fin,$where_usuarios)
{
	return
	"SELECT sAMAccountName as valor,Name as nombre
	FROM OpenQuery(ADSI,'SELECT sAMAccountName,Name,department FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user''  AND objectClass=''user''  and department=''Tecnologías de la Información''') Consulta ".$where_usuarios." ORDER BY Name";
}
function spb_gantt_get_query_ausencia($usuario,$inicio,$fin)
{
	$array_fecha_inicio = explode("/",$inicio);
	$array_fecha_fin    = explode("/",$fin);
	
	return
	"DECLARE @MYQUERY varchar(MAX) DECLARE @CALENDARIO varchar(50) DECLARE @OPERARIO varchar(50) DECLARE @EMPRESA varchar(50) 
	SET @CALENDARIO = (SELECT TOP 1 [Calendario] collate Modern_Spanish_CI_AS FROM [Importe_Horas] where usuario = '".$usuario."') 
	SET @OPERARIO = (SELECT TOP 1 operario collate Modern_Spanish_CI_AS FROM [Importe_Horas] where usuario = '".$usuario."')
	SET @EMPRESA = (SELECT TOP 1 Empresa collate Modern_Spanish_CI_AS FROM [Importe_Horas] where usuario = '".$usuario."')
	SET @MYQUERY = ' 
	SELECT tipo as tipo, CONVERT(varchar(20),fecha_inicio,105) as fecha_inicio, CONVERT(varchar(20),fecha_fin,105) as fecha_fin FROM 
	OPENQUERY ( NAVISIONSQL ,''
	select ''''festivo global'''' as tipo ,
	dia as fecha_inicio,
	dia as fecha_fin
	from [Navisionsql].[NAVSQL].[dbo].[SP Berner".'$Festivos'."] Festivo 
	where [Cód_ Sección] = '''''+@CALENDARIO+''''' and dia between ''''".$inicio."'''' AND ''''".$fin."''''
	UNION ALL
	SELECT ''''festivo particular'''' as tipo ,
	CONVERT(varchar(20),Inicio,105) as fecha_inicio, CONVERT(varchar(20),Fin,105) as fecha_fin
	FROM [Navisionsql].[NAVSQL].[dbo].['+@EMPRESA+'".'$Turnos'." de Vacaciones] Turno
	where Turno.Operario = '''''+@OPERARIO+'''''
	and Turno.Fin >= ''''".$inicio."'''' AND Turno.Inicio <= ''''".$fin."''''
	and Turno.Prevision = 0
	'' ) ' 
	EXEC(@MYQUERY)";
}	
function spb_gantt_get_query_servicios($Usuario,$inicio,$fin)
{
	$array_fecha_inicio = explode("/",$inicio);
	$array_fecha_fin    = explode("/",$fin);
	return
	"  SELECT 
	  TyP.[Id] as tarea
	 ,TyP.[Título] as titulo
	 ,CONVERT(varchar(20),TyP.[Fecha objetivo],105) as fecha_fin
	 ,CONVERT(varchar(20),TyP.[Fecha objetivo inicio],105) as fecha_inicio
	  FROM [Tareas y Proyectos] TyP
	  
	  INNER JOIN [Importe_Horas] Usuario ON Usuario.Nombre = TyP.[Asignado a]
	  
	  where 
	  TyP.[Fecha objetivo inicio]  <= '".$fin."' AND
	  TyP.[Fecha objetivo]  >= '".$inicio."' 
	  AND TyP.[Horas estimadas] <> 999 
	  AND TyP.[Horas estimadas] <> 0
	  AND TyP.[Horas estimadas] IS NOT NULL
	  AND TyP.[Valoración] = 'Aceptado'
	  AND TyP.Estado IN ('En Curso', 'Sin Iniciar', 'Esperando','Solicitada')
	  AND (TyP.Control is null OR TyP.Control = 0)
	  AND Usuario.usuario = '".$Usuario."'";
}
function spb_gantt_render_servicio($id_servicio,$id_delta,$id_usuarios)
{
	$ficha_de_servicio = '';
	$ficha_de_servicio = $ficha_de_servicio.'<div style="font-size: 24px;">Esto es un ejemplo de ficha de tarea</div>';
	$ficha_de_servicio = $ficha_de_servicio.'Servicio= '.$id_servicio.' Delta='.$id_delta.' PErsona='.$id_usuarios.'<br>';
	$ficha_de_servicio = $ficha_de_servicio.'Node Load Drupal 7 para mostrar los datos<br>';
	return $ficha_de_servicio;
}
function spb_gantt_usuarios_select()
{	
	DBConectar($GLOBALS["cfg_DataBase"]);	
	$contador         = 0;	
	$usuarios         = array();
	$rows=DBSelect(utf8_decode(spb_gantt_get_query_usuarios(date('d/m/Y'),date('d/m/Y'),'')));
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
function spb_usuarios_render()
{
	$lista_usuarios  = spb_gantt_usuarios_select();
	$usuarios        = json_decode(stripslashes($_POST['usuarios']));
	for($i=0; $i<count($lista_usuarios); $i++)
    {
		$existe = '<img src="./gantt/image/no.png" id="'.str_replace(".", "_",$lista_usuarios[$i]['Numero']).'" onClick="spb_gantt_selecciona_usuario(this);"/>';
	    foreach($usuarios as $usuario)
		{
			if(str_replace(".", "_",$lista_usuarios[$i]['Numero'])==$usuario)
			{
				$existe = '<img id="'.str_replace(".", "_",$lista_usuarios[$i]['Numero']).'" src="./gantt/image/yes.png" onClick="spb_gantt_selecciona_usuario(this);"/>';
				break;
			}			
		}
		echo '<div class="lista_usuario">'.$existe.$lista_usuarios[$i]['Nombre'].'</div>';
	}	
	echo '<div class="boton_lista_usuario" onClick="spb_gantt_selecciona_acepta_usuario(this);">Aceptar</div>';
}
function spb_detalle_dia_render()
{
	$detalle = spb_gantt_detalle_dia_select($_POST['dia'],$_POST['mes'],$_POST['anyo']);
	echo '   <div class="Usuario_detalle_dia Usuario_detalle titulo_detalle">Usuario</div>
			 <div class="Usuario_detalle_dia detalle_horas_dia titulo_detalle">Horas</div>
			 <div class="Usuario_detalle_dia detalle_horas titulo_detalle">Ocupadas</div>
			 <div class="Usuario_detalle_dia detalle_sobrecarga titulo_detalle">Sobrecarga</div>';
	foreach($detalle as $persona=>$valores)
	{
		$clase = '';
		if($valores['Vacaciones']=='1')
		{
			$clase                = ' vacaciones';
			$sobrecarga_calculada = 0-floatval($valores['Horas']);
		}
		else
		{
			$sobrecarga_calculada = floatval($valores['Horas Dia'])-floatval($valores['Horas']);
		}
		$clasesobrecarga = '';
		if(floatval($sobrecarga_calculada)<0)
		{
			$clasesobrecarga = ' sobrecarga_negativa';	
		}
		echo '<div class="Usuarios_detalle_dia">
				<div class="Usuario_detalle_dia'.$clase .' Usuario_detalle">'.$valores['Nombre'].'</div>
				<div class="Usuario_detalle_dia detalle_horas_dia">'.round(floatval($valores['Horas Dia']),2).'</div>
				<div class="Usuario_detalle_dia detalle_horas">'.round(floatval($valores['Horas']),2).'</div>
				<div class="Usuario_detalle_dia'.$clasesobrecarga.' detalle_sobrecarga">'.round(floatval($sobrecarga_calculada),2).'</div>';
		
		foreach($valores as $campo=>$valor)
		{
			if($campo=="Tareas")
			{
				if(is_array($valor))
				{
					foreach($valor as $campo_tarea=>$valor_tarea)
					{
							echo '
									<div class="Usuario_detalle_dia Usuario_detalle">' .'<span class="tarea_fechas_detalle">'. $campo_tarea . '</span><span class="fechas_detalle">' .$valor_tarea['inicio'].'<img src="./gantt/image/arrow.png"/>'.$valor_tarea['fin'].'</span></div>
									<div class="Usuario_detalle_dia detalle_horas"><span class="tarea_fechas_detalle"> '. round(floatval($valor_tarea['horas_estimadas']),2).'</span></div>
									<div class="Usuario_detalle_dia detalle_sobrecarga"><span class="tarea_fechas_detalle"> '.round(floatval($valor_tarea['horas']),2).'</span> </div>';
							
					}
				}			
			}						
		}
		echo '</div>';
	}

	//echo '<pre>'.print_r($detalle,1).'</pre>';			
}
function spb_gantt_detalle_dia_select($dia,$mes,$anyo)
{	
	DBConectar($GLOBALS["cfg_DataBase"]);	
	$contador         = 0;	
	$detalle          = array();
	$rows=DBSelect(utf8_decode("EXECUTE [CALCULADORA_OBJETIVOS_ASIGNACIONES_DIA] '".$dia."/".$mes."/".$anyo."' "));
	for(;DBNext($rows);)
	{		
		$nombre                = utf8_encode(DBCampo($rows,utf8_decode("nombre")));
		$usuario               = utf8_encode(DBCampo($rows,utf8_decode("usuario")));
		$vacaciones            = utf8_encode(DBCampo($rows,utf8_decode("vacaciones")));
		$horas_dia             = utf8_encode(DBCampo($rows,utf8_decode("horas_dia")));
		$horas                 = utf8_encode(DBCampo($rows,utf8_decode("horas")));
		$id                    = utf8_encode(DBCampo($rows,utf8_decode("id")));
		$horas_estimadas       = utf8_encode(DBCampo($rows,utf8_decode("Horas_Estimadas")));
		$inicio                = utf8_encode(DBCampo($rows,utf8_decode("inicio")));
		$fin                   = utf8_encode(DBCampo($rows,utf8_decode("fin")));
		if(isset($detalle[$usuario])==false)
		{
			$detalle[$usuario]     = array('Nombre' => $nombre,'Vacaciones' => $vacaciones, 'Horas Dia' => $horas_dia,'Horas' => 0, 'Tareas' => null);
		}	
		if($id!=0)
		{			
			$detalle[$usuario]['Horas']  = $detalle[$usuario]['Horas'] + floatval($horas);
			$detalle[$usuario]['Tareas'][$id] = array('horas' => $horas,'horas_estimadas' => $horas_estimadas, 'inicio' => $inicio, 'fin' => $fin);
		}
		$contador++;	
	}
	DBClose();	
	return $detalle;
}