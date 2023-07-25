<?php

if (!isset($portal)) {
	$portal = $_GET['portal'];
}

if ( file_exists ( "./conf/config.php" ) ) {
	include_once "./php/funciones.php";
	include_once "./conf/config.php";
	//include_once "./conf/config_" . curPageName () . ".php";
	include_once "./conf/config_$portal.php";
	include_once "./../soporte/DB.php";
	include_once "./../soporte/funcionesgenerales.php";
} else {
	include_once "../php/funciones.php";
	include_once "../conf/config.php";
	//include_once "../conf/config_" . curPageName () . ".php";
	include_once "../conf/config_$portal.php";
	include_once "../../soporte/DB.php";
	include_once "../../soporte/funcionesgenerales.php";
}

$envio = $_GET [ 'envio' ];
$contador = 0;
$disabled = '';
$Descripcion = '';
$ALGUNA_ESTA_VALORADO = '0';

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$insert = "
		SELECT CAST(ISNULL(Valoraciones.Observacion,'') AS VARCHAR(250)) AS OBSERVACION
			,Valoraciones.Concepto AS CONCEPTO
			,CONVERT(varchar(250),ISNULL(Valoraciones.Fecha,''),109) AS FECHA
			,CAST(ISNULL((	SELECT TOP 1 Mail.Nombre 
							FROM [Mail] AS Mail 
							WHERE Mail.Envio = Valoraciones.Envio AND Mail.Id = Valoraciones.Usuario),'') as varchar(250)) AS USUARIO
			,Valoraciones.Numero AS ID
			,ISNULL(Valoraciones.Valoracion,-1) AS NOTA
			,Concepto.Descripcion AS VALORACION
			,CAST(ISNULL(Tipo.Descripcion,'') AS VARCHAR(250)) AS NOTA_DESCRIPCION
		FROM [Valoraciones] AS Valoraciones 
			LEFT JOIN [Concepto_Tipo_Valoraciones] AS Concepto 
			ON Concepto.Numero = Valoraciones.Concepto 
			LEFT JOIN [Tipo_Valoraciones] AS Tipo 
			ON Tipo.Tipo = Concepto.Numero AND Tipo.Id = Valoraciones.Valoracion where Envio = $envio and Concepto.Activa = 1";

$insert = DBSelect ( utf8_decode ( $insert ) );
for(; DBNext ( $insert ) ;) {
	$contador = $contador + 1;
	$ID = ( DBCampo ( $insert , "ID" ) );
	$CONCEPTO = ( DBCampo ( $insert , "CONCEPTO" ) );
	$NOTA = ( DBCampo ( $insert , "NOTA" ) );
	$VALORACION = ( DBCampo ( $insert , "VALORACION" ) );
	$NOTA_DESCRIPCION = ( DBCampo ( $insert , "NOTA_DESCRIPCION" ) );
	$USUARIO_NOMBRE = /*utf8_encode*/ ( DBCampo ( $insert , "USUARIO" ) );
	$OBSERVACION = utf8_encode ( DBCampo ( $insert , "OBSERVACION" ) );
	$FECHA = utf8_encode ( DBCampo ( $insert , "FECHA" ) );
	if ( $NOTA != '-1' ) {
		$ALGUNA_ESTA_VALORADO = '1';
		$disabled = 'disabled';
	}
	$Descripcion = $Descripcion . '<div style="clear: both;"  class="div_valoracion_plegador" id="PLEGADOR_VALORACION_' . $VALORACION . '">' . $VALORACION . '</div><div class="PLEGAR_VALORACION" id="PLEGADOR_VALORACION_' . $VALORACION . '_PLEGAR">';
	$Descripcion = $Descripcion . '<div  style="clear: both;" title="' . $NOTA_DESCRIPCION . '" class="div_valoracion_plegar"><div class="rating" data-average="' . $NOTA . '" data-id="' . $ID . '" id="ESTRELLA_' . $CONCEPTO . '_' . $ID . '"></div></div>';
	$Descripcion = $Descripcion . '<div style="clear: both;"  id="ETIQUETAESTRELLA_' . $CONCEPTO . '_' . $ID . '">Observaciones de la nota:</div>';
	$Descripcion = $Descripcion . '<div style="clear: both;"><input type="text" style="width:100%;font-size: 12;" ' . $disabled . ' name="OBSERVACIONESESTRELLA_' . $CONCEPTO . '" id="OBSERVACIONESESTRELLA_' . $CONCEPTO . '" value="' . $OBSERVACION . '"></div>';
	$Descripcion = $Descripcion . '</div>';
	
}

echo $Descripcion;
if ( $ALGUNA_ESTA_VALORADO == "0" ) {
	echo '<input type="button" name="btn_valorar" id="btn_valorar" value="Valorar" onClick="valorar();">';
} else {
	echo '<div style="clear: both;" class="leyenda_valoracion">Valorado en ' . $FECHA . '</div>';
	echo '<div style="clear: both;" class="leyenda_valoracion">Por el usuario ' . $USUARIO_NOMBRE . '</div>';
}

DBFree ( $insert );
DBClose ();
?>