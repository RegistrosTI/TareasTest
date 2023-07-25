<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";


if ( isset ( $_GET [ 'usuario' ] ) ) {
	$usuario = $_GET [ 'usuario' ];
} else {
	$usuario = '';
}
$contador_tipo_a = 0;
$con = 0;

DBConectar($GLOBALS["cfg_DataBase"]);

$query = "SELECT oficina FROM Configuracion_Usuarios WHERE usuario = '$usuario'";
$query=DBSelect(utf8_decode($query));
for(;DBNext($query);)	{
	$oficinas=utf8_encode(DBCampo($query,"oficina"));
}
DBFree($query);
$oficinas = explode(',', $oficinas);
$oficina = $oficinas[0];

$parametros='<table class="tabla-parametros" id="tabla-parametros">';
$parametros = $parametros . "<tr><th WIDTH='*'>PARÁMETRO</th><th WIDTH='250'>VALOR</th><th WIDTH='45'>UNIDAD</th><th WIDTH='*'>PARÁMETRO</th><th WIDTH='250'>VALOR</th><th WIDTH='45'>UNIDAD</th></tr>";
$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_CONFIGURACION] '$usuario','$oficina' "));
for(; DBNext ( $insert ) ;) {
	$contador_tipo_a = $contador_tipo_a + 1;
	$TIPO = DBCampo ( $insert , "Tipo" );
	$TD = DBCampo ( $insert , "td" );
	$TR = DBCampo ( $insert , "tr" );
	$NUMERO = DBCampo ( $insert , "Numero" );
	$DESCRIPCION = utf8_encode( DBCampo ( $insert , ( "Descripcion" ) ) );
	$PREDETERMINADO = DBCampo ( $insert , "Predeterminado" );
	$PENDIENTE = DBCampo ( $insert , "pendiente" );
	$EXPLICACION = utf8_encode( DBCampo ( $insert , ( "Explicacion" ) ) );
	$VALOR = utf8_encode( DBCampo ( $insert , ( "Valor" ) ) );
	$UNIDAD = utf8_encode( DBCampo ( $insert , ( "Unidad" ) ) );
	$USUARIO = utf8_encode( DBCampo ( $insert , ( "Usuario" ) ) );
	
	if($USUARIO == ''){
		$EXPLICACION = $EXPLICACION . '*';
	}
	
	if ( $TIPO == '-1' ) {
		
 		if ( $con % 2 == 0) {
 			$parametros = $parametros . '<tr>';
 		}
 		
		$parametros = $parametros . "<td title='$EXPLICACION'>$EXPLICACION</td><td><input type='text' align='center' style='width:250px;' id='$DESCRIPCION' onChange='cambiaparametro(this)' value='$VALOR'/></td><td style='font-size:10px;'>$UNIDAD</td>";
 		if ( $con % 2 != 0 ) {
 			$parametros = $parametros . '</tr>';
 		}
 		
 		$con++;
	}
}

if ( $con % 2 != 0 ) {
	$parametros = $parametros . '<td></td><td></td><td></td></tr>';
}

$parametros=$parametros.'</table>';

if($oficina == 'TI'){
	$parametros=$parametros.'<small>(*) Parámetros globales.</small>';
}

echo '<div id="configuracion_parametros" onMouseOut="cambiacolororiginal(this)" onMouseOver="cambiacolor(this)" onClick="plegardesplegarconfiguracion(this)" class="plegador">Configuración</div>';		
echo '<div class="plegableconfiguracion" id="configuracion_parametros_plegar" >';
echo "<div id='div-parametros' class='div-parametros'>$parametros</div>";
echo '</div>';
echo '<div id="configuracion_consultas" onMouseOut="cambiacolororiginal(this)" onMouseOver="cambiacolor(this)" onClick="plegardesplegarconfiguracion(this)" class="plegador">Consultas Personalizadas</div>';		
echo '<div class="plegableconfiguracion" id="configuracion_consultas_plegar" >';
echo '</div>';

echo "<div id='configuracion_feedback' onMouseOut='cambiacolororiginal(this)' onMouseOver='cambiacolor(this)' onClick='plegardesplegarconfiguracion(this)' class='plegador'>Feedback</div>";
echo "<div id='configuracion_feedback_plegar' class='plegableconfiguracion'></div>";
if($oficina == 'TI'){
	echo "<div id='configuracion_actividades' onMouseOut='cambiacolororiginal(this)' onMouseOver='cambiacolor(this)' onClick='plegardesplegarconfiguracion(this)' class='plegador'>Actividades</div>";
	echo "<div id='configuracion_actividades_plegar' class='plegableconfiguracion'></div>";
	echo "<div id='configuracion_avisos' onMouseOut='cambiacolororiginal(this)' onMouseOver='cambiacolor(this)' onClick='plegardesplegarconfiguracion(this)' class='plegador'>Avisos</div>";		
	echo "<div id='configuracion_avisos_plegar' class='plegableconfiguracion' > </div>";	
	echo "<div id='configuracion_colas' onMouseOut='cambiacolororiginal(this)' onMouseOver='cambiacolor(this)' onClick='plegardesplegarconfiguracion(this)' class='plegador'>Colas</div>";		
	echo "<div id='configuracion_colas_plegar'   class='plegableconfiguracion' style='float: left;'></div>";
	echo "<div id='configuracion_colas_plegar_2' class='plegableconfiguracion' style='float: right;padding-left: 5px;'></div>";
	echo "<script type='text/pq-template' id='tmpl'><div id='tabs-2'></div></script>";
}
echo '<div style="clear: both;"><br><input type="button" id="btn_salir_configuracion" value="Salir"></div>';

DBClose();
?>