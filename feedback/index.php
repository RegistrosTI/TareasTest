<?php
$mensaje_error = "";

// ++ ***** ERROR SI FALTA EL PARAMETRO *****
if (!isset($_GET['test'])) {
    mostrar_error("Se ha producido un error y no se puede mostrar la valoración solicitada.");
}

$UID = $_GET['test'];

// ++ ***** ERROR SI EL PARAMETRO NO ES CORRECTO*****
if (strlen($_GET['test']) != 36) {
    mostrar_error("El id de la valoración no tiene un formato reconocible.");
}

require_once "../conf/config_filtros.php";
require_once "../php/funciones.php";
require_once "../conf/config.php";
require_once "../conf/config_MENU.php";
require_once "../../soporte/DB.php";
require_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);

$query = "
	SELECT
		count(uid) AS EXISTE
		,Tarea
		,Nombre
		,UsuarioAlta as USUARIOALTA
	FROM [GestionPDT].[dbo].[Feedback_Respuestas]
	where cast(UID as varchar(50)) = '$UID'
	GROUP BY Tarea,Nombre,UsuarioAlta
";
$query = DBSelect(utf8_decode($query));
$EXISTE = 0;
$TAREA = 0;
$NOMBRE = '';
$USUARIOALTA = '';
for (; DBNext($query);) {
    $EXISTE = DBCampo($query, "EXISTE");
    $TAREA = DBCampo($query, "Tarea");
    $NOMBRE = utf8_encode(DBCampo($query, "Nombre"));
    $USUARIOALTA = utf8_encode(DBCampo($query, "USUARIOALTA"));

    $u = "SELECT TOP 1 Name FROM GestionIDM.dbo.LDAP WHERE sAMAccountName = '$USUARIOALTA'";
    $u = DBSelect(utf8_decode($u));
    DBNext($u);
    $USUARIOALTA = utf8_encode(DBCampo($u, "Name"));
}

if ($EXISTE == 0) {
    mostrar_error("No se ha encontrado la valoración solicitada...<br><br>Puede que exista un correo con una solicitud posterior a la actual.");
}

$query = "
	SELECT
		CAST(Título AS VARCHAR(250))			AS TITULO
		,CAST(Solicitado AS VARCHAR(250))		AS SOLICITADO
		,CAST([Asignado a] AS VARCHAR(250))		AS ASIGNADO
		,CAST(Tipo AS VARCHAR(250))				AS TIPO
		,CAST(CosteTarea AS VARCHAR(250))		AS COSTE
		,CAST((CONVERT(varchar(20), [Fecha alta],103)) as varchar) 	AS FECHA
		,(SELECT SUM(Minutos) FROM Horas WHERE Tarea = $TAREA) AS MINUTOS
		,(SELECT CAST ( ( SUM(Minutos) / 60) AS VARCHAR(8)) + ':' +  CAST ( (SUM(Minutos) % 60)   AS VARCHAR(2))
			FROM Horas WHERE Tarea = $TAREA AND Minutos IS NOT NULL) AS TIEMPO
	FROM [Tareas y Proyectos] WHERE Id = $TAREA
";
$query = DBSelect(utf8_decode($query));
DBNext($query);
$TITULO = utf8_encode(DBCampo($query, "TITULO"));
$ASIGNADO = utf8_encode(DBCampo($query, "ASIGNADO"));
$TIPO = utf8_encode(DBCampo($query, "TIPO"));
$COSTE = utf8_encode(DBCampo($query, "COSTE"));
$FECHA = utf8_encode(DBCampo($query, "FECHA"));
$TIEMPO = utf8_encode(DBCampo($query, "TIEMPO"));

// ** FORMULARIO DE LAS PREGUNTAS
$query = "
	SELECT
		TipoFeedback
		,Id
		,Pregunta
		,ISNULL(Respuesta,'') AS Respuesta
		,ISNULL(CAST((CONVERT(varchar(20), FechaRespuesta,103)) as varchar),'') 	AS FechaRespuesta
		,ISNULL(UsuarioRespuesta,'') AS UsuarioRespuesta
		,ISNULL(UsuarioObservaciones,'') AS UsuarioObservaciones
	FROM Feedback_Respuestas
	WHERE UID = '$UID'
	ORDER BY TipoFeedback DESC
";
$query = DBSelect(utf8_decode($query));
$formulario_preguntas = "
	<div class='formulario'>
	<form id='formulario_preguntas'>
	<table class='tabla'>
		<tr>
			<th width='33%'>Pregunta</th>
			<th width='150px'>Valoración</th>
			<th width='*'>Observaciones</th>
		</tr>";
$ESTA_TODO_VALORADO = 0;
for (; DBNext($query);) {
    $TipoFeedback = utf8_encode(DBCampo($query, "TipoFeedback"));
    $Id = utf8_encode(DBCampo($query, "Id"));
    $Pregunta = utf8_encode(DBCampo($query, "Pregunta"));
    $Respuesta = utf8_encode(DBCampo($query, "Respuesta"));
    $FechaRespuesta = utf8_encode(DBCampo($query, "FechaRespuesta"));
    $UsuarioRespuesta = utf8_encode(DBCampo($query, "UsuarioRespuesta"));
    $UsuarioObservaciones = utf8_encode(DBCampo($query, "UsuarioObservaciones"));

    if ($UsuarioRespuesta == '') {
        $ESTA_TODO_VALORADO++;
    }

    $formulario_preguntas .= "<tr style='background-color: white;'>";
    $formulario_preguntas .= "<td>$Pregunta</td>";
    if ($TipoFeedback == 'Valoración') {
        $formulario_preguntas .= "<td><div class='rating' data-average='$Respuesta' data-id='$Id' id='ESTRELLA_$Id'></td>";
        $formulario_preguntas .= "<input type='hidden' id='HIDDENINPUT_$Id' value='$Respuesta'/>";
        $formulario_preguntas .= "<td><textarea rows='2' id='OBSERVACIONES_$Id' style='width:100%'>$UsuarioObservaciones</textarea></td>";
    } else {
        $formulario_preguntas .= "<td colspan='2'><textarea rows='2' id='OBSERVACIONES_$Id' class='obligado' style='width:100%'>$UsuarioObservaciones</textarea></td>";
        $formulario_preguntas .= "<input type='hidden' id='HIDDENINPUT_$Id' value='-1'/>";
    }
    $formulario_preguntas .= "</tr>";
}
$formulario_preguntas .= "</table>";

if ($ESTA_TODO_VALORADO > 0) {
    $formulario_preguntas .= '<input type="button" name="btn_valorar" id="btn_valorar" value="Valorar" onClick="valorar();">';
} else {
    $formulario_preguntas .= '<div style="clear: both;" class="leyenda_valoracion">Valorado el ' . $FechaRespuesta . '</div>';
    $formulario_preguntas .= '<div style="clear: both;" class="leyenda_valoracion">Por el usuario ' . $UsuarioRespuesta . '</div>';
}

$formulario_preguntas .= "</form></div>";
function mostrar_error($mensaje)
{
    $mensaje = utf8_decode($mensaje);
    echo "
		<div
			style='box-shadow: 20px 30px #888888;
			padding: 50px;font-weight: bold;
			font-size: 20pt;
			margin: 100px;
			text-align:center;
			border: solid 5px black;
			background-color: #3f51b5;
			color: white;
			border-radius: 40px;
			font-family: Arial, Helvetica, sans-serif;'> $mensaje
		</div>
	";
    die();
}
?>
<html>
<head>
<!-- <meta charset="ISO-8859-1"> -->
<meta charset="UTF-8">
<title>Feedback de Tarea <?php echo $TAREA; ?></title>
<link rel="shortcut icon" type="image/png" href="../imagenes/feedback_con.png">
<!--jQuery dependencies-->
<link rel="stylesheet" href="../css/jquery-ui-1.10.4.custom.min.css" />
<script src="../jquery/jquery.min.js"></script>
<script src="../jquery/jquery-ui.js"></script>

<!--jQuery dependencies-->
<!--Rating!-->
<link rel="stylesheet" href="../css/jRating.jquery.css" type="text/css" />
<script type="text/javascript" src="../js/jRating.jquery.js"></script>
<!--Rating!-->
<script>

	function valorar() {
		//Comprobar respuestas de valoración
		var enviar = true;
		$.each($("input[type='hidden']"), function (i, obj) {
			if ($(this).val() == 0){
				enviar = false;
			}
		});
		if(!enviar){
			mensaje('Error','Has de valorar todas las preguntas de 1 a 5 estrellas.');
		}

		//Comprobar respuestas de desarrollar
		if(enviar){
			$('.obligado').each(function(i, obj) {
				$(this).css('background-color', '#fff');
				if ($(this).val() == ''){
					enviar = false;
					$(this).css('background-color', '#ffcccc');
				}
			});
			if(!enviar){
				mensaje('Error','Las preguntas que no son de valorar son obligadas.');
			}
		}

		if (enviar){

			var respuestas = [];
			$.each($("input[type='hidden']"), function (i, obj) {
				var id_seleccionado = obj.id.split('_');
				id_seleccionado = id_seleccionado[1];
				//alert(id_seleccionado);
				respuestas.push({
					Id: id_seleccionado,
					Respuesta: $(this).val() ,
					UsuarioObservaciones: $("#OBSERVACIONES_"+id_seleccionado).val()
				});
			});

			$.ajax({
				url: "modificarRespuestas.php?respuestas=" + JSON.stringify(respuestas)
			}).done(function(data) {
				location.reload();
			});
		}
	}
	/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
	 * @author alberto
	 * @date 24/05/2017
	 * @description
	 * @param titulo
	 * @param mensaje
	 * @param [icono]
	 */
	function mensaje(titulo , mensaje, icono) {

		if (icono == undefined){
			icono = "user.png";
		}
		$( "#dialog-notificar" ).attr( 'title' , titulo );
		$( "#dialog-notificar" ).html( '<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='../imagenes/" + icono + "' width=\"30\" height=\"30\" />&nbsp;&nbsp;&nbsp;" + mensaje + '</p>' );
		$( "#dialog-notificar" ).dialog( {
			title : titulo,
			resizable : false ,
			height : "auto" ,
			width : 500 ,
			modal : true ,
			buttons : {
				"Cerrar" : function() {
					$( this ).dialog( "close" );
				}
			}
		} );
	}
	$(function () {
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
			onClick : function(element,rate){
				// ++ Asigno el valor a cada input hidden
				var id_seleccionado = element.id.split('_');
				id_seleccionado = id_seleccionado[1];
				$("#HIDDENINPUT_"+id_seleccionado).val(rate);
				// -- Asigno el valor a cada input hidden

				var elementos_pulsados = element.id;
				var elemento_pulsado = elementos_pulsados.split('_');
				if (elemento_pulsado[1] == '1')	{
					nota_1 = elemento_pulsado[2];
					id_nota_1 = rate;

				}
				if (elemento_pulsado[1] == '2'){
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
		});
	});
</script>
<style type="text/css">
textarea {
    resize: none;
}
.formulario {
	background-color: #eee;
	color: #eee;
	margin: 20px 40px 20px 40px;
	/*padding: 40px;*/
}

.tabla {
	border: #ccc solid 0px;
	width: 100%;
	padding: 10px;
}
.tabla td{
	padding: 10px;
}

.titulo_tarea {
	font-size: 18px;
	cursor: pointer;
}

.div_aviso {
	font-size: 12px;
}

.importe_horas {
	font-size: 20px;
	color: red;
}

.tag_importe_horas {
	font-size: 18px;
}

.slide-blanco-antes {
	float: left;
	width: 20px;
	padding-bottom: 1px;
	cursor: pointer;
}

.slide-blanco-despues {
	float: left;
	width: 20px;
	padding-bottom: 1px;
	cursor: pointer;
}

.slide-antes {
	float: left;
	width: 20px;
	padding-bottom: 1px;
	font-size: 10px;
	text-align: center;
	color: #999;
}

.slide-despues {
	float: left;
	width: 20px;
	padding-bottom: 1px;
	font-size: 10px;
	text-align: center;
	color: #999;
}

.slide-horas {
	float: left;
	width: 90px;
	padding-bottom: 1px;
	font-size: 24px;
	text-align: center;
	color: #aaa;
	cursor: pointer;
}

.slide-dias {
	float: left;
	width: 90px;
	padding-bottom: 1px;
	text-align: center;
	font-size: 24px;
	color: #777;
}

.slide-dias-fiesta {
	float: left;
	width: 90px;
	padding-bottom: 1px;
	font-size: 24px;
	text-align: center;
	color: red;
}

.PLEGAR_AVISO {
	overflow: hidden;
}

.plegalble {
	overflow: hidden;
}

.la-web {}

.cabecera_div {
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
	text-decoration: none; solid #bbb;
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

.mi-body {
	margin: auto;
}

.tabla-popup {
	width: 330px;
	height: 180px font-size:12px;
	margin: 10px;
}

.tool {
	font-size: 12px;
	vertical-align: middle;
	color: #222;
}

.div-tools {
	float: right;
}

.tool-mio {
	font-size: 12px;
	vertical-align: middle;
	color: red;
}

a img {
	border: none;
}

.navigation {
	margin: auto;
	position: relative;
	width: 95%;
	height: 45px;
	padding-top: 1px;
	background-color: rgb(68, 68, 68);
}

.cabecera {
	background-image: url('imagenes/logo.jpg');
	background-position: center;
	background-repeat: no-repeat;
	width: 95%;
	height: 100px;
	margin-left: auto;
	margin-right: auto;
	background-color: #000;
}

.fadebox {
	position: absolute;
	top: 0%;
	left: 0%;
	width: 100%;
	height: 2000px;
	background-color: black;
	z-index: 1001;
	-moz-opacity: 0.4;
	opacity: .40;
	filter: alpha(opacity = 40);
}

.cabecera-usuario {
	margin-left: auto;
	width: 200px;
	text-align: right;
	padding-right: 10px;
	font-size: 12px;
}

input {
	font-size: 12px;
}

body {
	font-family: "Franklin Gothic Medium", Franklin, sans-serif;
}

.cuerpo {
	width: 95%;
	margin-left: auto;
	margin-right: auto;
	padding: 0px;
}

.cabecera-menu {
	width: 1000px;
	margin-left: auto;
	margin-right: auto;
	padding: 0px;
}

.plegador {
	cursor: pointer;
	border-style: solid;
	border-color: #808080;
	border-width: 0.2px;
	background-color: #E6E6E6;
	font-size: 20px;
	width: 100%;
}

.div_valoracion_plegador {
	border-style: solid;
	border-color: #808080;
	border-width: 0.2px;
	background-color: #E6E6E6;
	font-size: 20px;
	width: 100%;
}

.cuerpo-menu {
	width: 150px;
	padding: 0px;
	float: left;
	font-size: 12px;
	margin-left: auto;
	margin-right: auto;
}

.pantalla_pedir {
	border-style: solid;
	background-color: #aaa;
	border-color: gray;
	width: 100%;
	height: 120px;
	border-width: 0.2px;
	font-size: 11px;
	padding-top: 12px;
}

.cuerpo-menu-opcion {
	border-bottom: 1px dashed black;
	list-style-type: none;
	padding-top: 0px;
}

.cuerpo-seccion {
	height: auto;
	font-size: 11px;
	width: 95% px;
	padding: 0px;
}

.pie {
	border-top: 2px solid black;
	width: 100%;
	margin-left: auto;
	margin-right: auto;
	padding-top: 0px;
	background-color: #444;
	color: #fff;
	clear: both;
	margin-top: 0px;
}

.lista-pedidos {
	font-size: 12px;
	margin-left: auto;
	margin-right: auto;
	border: 0px;
	padding: 0px;
}

tr.lista-pedidos {
	border: 0px;
	padding: 0px;
	margin: 0px;
	background-color: #AAA;
}

.columna-calendario {
	width: 20px;
	background-color: #AAA;
	text-align: center;
}

.ocupado {
	background-color: #F00;
}

.libre {
	background-color: #0F0;
}

.plegable {
	width: 100%;
	height: 350px;
	overflow: hidden;
}

.div_valoracion_plegar {
	width: 100%;
	height: auto;
	overflow: hidden;
}

.plegableconfiguracion {
	width: 100%;
	height: 350px;
	overflow: hidden;
}

.tabla-litle {
	font-size: 10px;
	width: 680px;
}

.tabla-adjuntar {
	font-size: 12px;
}

.tabla-normal {
	font-size: 12px;
	width: 100%;
}

.tabla-parametros {
	font-size: 12px;
}

.leyenda_valoracion {
	color: green;
	font-size: 10px;
	text-align: center;
}

.tabla-normal-observaciones {
	font-size: 12px;
	width: 100%;
	height: 100%;
}

.tabla-medium {
	font-size: 12px;
	width: 680px;
}

.tabla-morelitle {
	font-size: 8px;
	width: 100px;
}
</style>
</head>

<link rel="stylesheet" href="../css/estilos_nuevo.css" />
<body class="mi-body" style="padding-top: 0px; padding-left: 0px; padding-right: 0px; padding-bottom: 0px;">
	<div id="contenedor" class="contenedor">
		<!-- Cabecera de login!-->
		<div id="cabecera_nueva_login" class="cabecera_nueva_login">
			<div id="cabecera_nueva_login_izq" class="cabecera_nueva_login_izq">PORTAL DE TAREAS</div>
			<div id="cabecera_nueva_login_der3" class="cabecera_nueva_login_der3"></div>
			<div id="cabecera_nueva_login_der1" class="cabecera_nueva_login_der1" style="display: none;"></div>
			<div id="cabecera_nueva_login_der2" class="cabecera_nueva_login_der2">
				<span class="boton-login" id="boton-login" style="height: 20px !important">Solicitud feddback para	<?php	echo $NOMBRE; ?></span>
			</div>
		</div>
		<!-- Cabecera logo!-->
		<div id="cabecera_nueva" class="cabecera_nueva" style="background-color: #ddd !important;">
			<div id="cabecera_nueva_izq" class="cabecera_nueva_izq">
				<a href="#" title="Volver a INTRANET"><img src="../imagenes/logo_menu.png" /></a>
			</div>
			<div id="cabecera_nueva_der" class="cabecera_nueva_der">
				<h3><?php echo "$TAREA - $TITULO"; ?></h3>
			</div>
		</div>
		<!-- Cuerpo de la web!-->
		<div class="cuerpo_nuevo" id="micuerpo">
			<div id="cabecera_nueva" class="cabecera_nueva" style="background-color: #eee !important; height: 140px;">
				<div id="cabecera_nueva_izq" class="cabecera_nueva_izq" >
					<table>
						<tr>
							<td>Fecha:</td>
							<td><?php echo "$FECHA"; ?></td>
						</tr>
						<tr>
							<td>Responsable:</td>
							<td><?php echo "$ASIGNADO"; ?></td>
						</tr>
						<tr>
							<td>Tipo de tarea:</td>
							<td><?php echo "$TIPO"; ?></td>
						</tr>
						<tr>
							<td>Tiempo empleado:</td>
							<td><?php echo "$TIEMPO"; ?></td>
						</tr>
						<tr>
							<td>Feedback solicitado por:</td>
							<td><?php echo "$USUARIOALTA"; ?></td>
						</tr>
					</table>
				</div>
			</div>
			<div>
				<?php echo $formulario_preguntas; ?>
			</div>
		</div>
		<!-- Cuerpo de la web!-->

		<!-- Alertas -->
		<div id="dialog-confirm" title="Confirmar guardar" style="display: none;"></div>
		<div id="dialog-notificar" title="Aviso" style="display: none;"></div>
		<div id="dialog-cancelar" title="Cancelar y Borrar" style="display: none;"></div>
		<div id="dialog-iniciar" title="Iniciar Tarea" style="display: none;"></div>
	</div>
	<!-- Alertas -->

	<!-- Pie!-->
	<div class="footer" id="footer">
		<hr class="hr1">
		<hr class="hr2">
		<div id="footer_izq" class="footer_izq">
			<img src="../imagenes/logo_pie.png" />
		</div>
		<div id="footer_der" class="footer_der">
			<ul id="pie--menu">
				<div class="float_right" style="text-align: right">
					<li><a href="http://es.sp-berner.com/ver/1912/politica-de-cookies.html">Pol&iacute;tica de cookies</a></li>
					<!--<li class="separador"><a href="http://es.sp-berner.com/suscripciones/nueva">Newsletter</a></li>-->
					<li><img src="../imagenes/copyright.png" style="width: 10px; height: 10px;" />&nbsp;&nbsp;Sp-Berner Plastic Group</li>
				</div>
				<div class="float_right">
					<li><a href="http://es.sp-berner.com/ver/23/empresa.html">Quienes somos</a></li>
					<!-- <li class="separador"><a href="http://es.sp-berner.com/formularios/contacto">Contacto</a></li>  -->
					<li><a href="http://es.sp-berner.com/ver/937/Aviso_legal.thtml">Pol&iacute;tica de privacidad</a></li>
				</div>
			</ul>
		</div>
	</div>
	</div>
</body>