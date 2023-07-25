<?php
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);

$Usuario      = $_GET["usuario"];
$Menu         = $_GET["departamento"];
$Frase        = $_GET["frase"];
$Opciones     = $_GET["opciones"];
$pagina       = $_GET["pagina"];
$registros    = $_GET["registros"];
$contador     = 0;
$pieces       = explode(" ", $Frase);
$Mi_Menu_Toca = '-1';

$query=DBSelect(utf8_decode("SELECT Numero as Numero FROM Tipos WHERE Tipo = 100 and Descripcion = '".utf8_decode($Menu)."'"));
for(;DBNext($query);)
{		
	$Mi_Menu_Toca=(DBCampo($query,"Numero"));					
}
DBFree($query);	

if ($Mi_Menu_Toca == '1')
{
	$id_mh='1';
}
else
{
	$id_mh='2';
}	
$Descripcion     = '';
$Hijo            = '';
$Descripcion     = '';
$TOTAL_REGISTROS = 0;
$Tarea_Anterior  = -1;
$query=DBSelect(utf8_decode("EXECUTE [BUSCADOR_PALABRAS] '".str_replace("'", "''", ($Frase))."','".$Opciones."',".$pagina.",".$registros));

echo '<div>';
for(;DBNext($query);)
{	
	$contador       = $contador + 1;
	$TITULO         =  strip_tags(utf8_encode(DBCampo($query,"titulo")));
	$APARICIONES    =(DBCampo($query,"APARICIONES"));
	$TOTAL_REGISTROS=(DBCampo($query,"TOTAL_REGISTROS"));
	$TIPO           =(DBCampo($query,"tipo"));
	$FECHA          =(DBCampo($query,"Fecha"));
	$Tarea          =(DBCampo($query,"tarea"));
	if($Tarea_Anterior!=$Tarea)
	{
		if($Tarea_Anterior!=-1)
		{
			echo $Hijo.'...'.'</div><div style="color:green;font-size: 12px;clear: both;" >'.$FECHA.'</div>';
			$Hijo = '';
		}
		echo '<div style="color:blue;font-size: 20px;cursor: pointer;clear: both;" onClick="selecciono_tarea_historial('.$Tarea.','.$id_mh.');" class="titulo-buscar">'.substr ($Tarea.' - '.$TITULO,0,100).'</div><div style="color:grey;font-size: 15px;clear: both;">';
		$Tarea_Anterior=$Tarea;
	}
	
	$contiene = false;
	foreach ($pieces as $piece) 
	{			
		$resultado = strpos(strtoupper($TITULO), strtoupper($piece));
		if($resultado !== FALSE)
		{
			$contiene = true;				
			$TITULO   = str_replace(strtoupper($piece),'<strong>'.strtoupper($piece).'</strong>', strtoupper($TITULO));				
		}			
	}
	if ($contiene == true)
	{
		$inicio = $resultado - 50;
		if($inicio<0)
		{
			$inicio = 0;
		}
		if ($Hijo!='')
		{			
			$Hijo = $Hijo.'...'.substr($TITULO,$inicio,$inicio+100);
		}
		else
		{
			$Hijo = substr($TITULO,$inicio,$inicio+100);
		}						
	}
}
if($Tarea_Anterior!=-1)
{
	echo $Hijo.'...'.'</div><div style="color:green;font-size: 12px;clear: both;" >'.$FECHA.'</div>';
	$Hijo = '';
}
echo '</div>';
DBFree($query);	
	
if ($contador==0)
{
	echo "No se han encontrado coincidencias";
}
else
{
	$PAGINAS_TOTALES = intval($TOTAL_REGISTROS/$registros);
	if ($TOTAL_REGISTROS % $registros) 
	{
		$PAGINAS_TOTALES = $PAGINAS_TOTALES + 1;
	} 
	else 
	{
		$PAGINAS_TOTALES = $PAGINAS_TOTALES; 
	}
	$PRIMERA = $pagina - 5;
	$ULTIMA  = $pagina + 5;
	if($PRIMERA <= 0)
	{
		$PRIMERA=1;
	}
	if($PRIMERA+10>$ULTIMA)
	{
		$ULTIMA=$PRIMERA+10;
	}
	if($ULTIMA>$PAGINAS_TOTALES)
	{
		$ULTIMA=$PAGINAS_TOTALES;
	}
	echo '<div style="color:blue;font-size:30px;text-align:center;width:100%;">';
	for ($i = $PRIMERA; $i <= $ULTIMA; $i++) {
		echo '<div style="padding-right:20px; float:left;cursor: pointer;" onClick="Buscar_Palabras('.$i.');">'.$i.'</div>';
	}
	echo '</div>';
	
}	
DBClose();
?>
