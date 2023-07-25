<?php
$usuario = $_SERVER['REMOTE_USER'];
$envio   = $_GET['envio'];
$portal  = $_GET['portal'];

switch($portal){
	case "CONTABILIDAD":
		$nombre_portal = "GESTI&Oacute;N TAREAS &Aacute;REA CONTABLE";
		break;
	case "gestioncalidad":
		$nombre_portal = "GESTI&Oacute;N TAREAS CALIDAD";
		break;
	case "gestionlean":
		$nombre_portal = "GESTI&Oacute;N TAREAS LEAN";
		break;
	case "gestionproyectos":
		$nombre_portal = "GESTI&Oacute;N TAREAS PROYECTOS";
		break;
	case "gestionproyectosestructuras":
		$nombre_portal = "GESTI&Oacute;N TAREAS PROYECTOS-ESTRUCTURAS-PACKAGING";
		break;
	case "GESTIONTI":
		$nombre_portal = "GESTI&Oacute;N DE PROYECTOS, MEJORAS E INCIDENCIAS TI";
		break;
	default:
		$nombre_portal = "GESTI&Oacute;N DE TAREAS";
}
?>


<html>
	<head>
		<meta charset="ISO-8859-1">
		<title>Valoracion de Tareas</title>
		<style type="text/css">		
		.titulo_tarea
		{
			font-size: 18px;
			cursor: pointer; 
		}
		.div_aviso
		{
			font-size: 12px;
		}
		.importe_horas
		{
			font-size: 20px;
			color: red;
		}
		.tag_importe_horas
		{
			font-size: 18px;			
		}
		.slide-blanco-antes
		{
			float: left;
			width: 20px;
			padding-bottom: 1px;			
			cursor: pointer; 
		}
		.slide-blanco-despues
		{
			float: left;
			width: 20px;
			padding-bottom: 1px;			
			cursor: pointer; 
		}
		.slide-antes
		{
			float: left;
			width: 20px;
			padding-bottom: 1px;			
			font-size: 10px;
			text-align:center;
			color: #999;
		}
		.slide-despues
		{
			float: left;
			width: 20px;
			padding-bottom: 1px;			
			font-size: 10px;
			text-align:center;
			color: #999;
		}
		.slide-horas
		{
			float: left;
			width: 90px;
			padding-bottom: 1px;			
			font-size: 24px;
			text-align:center;
			color: #aaa;
			cursor: pointer; 
		}
		.slide-dias
		{
			float: left;
			width: 90px;
			padding-bottom: 1px;	
			text-align:center;			
			font-size: 24px;
			color: #777;
		}
		.slide-dias-fiesta
		{
			float: left;
			width: 90px;
			padding-bottom: 1px;			
			font-size: 24px;
			text-align:center;
			color: red;
		}
		.PLEGAR_AVISO{
		overflow:hidden;	
		}
		.plegalble{
		overflow:hidden;	
		}
		.la-web{

		}
		.cabecera_div
		{
			border-style: solid; 
			border-color: gray; 
			width: 100%; 
			height: 30px; 
			border-width: 0.2px; 
			background-color: #E6E6E6; 
			font-size: 20px;
		}
		.nav {
			margin: 0px;
			padding: 0px;
			list-style: none;
		}

		.nav li {
			float: left;
			width: 160px;
			position: relative;
		}

		.nav li a {
			background: #444;
			
			display: block;
			padding: 7px 8px;
			text-decoration: none;
			solid #bbb;
			height: 30px;
		}

		.nav li a:hover {
			color: #069;
		}

		.nav ul {
			display: none;
			position: absolute;
			margin-left: 0px;
			list-style: none;
			padding: 0px;
		}

		.nav ul li {
			width: 160px;
			float: left;
		}

		.nav ul a {
			display: block;
			padding: 7px 8px;
			color: #fff;
			text-decoration: none;
			border-bottom: 1px solid #222;
		}

		.nav ul li a:hover {
			color: #069;
		}
		.mi-body
		{
			margin: auto;
		}
		.tabla-popup
		{
			width: 330px; height: 180px
			font-size:12px;	
			
			margin:10px;		
		}
		.tool
		{
			
			font-size:12px;	
			vertical-align: middle;
			color: #222;
		}
		.div-tools
		{
			float:right;
		}
		.tool-mio
		{
			
			font-size:12px;	
			vertical-align: middle;
			color: red;
		}
		a img { border: none; }
		.navigation
		{
		    margin: auto; 
			
			position: relative; 
			width: 95%; 
			height: 45px; 
			padding-top: 1px; 
			background-color: rgb(68, 68, 68);
		}
		.cabecera
		{
			background-image:url('imagenes/logo.jpg');
			background-position:center;
			background-repeat:no-repeat;
			width:95%;
			height:100px;
			margin-left:auto;
			margin-right:auto;	
			background-color:#000;
		}
		.fadebox 
		 {
		  position: absolute;
		  top: 0%;
		  left: 0%;
		  width: 100%;
		  height: 2000px;
		  background-color: black;
		  z-index:1001;
		  -moz-opacity: 0.4;
		  opacity:.40;
		  filter: alpha(opacity=40);
		}
		.cabecera-usuario
		{
			margin-left:auto;
			width:200px;
			text-align:right;
			padding-right:10px;
			font-size:12px;
		}
		input
		{
			font-size:12px;
		}
		body
		{
			font-family:"Franklin Gothic Medium", Franklin, sans-serif;
		}
		.cuerpo
		{
			width:95%;
			margin-left:auto;
			margin-right:auto;	
			padding:0px;
		}
		.cabecera-menu
		{		
			width:1000px;		
			margin-left:auto;
			margin-right:auto;	
			padding:0px;
		}
		.plegador
		{
			cursor: pointer; 
			border-style: solid; 
			border-color: #808080;
			border-width: 0.2px; 
			background-color: #E6E6E6; 
			font-size: 20px;
			width:100%;
		}
		.div_valoracion_plegador
		{
			
			border-style: solid; 
			border-color: #808080;
			border-width: 0.2px; 
			background-color: #E6E6E6; 
			font-size: 20px;
			width:100%;
		}
		.cuerpo-menu
		{
			width:150px;
			padding:0px;
			float:left;
			font-size:12px;
			margin-left:auto;
			margin-right:auto;	

			
		}
		.pantalla_pedir
		{
			border-style: solid; 
			background-color: #aaa; 
			border-color: gray; 
			width: 100%; 
			height: 120px; 
			border-width: 0.2px; 
			font-size: 11px;
			
			padding-top:12px;
		}
		
		.cuerpo-menu-opcion
		{
			border-bottom: 1px dashed black;
			list-style-type: none;
			padding-top:0px;
		}
		.cuerpo-seccion
		{
			
			height:auto;
			font-size:11px;
			width:95%px;
			padding:0px;
		}
		.pie
		{
			border-top: 2px solid black;
			width:100%;
			margin-left:auto;
			margin-right:auto;	
			padding-top:0px;
			background-color:#444;
			color:#fff;
			
			clear:both;
			margin-top:0px;
		}
		.lista-pedidos
		{
			font-size:12px;
			margin-left:auto;
			margin-right:auto;
			border:0px;
			padding:0px;
		}
		tr.lista-pedidos
		{
			border:0px;
			padding:0px;
			margin:0px;
			background-color:#AAA;
		}
		.columna-calendario
		{
			width:20px;
			background-color:#AAA;
			text-align:center;
		}
		.ocupado
		{
			background-color:#F00;
		}
		.libre
		{
			background-color:#0F0;
		}
		.plegable
		{
			width: 100%; 
			height:350px;
			overflow:hidden;
		}
		.div_valoracion_plegar
		{
			width: 100%; 
			height:auto;
			overflow:hidden;
		}
		.plegableconfiguracion
		{
			width: 100%; 
			height:350px;
			overflow:hidden;
		}
		.tabla-litle
		{
			font-size:10px;
			width:680px;
		}
		.tabla-adjuntar
		{
			font-size:12px;
			
		}
		.tabla-normal
		{
			font-size:12px;
			width:100%;
			
		}
		.tabla-parametros
		{
			font-size:12px;
			
			
		}
		.leyenda_valoracion
		{
			font-size: 10px;
			text-align: center;
		}
		.tabla-normal-observaciones
		{
			font-size:12px;
			width:100%;
			height:100%;
		}
		.tabla-medium
		{
			font-size:12px;
			width:680px;
		}
		.tabla-morelitle
		{
			font-size:8px;
			width:100px;
		}	
		</style>

		<!--Uploader--> 
		<script src="uploader/upload.js"></script>
		<!--Uploader--> 

		<!--TinyEditor--> 
		<link rel="stylesheet" href="tinyeditor/tinyeditor.css">
		<script src="tinyeditor/tiny.editor.packed.js"></script>
		<!--TinyEditor--> 

		<!--jQuery dependencies--> 
		<link rel="stylesheet"     href="css/jquery-ui-1.10.4.custom.min.css" /> 
		<script src="js/funciones.js"></script>    		
		<script src="jquery/jquery.min.js"></script>  
		<script src="jquery/jquery-ui.min.js"></script>  
		<!--<script type="text/css" src="jquery/themes/Office/pqgrid.css" ></script> -->
		<!--jQuery dependencies--> 

		<!--Include Touch Punch file to provide support for touch devices--> 
		<script type="text/javascript" src="jquery/jquery.ui.touch-punch.js" ></script>     
		<!--Include Touch Punch file to provide support for touch devices--> 

		<!--ParamQuery Grid files--> 
		<link rel="stylesheet" href="jquery/themes/Office/pqgrid.dev.css" /> 
		<script type="text/javascript" src="jquery/pqgrid.min.js" ></script>  
		<!--ParamQuery Grid files--> 

		<!--Calendario!-->
		<style type="text/css">@import url("../css/calendar-win2k-cold-1.css");</style>
		<script language="JavaScript" type="text/javascript" src="../js/calendar.js">	</script>
		<script language="JavaScript" type="text/javascript" src="../js/calendar-es.js">	</script>
		<script language="JavaScript" type="text/javascript" src="../js/calendar-setup.js">	</script>
		<link rel="stylesheet" type="text/css" media="all" href="./calendar/jsDatePick_ltr.min.css" />
		
		<script type="text/javascript" src="./calendar/jsDatePick.jquery.min.1.3.js"></script>
		<script type="text/javascript" src="./calendar/jsDatePick.jquery.min.1.3.js"></script>
		<script type="text/javascript" src="jquery/jquery.ui-contextmenu.min.js"></script>
		<script type="text/javascript" src="jquery/jquery.ui-contextmenu.js"></script>
		<script type="text/javascript" src="jquery/jquery-ui-timepicker-addon.js"></script>
		
		<!--Rating!-->
		<link rel="stylesheet" href="css/jRating.jquery.css" type="text/css" />
		<script type="text/javascript" src="js/jRating.jquery.js"></script>
		<!--Rating!-->	
		
		<link rel="shortcut icon" type="image/png" href="imagenes/icon_2.png"></link>
		<!--Calendario!-->

		<!--Reloj de Arena!-->
		<script language="JavaScript" type="text/javascript" src="./js/spin.js"></script>
		<!--Reloj de Arena!-->

		<!-- Bootstrap styles -->
		<link rel="stylesheet" href="files/css/bootstrap.min.css">
		<!-- Bootstrap styles -->
		
		<!-- Generic page styles -->
		<link rel="stylesheet" href="files/css/style.css">
		<link rel="stylesheet" href="files/css/jquery.fileupload.css">
		<!--Script Generales!-->

		<!-- Multiple Select -->
		<link href="css/multiple-select.css" rel="stylesheet"/>
		<script src="jquery/jquery.multiple.select.js"></script>
		<!-- Multiple Select -->
		
		<script language="JavaScript">		
		var idproceso            = 9999;
		var usuariovalidado      ="<?php echo $usuario; ?>" ;
		var envio                ="<?php echo $envio; ?>" ;		
		var nota_1               = '';
		var id_nota_1            = '';
		var nota_2               = '';
		var id_nota_2            = '';
		var observaciones_nota_1 = '';
		var observaciones_nota_2 = '';		
		var portal				 ="<?php echo $portal; ?>" ;		
		</script>
		<!--Script Generales!-->

		
		<!--Script para el GRID!-->
		<script>
		
		</script>  
		<!--Script para el GRID!-->
		
		<script>
		$(function () 
		{ 
			var sndisable=true;
			if ($('#btn_valorar').length){
				sndisable= false;
			}

			$(".rating").jRating({
			step:true,
			rateMax:5,
			isDisabled:sndisable,
			length:5,
			sendRequest:false,
			canRateAgain:true,
			nbRates:99999999,
			CustomLeyend: ["Nada satisfecho","Poco satisfecho","Medianamente satisfecho","Bastante satisfecho","Muy satisfecho"],
			onClick : function(element,rate)
			{							
				var elementos_pulsados = element.id;
				var elemento_pulsado = elementos_pulsados.split('_');
				if (elemento_pulsado[1] == '1')
				{
					nota_1 = elemento_pulsado[2];
					id_nota_1 = rate;
					
				}
				if (elemento_pulsado[1] == '2')
				{
					nota_2 = elemento_pulsado[2];
					id_nota_2 = rate;
					
				}
				
			},
	        onSuccess : function(){
				alert('Success : your rate has been saved :)');
			},
			onError : function(){
				alert('Error : please retry');
			}
			}
			);
		});

		function valorar()
		{


			observaciones_nota_1 = $('#OBSERVACIONESESTRELLA_1').val();
			observaciones_nota_2 = $('#OBSERVACIONESESTRELLA_2').val();
			if (nota_1=='' || nota_2=='' || id_nota_1=='' || id_nota_2== '')
			{
				alert('Puntue los 2 conceptos');
			}
			else
			{
				if (parseInt(id_nota_1)<4 && observaciones_nota_1=='')
				{
					alert('Es obligado que introduzca una observacion en Calidad en el trabajo');
				}
				else
				{			
					if (parseInt(id_nota_2)<4 && observaciones_nota_2=='')
					{
						alert('Es obligado que introduzca una observacion en Enfoque al cliente');
					}
					else
					{
						var url = 'valoracion/modificarvaloracion.php?usuario='+encodeURIComponent(usuariovalidado)+"&nota_1="+nota_1+"&nota_2="+nota_2+"&id_nota_1="+id_nota_1+"&id_nota_2="+id_nota_2+"&observaciones_nota_1="+encodeURIComponent(observaciones_nota_1)+"&observaciones_nota_2="+encodeURIComponent(observaciones_nota_2)+"&portal="+encodeURIComponent(portal);
						$.ajax({ url: url }).done(function()
						{
							dialogo("Gracias por su valoración");
							
							location.reload();

						});

							
					}
				}
			}	
			
		}
		</script>  
	</head>

<!-- Comienza el cuerpo del portal  text-align: center; !-->
<?php if ($_SERVER['REMOTE_ADDR'] != '10.5.30.19x'){ // Nuevo diseño?>
<link rel="stylesheet" href="css/estilos_nuevo.css" /> 
<body class="mi-body" style="padding-top: 0px; padding-left: 0px; padding-right: 0px; padding-bottom: 0px;">
	<div id="contenedor" class="contenedor">
		<!-- Cabecera de login!-->
		<div id="cabecera_nueva_login" class="cabecera_nueva_login">
			<div id="cabecera_nueva_login_izq" class="cabecera_nueva_login_izq"><?php echo $nombre_portal; ?></div>
			<div id="cabecera_nueva_login_der3" class="cabecera_nueva_login_der3"></div>
			<div id="cabecera_nueva_login_der1" class="cabecera_nueva_login_der1" style="display: none;"></div>
			<div id="cabecera_nueva_login_der2" class="cabecera_nueva_login_der2">
				
			</div>
		</div>
		<!-- Cabecera logo!-->
		<div id="cabecera_nueva" class="cabecera_nueva">
			<div id="cabecera_nueva_izq" class="cabecera_nueva_izq">
				<a href="#" title="Volver a INTRANET"><img src="imagenes/logo_menu.png" /></a>
			</div>
			<div id="cabecera_nueva_der" class="cabecera_nueva_der">
				<h3>VALORACI&OacuteN DE TAREAS</h3>
			</div>
		</div>
		<!-- Cuerpo de la web!-->
		<div class="cuerpo_nuevo" id="micuerpo">
							<?php	
					require "./valoracion/titulo_valoracion.php";
					// devuelve: '<div style="clear: both;"  class="div_valoracion_plegador" id="PLEGADOR_VALORACION_-1">Nº TAREA - TITULO TAREA</div>';
					require "./valoracion/valoracion.php";
					?>			
		</div>
		<!-- Cuerpo de la web!-->		
		
		<!-- Alertas -->
		<div id="dialog-confirm" title="Confirmar guardar" style="display:none;"></div>
		<div id="dialog-notificar" title="Aviso" style="display:none;"></div>
		<div id="dialog-cancelar" title="Cancelar y Borrar" style="display:none;"></div>
		<div id="dialog-iniciar" title="Iniciar Tarea" style="display:none;"></div>
		<div id="banner" title="Banner" style="display:none;"><div id="contenido-banner" title="Contenido Banner"></div>						
		</div>
		<!-- Alertas -->
		

		<!-- Slide-Historico -->
		<div id="slide-historico" title="Historico" style="display:none;">						
		</div>
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
						<li class="separador"><a href="http://es.sp-berner.com/ver/1912/politica-de-cookies.html">Pol&iacute;tica de cookies</a></li>
						<!--<li class="separador"><a href="http://es.sp-berner.com/suscripciones/nueva">Newsletter</a></li>-->
						<li><img src="imagenes/copyright.png" style="width: 10px; height: 10px;" />&nbsp;&nbsp;Sp-Berner Plastic Group</li>
					</div>
					<div class="float_right">
						<li class="separador"><a href="http://es.sp-berner.com/ver/23/empresa.html">Quienes somos</a></li>
						<!-- <li class="separador"><a href="http://es.sp-berner.com/formularios/contacto">Contacto</a></li>  -->
						<li class="separador"><a href="http://es.sp-berner.com/ver/937/Aviso_legal.thtml">Pol&iacute;tica de privacidad</a></li>
					</div>
				</ul>
			</div>
		</div>				
	</div>
</body>
<?php }else{ // Viejo diseño?>	

	<body class="mi-body" style="padding-top:0px;padding-left:0px;padding-right:0px;padding-bottom:0px; ">	
		<div class="la-web">
		
			<!-- Cabecera con el logotipo!-->
			<div class="cabecera" style="position: relative;">
				<div style="background-color: transparent;float: right;bottom: 0px;padding-top: 85px; Color:white; font-size:11px"><b style="color: red;">Login:</b><?=$usuario?>
				</div>
			</div>
			<!-- Cabecera con el logotipo!-->
			
		
						
			<!-- Alertas -->
			<div id="dialog-confirm" title="Confirmar guardar" style="display:none;">				
			</div>
			<div id="dialog-cancelar" title="Cancelar y Borrar" style="display:none;">				
			</div>
			<div id="dialog-iniciar" title="Iniciar Tarea" style="display:none;">						
			</div>
			<!-- Alertas -->
			
						
			<!-- Cuerpo de la web!-->
			<div class="cuerpo" id="micuerpo">

					<?php	
					require "./valoracion/titulo_valoracion.php";
					// devuelve: '<div style="clear: both;"  class="div_valoracion_plegador" id="PLEGADOR_VALORACION_-1">Nº TAREA - TITULO TAREA</div>';
					require "./valoracion/valoracion.php";
					?>			
				<!-- Pantalla de Aprobaciones!-->		
				<div class="cuerpo-seccion" id="misecciondatos" style="display:none;">						
				</div>		
				<!-- Pantalla de Aprobaciones!-->		

				

				
				<!-- Pantalla de Pie!-->
				<div  class="pie" style="text-align:center"><img src="imagenes/copyright.png" style="width:15px;">Sp-Berner Plastic Group S.L.</div>	
				<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
				<script src="files/js/vendor/jquery.ui.widget.js"></script>
				<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
				<script src="files/js/jquery.iframe-transport.js"></script>
				<!-- The basic File Upload plugin -->
				<script src="files/js/jquery.fileupload.js"></script>				
			</div>
			<!-- Cuerpo de la web!-->
			
		</div>
	</body>
<?php } ?>	
</html>
