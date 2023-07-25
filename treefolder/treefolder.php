<?php
//$allowext = array("zip","rar","html","lot","exe","txt","pdf","xlsx","msg");
//scanDirectories(utf8_decode('//sp-berner.local/grupos/Costes/Archivo costes/5.-/5.3.1.385. Dirección - Rentabilidad - Ambientador APPLE  2014'),$allowext,1);


if(isset($_GET['archivo']))
{
	$fichero = utf8_decode($_GET['archivo']);
	if (file_exists($fichero)) 
	{
		$FicheroDescripcion = substr($fichero, strrpos($fichero, '/') + 1);
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
	die();
}
if(isset($_POST['treefolder_insert']))
{
	treefolder_insert();	
	die();

}
if(isset($_POST['treefolder']))
{
	$rootDir  = $_POST['ruta'];
	$allowext = array("zip","rar","html","lot","exe","txt","pdf","xlsx","msg");
	scanDirectories(utf8_decode($rootDir),$allowext,1);
	die();
}
if(isset($_POST['treefolder_folders']))
{
	treefolder_folders();	
	die();
}
if(isset($_POST['treefolder_erase_folders']))
{
	erase_treefolder_folders();	
	die();
}

function erase_treefolder_folders()
{
	include_once "../conf/config_filtros.php";
	include_once "../php/funciones.php";
	include_once "../conf/config.php";
	include_once "../conf/config_".curPageName().".php";
	include_once "../../soporte/DB.php";
	include_once "../../soporte/funcionesgenerales.php";
		
	$tarea = $_POST['Numero'];

	DBConectar($GLOBALS["cfg_DataBase"]);
	$q=DBSelect(utf8_decode("DELETE FROM [folder] WHERE Numero = ".$tarea." "));

	DBFree($q);				
	DBClose();
}
function treefolder_folders()
{
	include_once "../conf/config_filtros.php";
	include_once "../php/funciones.php";
	include_once "../conf/config.php";
	include_once "../conf/config_".curPageName().".php";
	include_once "../../soporte/DB.php";
	include_once "../../soporte/funcionesgenerales.php";

		
	$tarea = $_GET['tarea'];

	DBConectar($GLOBALS["cfg_DataBase"]);
	$q=DBSelect(utf8_decode("SELECT [Numero],[Tarea],[usuario],[carpeta],[ruta]  FROM [folder] where [Tarea] = ".$tarea." "));
	for(;DBNext($q);)
	{
		$carpeta=DBCampo($q,"carpeta");
		$ruta=DBCampo($q,"ruta");
		$Numero=DBCampo($q,"Numero");
		echo '<div class="treefolder_lista" id="treefolder_'.$Numero.'"><img class="init_treefolder" src="./imagenes/erase.png" onClick="erase_treefolder('.$Numero.')"><div class="init_treefolder" onClick="init_treefolder(\''.utf8_encode($ruta).'\');">'.utf8_encode($carpeta).'</div></div>';
	}	
	DBFree($q);				
	DBClose();
}
function treefolder_insert()
{
	include_once "../conf/config_filtros.php";
	include_once "../php/funciones.php";
	include_once "../conf/config.php";
	include_once "../conf/config_".curPageName().".php";
	include_once "../../soporte/DB.php";
	include_once "../../soporte/funcionesgenerales.php";

		
	$ruta    = $_POST['ruta'];
	$carpeta = $_POST['nombre'];
	$usuario = $_GET['usuario'];
	$tarea   = $_POST['tarea'];
	$ruta    = str_replace('H:', '//sp-berner.local/grupos', $ruta);
	$ruta    = str_replace('\\', '/', $ruta);
	$carpeta = str_replace('\\', '/', $carpeta);
	
	DBConectar($GLOBALS["cfg_DataBase"]);
	$q=DBSelect(utf8_decode("INSERT INTO [folder] ([Tarea],[usuario],[carpeta],[ruta]) OUTPUT Inserted.Numero VALUES (".$tarea.",'".$usuario."','".$carpeta."','".$ruta."')"));
		   
	$Numero=(DBCampo($q,("Numero")));
	
	DBFree($q);				
	DBClose();
	
	echo '<div class="treefolder_lista" id="treefolder_'.$Numero.'"><img class="init_treefolder" src="./imagenes/erase.png" onClick="erase_treefolder('.$Numero.')"><div class="init_treefolder" onClick="init_treefolder(\''.utf8_encode($ruta).'\');">'.utf8_encode($carpeta).'</div></div>';
}
function scanDirectories($rootDir, $allowext, $level) 
{
    $dirContent = scandir(($rootDir));

	echo '<ul>';
	if($level==1)
	{
		echo '<li class="folder">'.utf8_encode(substr($rootDir, strrpos($rootDir, '/') + 1));
	}
    foreach($dirContent as $key => $content) 
	{
        $path = $rootDir.'/'.$content;
        $ext  = substr($content, strrpos($content, '.') + 1);
		if($content!="." && $content!="..")
		{
			if(is_file($path) && is_readable($path)) 
			{
				if(in_array($ext, $allowext)) 
				{
					echo '<li><div onClick="open_treefolder(\''.utf8_encode($path).'\');">'.utf8_encode($content).'</div></li>';					
				}
			}
			elseif(is_dir($path) && is_readable($path)) 
			{
				echo '<li class="folder">'.utf8_encode($content);
				scanDirectories($path, $allowext, $level+1);
			}
        }
    }
	echo '</li></ul>';	
    return true;
}

?>