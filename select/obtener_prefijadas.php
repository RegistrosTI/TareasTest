<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

if ( isset ( $_GET [ 'usuario' ] ) ) {
	$usuario = $_GET [ 'usuario' ];
}

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$select = "select count(*) as predefinidas FROM [GestionPDT].[dbo].[Tareas_Predefinidas] where usuario = '$usuario' ";
$select = DBSelect ( utf8_decode ( $select ) );
$predefinidas = DBCampo ( $select , "predefinidas" );
DBFree ( $select );
// Si no tiene predefinidas, las creamos
if ( $predefinidas == 0 ) {
	$select = "";
	for( $contador = 1 ; $contador < 10 ; $contador ++ ) {
		$select = $select . "insert into [GestionPDT].[dbo].[Tareas_Predefinidas]( usuario,numero,titulo,alias,tarea) values ('$usuario',$contador,'','',0) ;";
	}
	$select = DBSelect ( utf8_decode ( $select ) );
	DBFree ( $select );
}

$select = "SELECT [usuario],[numero],COALESCE([tarea],0) AS tarea,titulo,alias FROM [GestionPDT].[dbo].[Tareas_Predefinidas]  where usuario = '$usuario' ORDER BY NUMERO; ";
$select = DBSelect ( utf8_decode ( $select ) );

$respuesta = '<table class="tabla-prefijadas" id="tabla-prefijadas" border="0">';
$respuesta = $respuesta . "<tr><th style='width:45px;text-align: center;'>Atajo</th><th style='width:45px;text-align: center;'>Tarea</th><th style='width:500px;text-align: center;'>Descripción</th><th style='width:45px;text-align: center;'>Alias</th></tr>";
$contador = 1;
for(; DBNext ( $select ) ;) {
	$respuesta = $respuesta . "<td style='width:45px;text-align: center;'>Alt + $contador</td>";
	$TITULO = utf8_encode ( DBCampo ( $select , "titulo" ) );
	$TAREA = utf8_encode ( DBCampo ( $select , "tarea" ) );
	$ALIAS = utf8_encode ( DBCampo ( $select , "alias" ) );
	if ( $TAREA == 0 ) {
		$respuesta = $respuesta . "<td></td>";
	} else {
		$respuesta = $respuesta . "<td><input type='text' style='width:45px;text-align: right;' value='$TAREA' disabled ='disabled'/></td>";
	}
	$respuesta = $respuesta . "<td><input type='text' style='width:500px;text-align: left;' id='autocompleteprefijada_$contador' name='autocompleteprefijada_$contador' onChange='modificar_tarea_prefijada(this,\"$usuario\");' value='$TITULO'/></td>";
	if ( $TAREA == 0 ) {
		$respuesta = $respuesta . "<td></td>";
	} else {
		$respuesta = $respuesta . "<td><input type='text' style='width:30px;text-align: left;' id='alias_$contador' name='alias_$contador' maxlength='3' onChange='modificar_alias_tarea_prefijada(this,\"$usuario\");' value='$ALIAS' /></td>";
	}
	$respuesta = $respuesta . "</tr>";
	$contador ++;
}
DBFree ( $select );

$respuesta = $respuesta . "<tr><td>F1</td>";
$respuesta = $respuesta . "<td></td>";
$respuesta = $respuesta . "<td><input type='text' style='width:500px;text-align: left;' id='tareaprefijada_0' value='INICIAR TAREA ACTUAL' disabled ='disabled'/></td>";
$respuesta = $respuesta . "</tr>";

$respuesta = $respuesta . "<tr><td>F2</td>";
$respuesta = $respuesta . "<td></td>";
$respuesta = $respuesta . "<td><input type='text' style='width:500px;text-align: left;' id='tareaprefijada_0' value='FINALIZAR TAREA ACTUAL' disabled ='disabled'/></td>";
$respuesta = $respuesta . "</tr>";

$respuesta = $respuesta . "<tr><td>F4</td>";
$respuesta = $respuesta . "<td></td>";
$respuesta = $respuesta . "<td><input type='text' style='width:500px;text-align: left;' id='tareaprefijada_0' value='ABRIR TAREA ACTUAL' disabled ='disabled'/></td>";
$respuesta = $respuesta . "</tr>";

$respuesta = $respuesta . '</table>';

echo $respuesta;
DBClose ();
?>