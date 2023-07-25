<?php
class Logg
{

    
    public static $fileName = 'new_log';
    public static $filePath = '/';
    public static $fileType = '.log';
    
    public static function addInfo($logMsg, $fName = null, $fPath = null)
    {

        $fName = $fName ?: self::$fileName;
        $fPath = $fPath ?: self::$filePath;
        self::addData($logMsg, $fName, $fPath, ' |  INFO   |');
    }
    
    public static function addWarn($logMsg, $fName = null, $fPath = null)
    {
        $fName = $fName ?: self::$fileName;
        $fPath = $fPath ?: self::$filePath;
        self::addData($logMsg, $fName, $fPath, ' | WARNING |');
    }
    
    public static function addErr($logMsg, $fName = null, $fPath = null)
    {
        $fName = $fName ?: self::$fileName;
        $fPath = $fPath ?: self::$filePath;
        self::addData($logMsg, $fName, $fPath, ' |  ERROR  |');
    }
    
    public static function addConf($logMsg, $fName = null, $fPath = null)
    {
        $fName = $fName ?: self::$fileName;
        $fPath = $fPath ?: self::$filePath;
        self::addData($logMsg, $fName, $fPath, ' | CONFIG  |');
    }
    
    private static function addData($logMsg, $fName, $fPath, $type)
    {
        $handle = fopen($fPath . $fName . self::$fileType, 'a');
        
        if (gettype($logMsg) == 'array') {
            if (count($logMsg)%2 == 0) {
                $logText = '';
                for ($i = 0; $i < count($logMsg); $i+=2) {
                    $logText .= str_pad($logMsg[$i], $logMsg[$i+1], " ", STR_PAD_BOTH).'|';
                }
                fwrite($handle, sprintf("| %s%s", date("Y-m-d H:i:s"), $type.$logText.PHP_EOL));
            } else {
                throw new Exception('Wrong arguments for Array-type log.');
            }
        } else {
            fwrite($handle, sprintf("| %s%s", date("Y-m-d H:i:s"), $type.' '.$logMsg.PHP_EOL));
        }
        
        fclose($handle);
    }
    
    public static function addLine($lines = 1, $fName = null, $fPath = null)
    {
        $fName = $fName ?: self::$fileName;
        $fPath = $fPath ?: self::$filePath;
        $handle = fopen($fPath . $fName . self::$fileType, 'a');
        fwrite($handle, str_repeat(PHP_EOL, $lines));
        fclose($handle);
    }
}
?>