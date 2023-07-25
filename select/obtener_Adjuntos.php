<?php
// UTF ñáç
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

// RECIBIMOS PARAMETROS
// 1 SI EXISTE EN LA TABLA USUARIOS
if ( isset ( $_GET [ 'id' ] ) ) {
	$id_pantalla = $_GET [ 'id' ];
} else {
	$id_pantalla = '-1';
}

if ( isset ( $_GET [ 'incidencia' ] ) ) {
	$incidencia = $_GET [ 'incidencia' ];
} else {
	$incidencia = '-1';
}

if ( isset ( $_GET [ 'usuario' ] ) ) {
	$usuario = $_GET [ 'usuario' ];
} else {
	$usuario = '';
}
// FIN RECIBIMOS PARAMETROS

$Registros = 0;
if ( $id_pantalla == 1 ) {
	$multiportal_rol = '1';
	$readonly = '';
	$disabled = '';
}
if ( $id_pantalla ==  - 1 ) {
	$multiportal_rol = '-1';
	$readonly = ' readonly="readonly" ';
	$disabled = ' disabled="disabled" ';
}

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

Get_Adjuntos($id_pantalla,$incidencia);




function Get_Adjuntos( $id_pantalla , $incidencia ) {
	$devolver = '';
	$maxfilesize_permitido = ini_get ( 'upload_max_filesize' );
	$contador_de_tablas = 1;
	$contador_de_filas = 1;
	$ExistenFicheros = 0;
	$tablaficheros = '';
	$tablaficheros1 = '';
	$tablaficheros2 = '';
	$tablaficheros3 = '';
	$tablaficheros4 = '';
	$automatico= '';
	
	if ( $id_pantalla == 1 ) {
		$u = DBSelect ( ( "SELECT [Tipo],[Numero],Publico ,[Tarea],CAST(Descripcion as varchar(250)) as Descripcion,CAST(Fichero as varchar(250)) as Fichero, usuario as Usuario, CONVERT(varchar(50),Fecha,103) as Fecha FROM [Adjuntos] where Tarea = " . $incidencia . "" ) );
		
		$e = DBSelect (("SELECT  COUNT(Numero) as c FROM [Adjuntos] where Tarea = " . $incidencia . "" ));
		$ExistenFicheros = ( DBCampo ( $e , "c" ) );
		
	} else {
		$u = DBSelect ( ( "SELECT [Tipo],[Numero],Publico ,[Tarea],CAST(Descripcion as varchar(250)) as Descripcion,CAST(Fichero as varchar(250)) as Fichero, usuario as Usuario, CONVERT(varchar(50),Fecha,103) as Fecha FROM [Adjuntos] where Tarea = " . $incidencia . " and Publico IN (1,2) " ) );
	   
		$e = DBSelect ( ( "SELECT  COUNT(Numero) as c FROM [Adjuntos] where Tarea = " . $incidencia . " and Publico IN (1,2) " ) );
		$ExistenFicheros = ( DBCampo ( $e , "c" ) );
		
	}
	
	if ($ExistenFicheros > 0 ){
		$tablaficheros1 = $tablaficheros1 . '<tr style="border: 2px solid #a59e9e; font-size: 15px;"><td style="width: 5%; padding-left:4px;"> Estado </td><td style="width: 60%;"> Archivo </td><td style="width: 25%;"> Subido </td><td style="width: 10%;"> Fecha Subida </td></tr>';
	}
	
	for(; DBNext ( $u ) ;) {
		$contador_de_filas = $contador_de_filas + 1;
		$Publico = ( DBCampo ( $u , ( "Publico" ) ) );
		$FicheroNumero = ( DBCampo ( $u , ( "Numero" ) ) );
		$Tipo = ( DBCampo ( $u , ( "Tipo" ) ) );
		$FicheroTarea = ( DBCampo ( $u , "Tarea" ) );
		$FicheroDescripcion = ( DBCampo ( $u , "Descripcion" ) );
		$FicheroCarpeta = ( DBCampo ( $u , "Fichero" ) );
		$UsuarioFichero = ( DBCampo ( $u , "Usuario" ) );
		$FechaSubida = ( DBCampo ( $u , "Fecha" ) );
		
		$r = DBSelect (("SELECT Name as Nombre FROM [GestionIDM].[dbo].[LDAP] WHERE sAMAccountName ='". $UsuarioFichero ."'"));
		$UsuarioNombre = ( DBCampo ( $r, "Nombre" ) );
		
		if ( $id_pantalla == 1 ) {
			if ( $Publico == '1' ) {
				$imagen_archivo = '<img src="./imagenes/transparent.png" width="20" name="IMAGEN_FICHERO_' . $FicheroNumero . '" id="IMAGEN_FICHERO_' . $FicheroNumero . '"></img>';
			} else {
				if ( $Publico == '2' ) {
					$imagen_archivo = '<img src="./imagenes/share.png" width="20" name="IMAGEN_FICHERO_' . $FicheroNumero . '" id="IMAGEN_FICHERO_' . $FicheroNumero . '"></img>';
				} else {
					$imagen_archivo = '<img src="./imagenes/private.png" width="20" name="IMAGEN_FICHERO_' . $FicheroNumero . '" id="IMAGEN_FICHERO_' . $FicheroNumero . '"></img>';
				}
			}
		} else {
			if ( $Publico == '2' ) {
				$imagen_archivo = '<img src="./imagenes/share.png" width="20" name="IMAGEN_FICHERO_' . $FicheroNumero . '" id="IMAGEN_FICHERO_' . $FicheroNumero . '"></img>';
			} else {
				$imagen_archivo = '<img src="./imagenes/transparent.png" width="20" name="IMAGEN_FICHERO_' . $FicheroNumero . '" id="IMAGEN_FICHERO_' . $FicheroNumero . '"></img>';
			}
		}
		
		$imagen_archivo_tipo = '<img src="./imagenes/' . $Tipo . '.png" width="20" name="IMAGEN_FICHERO_TIPO_' . $FicheroNumero . '" id="IMAGEN_FICHERO_TIPO_' . $FicheroNumero . '"></img>';
		
		if ($FicheroDescripcion == 'CorreoOriginal.eml') {
			$automatico ='(Automático)';	
		}else{
			$automatico ='';
		}
		
		$tablaficheros1 = $tablaficheros1 . '<tr style="border: 1px solid #a59e9e; padding: 1px 2px;"  id="anadido_imagen_' . $contador_de_filas . '">
																			<td style="width: 5%;">' . $imagen_archivo . $imagen_archivo_tipo . '</td>
																			<td style="width: 60%;"><a style="cursor: pointer;" class="ver_fichero" id="FICHERO_' . $FicheroNumero . '" href="select/archivo.php?id=' . $FicheroNumero . '">' . utf8_encode ( $FicheroDescripcion ) . '</td>
																			<td style="width: 25%;">'.utf8_encode ( $UsuarioNombre ).' '.$automatico.'</td>
																			<td style="width: 10%;">'.$FechaSubida.'</td>
																	</tr>';
	}
	DBFree ( $u );
	$devolver = $devolver . '<br>';
	//$devolver = $devolver . '<span class="btn btn-success fileinput-button"><i class="glyphicon glyphicon-plus"></i><span>Archivo...</span><input id="fileupload" type="file" name="files[]" multiple></span>    <br> ' . $maxfilesize_permitido . ' Máximo permitido.	<br>    <div id="progress" class="progress">        <div class="progress-bar progress-bar-success"></div>    </div>        <div id="files" class="files"></div>';
	$devolver = $devolver . '<div style="float:left; width: 100%;" id="adjuntosdiv">';
	$devolver = $devolver . '<table style ="width: 97%;margin: 0% 1.5%;" class="tabla-adjuntar" id="tablaadjuntos1">';
	$devolver = $devolver . $tablaficheros1;
	$devolver = $devolver . '</table>';
	$devolver = $devolver . '</div>';
	
	echo  $devolver;

}
?>
