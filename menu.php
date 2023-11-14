<?php
// UTF-8 Ñ Á
require_once "./php/funciones.php";
$NOMBRE_PORTAL = "MENU";
$test = false;
$favicon = "icon.png";
if(isset($_SERVER [ 'HTTP_HOST' ]) && strpos ( strtoupper ( $_SERVER [ 'HTTP_HOST' ])  , 'TEST' ) !== false ){
	$test = true;
	$favicon = "icon_mant.png";
}
// ++ ***** RECONOCIMIENTO DEL NAVEGADOR *****
require_once "conf/Browser/lib/Browser.php";
$browser = new Browser ();
$version_obsoleta = 0;
$mensaje = "";
if ( ( $browser -> getBrowser () == Browser :: BROWSER_IE || $browser -> getBrowser () == Browser :: BROWSER_EDGE || $browser -> getBrowser () == Browser :: BROWSER_OPERA) ) {
	$version_obsoleta = 1;
	$mensaje= " Su navegador <b>" . $browser -> getBrowser () . "</b> no est&aacute; soportado, por favor utilice <b>Firefox</b> o <b>Chrome</b>. Si no dispusiera de alguno de estos navegadores p&oacute;ngase en contacto con el departamento de sistemas mediante env&iacuteo de un correo a:<br><br><a href='mailto:soporte.ti@sp-berner.com?subject=Solicitud para instalar navegador Firefox o Chrome.&body=Buenas. Solamente dispongo del navegador de internet " . $browser -> getBrowser () . " y hago esta solicitud para que me instalen Firefox o Chrome. Muchas gracias.' style='color:white'><b>soporte.ti@sp-berner.com</b></a>.";
}
if ( ( $browser -> getBrowser () == Browser :: BROWSER_FIREFOX && $browser -> getVersion () < 52 ) ||
		( $browser -> getBrowser () == Browser :: BROWSER_CHROME && $browser -> getVersion () < 49 )  ||
		( $browser -> getBrowser () == Browser :: BROWSER_SAFARI && $browser -> getVersion () < 11 ) ) {
			$version_obsoleta = 1;
			$mensaje= " Su navegador " . $browser -> getBrowser () . " en versi&oacute;n " . $browser -> getVersion () . ", no est&aacute; soportado, por favor contacte con sistemas para su actualizaci&oacute;n enviando un correo a:<br><br><a href='mailto:soporte.ti@sp-berner.com?subject=Solicitud para actualizar mi navegador " . $browser -> getBrowser () . ".&body=Buenas. Actualmente dispongo del navegador de internet " . $browser -> getBrowser () . " ver. " . $browser -> getVersion () . " y hago esta solicitud para actualizar. Muchas gracias.' style='color:white'><b>soporte.ti@sp-berner.com</b></a>.";
		}
		
		if($version_obsoleta == 1){
			echo "
	<style>
		#content {
			width: 100%;
			height: 80%;
			text-align: center;
		}
		#titulo {
			font-size: 50px;
		}
		#mensaje{
			border: 1px solid #8A4141;
			background-color: red;
			font-size: 20px;
			color: white;
			padding: 5px;
			margin: 5px;
			border-radius: 5px;
		}
	</style>
	<div id='content'>
		<span id='titulo'>NAVEGADOR NO SOPORTADO!</span>
		<br>
		<div id='mensaje'>$mensaje</div>
	</div>";
			die();
		}
		// -- ***** RECONOCIMIENTO DEL NAVEGADOR *****

require_once "./php/usuario_remoto.php";
require_once "./php/version.php";
require_once "./php/capacidad.php";
require_once "./php/control_acceso.php";


//++ comprobar externo y si existe el usuario en el portal
// Si el usuario no existe y no entra en un departamento válido, se le muestra un aviso o menú de opciones
$externo = '';
if ( isset ( $_GET [ 'ext' ] ) ){
	$externo = strtoupper ( $_GET [ 'ext' ] );
}	
if ( isset ( $_GET [ 'EXT' ] ) ){
	$externo = strtoupper ( $_GET [ 'EXT' ] );
}
include_once "./php/comprobarExterno.php";
//-- comprobar externo y si existe el usuario en el portal

$nombre_portal = "";
switch ( $externo ) {
	case "TI" :
		$nombre_portal = "GESTIÓN DE TAREAS - TECNOLOGÍAS DE LA INFORMACIÓN";
		$oficina_usuario_validado = 'TI';
		break;
	case "SAC" :
		$nombre_portal = "GESTIÓN DE TAREAS - SERVICIO DE ATENCIÓN AL CLIENTE";
		$oficina_usuario_validado = 'SAC';
		break;
	case "COMERCIAL" :
		$nombre_portal = "GESTIÓN DE TAREAS - COMERCIAL";
		$oficina_usuario_validado = 'COMERCIAL';
		break;
	case "MARKETING" :
		$nombre_portal = "GESTIÓN DE TAREAS - MARKETING";
		$oficina_usuario_validado = 'MARKETING';
		break;
	case "EXPORTACION" :
		$nombre_portal = "GESTIÓN DE TAREAS - EXPORTACIÓN";
		$oficina_usuario_validado = 'EXPORTACION';
		break;
	case "INNOVACION" :
		$nombre_portal = "GESTIÓN DE TAREAS - INNOVACIÓN";
		$oficina_usuario_validado = 'INNOVACION';
		break;
	case "ADMINISTRACION" :
		$nombre_portal = "GESTIÓN DE TAREAS - ADMINISTRACIÓN";
		$oficina_usuario_validado = 'ADMINISTRACION';
		break;
	case "PEyP" :
		$nombre_portal = "GESTIÓN DE TAREAS - PROYECTOS, ESTRUCTURAS Y PACKAGING";
		$oficina_usuario_validado = 'PEyP';
		break;
	case "LABORATORIO" :
		$nombre_portal = "GESTIÓN DE TAREAS - LABORATORIO";
		$oficina_usuario_validado = 'LABORATORIO';
		break;
	case "SPALEX" :
		$nombre_portal = "GESTIÓN DE TAREAS - SPALEX";
		$oficina_usuario_validado = 'SPALEX';
		break;
	case "COSTES" :
		$nombre_portal = "GESTIÓN DE TAREAS - COSTES";
		$oficina_usuario_validado = 'COSTES';
		break;
	case "COSTE" :
		$nombre_portal = "GESTIÓN DE TAREAS - COSTES";
		$oficina_usuario_validado = 'COSTE';
		break;
	case "UNIDAD_DIGITAL" :
		$nombre_portal = "GESTIÓN DE TAREAS - UNIDAD DIGITAL";
		$oficina_usuario_validado = 'UNIDAD_DIGITAL';
		break;
	case "NDES" :
		$nombre_portal = "GESTIÓN DE TAREAS - NUEVOS DESARROLLOS";
		$oficina_usuario_validado = 'NDES';
		break;
	case "LEAN" :
		$nombre_portal = "GESTIÓN DE TAREAS - LEAN";
		$oficina_usuario_validado = 'LEAN';
		break;
	case "OPERACIONES" :
		$nombre_portal = "GESTIÓN DE TAREAS - OPERACIONES";
		$oficina_usuario_validado = 'OPERACIONES';
		break;
	case "MANTENIMIENTO" :
		$nombre_portal = "GESTIÓN DE TAREAS - MANTENIMIENTO";
		$oficina_usuario_validado = 'MANTENIMIENTO';
		break;
	case "MATERIALES" :
		$nombre_portal = "GESTIÓN DE TAREAS - MATERIALES";
		$oficina_usuario_validado = 'MATERIALES';
		break;
	default :
		//echo "401";
		//die();
		$nombre_portal = "GESTIÓN DE TAREAS";
}
if ($test) {
	$nombre_portal = "<span style='color:red;background-color:yellow;padding-left:5px;padding-right:5px'>$nombre_portal ****TEST****</span>";
}
?>
<html>
<head>
<!-- <meta charset="ISO-8859-1"> -->
<meta charset="UTF-8">
<title>GESTI&Oacute;N DE TAREAS</title>
<!--SP-BERNER CONDICIONAL-->
<style type="text/css">
<?php include "./visibilidad/visibilidad_colores.php"; ?>
</style>

<!--jQuery dependencies-->
<link rel="stylesheet" href="css/jquery-ui-1.10.4.custom.min.css?v=<?php echo $versionPortal; ?>" />
<script src="js/pantalla.js?v=<?php echo $versionPortal; ?>"></script>
<script src="js/funciones.js?v=<?php echo $versionPortal; ?>"></script>
<script src="js/shortcuts.js"></script>
<!-- <script src="teletrabajo/assets/js/teletrabajo.js"></script> -->
<script src="grid/control_grid.js?v=<?php echo $versionPortal; ?>"></script>
<script src="jquery/jquery.min.js"></script>  
<!-- <script src="jquery/jquery-3.2.1.min.js"></script>-->
<!--  <script src="jquery/jquery-ui.min.js"></script> -->
<script src="jquery/jquery-ui.js"></script>
<!--jQuery dependencies-->

<!-- XDAN DATEPICKER https://github.com/xdan/datetimepicker -->
<script type="text/javascript" src="jquery/datetimepicker-master/build/jquery.datetimepicker.full.js"></script>
<link rel="stylesheet" href="jquery/datetimepicker-master/build/jquery.datetimepicker.min.css" />

<!--treefolder-->
<script src="treefolder/treefolder.js" type="text/javascript"></script>
<link href="treefolder/treefolder.css" rel="stylesheet" type="text/css">
<!--treefolder-->

<!--feedback-->
<script src="feedback/feedback.js?v=<?php echo $versionPortal; ?>" type="text/javascript"></script>
<link href="feedback/feedback.css?v=<?php echo $versionPortal; ?>" rel="stylesheet" type="text/css">
<!--feedback-->

<!--Uploader-->
<script src="uploader/upload.js"></script>
<!--Uploader-->

<!--SKIN DE JQUERY-->
<!--<link href="jquery/jquery-ui.css" rel="stylesheet">-->
<!--SKIN DE JQUERY-->




<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="files/js/vendor/jquery.ui.widget.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="files/js/jquery.iframe-transport.js"></script>
<!-- addon de time para jquerydatepicker INCOMPATIBLE CON JQUERYFILEUPLOAD-->
<!-- <script type="text/javascript" src="jquery/jquery-ui-timepicker-addon.js"></script> -->


<!-- The basic File Upload plugin -->
<script src="files/js/jquery.fileupload.js"></script>

<!--Include Touch Punch file to provide support for touch devices-->
<script type="text/javascript" src="jquery/jquery.ui.touch-punch.js"></script>
<!--Include Touch Punch file to provide support for touch devices-->

<!--ParamQuery Grid files-->
<!--<script type="text/css" src="jquery/themes/Office/pqgrid.css" ></script> -->
<link rel="stylesheet"        href="jquery/paramquery-2.4.1/pqgrid.dev.css?v=<?php echo $versionPortal; ?>" />
<!-- APARTIR DE VERSION 3.0.0 HACE FALTA <link rel="stylesheet"        href="jquery/paramquery-2.4.1/pqgrid.ui.dev.css?v=<?php //echo $versionPortal; ?>" /> -->
<script type="text/javascript" src="jquery/paramquery-2.4.1/pqgrid.dev.js?v=<?php  echo $versionPortal; ?>"></script>
<!--ParamQuery Grid files-->

<!--Slider-->
<link rel="stylesheet" href="css/tm-slider.css" type="text/css" media="screen">
<script src="js/tms.js" type="text/javascript"></script>
<script src="js/tms_presets.js" type="text/javascript"></script>
<!--Slider-->

<!--Gant!-->
<link rel="stylesheet" type="text/css" media="all" href="gantt/spb_gantt.css" />
<script src="gantt/spb_gantt.js" type="text/javascript"></script>
<!--Gant!-->

<!--Calendario!

<style type="text/css">
@import url("../css/calendar-win2k-cold-1.css");
</style>
<script language="JavaScript" type="text/javascript" src="../js/calendar.js">	</script>
<script language="JavaScript" type="text/javascript" src="../js/calendar-es.js">	</script>
<script language="JavaScript" type="text/javascript" src="../js/calendar-setup.js">	</script>
<link rel="stylesheet" type="text/css" media="all" href="./calendar/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="./calendar/jsDatePick.jquery.full.1.3.js"></script>
<script type="text/javascript" src="./calendar/jsDatePick.jquery.full.1.3.js"></script>
<!--Calendario!-->

<!--Menú Contextual!-->
<script type="text/javascript" src="jquery/jquery.ui-contextmenu.min.js"></script>
<script type="text/javascript" src="jquery/jquery.ui-contextmenu.js"></script>
<!--Menú Contextual!-->

<!-- Include Fancytree skin and library -->
<link href="fancytree/ui.fancytree.css" rel="stylesheet" type="text/css">
<script src="fancytree/jquery.fancytree.js" type="text/javascript"></script>
<!-- Include Fancytree skin and library -->


<!--SCROLL!-->
<link rel="Stylesheet" type="text/css" href="css/smoothDivScroll.css" />
<!--SCROLL!-->

<!--Uploader-->
<!--<script type="text/javascript" src="jquery/json2.js"></script>-->
<!--<script type="text/javascript" src="jquery/json3.min.js"></script>-->
<!--Uploader-->

<!--Reloj de Arena!-->
<script language="JavaScript" type="text/javascript" src="./js/spin.js"></script>
<!--Reloj de Arena!-->

<!--TinyEditor-->
<link rel="stylesheet" href="tinyeditor/tinyeditor.css">
<script src="tinyeditor/tiny.editor.packed.js"></script>
<script src="tinyeditor/tiny.editor.js"></script>
<!--TinyEditor-->

<!--TinyMCE-->
<script src="tinymce_4.5.4/tinymce.min.js"></script>
<!--TinyMCE-->

<!--Rating!-->
<link rel="stylesheet" href="css/jRating.jquery.css" type="text/css" />
<script type="text/javascript" src="js/jRating.jquery.js"></script>
<!--Rating!-->

<!-- Bootstrap styles -->
<link rel="stylesheet" href="files/css/bootstrap.min.css">
<!-- Bootstrap styles -->

<!-- Generic page styles -->
<link rel="stylesheet" href="files/css/jquery.fileupload-ui.css">
<link rel="stylesheet" href="files/css/jquery.fileupload.css">
<!--Script Generales!-->

<!-- Multiple Select -->
<link href="css/multiple-select.css" rel="stylesheet" />
<script src="jquery/jquery.multiple.select.js"></script>
<!-- Multiple Select -->

<!-- Icono -->
<link rel="shortcut icon" type="image/png" href="imagenes/<?=$favicon?>?v=<?php echo $versionPortal; ?>"></link>
<!-- Icono -->

<!--CSS-->
<link rel="stylesheet" href="css/estilos_<?php echo curPageName(); ?>.css?v=<?php echo $versionPortal; ?>" />
<!--CSS-->

<script language="JavaScript">
// VARIABLES GLOBALES
// Rango de Navision necesario para que pueda entrar en el portal.
var idproceso                         = 9999;
var ambito							  = <?php echo $ambito; ?> ;
var ambito_global				 	  = <?php echo $ambito_global; ?> ;
var OcultarHoraGrids				  = <?php echo $OcultarHoraGrids; ?> ;
var Area							  ="<?php echo $Area; ?>";
var usuariovalidado                   ="<?php echo $usuario; ?>" ;
var departamentousuariovalidado       ="<?php echo $departamento_usuario_validado; ?>" ;
var nombreusuariovalidado             ="<?php echo $nombre_usuario_validado; ?>" ;
var oficinausuariovalidado            ="<?php echo $oficina_usuario_validado; ?>" ;
var acceso_permitido                  = <?php echo $acceso_permitido; ?>;
var evaluador		                  = <?php echo $evaluador; ?>;
var planificador_comun               = <?php echo $planificador_comun; ?>;
var maxfilesize_permitido             ="<?php echo $maxfilesize_permitido; ?>" ;
var portal							  ="<?php echo $NOMBRE_PORTAL; ?>" ;
var nombre_portal					  = "<?php echo $nombre_portal; ?>" ;
var versionPortal					  = <?php echo $versionPortal; ?>;
var versionPortalUsuario			  = <?php echo $versionPortalUsuario; ?>;
var externo							  = "<?php echo $externo; ?>";
var fecha_historial                   = 1;
var num                               = 0;
var filas_historico                   = -1;
var semana                            = 0;
var id_visualizacion                  = -1;
var nota_1                            = '';
var id_nota_1                         = '';
var nota_2                            = '';
var id_nota_2                         = '';
var observaciones_nota_1              = '';
var observaciones_nota_2              = '';
var versionobsoleta                   ="<?php echo $versionobsoleta; ?>" ;
var listacolas                        = [];
var FECHA_HORA_SELECCIONADA           = '';
var ID_COLA_SELECCIONADA              ='-1';
var ID_COLA_USUARIO_SELECCIONADA      ='-1';
var ID_CONSULTA_USUARIO_SELECCIONADA  ='-1';
var ID_SELECCIONADA                   = '-1';
var ID_HORA_SELECCIONADA              = '-2';
var ID_FICHADA_SELECCIONADA           = -1;
var ID_PLAN_SELECCIONADA              = -1;
var refreshIntervalId                 = -1;
var x                                 = -1;
var y                                 = -1;
var drageando                         = false;
var semana_a_mostrar                  = -1;
var ID_HORA_SELECCIONADA_ESTADO       = '-2';
var ID_COLA_USUARIO_SELECCIONADA      = '-1';
var ID_COMENTARIO_SELECCIONADO        = '-2';
var ID_PANTALLA                       =0;
var pdc                               ="";
var ALTO_GRID_HORAS                   =300;
var ALTO_GRID_COMENTARIOS             =300;
var estados                           = [];
var estados_cargados                  = []; 
var lista_opciones_seleccionada       = '1111111';
var tipo_visibilidad_1                = '';
var tipo_visibilidad_2                = '';
var tipo_visibilidad_3                = '';
var tipo_visibilidad_4                = '';
var tipo_visibilidad_5                = '';
var tipo_visibilidad_6                = '';
var tipo_visibilidad_7                = '';
var tipo_visibilidad_8                = '';
var tipo_visibilidad_9                = '';
var tipo_visibilidad_A                = '';
var tipo_visibilidad_A_Descripcion    = '';
var lista_personalizada               = '';
var antesdehistorial                  =-1;
var antesdehistorialOptions           = [];
var editor;	
var $grid_horas_en_un_dia			  = '';
var $GridPlanificacionesTareas    	  = '';
var existe_grid_horas_en_un_dia       = false;
var masivo                            = 0;
var rPP_mio                           = 20;
var palabra_a_buscar                  = '';
// opts spinner
var opts = {
		lines : 12 ,
		length : 0 ,
		width : 40 ,
		radius : 84 ,
		corners : 1 ,
		rotate : 0 ,
		direction : 1 ,
		color : '#3f51b5' ,
		opacity: 0.1,
		speed : 3 ,
		trail : 60 ,
		shadow : true ,
		hwaccel : false ,
		className : 'spinner' ,
		zIndex : 2e9 ,
		top : 'auto' ,
		left : 'auto',
		scale : 1
	};

$(function () { 
	<?php include "./visibilidad/visibilidad_grid.php";?>;
	//alert(<?php //echo " ROL: $Numero_Grid"; ?>);	
	<?php if($Numero_Grid=='1') {// rol 1 ?>
		id_visualizacion = 1;
		Banner(1);
		crearslidesemanal(2);
	<?php } ?>
	<?php if($Numero_Grid=='-1') {// rol -1 ?>	
		id_visualizacion = -1;
		Banner(-1);
	<?php } ?>

	//Prevenimos la activacion del fileupload por drag and drop
	$('body').on('drop', function (e) {
	     return false;
	});



	if( externo == ''){
		
		CargaBannerFrecuentes();
		
		//Comprobamos la version del portal al iniciar el portal, y después cada 30 minutos
		comprobar_version_portal();
		setInterval(function(){ 
			comprobar_version_portal();
		}, 30*60*1000); // 30 minutos
		
		//Actualizar banner de tareas y comprobar si hay cuñas en la semana actual sin fichada para el usuario activo
		actualizar_banner_tareas();
		setInterval(function(){ 
			actualizar_banner_tareas();
		}, 5*61*1000); // 5 minutos y 5 segundos
		//}, 1*5*1000); // 5 segundos

	}	
		

	
	CargaGridTarea('Tareas','grid_array');
	
	CargaGridHorasEnUnDia();

	CargaGridTeletrabajo();
	
	Actualiza_Avisos();

	jQuery.datetimepicker.setLocale('es');
	
	//Menu Desplegable
	//$('.nav li').hover(function () {$('ul', this).fadeIn();},function () {$('ul', this).fadeOut();});
	$('.nav li').hover(function () {$('ul', this).show(0);},function () {$('ul', this).hide(0);});	
	//Reescalar Ventana
	if (versionobsoleta==false)
	{
		$(window).resize(function(){
			$( "#grid_array" ).pqGrid( "option", "width", '100%' );
			$( "#grid_array_comentarios_plegar" ).pqGrid( "option", "width", '100%' );
			$( "#grid_array_horas_plegar" ).pqGrid( "option", "width", '100%' );
			//$( "#grid_array_evaluacion_plegar" ).pqGrid( "option", "width", '100%' );
			$( "#grid_array_resultado" ).pqGrid( "option", "width", '100%' );
			$( "#grid_array_resultado" ).pqGrid( "option", "height", '80%' );
		});	
	}
	//Menu derecho del Portal
	$('#navegador-menu').bind('contextmenu', function(e) 
	{	
		// evito que se ejecute el evento	
		e.preventDefault();	
		// conjunto de acciones a realizar
							
		$( "#dialog-iniciar" ).html(ObtenerMenuContextualNavegador());			
		$( "#dialog-iniciar" ).dialog(
		{
			resizable: false,title: 'Configuracion',width:300,height:300,modal: true,					
			buttons: 
			{
				"Exit": function() 
				{							
					$( this ).dialog( "close" );															
				}
			}				
		});	
	});
	//Acciones especiales
	<?php
	if ( $Numero_Grid == '1' ) {
		?>
		// CARGAMOS MENÚ LATERAL
		$('nav#jqmenu').mmenu();
		
		// ATAJOS

		shortcut.add("Alt+1",function() {
			IrATareaPrefijada(1);
		});
		shortcut.add("Alt+2",function() {
			IrATareaPrefijada(2);
		});
		shortcut.add("Alt+3",function() {
			IrATareaPrefijada(3);
		});
		shortcut.add("Alt+4",function() {
			IrATareaPrefijada(4);
		});
		shortcut.add("Alt+5",function() {
			IrATareaPrefijada(5);
		});
		shortcut.add("Alt+6",function() {
			IrATareaPrefijada(6);
		});
		shortcut.add("Alt+7",function() {
			IrATareaPrefijada(7);
		});
		shortcut.add("Alt+8",function() {
			IrATareaPrefijada(8);
		});
		shortcut.add("Alt+9",function() {
			IrATareaPrefijada(9);
		});
		
		shortcut.add("Alt+0",function() {
			PararTarea( );
		});
	
		
		shortcut.add("F1",function() {
			IniciarLaTarea();
		});
		shortcut.add("F2",function() {
			PararTarea();
		});
		shortcut.add("F3",function() {
			//Buscador();
		});
		shortcut.add("F4",function() {
			irActual(0);
		});

		
	
		if(false){
			//Atajos anteriores
			$(document).keydown(function(event) 
			{
				var minavegador="<?php echo $minavegador; ?>" ;			  
				if (minavegador == "Chrome")
				{
					if (event.keyCode == 117) { // F1					
						IniciarLaTarea();
					}
					if (event.keyCode == 118) { // F2
						PararTarea();
					}
					if (event.keyCode == 119) { // F3
						irActual(0);
					}
					if (event.keyCode == 120) { // F4
						Buscador();
					}
			  }
			  else
			  {
				  if (event.keyCode == 112) { // F1
					IniciarLaTarea();
				  }
				  if (event.keyCode == 113) { // F2
					PararTarea();
				  }
				  if (event.keyCode == 114) { // F3
					irActual(0);
				  }
				  if (event.keyCode == 115) { // F4
					Buscador();
				  }
			  }
			});
		}
	<?php
	}
	?>

	//ID_SELECCIONADA == 30000;
	//cambia_menu(3);

	//jquery.ui tabs
	/*$( "#tabs-evaluacion-masiva" ).tabs({
		  active: 0,
		  collapsible: false,
		  event: "click"
	}); */
	
	//Pedido por Jose Carlos y Carlos, que al abrir se carguen solo las pendientes
	//selecciono_sininiciar('visibilidad_tipo_7_2','2');
});   

</script>
<script>
	
</script>
<script src="js/comprobaciones_<?php echo curPageName(); ?>.js"> </script>
<!--Script Generales!-->
</head>

<!-- Comienza el cuerpo del portal  text-align: center; !-->

<link rel="stylesheet" href="css/estilos_nuevo.css" />
<link rel="stylesheet" href="css/jquery.mmenu.css" />
<script type="text/javascript" src="jquery/jquery.mmenu.js"></script>

<body class="mi-body" style="padding-top: 0px; padding-left: 0px; padding-right: 0px; padding-bottom: 0px;">
<!-- <div style="position:fixed;z-index:9999;top: 0px; left: 20%;width:60%; color: red;background-color:yellow;height:41px;padding:10px;font-weight:bold;text-align:center;">Se ha deshabilitado temporalmente la subida de archivos, disculpe las molestias.</div> -->
	<div id="page">

		<div class="content">
			<div id="contenedor" class="contenedor">
				<!-- Cabecera de login!-->
				<div id="cabecera_titulo_tarea" class="cabecera_titulo_tarea"></div>
				<div id="cabecera_nueva_login" class="cabecera_nueva_login">
					<div id="cabecera_nueva_login_izq" class="cabecera_nueva_login_izq">
						<?php if($externo == ''){ ?><a href="#jqmenu"><span><img src="imagenes/menu-lateral3.png" height="30" /></span></a><?php }?>
						<?php echo /*utf8_decode*/($nombre_portal); ?></div>
					<div id="cabecera_nueva_login_der3" class="cabecera_nueva_login_der3"></div>
					<div id="cabecera_nueva_login_der1" class="cabecera_nueva_login_der1" style="display: none;"></div>
					<div id="cabecera_nueva_login_der2" class="cabecera_nueva_login_der2">
						<span class="boton-login" id="boton-login">
						<?php	if($externo != ''){
									echo "$nombre_usuario_validado (Conectado a departamento $oficina_usuario_validado)";
								} else {
									if($Area != ''){
										echo "$nombre_usuario_validado ($oficina_usuario_validado" . "[$Area])";
									}else{
										echo "$nombre_usuario_validado ($oficina_usuario_validado)";
									} 
								}
						?></span>
					</div>
				</div>
				<!-- Cabecera logo!-->
				<div id="cabecera_nueva" class="cabecera_nueva">
					<div id="cabecera_nueva_izq" class="cabecera_nueva_izq">
						<img src="imagenes/logo_menu.png" />
					</div>
					<div id="cabecera_nueva_der" class="cabecera_nueva_der">
						<h3>LISTA DE TAREAS</h3>
					</div>
				</div>

				<!-- Slide-Semanal -->
				<div id="slide-semanal" class="Resumen Horas Semanales" style="display: none;"></div>
				<!-- Slide-Semanal -->
				
				<!-- Cuerpo de la web!-->
				<div class="cuerpo_nuevo" id="micuerpo">

					<!-- ++ Resumen de Horas!-->		
					<?php if($Numero_Grid=='1')  { ?>
					<div id="resumen_horas" class="resumen_horas_div"></div>
					<div id="resumen_frecuentes" class="resumen_frecuentes_div"></div>
					<div id="resumen_tareas" class="resumen_tareas_div"></div>
					<?php } ?>
					<!-- -- Resumen de Horas!-->

				<!-- Si el usuario no está activo y no entra como externo, se le muestra la lista de posibles deptos -->
				<?php if($usuarioActivo == 0 && $externo == ""){ ?>
					<div style="width: 80%;margin-right: 10%;margin-left: 10%;border: solid 0px;text-align: center;">
						<h3>
							El departamento solicitado no existe o su usuario no est&aacute; activo para ning&uacute;n portal de tareas, a continuaci&oacute;n se muestran los posibles portales de tareas a los que usted puede entrar como invitado:
						</h3>
						<div style="border: solid 0px;text-align: left;display: inline-block;font-size: large;">
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=ADMINISTRACION">Administraci&oacute;n</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=COMERCIAL">Comercial</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=COSTES">Costes</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=COSTE">Coste</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=DG">Direcci&oacute;n general</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=EXPORTACION">Exportaci&oacute;n</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=INNOVACION">Innovaci&oacute;n</a></li>	
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=LABORATORIO">Laboratorio</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=LEAN">Lean manufacturing</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=MANTENIMIENTO">Mantenimiento</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=MATERIALES">Materiales</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=MARKETING">Marketing</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=NDES">Nuevos desarrollos</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=OPERACIONES">Operaciones</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=PEyP">Proyectos y estructuras</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=SAC">Servicio Atenci&oacute;n Cliente</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=SPALEX">Spalex</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=TI">Tecnolog&iacute;as de la informaci&oacute;n</a></li>
							<li><a href="https://tareas.sp-berner.com/menu.php?ext=UNIDAD_DIGITAL">Unidad Digital</a></li>
						</div>
					</div>
				<?php }else{?>
					<!-- MENU DE LA IZQUIERDA!-->
					<div class="cuerpo_nuevo_menu" id="mimenu"> <?php include "./visibilidad/visibilidad_menu.php";	?></div>
					<!-- MENU DE LA IZQUIERDA!-->
					
					<!-- GRID DE TAREAS!-->
					<div class="cuerpo-seccion" id="miseccion">
						<div id="grid_array" style="width: 100%; height: 100%; border-radius: 0px !important;"></div>
					</div>
					<!-- GRID DE TAREAS!-->
				<?php }?>

					<!-- ++ PLANIFICADOR DE TODAS TAREAS !-->
					<div class="cuerpo-seccion" id="miseccionplanificadortareas" style="display: none;">
						<div id="miseccionplanificadortareas_grid_array" style="width: 100%; height: 100%;"></div>
					</div>
					<!-- ++ PLANIFICADOR DE TODAS TAREAS !-->
					
					<!-- ++ GRID ENTRADA CORREO !-->
					<div class="cuerpo-seccion" id="miseccionentradacorreo" style="display: none;">
						<div id="miseccionentradacorreo_grid_array" style="width: 100%; height: 100%;"></div>
					</div>
					<!-- ++ GRID ENTRADA CORREO !-->
				
					<!-- ++ Pantalla de Aprobaciones!-->
					<div class="cuerpo-seccion" id="misecciondatos" style="display: none;"></div>
					<!-- -- Pantalla de Aprobaciones!-->

					<!-- ++ Pantalla de Configuracion!-->
					<div class="cuerpo-seccion" id="miseccionconfiguracion" style="display: none;"></div>
					<!-- -- Pantalla de Configuracion!-->

					<!-- ++ Pantalla de Prefijar Tareas!-->
					<div class="cuerpo-seccion" id="miseccionprefijartareas" style="display: none;"></div>
					<!-- -- Pantalla de Prefijar Tareas!-->

					<!-- ++ Pantalla de ...!-->
					<div class="cuerpo-seccion" id="miseccionplanificador" style="display: none;"></div>
					<!-- -- Pantalla de ...!-->
					
					<!-- ++ POP-UP PLANIFICADOR DE UNA TAREA !-->
					<div class="POPUP" id="miseccionplanificadortarea" style="display: none;">
						<div class="POPUP-TOP">
							<div class="POPUP-TOP-TITLE" id="POPUP-TOP-TITLE"></div>
							<div class="POPUP-TOP-EXIT">
								<a href="#" onClick=" $('#miseccionplanificadortarea').css('display', 'none');"> <img src="imagenes/salir.png" alt="Salir" title="Salir" height="32" width="32" />
								</a>
							</div>
						</div>
						<div class="POPUP-GRID" id="gridPlanificacionTarea"></div>
					</div>
					<!-- ++ POP-UP PLANIFICADOR DE UNA TAREA !-->

					<!-- ++ POP-UP COLABROADOR DE UNA TAREA !-->
					<div class="POPUP" id="miseccioncolaboradortarea" style="display: none;">
						<div class="POPUP-TOP">
							<div class="POPUP-TOP-TITLE" id="POPUP-TOP-TITLE-COLABORA"></div>
							<div class="POPUP-TOP-EXIT">
								<a href="#" onClick=" $('#miseccioncolaboradortarea').css('display', 'none');"> <img src="imagenes/salir.png" alt="Salir" title="Salir" height="32" width="32" />
								</a>
							</div>
						</div>
						<div class="POPUP-GRID" id="gridColaboradorTarea"></div>
					</div>
					<!-- ++ POP-UP COLABORADOR DE UNA TAREA !-->

					<!-- ++ POP-UP DE COMENTARIOS DE TAREA !
					<div class="POPUP" id="miseccioncomentariostarea" style="display: none;">
						<table border="0">
						<tr>
							<td colspan="2">
								<div class="POPUP-TOP">
									<div class="POPUP-TOP-TITLE" id="POPUP-TOP-TITLE-COMENTARIOS"></div>
									<div class="POPUP-TOP-EXIT">
										<a href="#" onClick=" $('#miseccioncomentariostarea').css('display', 'none');"> <img src="imagenes/salir.png" alt="Salir" title="Salir" height="32" width="32" />
										</a>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td width="49%">
								<div class="POPUP-TITLE-SMALL" id="POPUP-TOP-TITLE-COMENTARIOS">Observaciones</div>
								<div class="POPUP-OBSERVACIONES" id="observacionesTarea"></div>
							</td>
							<td width="49%" rowspan="2">
								<div class="POPUP-TITLE-SMALL" id="POPUP-TOP-TITLE-COMENTARIOS">Tiempos y comentarios</div>
								<div class="POPUP-GRID-COMENTARIOS-FICHADAS" id="gridComentariosFichasTarea"></div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="POPUP-TITLE-SMALL" id="POPUP-TOP-TITLE-COMENTARIOS">Comentarios</div>
								<div class="POPUP-GRID-COMENTARIOS" id="gridComentariosTarea"></div>
							</td>
						</tr>	
						</table>
					</div>
					<!-- ++ PLANIFICADOR DE UNA TAREA !-->

					<!-- ++ Pantalla de Evaluaciones usuario !-->
					<div class="cuerpo-seccion" id="miseccionevaluacionesusuarios" style="display: none;">
						<div class="div-eva-masiva">
							<div id="grid_array_evaluacion_usuarios" style="width: 100%; height: 100%;"></div>
						</div>
					</div>			
					<!-- ++ Pantalla de Evaluaciones usuario !-->
					
					<!-- ++ Pantalla de Evaluadores!-->
					<div class="cuerpo-seccion" id="miseccionevaluacionmasiva" style="display: none;">
						<form id="fem" >
							<div class="div-eva-masiva">
								<table border="1" width="1100">
									<colgroup>
										<col span="4">
										<col span="1" style="border-left: solid grey 1px;">
										<col span="1">
									</colgroup>
									<tr>
										<td colspan="4"><h4 id='titulo-eva-masiva'>Evaluaci&oacute;n masiva de tareas</h4></td>
										<td colspan="2"><h4 id='titulo-eva-masiva'>Generar evaluaciones de fichajes diferidos</h4></td>
									</tr>
									</tr>
										<td colspan=2></td>
										<td><label>Oficina: </label></td>
										<td>
											<select id="femOficina" name="femOficina">
												<?php echo $oficina_usuario_validado_options; ?>
											</select>
										</td>
										<td><label>Oficina: </label></td>
										<td>
											<select id="femOficinaDiferida" name="femOficinaDiferida">
												<?php echo $oficina_usuario_validado_options; ?>
											</select>
										</td>
									<tr>
									<tr>
										<td width="140"><label>Desde Fecha: </label></td>
										<td width="210"><input type="text" id="femDesde" name="femDesde" value=""></td>
										<td width="140"><label>Hasta Fecha: </label></td>
										<td width="210"><input type="text" id="femHasta" name="femHasta" value=""></td>
										<td width="140"><label>Fecha Periodo: </label></td>
										<td width="210"><input type="text" id="femFechaPeriodo" name="femFechaPeriodo" value=""></td>
									</tr>
									<tr>
										<td><label>Usuario a evaluar: </label></td>
										<td><input type="text" id="femAutocompleteUsuario" name="femAutocompleteUsuario" ></td>
										<td><label>Nota Evaluador: </label></td>
										<td>
											<select id="femValoracion" name="femValoracion">
												<option value=-1></option>
												<option value=0>0</option>
												<option value=1>1</option>
												<option value=2>2</option>
												<option value=3>3</option>
												<option value=4>4</option>
												<option value=5>5</option>
												<option value=6>6</option>
												<option value=7>7</option>
												<option value=8>8</option>
												<option value=9>9</option>
												<option value=10>10</option>
											</select>
										</td>
										<td><label>Usuario a generar: </label></td>
										<td><input type="text" id="femAutocompleteUsuarioDiferido" name="femAutocompleteUsuarioDiferido" ></td>
									</tr>
									<tr>
										<td colspan="3"><div style="color: red; text-align: center; font-weight: 900; font-size: x-small;">LA EVALUACI&Oacute;N MASIVA<br>NO SOBREESCRIBIR&Aacute; VALORES INDIVIDUALES EXISTENTES</div></td>
										<td colspan="1"><input type="button" id="femBtnCalcular1" value="Ejecutar proceso" onClick="evaluacion_masiva();"></td>
										<td colspan="1"></td>
										<td colspan="1"><input type="button" id="femBtnCalcular2" value="Ejecutar proceso" onClick="genera_evaluaciones_diferidas();"></td>
									</tr>
								</table>
							</div>
						</form>
						<div class="div-eva-masiva">
							<h4>Evaluaci&oacute;n individual de tareas</h4>
							<div id="grid_array_evaluacion_masiva" style="width: 100%; height: 100%;"></div>
						</div>
					</div>
					<!-- -- Pantalla de Evaluadores!-->

					<!-- Pantalla de Gant!-->
					<div class="cuerpo-seccion" id="misecciongant" style="display: none;">
						<div id="spb_gantt_contenedor"></div>
					</div>
					<!-- Pantalla de Gant!-->

					<!-- Pantalla de Lista Menus!-->
					<div class="cuerpo-seccion" id="miseccionlistamenu" style="display: none;"></div>
					<!-- Pantalla de Configuracion!-->

					<!-- Pantalla de Horas en un dia!-->
					<div class="cuerpo-seccion" id="miseccionhorasenundia" style="display: none;">
						<label for="from">Dia</label><input type="text" id="fechacalculohorasenundia" name="fechacalculohorasenundia"> <input type="button" id="btn_calcular_horasenundia" value="Calcular">
						<div id="horasunundia_grid"></div>
					</div>
					<!-- Pantalla de Horas en un dia!-->

					<!-- Pantalla para teletrabajo!-->
				    <div class="cuerpo-seccion" id="miseccionteletrabajo" style="display: none;">
					    <div class="input-block" id="Imputar_teletrabajo">
						<?php include "./teletrabajo/teletrabajo.php";	?>
						   
						</div>
					</div>
					<!-- Pantalla para teletrabajo!-->

					<!-- ++ Pantalla de Masiva!-->
					<div class="cuerpo-seccion" id="misecciondatosmasivo" style="display: none;">
						<div id="la_tarea_masiva" class="cabecera_div"></div>
						<div class="pantalla_pedir" style="text-align: center;">
							<label for="from">Desde</label><input type="text" id="from" name="from" autocomplete="off"> <label for="to">Hasta</label><input type="text" id="to" name="to" autocomplete="off"> <label for="minimo_configurado">Minima porcion de tiempo</label><input type="text" id="minimo_configurado" name="minimo_configurado" value="10">
							<div id="horas_masivas"></div>
							<input type="checkbox" name="masivo_principal" id="masivo_principal">Establecer como tarea principal<br> <input type="button" id="btn_calcular" value="Calcular"><input type="button" id="btn_calcularyejecutar" value="Ejecutar"><input type="button" id="btn_nocalcular" value="Salir">
						</div>
						</br>
						<div id="grid_array_resultado" style="width: 100%; height: 100%; display: none;"></div>
					</div>
					<!-- -- Pantalla de Masiva!-->


					<!-- Pantalla de Informes!-->
					<div class="cuerpo-seccion" id="miseccioninformes" style="display: none;">
						<div class="cabecera_div" id="informe-parametros" onMouseOut="cambiacolororiginal(this)" onMouseOver="cambiacolor(this)" onClick="plegardesplegar_parametroinforme(this)" style="display: none;">
							Solicitud de datos
							<div id="boton_lanza_informes" style="float: right"></div>
						</div>
						<div id="informe-parametros_plegar" class="pantalla_pedir" style="display: none;"></div>
						<div id="informe-mio" style="height: 90%;"></div>
					</div>


				</div>
				<!-- Cuerpo de la web!-->

				<!-- Alertas -->
				<div id="dialog-confirm" title="Confirmar guardar" style="display: none;"></div>
				<div id="dialog-notificar" title="Aviso" style="display: none;"></div>
				<div id="dialog-cancelar" title="Cancelar y Borrar" style="display: none;"></div>
				<div id="dialog-iniciar" title="Iniciar Tarea" style="display: none;"></div>
				<div id="dialog-planificar" title="Planificador de Tarea" style="display: none; z-index: 10; position: relative;"></div>
				<div id="banner" title="Banner" style="display: none;">
					<div id="contenido-banner" title="Contenido Banner"></div>
				</div>
				<!-- Alertas -->

				<!-- GIF Esperando!-->
				<div id="Esperando" style="position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%); display: none; z-index: 10003; border: solid 0px green;">
					<img src='imagenes/working2.gif' style="border: solid 3px #888888; border-radius: 30px; box-shadow: 10px 10px 5px #888888;" />
				</div>
				<!-- GIF Esperando!-->

				<!-- Slide-Historico -->
				<div id="slide-historico" title="Historico" style="display: none;"></div>
				<!-- Slide-Historico -->

				<!-- Pie!-->
				<div class="footer" id="footer">
					<hr class="hr1">
					<hr class="hr2">
					<div id="footer_izq" class="footer_izq">
						<img src="imagenes/logo_pie.png" />
					</div>
					<div id="footer_der" class="footer_der">
						<ul id="pie--menu">
							<div class="float_right" style="text-align: right">
								<!-- <li class="separador"><a href="http://es.sp-berner.com/ver/1912/politica-de-cookies.html">Pol&iacute;tica de cookies</a></li>-->
								<li class="separador"><img src="imagenes/copyright.png" style="width: 10px; height: 10px;" />&nbsp;&nbsp;Sp-Berner Plastic Group</li>
								<li><a href='php/cambios.php' target='_new'>v. <?php echo $versionPortalUsuario; ?></a></li>
							</div>
							<div class="float_right">
								<li class="separador"><a href="https://intranet.sp-berner.com/" target='_new'>Intranet</a></li>
								<li class="separador"><a href="http://es.sp-berner.com/ver/23/empresa.html">Quienes somos</a></li>
								<!-- <li class="separador"><a href="http://es.sp-berner.com/formularios/contacto">Contacto</a></li>  -->
								<li class="separador"><a href="http://es.sp-berner.com/ver/937/Aviso_legal.thtml">Pol&iacute;tica de privacidad</a></li>
								<!--<li class="separador"><a href="http://es.sp-berner.com/suscripciones/nueva">Newsletter</a></li>-->
							</div>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<nav id="jqmenu">
			<ul>
				<!-- Menu Lateral!-->
			<?php include "./visibilidad/visibilidad_menu_lateral.php"; ?>
		<!-- Menu Lateral!-->
			</ul>
		</nav>
	</div>
</body>
</html>