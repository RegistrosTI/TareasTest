<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id = $_GET['id'];

DBConectar($GLOBALS["cfg_DataBase"]);

$query="
	SELECT 
		[Numero] 
		,[Tarea]
		,CAST(Descripcion as varchar(250)) as Descripcion
		,CAST(Fichero as varchar(250)) as Fichero 
	FROM [Adjuntos]  
	WHERE Numero = ".$id;

$u=DBSelect(($query));
$FicheroNumero     =(DBCampo($u,("Numero")));		
$FicheroTarea      =(DBCampo($u,"Tarea"));
$FicheroDescripcion=(DBCampo($u,"Descripcion"));			
$FicheroCarpeta    =(DBCampo($u,"Fichero"));
DBFree($u);		

$fichero='../'.$FicheroCarpeta.'/'.$FicheroNumero.'_'.$FicheroDescripcion;
echo $fichero;
if (file_exists($fichero)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');    
	header('Content-Disposition: attachment; filename="'.basename($FicheroDescripcion).'"');	
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($fichero));
    ob_clean();
    flush();

    readfile($fichero);
    exit;
}
else
{
?>
<body>
<h2>Lo sentimos...<br>El archivo al que intenta acceder no existe. <?=$FicheroDescripcion?><br>
Es posible que Navision aun no haya integrado los datos en el servidor, o que se haya eliminado de la base de datos posteriormente al env�o del mensaje desde el que accede.</h2>
</body>
<?php
}
?>
