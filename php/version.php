<?php
$minavegador = "";
$user_agent  = $_SERVER['HTTP_USER_AGENT'];
 

 
 $navegadores = array(
 'Opera' => 'Opera',
 'Mozilla Firefox'=> '(Firebird)|(Firefox)',
 'Google Chrome'=>'(Chrome)',
 'Galeon' => 'Galeon',
 'Mozilla'=>'Gecko',
 'MyIE'=>'MyIE',
 'Lynx' => 'Lynx',
 'Chrome'=>'Chrome',
 'Netscape' => '(CHROME/23\.0\.1271\.97)|(Mozilla/4\.75)|(Netscape6)|(Mozilla/4\.08)|(Mozilla/4\.5)|(Mozilla/4\.6)|(Mozilla/4\.79)',
 'Konqueror'=>'Konqueror',
 'Internet Explorer 7' => '(MSIE 7\.[0-9]+)',
 'Internet Explorer 6' => '(MSIE 6\.[0-9]+)',
 'Internet Explorer 5' => '(MSIE 5\.[0-9]+)',
 'Internet Explorer 4' => '(MSIE 4\.[0-9]+)',
 );
 foreach($navegadores as $navegador=>$pattern)
 {
 if (preg_match('#'.$pattern.'#i', $user_agent))
	$minavegador = $navegador;
 }

 
function isIE()
{
    preg_match('/MSIE (.*?);/',$_SERVER['HTTP_USER_AGENT'], $matches);

    if (count($matches)>1)
    {
        return $matches[1];
    }
    return false;
}

$version=isIE();

$versionobsoleta = false;
if($version)
{
    switch($version)
    {
        case $version<=8:
            $versionobsoleta = true;
            break;
        case $version==9:
            $versionobsoleta = false;
            break;
        case $version==10:
            $versionobsoleta = false;
            break;
    }
}
if ($versionobsoleta==true)
{
	echo '<!DOCTYPE html>  ';
}
?>