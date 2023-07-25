<?php
// UTF8 ñáç
/**
 * ***********************************************************
 * AUTOR: Alberto Ruiz
 * FECHA: 25/05/2020
 * DESC: ENVIO DE ALERTAS POR CORREO
 * ***********************************************************
 */

// ****************************************************INCLUDES NECESARIOS
if ( 1 == 1 ) {
	$GLOBALS [ "cfg_HostDB" ] = 'SQL2016';
	//$GLOBALS [ "cfg_HostDB" ] = 'SQL2016-EXPRESS\SQL2016EXPRESS';
	$GLOBALS [ "cfg_UserDB" ] = 'gestionti';
	$GLOBALS [ "cfg_PassDB" ] = 'gestionti';
	$GLOBALS [ "cfg_DataBase" ] = 'GestionPDT';
	
	
	include "../../soporte/DB.php";
	require "../conf/config.php";
	include "../../soporte/funcionesgenerales.php";
	
	// incluimos configuracion de correo
	require "../conf/mail.php";
	

}
// ****************************************************INCLUDES NECESARIOS

// ****************************************************INICIO
DBConectar ( $GLOBALS [ "cfg_DataBase" ] );
// ****************************************************INICIO

// ******************************************************************************************************** VARIABLES

$insert = '';

// BUSCAMOS LOS USUARIO QUE LES TENGA QUE LLEGAR EL MENSAJE
$query = "
SELECT 
	Valor
	--,DATEDIFF(DAY,[Fecha objetivo],CONVERT(date, getdate())) Diferencia
	,dbo.SF_OBTENER_DIAS_HABILES(t.[Fecha objetivo],CONVERT(date, getdate())) Diferencia
	,T.Id Tarea
	,(SELECT sAMAccountName FROM GestionIDM.DBO.LDAP WHERE Name = T.[Asignado a]) Usuario
	,(SELECT Mail FROM GestionIDM.DBO.LDAP WHERE Name = T.[Asignado a]) Mail
	--,* 
FROM [Tareas y Proyectos] T
INNER JOIN Configuracion C ON C.Usuario = (SELECT sAMAccountName FROM GestionIDM.DBO.LDAP WHERE Name = T.[Asignado a]) AND C.Parametro = 'alerta_fecha_objetivo'
WHERE  [Fecha objetivo] IS NOT NULL
	AND 0 = (SELECT COUNT(TIPO) FROM TIPOS WHERE TIPO = 5 AND Oficina = T.Oficina AND Finalizado = 1 AND Descripcion = T.Estado)-- EXCLUYE FINALIZADAS
	AND C.Valor NOT LIKE  '0;%' AND C.Valor <> '0' AND C.Valor <> '' AND C.Valor IS NOT NULL
	AND T.Control = 0
ORDER BY T.Id DESC
";

$query = DBSelect ( utf8_decode ( $query ) );
for(; DBNext ( $query ) ;) {
	
	$TAREA = utf8_encode ( DBCampo ( $query , "Tarea" ) );
	$USUARIO = utf8_encode ( DBCampo ( $query , "Usuario" ) );
	$VALOR = utf8_encode ( DBCampo ( $query , "Valor" ) );
	$DIFERENCIA = utf8_encode ( DBCampo ( $query , "Diferencia" ) );
	
	$VALOR = explode(';', $VALOR);
	if( is_numeric($VALOR[0]) && is_numeric($VALOR[1]) && $VALOR[0] != 0 ){
		$CORREO = 'NO';
		$PANTALLA = 'NO';
		if( $DIFERENCIA == $VALOR[1]){
			if($VALOR[0] == 1 || $VALOR[0] == 2){
				$CORREO = 'SI';
			}
			if($VALOR[0] == 1 || $VALOR[0] == 3){
				$PANTALLA = 'SI';
			}
			
			$insert .= "
			INSERT INTO GestionPDT.dbo.Alertas(Fecha,Tipo,Tarea,Usuario,Correo,Enviado,Pantalla,Leido)
			SELECT 
				GETDATE()
				,'alerta_fecha_objetivo'
				,$TAREA
				,'$USUARIO'
				,'$CORREO'
				,'NO'
				,'$PANTALLA'
				,'NO'";
		}
	}
}


if($insert != ''){
	DBSelect ( utf8_decode ( $insert ) );
}


// ********************************************************************************************************CODIGO
  
// ****************************************************FINAL
DBFree ( $query );
DBClose ();
// ****************************************************FINAL


?>