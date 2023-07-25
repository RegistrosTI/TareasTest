<?php
header("Cache-Control: no-store, no-cache, must-revalidate");
//header("Content-Type: text/html; charset=ISO-8859-1");
//Includes necesarios
require_once ('./bin/SSRSReport.php');

//Variables globales y del GET
$id= $_GET["id"];
$codusuario= $_GET["usuario"];
$codinforme= $_GET["informe"];
$codcarpeta= $_GET["carpeta"];

if (isset($_GET["parametros"]))
{
	$parametros=$_GET["parametros"];
}
else
{
	$parametros="";
}
if (isset($_GET["accion"]))
{
	$accion=$_GET["accion"];
}
else
{
	$accion=0;
}


//Definimos el informe a consultar
define("REPORT", "/".$codcarpeta."/".$codinforme);

//Cargamos la configuracion
$settings = parse_ini_file("./bin/app.config", 1);
try
{
    $rs = new SSRSReport(new Credentials($settings["UID"], $settings["PASWD"]),$settings["SERVICE_URL"]);
    if (isset($_REQUEST['rs:Command']))
    {
        switch($_REQUEST['rs:Command'])
        {
            case 'Sort':
                $rs->Sort2($_REQUEST['rs:SortId'],
                           $_REQUEST['rs:SortDirection'],
                           $_REQUEST['rs:ClearSort'],
                           PageCountModeEnum::$Estimate,
                           $ReportItem,
                           $ExecutionInfo);
                  break;
            default:
                echo 'Unknown :' . $_REQUEST['rs:Command'];
                exit;
        }
    }
    else
    {	
		$i=0;
        $executionInfo = $rs->LoadReport2(REPORT, NULL);
		if ($parametros!="")
		{
			$parameters = array();
			$lista = explode("|", $parametros);
			if (is_array($lista)) 
			{ 
				//El resultado es un array
				foreach($lista as $param) 
				{ 				    
					//parametro				
					$dupla = explode("=", $param);
					$parameters[$i] = new ParameterValue();
					$parameters[$i]->Name = $dupla[0];
					$parameters[$i]->Value = $dupla[1];					
					$i=$i+1;
				}
			}
			else
			{
				//El resultado no es un array		   
				$dupla = explode("=", $lista);
				$parameters[0] = new ParameterValue();
				$parameters[0]->Name = $dupla[0];
				$parameters[0]->Value = $dupla[1];
			}
			$rs->SetExecutionParameters2($parameters);
		}		
    }
	if ($accion == 0)
	{
		$renderAsHTML = new RenderAsHTML();
	}
	if ($accion == 1)
	{
		$renderAsHTML = new RenderAsPDF();
	}
	if ($accion == 2)
	{
		$renderAsHTML = new RenderAsEXCEL();
	}
    //The ReplcementRoot option of HTML rendering extension is used to
    //redirect all calls to reporting serice server to this php file.
    //The StreamRoot option of HTML rendering extension used instruct
    //HTML rendering extension about how to construct the URLs to images in the
    //report.
    //Please refer description of Sort2, Render2 and RenderStream API in
    //the userguide (./../../../docs/User Guide.html) for more details
    //about these options.
    $renderAsHTML->ReplacementRoot = getPageURL();
    $renderAsHTML->StreamRoot = './images/';
    $result_html = $rs->Render2($renderAsHTML,
                                 PageCountModeEnum::$Actual,
                                 $Extension,
                                 $MimeType,
                                 $Encoding,
                                 $Warnings,
                                 $StreamIds);
	//echo $result_html;
	
						 
    foreach($StreamIds as $StreamId)
    {
        $renderAsHTML->StreamRoot = null;
        $result_png = $rs->RenderStream($renderAsHTML,
                                    $StreamId,
                                    $Encoding,
                                    $MimeType);

        if (!$handle = fopen("./images/" . $StreamId, 'wb'))
        {
            echo "Cannot open file for writing output";
            exit;
        }

        if (fwrite($handle, $result_png) === FALSE)
        {
            echo "Cannot write to file";
            exit;
        }
        fclose($handle);
    }	
	
	if ($accion == 0)
	{		
		echo '<table><tr><td><img src="./imagenes/download-pdf.png" alt="PDF" width="100" height="35" onClick="ver_informe('.$id.",1)".'" onmouseover="" style="cursor: pointer;"></td><td><img src="./imagenes/download-excel.png" alt="EXCEL" width="100" height="35" onClick="ver_informe('.$id.",2)".'" onmouseover="" style="cursor: pointer;"></td></tr></table>';
		echo '<div align="center">';
		echo '<div style="overflow:auto; width:1000px; height:800px">';
			echo $result_html;
		echo '</div>';
		echo '</div>';		
		
	}
	
	if ($accion == 1)
	{		
		header("Content-type: application/octet-stream");
		header("Content-disposition: attachment;filename=".$codinforme.".pdf");
		echo $result_html;
						
	}
	if ($accion == 2)
	{	
		$fichero="render/".$codinforme.".xls";
		$handle = fopen($fichero, 'wb'); 
		fwrite($handle, $result_html); 
		fclose($handle);
		
		if (file_exists($fichero)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');    
			header('Content-Disposition: attachment; filename="'.basename($codinforme).'.xls"');	
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
	
	
	}
	
}
catch(SSRSReportException $serviceExcprion)
{
    echo  $serviceExcprion->GetErrorMessage();
}

/**
 *
 * @return <url>
 * This function returns the url of current page.
 */
function getPageURL()
{
	//aqui david
    //$PageUrl = $_SERVER["HTTPS"] == "on"? 'https://' : 'http://';
	$PageUrl = 'http://';
    $uri = $_SERVER["REQUEST_URI"];
    $index = strpos($uri, '?');
    if($index !== false)
    {
	$uri = substr($uri, 0, $index);
    }
    $PageUrl .= $_SERVER["SERVER_NAME"] .
                ":" .
                $_SERVER["SERVER_PORT"] .
                $uri;
    return $PageUrl;
}

?>