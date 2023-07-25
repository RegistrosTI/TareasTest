<?php
//utf8 ñáç
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_".curPageName().".php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$usuario_deseado = $_GET["usuario"];
$id_pantalla     = $_GET["id_pantalla"];
$tarea           = $_GET["tarea"];
$contador        =0;
$tool            = array();


DBConectar($GLOBALS["cfg_DataBase"]);

if($id_pantalla=="1")
{
	$pantalla="";
}
else
{
	$pantalla=" AND Publico IN (1,2) ";
}

$q = "
	SELECT 'COSTES' AS DESCRIPCION
		,(SELECT COUNT(*) FROM [Costes] WHERE tarea = $tarea) AS TOTAL
		,(SELECT COUNT(*) FROM [Costes] WHERE tarea = $tarea AND usuario = '$usuario_deseado') AS CANTIDAD
		,(SELECT CAST(SUM(Importe) AS varchar(250)) FROM [Costes] WHERE tarea = $tarea) AS TOTAL_TEXTO
		,(SELECT CAST(SUM(Importe) AS varchar(250)) FROM [Costes] WHERE tarea = $tarea AND usuario = '$usuario_deseado') AS CANTIDAD_TEXTO
	
	UNION ALL 
	
	SELECT 'ADJUNTO' AS DESCRIPCION				
		,(SELECT COUNT(*) FROM [Adjuntos] WHERE tarea = $tarea  $pantalla ) AS TOTAL
		,(SELECT COUNT(*) FROM [Adjuntos] WHERE tarea = $tarea  AND usuario = '$usuario_deseado' $pantalla) AS CANTIDAD
		,'' AS TOTAL_TEXTO
		,'' AS CANTIDAD_TEXTO
	
	UNION ALL 
	
	SELECT 'VALORACION' AS DESCRIPCION
		,(	SELECT COUNT(*) 
			FROM [Valoraciones] 
				INNER JOIN [Concepto_Tipo_Valoraciones] AS Tipos 
				ON Tipos.Numero = Valoraciones.Concepto AND Tipos.Grupo = 1	WHERE tarea = $tarea ) AS TOTAL
		,(	SELECT COUNT(*)
			FROM [Valoraciones] 
				INNER JOIN [Concepto_Tipo_Valoraciones] AS Tipos 
				ON Tipos.Numero = Valoraciones.Concepto AND Tipos.Grupo = 1 WHERE tarea = $tarea AND usuario = '$usuario_deseado') AS CANTIDAD
		,'' AS TOTAL_TEXTO
		,'' AS CANTIDAD_TEXTO
						
	UNION ALL 
	
	SELECT 'ARBOL' AS DESCRIPCION
		,((	SELECT COUNT(*) 
			FROM [Tareas y Proyectos] 
			WHERE [Tarea / Proyecto] = $tarea AND ([Control] = 0 OR [Control] is null)) 
		 + (SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END
			FROM [Tareas y Proyectos] AS Padre 
				INNER JOIN [Tareas y Proyectos] AS Hijo 
				ON Padre.Id = Hijo.[Tarea / Proyecto] 
			WHERE Padre.Id = $tarea 
				AND (Padre.[Control] = 0 
				OR Padre.[Control] is null)) 
		 + (SELECT COUNT(*) 
			FROM [Tareas y Proyectos] 
			WHERE ([Tarea / Proyecto] IS NOT NULL 
				OR [Tarea / Proyecto] = '') AND Id = $tarea AND ([Control] = 0 OR [Control] is null)) 
		 + (SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END 
			FROM [Tareas y Proyectos] AS Padre 
				INNER JOIN [Tareas y Proyectos] AS Hijo 
				ON Padre.Id = Hijo.[Tarea / Proyecto] 
			WHERE Hijo.Id = $tarea 
				AND (Padre.[Control] = 0 OR Padre.[Control] is null) 
				AND (Hijo.[Control]  = 0 OR Hijo.[Control]  is null))) AS TOTAL
		,0 AS CANTIDAD
		,'' AS TOTAL_TEXTO
		,'' AS CANTIDAD_TEXTO 
					
	UNION ALL 
					
	SELECT 'HORAS' AS DESCRIPCION
		,(SELECT COUNT(*) FROM [Horas] WHERE tarea = $tarea) AS TOTAL
		,(SELECT COUNT(*) FROM [Horas] WHERE tarea = $tarea AND usuario = '$usuario_deseado') AS CANTIDAD
		,'' AS TOTAL_TEXTO
		,'' AS CANTIDAD_TEXTO 
				
	UNION ALL
				
	SELECT 'COMENTARIOS' AS DESCRIPCION
		,(SELECT COUNT(*) FROM [Comentarios] WHERE tarea = $tarea) AS TOTAL
		,(SELECT COUNT(*) FROM [Comentarios] WHERE tarea = $tarea AND usuario = '$usuario_deseado') AS CANTIDAD
		,'' AS TOTAL_TEXTO
		,'' AS CANTIDAD_TEXTO 
	
	UNION ALL 
				
	SELECT 'OBSERVACIONES' AS DESCRIPCION
		,(	SELECT COUNT(*) 
			FROM [Tareas y Proyectos] 
			WHERE ([Descripción] LIKE '' OR [Descripción] IS NULL) AND Id = $tarea AND ([Control] = 0 OR [Control] IS NULL)) AS TOTAL
		,(1) AS CANTIDAD
		,'' AS TOTAL_TEXTO
		,'' AS CANTIDAD_TEXTO
						
	UNION ALL 
					
	SELECT 'TIEMPO' AS DESCRIPCION
		,0 AS TOTAL
		,0 AS CANTIDAD
		,ISNULL( (	SELECT 
						CAST(  CAST(ISNULL(Tareas.[Horas reales],0)*60+ISNULL(SUM(Horas.Minutos),0) AS INT) / (24 * 60 ) AS varchar(120)) + ':' + 
						CAST(( CAST(ISNULL(Tareas.[Horas reales],0)*60+ISNULL(SUM(Horas.Minutos),0) AS INT) % (24 * 60 )) / (60) AS varchar(120)) + ':' +
						CAST(((CAST(ISNULL(Tareas.[Horas reales],0)*60+ISNULL(SUM(Horas.Minutos),0) AS INT) % (24 * 60 )) % (60 )) AS varchar(120)) + ' [T ' + 
						CAST(  CAST(ISNULL(Tareas.[Horas reales],0)*60+ISNULL(SUM(Horas.Minutos),0) AS INT) / (60) AS varchar(120)) + ':' +
						CAST(((CAST(ISNULL(Tareas.[Horas reales],0)*60+ISNULL(SUM(Horas.Minutos),0) AS INT) % (24 * 60 )) % (60 )) AS varchar(120)) + '] '
		FROM [Tareas y Proyectos] AS Tareas 
						LEFT JOIN [Horas] AS Horas 
						ON Horas.Tarea = Tareas.Id
					WHERE Tareas.Id = $tarea AND (Tareas.[Control] = 0 OR Tareas.[Control] IS NULL) 
					GROUP BY  Id,Tareas.[Horas reales]),'0') AS TOTAL_TEXTO
		,ISNULL((SELECT 
						CAST(  CAST(ISNULL(Tareas.[Horas reales],0)*60+ISNULL(SUM(Horas.Minutos),0) AS INT) / (24 * 60 ) AS varchar(120)) + ':' + 
						CAST(( CAST(ISNULL(Tareas.[Horas reales],0)*60+ISNULL(SUM(Horas.Minutos),0) AS INT) % (24 * 60 )) / (60) AS varchar(120)) + ':' +
						CAST(((CAST(ISNULL(Tareas.[Horas reales],0)*60+ISNULL(SUM(Horas.Minutos),0) AS INT) % (24 * 60 )) % (60 )) AS varchar(120)) + ' [T ' + 
						CAST(  CAST(ISNULL(Tareas.[Horas reales],0)*60+ISNULL(SUM(Horas.Minutos),0) AS INT) / (60) AS varchar(120)) + ':' +
						CAST(((CAST(ISNULL(Tareas.[Horas reales],0)*60+ISNULL(SUM(Horas.Minutos),0) AS INT) % (24 * 60 )) % (60 )) AS varchar(120)) + '] '
		FROM [Tareas y Proyectos] AS Tareas 
					LEFT JOIN [Horas] AS Horas 
					ON Horas.Tarea = Tareas.Id
				WHERE Tareas.Id = $tarea AND Horas.Usuario = '$usuario_deseado' AND (Tareas.[Control] = 0 OR Tareas.[Control] is null) 
				GROUP BY  Id,Tareas.[Horas reales]),'0:0:0') AS CANTIDAD_TEXTO";

//echo ($q);
$q=DBSelect(utf8_decode($q));
	
for(;DBNext($q);)
{	
	$TOTAL           = DBCampo($q,"TOTAL");
	$CANTIDAD        = (DBCampo($q,utf8_decode("CANTIDAD")));
	$DESCRIPCION     = (DBCampo($q,utf8_decode("DESCRIPCION")));
	$TOTAL_TEXTO     = (DBCampo($q,utf8_decode("TOTAL_TEXTO")));
	$CANTIDAD_TEXTO  = (DBCampo($q,utf8_decode("CANTIDAD_TEXTO")));			
	$tool[$contador] = array("TOTAL"=>$TOTAL,"CANTIDAD"=>$CANTIDAD,"DESCRIPCION"=>$DESCRIPCION,"TOTAL_TEXTO"=>$TOTAL_TEXTO,"CANTIDAD_TEXTO"=>$CANTIDAD_TEXTO);
	$contador        = $contador+1;
}

DBFree($q);	
DBClose();

$json_arr = array('data'=>$tool);
$php_json = json_encode($json_arr);
echo $php_json;
?>