<?php
//****************************************************INCLUDES NECESARIOS
include "../conf/config.php";
include "../../soporte/DB.php";
include "../../soporte/funcionesgenerales.php";
//****************************************************INCLUDES NECESARIOS

$Fichero_Nombre = $_GET['id'];


$fichero='./'.$Fichero_Nombre;

if (file_exists($fichero)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');    
	header('Content-Disposition: attachment; filename="'.basename($Fichero_Nombre).'"');	
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
<h2>Lo sentimos...<br>El documento al que intenta acceder no existe. 
</body>
<?php
}
?>
