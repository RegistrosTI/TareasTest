<?php
if (isset($_POST["excel"]) && isset($_POST["extension"]))        
{
    $extension = $_POST["extension"];    
    if ($extension == "csv" || $extension == "xml")
    {                
        $excel = $_POST["excel"];
        $filename = "pqGrid." . $extension;              
        $file = dirname($_SERVER["SCRIPT_FILENAME"])."\\".$filename;
        file_put_contents($file, $excel);        
        echo $filename;        
    }
}
else if(isset($_GET["filename"]))
{
    $filename = $_GET["filename"];
    if ($filename == "pqGrid.csv" || $filename == "pqGrid.xml")
    {
        $file = dirname($_SERVER["SCRIPT_FILENAME"])."\\".$filename;
 
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
        }    
    }
}

?>