<?php

include_once "../conf/config.php";
include_once "../conf/config_menu.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$data = json_decode(file_get_contents('php://input'), true);

$fecha                   = $data['fecha'];
$horaInicio              = $data['horaInicio'];
$horaFinalizacion        = $data['horaFinalizacion'];
$totalHoras              = $data['totalHoras'];
$tipologiaTareaRealizada = $data['tipologiaTareaRealizada'];
$planificada             = isset($data['planificada']) ? $data['planificada'] : null;
$periodicidad            = $data['periodicidad'];
$objetivoTarea           = $data['objetivoTarea'];
$resultado               = isset($data['resultado']) ? $data['resultado'] : null;
$resultadoAlcanzado      = $data['resultadoAlcanzado'];
$usuario                 = $_SERVER['REMOTE_USER'];
$date                    = new DateTime();
$semana                  = $date->format("W");
$anyo                    = $date->format("Y");
$mes                     = $date->format("m");

$contador = 0;
$id = '';

$query = "
INSERT INTO [dbo].[Teletrabajo_Imputado]
           ([Fecha]
           ,[Usuario]
           ,[HoraInicio]
           ,[HoraFinalizacion]
           ,[TotalHoras]
           ,[TipologiaTareaRealizada]
           ,[Planificada]
           ,[Periodicidad]
           ,[ObjetivoTarea]
           ,[Resultado]
           ,[ResultadoAlcazado]
           ,[Anyo]
           ,[Mes]
           ,[Semana]
           ,[FechaModificacion]
           ,[FechaCreacion])
     VALUES
           (CAST('$fecha' AS date)
           ,'$usuario'
           ,CAST('$horaInicio' AS time)
           ,CAST('$horaFinalizacion' AS time)
           ,$totalHoras
           ,'$tipologiaTareaRealizada'
           ,'$planificada'
           ,'$periodicidad'
           ,'$objetivoTarea'
           ,'$resultado'
           ,'$resultadoAlcanzado'
           ,'$anyo'
           ,'$mes'
           ,'$semana'
           ,CURRENT_TIMESTAMP
           ,CURRENT_TIMESTAMP)
";
   $insert = iconv('UTF-8', 'ISO-8859-1', $query); //decode
   $insert = DBSelect ($insert);
   DBFree($insert);
  
$query = "
      SELECT
            Id
           ,CAST((CONVERT(varchar(20),  Fecha,103)) as varchar) AS Fecha
           ,[Usuario]
           ,CAST((CONVERT(varchar(20),  HoraInicio,108)) as varchar) AS HoraInicio
           ,CAST((CONVERT(varchar(20),  HoraFinalizacion,108)) as varchar) AS HoraFinalizacion
           ,[TotalHoras]
           ,[TipologiaTareaRealizada]
           ,[Planificada]
           ,[Periodicidad]
           ,[ObjetivoTarea]
           ,[Resultado]
           ,[ResultadoAlcazado]
           ,[Anyo]
           ,[Mes]
           ,[Semana]
            FROM [Teletrabajo_Imputado] 
      WHERE Usuario = '$usuario' AND CONVERT(DATE, Fecha) = CONVERT(DATE, GETDATE()) AND (Eliminado <> 'SI' OR Eliminado IS NULL)
      ORDER BY HoraInicio
        ";
   $query = iconv('UTF-8', 'ISO-8859-1', $query); //decode
   $query = DBSelect ($query);
   
   

    for(;DBNext($query);)
    {	
        $id                      = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "Id"));
        $fecha                   = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "Fecha"));
        $usuario                 = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "Usuario"));
        $horaInicio              = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "HoraInicio"));
        $horaFinalizacion        = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "HoraFinalizacion"));
        $totalHoras              = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "TotalHoras"));
        $tipologiaTareaRealizada = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "TipologiaTareaRealizada"));
        $planificada             = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "Planificada"));
        $periodicidad            = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "Periodicidad"));
        $objetivoTarea           = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "ObjetivoTarea"));
        $resultado               = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "Resultado"));
        $resultadoAlcanzado      = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "ResultadoAlcazado"));
        $anyo                    = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "Anyo"));
        $mes                     = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "Mes"));
        $semana                  = iconv('ISO-8859-1', 'UTF-8', DBCampo($query, "Semana"));
        $teletrabajo[$contador]  =array("id"=>$id,"Fecha"=>$fecha,"Usuario"=>$usuario,"Hora-Inicio"=>$horaInicio,"Hora-Finalización"=>$horaFinalizacion,"Total-Horas"=>$totalHoras,"Tipologia-TareaRealizada"=>$tipologiaTareaRealizada,"Planificada"=>$planificada,"Periodicidad"=>$periodicidad,"Objetivo-Tarea"=>$objetivoTarea,"Resultado"=>$resultado,"Descripción-Resultado-Alcanzado"=>$resultadoAlcanzado,"Año"=>$anyo,"Mes"=>$mes,"Semana"=>$semana);
        $contador                =$contador+1;
    }
DBFree($query);	
DBClose();

$json_arr = array('data'=>$teletrabajo);
$response = json_encode($json_arr);

//$response = ['status' => 'success', 'message' => 'Data received successfully'];
echo json_encode($response);
?>