<?php
DBConectar($GLOBALS["cfg_DataBase"]);

$q=DBSelect(utf8_decode("SELECT Tipos.[Numero],CAST('colorprioridad_' + CAST(Tipos.[Numero] as varchar(250)) as varchar(250)) as CLASE
						,CAST(Tipos.[Descripcion] as varchar(250)) as Descripcion,ISNULL((SELECT Conf.[Valor] FROM [Configuracion] Conf
						where Conf.Parametro = CAST(Tipos.[Numero] as varchar(250)) + '_colorprioridad' AND Conf.usuario = '".$usuario."' AND Conf.UnidadMedida = 'HTML'),'') as span
						FROM [Tipos] Tipos where Tipos.tipo = 4 UNION ALL
						SELECT Tipos.[Numero] ,CAST('colorestado_' + CAST(Tipos.[Numero] as varchar(250)) as varchar(250)) as CLASE,CAST(Tipos.[Descripcion] as varchar(250)) as Descripcion
						,ISNULL((SELECT Conf.[Valor] FROM [Configuracion] Conf where Conf.Parametro = CAST(Tipos.[Numero] as varchar(250)) + '_colorestado' 
						AND Conf.usuario = '".$usuario."' AND Conf.UnidadMedida = 'HTML'),'') as span FROM [Tipos] Tipos  where Tipos.tipo = 5 UNION ALL
						SELECT -1 ,CAST(Conf.Parametro as varchar(250)) as CLASE,CAST(Conf.[Descripcion] as varchar(250)) as Descripcion,ISNULL(Conf.[Valor],'') as span
						FROM [Configuracion] Conf where Conf.Parametro = 'mi_colormio' AND Conf.usuario = '".$usuario."' "));

for(;DBNext($q);)
{	
	$CLASE       =DBCampo($q,("CLASE"));
	$Descripcion =(DBCampo($q,("Descripcion")));
	$Span        =(DBCampo($q,("span")));
	
	echo ".".$CLASE;
	echo "{ ";
	echo " ".$Span." ";								
	echo "}	";	
}

DBFree($q);	
DBClose();
?>