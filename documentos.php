<?php
header("Cache-Control: no-store, no-cache, must-revalidate");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="ISO-8859-1">
<title>Documentos</title>
<style type="text/css">
	.cabecera
	{
		background-image:url('imagenes/logo.png');
		background-repeat:no-repeat;
		width:1000px;
		height:170px;
		margin-left:auto;
		margin-right:auto;	
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
		background-image:url('imagenes/sombra-h.png');
		background-repeat:repeat-x;
		width:1000px;
		margin-left:auto;
		margin-right:auto;	
		padding:0px;
	}
	.cuerpo-menu
	{
		width:200px;
		padding:0px;
		float:left;
		font-size:14px;
		height:100%;
	}
	.cuerpo-menu-opcion
	{
		border-bottom: 1px dashed black;
		list-style-type: none;
		padding-top:10px;
	}
	.cuerpo-seccion
	{
		background-image:url('imagenes/sombra-v.png');
		background-repeat:repeat-y;
		float:left;
		width:500px;
		padding:10px;
	}
	.pie
	{
		border-top: 2px solid black;
		width:1000px;
		margin-left:auto;
		margin-right:auto;	
		padding-top:0px;
		background-color:#777777;
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
	.tabla-litle
	{
		font-size:10px;
		width:650px;
	}
	.tabla-medium
	{
		font-size:12px;
		width:650px;
	}
</style>
<script language="JavaScript">
function nuevoAjax()
{
	//Funcion del Ajax
	var xmlhttp=false;
	try
	{
		xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");		
	}
	catch(e)
	{
		try
		{
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch(E)
		{
			if (!xmlhttp && typeof XMLHttpRequest!='undefined') xmlhttp=new XMLHttpRequest();
		}
	}
	return xmlhttp;
}
function getURLvar(var_name)
{
	//Funcion que obtiene las variables de la URL
	var re = new RegExp(var_name + "(?:=([^&]*))?", "i");
	var pm = re.exec(decodeURIComponent(location.search));
	if(pm === null) return " ";
	return pm[1] || "";
}
function obtener_documentos()
{	
	//Obtenemos la lista de documentos
    var id = getURLvar('id');    
	var ajax=nuevoAjax();
	var lista;	
	ajax.open("POST","select/lista_documentos.php?id="+id,true);
	ajax.onreadystatechange=function()
	{
		if (ajax.readyState!=4)
		{
			//NO ESTA LISTO!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!			
		}
		else
		{
			//Obtenemos el resultado
			lista=(ajax.responseText);			
			
			//Inyectamos el codigo HTML en nuestro codigo
			var mimenu=document.getElementById('cbpenddiv');				
			mimenu.innerHTML= lista;
		}
	}	
	ajax.send(null);	
}
</script>
</head>
<!--Mi Cuerpo!-->
<body onload="obtener_documentos()">
	<div id="micuerpo"><tr><td><div id="cbpenddiv"></div></td></tr></div>
</body>
</html>
