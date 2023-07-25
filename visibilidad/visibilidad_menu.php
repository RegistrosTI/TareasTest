<?php
// UTF ñaç
include_once "./conf/config.php";
include_once "./conf/config_menu.php";
include_once "../soporte/DB.php";
include_once "../soporte/funcionesgenerales.php";

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$Numero_Menu_Toca = '-1';
if ($externo ==''){
	$query = "SELECT usuario FROM Configuracion_Usuarios WHERE	Usuario = '$usuario'";
	$query = DBSelect ( ( $query ) );
	for(; DBNext ( $query ) ;) {
		$Numero_Menu_Toca = "1";
	}
	DBFree ( $query );	
}

$multiportal_rol = $Numero_Menu_Toca;
$multiportal_tipo = 'MENU';
$multiportal_ventana = curPageName ();
include_once "./visibilidad/visibilidad_config.php";

if ( $Numero_Menu_Toca == '1' ) {
	$id_mh = '1'; // mi departamento
} else {
	$id_mh = '2'; // soy ajeno
}

$Mi_Menu_Historial = '';
$Menu_Historial = '';

// CONSULTA QUE DEVUELVE LAS X TAREAS DEL HISTORIAL
$mh = "
		SELECT TOP (ISNULL( (SELECT CAST([Valor] AS int) FROM [Configuracion] WHERE Parametro = 'cantidadhistorial' and Usuario = '" . utf8_decode ( $usuario ) . "'),6)) 
			 H.[Tarea]
			,H.[Titulo]
			,H.[prioridad]
			,H.[fecha]
			,H.[Marcada] 
		FROM [Historico] AS H 
			INNER JOIN [Tareas y Proyectos] AS T 
			ON T.Id = H.Tarea AND T.Control <> 1 
		where H.Usuario = '" . utf8_decode ( $usuario ) . "' 
		order by H.[Marcada] desc,H.[fecha] desc";
//die($mh);
$mh = DBSelect ( ( $mh ) );
for(; DBNext ( $mh ) ;) {
	$Historial_Tarea = ( DBCampo ( $mh , ( "Tarea" ) ) );
	$Historial_Marcada = ( DBCampo ( $mh , ( "Marcada" ) ) );
	$Historial_Titulo = utf8_encode( DBCampo ( $mh ,  ( "Titulo" ) ) );
	$Historial_Prioridad = ( DBCampo ( $mh , ( "prioridad" ) ) );
	if ( $Historial_Marcada == '1' ) {
		$imagen_marcada = '<img src="./imagenes/marcada.png" width="20" height="20">';
	} else {
		$imagen_marcada = '';
	}
	$Menu_Historial = $Menu_Historial . '<li class="botonAngular text_left" id="li_historial_' . $Historial_Tarea . '" onClick="selecciono_tarea_historial(\'' . $Historial_Tarea . '\',' . $id_mh . ');" style="z-index:5002;width: 450px;"><span style="font-size:10px;italic;">' . $imagen_marcada . '&nbsp;' . $Historial_Tarea . '</span> &nbsp; <span style="font-size:12px;italic;">' . $Historial_Titulo . '</span></li>   ';
}
if ( $Menu_Historial != '' ) {
	$Mi_Menu_Historial = '';
	if ( BuscarOcultar ( ( 'Historial' ) , $opciones_ocultar , $nombre ) == true ) {
		$Mi_Menu_Historial = '<ul class="nav" id="menu" style="float:left;width:150px;">';
		//$Mi_Menu_Historial = $Mi_Menu_Historial . '<li class="botonAngular text_left" onClick="selecciono_todo_historial()">' . $nombre . '';
		$Mi_Menu_Historial = $Mi_Menu_Historial . '<li class="botonAngular text_left" onClick=""><img src="./imagenes/calendar.png" width="20" height="20">&nbsp;' . $nombre . '';
		$Mi_Menu_Historial = $Mi_Menu_Historial . '<ul style="z-index:5002;left:130px;top:0px">';
		$Mi_Menu_Historial = $Mi_Menu_Historial . $Menu_Historial;
		$Mi_Menu_Historial = $Mi_Menu_Historial . '</ul>';
		$Mi_Menu_Historial = $Mi_Menu_Historial . '</li>';
		$Mi_Menu_Historial = $Mi_Menu_Historial . '</ul>';
	}
}

$Menu_Colas = '';
if ( BuscarOcultar ( ( 'Colas' ) , $opciones_ocultar , $nombre ) == true ) {
	$Menu_Colas = '<ul class="nav" id="menu" style="width:150px;">';
	$Menu_Colas = $Menu_Colas . '<li class="botonAngular text_left"><img src="./imagenes/list.png" width="20" height="20">&nbsp;' . $nombre . '';
	$Menu_Colas = $Menu_Colas . '<ul style="z-index:5002;left:130px;top:0px">';
	$Menu_Colas = $Menu_Colas . '<li  id="li_colas" class="botonAngular text_left" onClick="cambia_menu(1);selecciono_colas(\'visibilidad_colas\',-1,\'Todas\');">Todas<img id="visibilidad_colas" class="visibilidad_colas" src="./imagenes/RedCheckBox.png" width="20" height="20"></li>   ';
	$Menu_Colas = $Menu_Colas . '<li  id="li_colas_0" class="botonAngular text_left" onClick="cambia_menu(1);selecciono_colas(\'visibilidad_colas_0\',0,\'Mis Colas\');">Mis Colas<img id="visibilidad_colas_0" class="visibilidad_colas" src="./imagenes/Transparent.png" width="20" height="20"></li>   ';
	// ****************************************************COLAS QUE TOCA
	if ( 3 == 3 ) {
		$query = DBSelect ( ( "SELECT Colas.[Numero],Colas.[Descripcion],Usuarios.[Usuario] FROM [Colas_Usuarios] Usuarios INNER JOIN [Colas] Colas ON Usuarios.Cola = Colas.Numero WHERE Usuarios.Usuario = '" . utf8_decode ( $usuario ) . "'" ) );
		
		for(; DBNext ( $query ) ;) {
			$Menu_Colas_Numero = ( DBCampo ( $query , "Numero" ) );
			$Menu_Colas_Descripcion = ( DBCampo ( $query , "Descripcion" ) );
			
			$Menu_Colas = $Menu_Colas . '<li id="li_colas_' . $Menu_Colas_Numero . '"  class="botonAngular text_left" onClick="cambia_menu(1);selecciono_colas(\'visibilidad_colas_' . $Menu_Colas_Numero . '\',' . $Menu_Colas_Numero . ',\'' . $Menu_Colas_Descripcion . '\');" ><i>' . $Menu_Colas_Descripcion . '</i><img id="visibilidad_colas_' . $Menu_Colas_Numero . '" class="visibilidad_colas" src="./imagenes/Transparent.png" width="20" height="20"></li>   ';
		}
		DBFree ( $query );
	}
	// ****************************************************COLAS QUE TOCA
	$Menu_Colas = $Menu_Colas . '</ul>';
	$Menu_Colas = $Menu_Colas . '</li>';
	$Menu_Colas = $Menu_Colas . '</ul>';
}

$maxima_longitud = 20;
$cantidad_personalizadas = 0;
$Lista_Consultas_Personalizadas = '';
$array_de_valores = '';
// ****************************************************MIS CONSULTAS
if ( 3 == 3 ) {
	$query = DBSelect ( ( "SELECT Numero,[Descripcion] FROM [Consultas_Personalizadas] where USuario = '" . utf8_decode ( $usuario ) . "'" ) );
	for(; DBNext ( $query ) ;) {
		$Lista_Consultas_Personalizadas_Numero = ( DBCampo ( $query , "Numero" ) );
		$Lista_Consultas_Personalizadas_Descripcion = ( DBCampo ( $query , "Descripcion" ) );
		if ( strlen ( $Lista_Consultas_Personalizadas_Descripcion ) > $maxima_longitud ) {
			$maxima_longitud = strlen ( $Lista_Consultas_Personalizadas_Descripcion );
		}
		$Lista_Consultas_Personalizadas = $Lista_Consultas_Personalizadas . '<li class="botonAngular text_left" id="li_consulta_personalizada_' . $Lista_Consultas_Personalizadas_Numero . '" onClick="cambia_menu(1);selecciono_consulta_personalizada(' . $Lista_Consultas_Personalizadas_Numero . ');" style="z-index:5002;width: -__LONGITUD__-px;">' . $Lista_Consultas_Personalizadas_Descripcion . '<img id="li_consulta_personalizada_' . $Lista_Consultas_Personalizadas_Numero . '_visibilidad" class="visibilidad_tipo_8" src="./imagenes/Transparent.png" width="20" height="20"></li>   ';
		$cantidad_personalizadas = $cantidad_personalizadas + 1;
		$array_de_valores = $array_de_valores . $Lista_Consultas_Personalizadas_Numero . ',';
	}
	DBFree ( $query );
}
// ****************************************************MIS CONSULTAS
if ( $cantidad_personalizadas > 0 ) {
	$array_de_valores = trim ( $array_de_valores , ',' );
	echo '<script>var cantidad_personalizadas= [' . $array_de_valores . ']; </script>';
} else {
	echo '<script>var cantidad_personalizadas= []; </script>';
}
if ( $Numero_Menu_Toca == '-1' ) {
	echo '<script>tipo_visibilidad_1=3</script>';
}
if ( $Numero_Menu_Toca == '1' ) {
	echo '<script>tipo_visibilidad_1=1</script>';
}
echo '<script>tipo_visibilidad_2=1</script>';
echo '<script>tipo_visibilidad_3=1</script>';
echo '<script>tipo_visibilidad_4=1</script>';
echo '<script>tipo_visibilidad_5=1</script>';
echo '<script>tipo_visibilidad_6=1</script>';
echo '<script>tipo_visibilidad_7=1</script>';
echo '<script>tipo_visibilidad_8=1</script>';
echo '<script>tipo_visibilidad_9=1</script>';
echo '<script>tipo_visibilidad_A=-1</script>';

echo '<div id="navegador-menu">';
if ( BuscarOcultar ( ( 'Tareas' ) , $opciones_ocultar , $nombrepadre ) == true ) {
	$maxima_longitud_Menu1_opcion = 1;
	$Menu1_opcion1 = false;
	$Menu1_opcion1_txt = 'Todas';
	$Menu1_opcion2 = false;
	$Menu1_opcion2_txt = 'Mi Departamento';
	$Menu1_opcion3 = false;
	$Menu1_opcion3_txt = 'Mias';
	if ( BuscarOcultar ( ( 'Tareas|' . $Menu1_opcion1_txt ) , $opciones_ocultar , $nombre ) == true ) {
		$Menu1_opcion1 = true;
		$Menu1_opcion1_txt = $nombre;
		if ( strlen ( $nombre ) > $maxima_longitud_Menu1_opcion ) {
			$maxima_longitud_Menu1_opcion = strlen ( $Menu1_opcion1_txt ) + 5;
		}
	}
	
	if ( BuscarOcultar ( ( 'Tareas|' . $Menu1_opcion2_txt ) , $opciones_ocultar , $nombre ) == true ) {
		$Menu1_opcion2 = true;
		$Menu1_opcion2_txt = $nombre;
		if ( strlen ( $nombre ) > $maxima_longitud_Menu1_opcion ) {
			$maxima_longitud_Menu1_opcion = strlen ( $Menu1_opcion2_txt ) + 5;
		}
	}
	
	if ( BuscarOcultar ( ( 'Tareas|' . $Menu1_opcion3_txt ) , $opciones_ocultar , $nombre ) == true ) {
		$Menu1_opcion3 = true;
		$Menu1_opcion3_txt = $nombre;
		if ( strlen ( $nombre ) > $maxima_longitud_Menu1_opcion ) {
			$maxima_longitud_Menu1_opcion = strlen ( $Menu1_opcion3_txt ) + 5;
		}
	}
	echo '	<ul class="nav" id="menu" >  ';
	echo '		<li class="botonAngular text_left"  onClick="cambia_menu(1)"><div class="text_left  float_left"><img src="./imagenes/task.png" width="20" height="20">&nbsp;' . $nombrepadre . '</div>';
	echo '			<ul style="z-index:9999;left:130px;top:0px">      ';
	if ( $Menu1_opcion1 == true ) {
		echo '				<li class="botonAngular text_left" id="li_todas" onClick="cambia_menu(1);selecciono(\'visibilidad_tipo_1_1\',1);" style="z-index:5002;width:' . ( $maxima_longitud_Menu1_opcion * 10 ) . 'px;">' . $Menu1_opcion1_txt . '<img id="visibilidad_tipo_1_1" class="visibilidad_tipo_1" src="./imagenes/RedCheckBox.png" width="20" height="20"></li>      ';
	}
	if ( $Menu1_opcion2 == true ) {
		echo '				<li class="botonAngular text_left" id="li_todas_dep" onClick="cambia_menu(1);selecciono(\'visibilidad_tipo_1_2\',2);" style="z-index:5002;width:' . ( $maxima_longitud_Menu1_opcion * 10 ) . 'px;">' . $Menu1_opcion2_txt . '<img id="visibilidad_tipo_1_2" class="visibilidad_tipo_1" src="./imagenes/Transparent.png" width="20" height="20"></li>   ';
	}
	if ( $Menu1_opcion3 == true ) {
		echo '				<li class="botonAngular text_left" id="li_todas_mias" onClick="cambia_menu(1);selecciono(\'visibilidad_tipo_1_3\',3);" style="z-index:5002;width:' . ( $maxima_longitud_Menu1_opcion * 10 ) . 'px;">' . $Menu1_opcion3_txt . '<img id="visibilidad_tipo_1_3" class="visibilidad_tipo_1" src="./imagenes/Transparent.png" width="20" height="20"></li>        ';
	}
	echo '			</ul>  ';
	echo '		</li>';
	echo '	</ul>';
}

if ( BuscarOcultar ( ( 'Filtros' ) , $opciones_ocultar , $nombre ) == true ) {
	echo '	<ul class="nav" style="width:140px;">  ';
	echo '		<li class="botonAngular text_left"><span id="menu_filtro"><img src="./imagenes/filter.png" width="20" height="20">&nbsp;' . $nombre . '</span>';
	echo '			<ul style="z-index:5002;left:130px;top:0px">      ';
	if ( BuscarOcultar ( ( 'Filtros|Participo' ) , $opciones_ocultar , $nombre ) == true ) {
		echo str_replace ( '-__LONGITUD__-' , $maxima_longitud * 10 , '				<li class="botonAngular text_left" id="li_desarrollo_participo" onClick="cambia_menu(1);selecciono_participo(\'visibilidad_tipo_2_2\',2);" style="z-index:5002;width: -__LONGITUD__-px;">' . $nombre . '<img id="visibilidad_tipo_2_2" class="visibilidad_tipo_2" src="./imagenes/Transparent.png" width="20" height="20"></li>   ' );
	}
	if ( BuscarOcultar ( ( 'Filtros|Iniciada' ) , $opciones_ocultar , $nombre ) == true ) {
		echo str_replace ( '-__LONGITUD__-' , $maxima_longitud * 10 , '				<li class="botonAngular text_left" id="li_desarrollo_iniciada" onClick="cambia_menu(1);selecciono_iniciada(\'visibilidad_tipo_3_2\',2);" style="z-index:5002;width: -__LONGITUD__-px;">' . $nombre . '<img id="visibilidad_tipo_3_2" class="visibilidad_tipo_3" src="./imagenes/Transparent.png" width="20" height="20"></li>   ' );
	}
	if ( BuscarOcultar ( ( 'Filtros|En Curso' ) , $opciones_ocultar , $nombre ) == true ) {
		echo str_replace ( '-__LONGITUD__-' , $maxima_longitud * 10 , '				<li class="botonAngular text_left" id="li_desarrollo_pendiente" onClick="cambia_menu(1);selecciono_pendiente(\'visibilidad_tipo_4_2\',2);" style="z-index:5002;width: -__LONGITUD__-px;">' . $nombre . '<img id="visibilidad_tipo_4_2" class="visibilidad_tipo_4" src="./imagenes/Transparent.png" width="20" height="20"></li>   ' );
	}
	if ( BuscarOcultar ( ( 'Filtros|Reciente' ) , $opciones_ocultar , $nombre ) == true ) {
		echo str_replace ( '-__LONGITUD__-' , $maxima_longitud * 10 , '				<li class="botonAngular text_left" id="li_desarrollo_reciente" onClick="cambia_menu(1);selecciono_reciente(\'visibilidad_tipo_5_2\',2);" style="z-index:5002;width: -__LONGITUD__-px;">' . $nombre . '<img id="visibilidad_tipo_5_2" class="visibilidad_tipo_5" src="./imagenes/Transparent.png" width="20" height="20"></li>   ' );
	}
	if ( BuscarOcultar ( ( 'Filtros|Sin Asignar' ) , $opciones_ocultar , $nombre ) == true ) {
		echo str_replace ( '-__LONGITUD__-' , $maxima_longitud * 10 , '				<li class="botonAngular text_left" id="li_desarrollo_sinasignar" onClick="cambia_menu(1);selecciono_sinasignar(\'visibilidad_tipo_6_2\',2);" style="z-index:5002;width: -__LONGITUD__-px;">' . $nombre . '<img id="visibilidad_tipo_6_2" class="visibilidad_tipo_6" src="./imagenes/Transparent.png" width="20" height="20"></li>   ' );
	}
	if ( BuscarOcultar ( ( 'Filtros|Pendiente' ) , $opciones_ocultar , $nombre ) == true ) {
		echo str_replace ( '-__LONGITUD__-' , $maxima_longitud * 10 , '				<li class="botonAngular text_left" id="li_desarrollo_sininiciar" onClick="cambia_menu(1);selecciono_sininiciar(\'visibilidad_tipo_7_2\',2);" style="z-index:5002;width: -__LONGITUD__-px;">' . $nombre . '<img id="visibilidad_tipo_7_2" class="visibilidad_tipo_7" src="./imagenes/Transparent.png" width="20" height="20"></li>   ' );
	}
	if ( BuscarOcultar ( ( 'Filtros|Retrasadas' ) , $opciones_ocultar , $nombre ) == true ) {
		echo str_replace ( '-__LONGITUD__-' , $maxima_longitud * 10 , '				<li class="botonAngular text_left" id="li_desarrollo_retrasadas" onClick="cambia_menu(1);selecciono_retrasadas(\'visibilidad_tipo_8_2\',2);" style="z-index:5002;width: -__LONGITUD__-px;">' . $nombre . '<img id="visibilidad_tipo_8_2" class="visibilidad_tipo_8" src="./imagenes/Transparent.png" width="20" height="20"></li>   ' );
	}
	if ( BuscarOcultar ( ( 'Filtros|Teletrabajo' ) , $opciones_ocultar , $nombre ) == true ) {
		echo str_replace ( '-__LONGITUD__-' , $maxima_longitud * 10 , '				<li class="botonAngular text_left" id="li_desarrollo_retrasadas" onClick="cambia_menu(1);selecciono_teletrabajo(\'visibilidad_tipo_9_2\',2);" style="z-index:5002;width: -__LONGITUD__-px;">' . $nombre . '<img id="visibilidad_tipo_9_2" class="visibilidad_tipo_8" src="./imagenes/Transparent.png" width="20" height="20"></li>   ' );
	}
	echo str_replace ( '-__LONGITUD__-' , $maxima_longitud * 10 , $Lista_Consultas_Personalizadas );
	echo '			</ul>  ';
	echo '		</li>';
	echo '	</ul>';
}
echo $Mi_Menu_Historial;
echo $Menu_Colas;
if ( BuscarOcultar ( ( 'Planificador' ) , $opciones_ocultar , $nombre ) == true ) {
	echo '	<ul class="nav" id="menu" style="width:140px;">  ';
	echo '		<li class="botonAngular text_left" onClick="cambia_menu(20)" id="btnPlanificador"><img src="./imagenes/planificador.png" width="20" height="20">&nbsp;' . $nombre . '</li>';
	echo '	</ul>	';
}


if ( BuscarOcultar ( ( 'Avisos' ) , $opciones_ocultar , $nombre ) == true ) {
	echo '	<ul class="nav" id="menu" style="width:140px;">  ';
	echo '		<li class="botonAngular text_left" onClick="ver_avisos()"><img src="./imagenes/bell.png" width="20" height="20">&nbsp;' . $nombre . ' <img name="imagen_avisos" id="imagen_avisos" src="./imagenes/Transparent.png" width="20" height="20"></li>    ';
	echo '	</ul>';
}
if ( BuscarOcultar ( ( 'Prefijar'/*Sin las Frecuentes*/ ) , $opciones_ocultar , $nombre ) == true ) {
	echo '	<ul class="nav" id="menu" style="width:140px;">  ';
	echo '		<li class="botonAngular text_left" onClick="cambia_menu(11)"><img style="float:left" src="./imagenes/repeat.png" width="20" height="20"><div style="float:left;width:50px;margin-left:3px">' . $nombre . '</div></li>';
	echo '	</ul>	';
}
if ( BuscarOcultar ( ( 'Informes' ) , $opciones_ocultar , $nombre ) == true ) {
	echo '	<ul class="nav" id="menu" style="width:140px;">  ';
	echo '		<li class="botonAngular text_left" onClick="cambia_menu(8)"><img src="./imagenes/informe2.png" width="20" height="20">&nbsp;' . $nombre . '</li>';
	echo '	</ul>	';
}
if ( BuscarOcultar ( ( 'Configuración' ) , $opciones_ocultar , $nombre ) == true ) {
	echo '	<ul class="nav" id="menu" style="width:140px;">  ';
	echo '		<li class="botonAngular text_left" onClick="cambia_menu(7)"><img src="./imagenes/configuration.png" width="17" height="20">&nbsp;' . $nombre . '</li>    ';
	echo '	</ul>';
}
echo '</div><!-- Fin Menu!-->';
//die();
DBClose ();
?>