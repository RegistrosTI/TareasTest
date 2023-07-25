<?php

// ****************************************************INCLUDES NECESARIOS
if ( 1 == 1 ) {
	$GLOBALS [ "cfg_HostDB" ] = 'SQL2016';
	$GLOBALS [ "cfg_UserDB" ] = 'gestionti';
	$GLOBALS [ "cfg_PassDB" ] = 'gestionti';
	$GLOBALS [ "cfg_DataBase" ] = 'GestionPDT';
	
	
	include "../../soporte/DB.php";
	require "../conf/config.php";
	include "../../soporte/funcionesgenerales.php";
}
// ****************************************************INCLUDES NECESARIOS

// ****************************************************INICIO
DBConectar ( $GLOBALS [ "cfg_DataBase" ] );
// ****************************************************INICIO

// ********************************************************************************************************OBTENEMOS LOS REGISTROS QUE HAY EN EL LDAP DE TIPO USUAUARIO Y ACTIVOS

$estilos2 = "
		.rojo{background-color: #F5A9A9; color: #000}
		.datagrid table { border-collapse: collapse; text-align: left; width: 100%; padding: 100px;}
		.datagrid {font: normal 12px/150% Arial, Helvetica, sans-serif; background: #fff; overflow: hidden; border: 2px solid #006699; }
		.datagrid table td, .datagrid table th { padding: 3px 10px; }
		.datagrid table td { border: 1px solid #006699; }
		.datagrid table thead th {background-color:#006699; color:#FFFFFF; font-size: 15px; font-weight: bold; border-left: 1px solid #0070A8; }
		.datagrid table thead th:first-child { border: none; }
		.datagrid table tbody td { color: #00557F; border-left: 1px solid #E1EEF4;font-size: 12px;font-weight: normal; }
		.datagrid table tbody td:first-child { border-left: none; }
		.datagrid table tbody tr:last-child td { border-bottom: none; }";

$mensaje = "
<html>
<head>
	<meta charset='UTF-8'>
	<title>Histórico de cambios</title>
	<link rel=stylesheet href='../css/estilos_nuevo.css' />
	<style type='text/css'>$estilos2</style
</head>
<body>
	<h3>Log de cambios</h3>
	<div class='datagrid'>
		<table>
			<thead>
				<TR>
					<TH>Versión</TH>
					<TH>Fecha</TH>
					<TH>Cambios</TH>
				</TR>
			</thead>
		<tbody>
";

$query2 = "
		select 
			Version
			,CAST((CONVERT(varchar(20),  Fecha,113)) as varchar) AS Fecha
			,ISNULL(Cambios,'Pequeñas mejoras y correciones.') AS Cambios 
		from Versiones order by Version desc";

$query2 = DBSelect ( utf8_decode ( $query2 ) );
for(; DBNext ( $query2 ) ;) {
	
	$Version = utf8_encode ( DBCampo ( $query2 , "Version" ) );
	$Fecha = utf8_encode ( DBCampo ( $query2 , "Fecha" ) );
	$Cambios = utf8_encode ( DBCampo ( $query2 , "Cambios" ) );
	
	$mensaje .= "<tr>";
	$mensaje .= "  <td>$Version</td>";
	$mensaje .= "  <td>$Fecha</td>";
	$mensaje .= "  <td>$Cambios</td>";
	$mensaje .= "</tr>";
}

$mensaje .= "</tbody></table></div></body></html>";

// ********************************************************************************************************OBTENEMOS LOS REGISTROS QUE HAY EN EL LDAP DE TIPO USUAUARIO Y ACTIVOS

// ****************************************************FINAL
DBClose ();
// ****************************************************FINAL

echo $mensaje;

?>