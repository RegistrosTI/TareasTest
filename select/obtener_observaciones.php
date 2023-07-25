<?php
// include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$id		= $_POST [ 'id' ];
$observaciones	= "<FONT COLOR='RED'><B>LA TAREA NO TIENE OBSERVACIONES</B></FONT>";

$q = "
	SELECT 
		COALESCE([Descripción],'')	AS Observaciones 
		,Categoría					AS Categoria
		,Tipo						AS Tipo
		,Solicitado					AS Solicitado
		,CAST((CONVERT(varchar(20), [Fecha alta],103)) as varchar)		AS FechaAlta
		,CAST((CONVERT(varchar(20), [Fecha objetivo],103)) as varchar)	AS FechaObjetivo
		,Prioridad					AS Prioridad
		,Usuario					AS Usuario
		,[Asignado a]				AS Asignado
	FROM [GestionPDT].[dbo].[Tareas y Proyectos] 
	WHERE ID = $id AND control = 0 	
";
$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$Observaciones = (html_entity_decode(utf8_encode ( DBCampo ( $q , utf8_decode ( "Observaciones" ) ) ) ));
	$Categoria = utf8_encode ( DBCampo ( $q , utf8_decode ( "Categoria" ) ) ) ;
	$Tipo = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tipo" ) ) ) ;
	$Solicitado = utf8_encode ( DBCampo ( $q , utf8_decode ( "Solicitado" ) ) ) ;
	$FechaAlta = utf8_encode ( DBCampo ( $q , utf8_decode ( "FechaAlta" ) ) ) ;
	$FechaObjetivo = utf8_encode ( DBCampo ( $q , utf8_decode ( "FechaObjetivo" ) ) ) ;
	$Prioridad = utf8_encode ( DBCampo ( $q , utf8_decode ( "Prioridad" ) ) ) ;
	$Usuario = utf8_encode ( DBCampo ( $q , utf8_decode ( "Usuario" ) ) ) ;
	$Asignado = utf8_encode ( DBCampo ( $q , utf8_decode ( "Asignado" ) ) ) ;
}
DBFree ( $q );





$tablaDatosTarea = "
	<table width='100%' style='font-family: Lucida Console;margin-top:5px;'>
		<tr>
			<td width='50%'><span style='font-weight:bold;'>Solicitado:</span> <span style='color:navy;'>$Solicitado</span></td>
			<td width='50%'><span style='font-weight:bold;'>Prioridad:</span> <span style='color:navy;'>$Prioridad</span></td>
		</tr>
		<tr>
			<td width='50%'><span style='font-weight:bold;'>Alta:</span> <span style='color:navy;'>$Usuario</span></td>
			<td width='50%'><span style='font-weight:bold;'>Fecha Alta:</span> <span style='color:navy;'>$FechaAlta</span></td>
		</tr>
		<tr>
			<td width='50%'><span style='font-weight:bold;'>Asignado:</span> <span style='color:navy;'>$Asignado</span></td>
			<td width='50%'><span style='font-weight:bold;'>Fecha Objetivo:</span> <span style='color:navy;'>$FechaObjetivo</span></td>
		</tr>
		<tr>
			<td width='50%'><span style='font-weight:bold;'>Tipo:</span> <span style='color:navy;'>$Tipo</span></td>
			<td width='50%'><span style='font-weight:bold;'>Categoría:</span> <span style='color:navy;'>$Categoria</span></td>
		</tr>
	</table>

";

/***** ADJUNTOS ******/
$tablaAdjuntos = Get_Adjuntos(1, $id);
/***** ADJUNTOS ******/

DBClose ();
echo "$Observaciones@@@$tablaDatosTarea@@@$tablaAdjuntos";


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
		}
		
		$tablaficheros1 = $tablaficheros1 . '<tr style="border: 1px solid #a59e9e; padding: 1px 2px;"  id="anadido_imagen_' . $contador_de_filas . '">
																			<td style="width: 5%;">' . $imagen_archivo . $imagen_archivo_tipo . '</td>
																			<td style="width: 60%;"><a style="cursor: pointer;" class="ver_fichero" id="FICHERO_' . $FicheroNumero . '" href="select/archivo.php?id=' . $FicheroNumero . '">' . utf8_encode ( $FicheroDescripcion ) . '</td>
																			<td style="width: 25%;">'.utf8_encode ( $UsuarioNombre ).' '.$automatico.'</td>
																			<td style="width: 10%;">'.$FechaSubida.'</td>
																	</tr>';
	}
	
	DBFree ( $u );
	
	$devolver = $devolver . '<div style="float:left; width: 100%;" id="adjuntosdiv">';
	$devolver = $devolver . '<table style ="width: 97%;margin: 0% 1.5%;" class="tabla-adjuntar" id="tablaadjuntos1">';
	$devolver = $devolver . $tablaficheros1;
	$devolver = $devolver . '</table>';
	$devolver = $devolver . '</div>';
	return $devolver;
}
?>