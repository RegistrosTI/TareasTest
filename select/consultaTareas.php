<?php
// UTF ñáç
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$usuario_deseado = $_GET [ "usuario" ];
$id_usuario = $_GET [ "idusuario" ];
$idvisualizacion = $_GET [ "idvisualizacion" ];

// $ambito = $_GET["ambito"]; //Mejor no lo pasamos por el get y lo consultamos desde aqui
// $ambito_global = $_GET["ambito_global"]; //Mejor no lo pasamos por el get y lo consultamos desde aqui
$nombreusuariovalidado = ( $_GET [ "nombreusuariovalidado" ] );
$tipo_deseado_1 = $_GET [ "tipo_1" ];
$tipo_deseado_2 = $_GET [ "tipo_2" ];
$tipo_deseado_3 = $_GET [ "tipo_3" ];
$tipo_deseado_4 = $_GET [ "tipo_4" ];
$tipo_deseado_5 = $_GET [ "tipo_5" ];
$tipo_deseado_6 = $_GET [ "tipo_6" ];
$tipo_deseado_7 = $_GET [ "tipo_7" ];
$tipo_deseado_8 = $_GET [ "tipo_8" ];
$tipo_deseado_9 = $_GET [ "tipo_9" ];
$tipo_deseado_A = $_GET [ "tipo_A" ];
$lista_personalizada = $_GET [ "lista_personalizada" ];

$externo = $_GET [ "externo" ];
//Si el usuario es externo debe de ver todas sus tareas
if($externo != ''){
	$tipo_deseado_1 = 1; //1-Todas las tareas, 2-Solo tareas de depto-programa, 3-Solo mis tareas
}

$records_per_page = $_GET [ "pq_rpp" ];
$cur_page = $_GET [ "pq_curpage" ];
if ( $cur_page == '0' ) {
	$cur_page = '1';
}
$pq_sort = $_GET [ "pq_sort" ];
$sortQuery = SortHelper :: deSerializeSort ( $pq_sort , 'TyP.' );
$sortQuery2 = SortHelper :: deSerializeSort ( $pq_sort , 'Resultado.' );
$filterQuery = "";
$filterParam = array ();
if ( isset ( $_GET [ "pq_filter" ] ) ) {
	$pq_filter = $_GET [ "pq_filter" ];
	$dsf = FilterHelper :: deSerializeFilter ( $pq_filter );
	$filterQuery = $dsf -> query;
	$filterParam = $dsf -> param;
}
$contador = 0;
$filas_totales = 0;
$tareas = array ();
if ( isset ( $_GET [ "historial" ] ) ) {
	$solo_el_historial = true;
	$solo_el_historial_cantidad = $_GET [ "historial" ];
} else {
	$solo_el_historial = false;
	$solo_el_historial_cantidad = '6';
}
$filtro_adicional = ( $filterQuery );



DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

// ****************************************************FILTRO ADICIONAL DE TIPO 1
if ( 1 == 1 ) {
	
	if ( $tipo_deseado_1 == '1' ) {
		// No hacemos nada adicional
	}
	if ( $tipo_deseado_1 == '2' ) {
		// Solamente las de mi departamento programa
		if ( $filtro_adicional == '' ) {
			$filtro_adicional = ' WHERE ';
		} else {
			$filtro_adicional = $filtro_adicional . ' AND ';
		}
		// Alberto 12/05/2017, quito el uso del campo de oficina para los responsables por que no se usa y además hace un hardcode solo para nuestra BD GestionPDT
		// es necesario para otras cosas
		if ( false ) {
			$oficina_usuario_validado = '';
			$qq = DBSelect ( utf8_decode ( "SELECT department,sAMAccountName,Name,physicalDeliveryOfficeName  FROM	(SELECT physicalDeliveryOfficeName,department,sAMAccountName ,Name,mail,CASE WHEN ((2 & userAccountControl) = 2 ) THEN 0 ELSE 1 END AS Activo  FROM OpenQuery(ADSI, 'SELECT physicalDeliveryOfficeName,department,sAMAccountName,Name,mail,userAccountControl FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user'' AND objectClass=''user''  AND sAMAccountName = ''"./*utf8_encode*/( $id_usuario ) . "''')) Consulta" ) );
			for(; DBNext ( $qq ) ;) {
				$oficina_usuario_validado = utf8_encode ( DBCampo ( $qq , "physicalDeliveryOfficeName" ) );
			}
			DBFree ( $qq );
			if ( $oficina_usuario_validado == 'Responsable' ) {
				$filtro_adicional = $filtro_adicional . " ([Departamento Solicitud] = (SELECT TOP 1 Departamento.[Departamento Solicitud] FROM [GestionPDT].[dbo].[Tareas y Proyectos] Departamento WHERE Departamento.solicitado='" . utf8_decode ( $usuario_deseado ) . "' order by Departamento.[Fecha alta] desc) AND
				[Programa Solicitud] = (SELECT TOP 1 Programa.[Programa Solicitud] FROM [GestionPDT].[dbo].[Tareas y Proyectos] Programa WHERE Programa.solicitado='" . utf8_decode ( $usuario_deseado ) . "' order by Programa.[Fecha alta] desc)) ";
			}
			if ( $oficina_usuario_validado == 'Responsable Superior' ) {
				$filtro_adicional = $filtro_adicional . " ([Departamento Solicitud] = (SELECT TOP 1 Departamento.[Departamento Solicitud] FROM [GestionPDT].[dbo].[Tareas y Proyectos] Departamento WHERE Departamento.solicitado='" . utf8_decode ( $usuario_deseado ) . "' order by Departamento.[Fecha alta] desc)) ";
			}
		}
	}
	if ( $tipo_deseado_1 == '3' ) {
		// Solamente las mias
		if ( $filtro_adicional == '' ) {
			$filtro_adicional = ' WHERE ';
		} else {
			$filtro_adicional = $filtro_adicional . ' AND ';
		}
		$filtro_adicional = $filtro_adicional . " [Asignado a] = '"./*utf8_decode*/( $usuario_deseado ) . "' ";
	}
}
// ****************************************************FILTRO ADICIONAL

// ****************************************************FILTRO ADICIONAL DE TIPO 2
if ( 1 == 1 ) {
	
	if ( $tipo_deseado_2 == '1' ) {
		// No hacemos nada adicional
	}
	if ( $tipo_deseado_2 == '2' ) {
		// Solamente las de mi departamento programa
		if ( $filtro_adicional == '' ) {
			$filtro_adicional = ' WHERE ';
		} else {
			$filtro_adicional = $filtro_adicional . ' AND ';
		}
		$filtro_adicional = $filtro_adicional . " ([asignado a] = '"./*utf8_decode*/( $usuario_deseado ) . "' OR Id IN (Select Horas.Tarea FROM [Horas] Horas WHERE Horas.[Usuario] = '"./*utf8_decode*/( $id_usuario ) . "')) ";
	}
}
// ****************************************************FILTRO ADICIONAL

// ****************************************************FILTRO ADICIONAL DE TIPO 3
if ( 1 == 1 ) {
	
	if ( $tipo_deseado_3 == '1' ) {
		// No hacemos nada adicional
	}
	if ( $tipo_deseado_3 == '2' ) {
		// Solamente las que estan activas
		if ( $filtro_adicional == '' ) {
			$filtro_adicional = ' WHERE ';
		} else {
			$filtro_adicional = $filtro_adicional . ' AND ';
		}
		$filtro_adicional = $filtro_adicional . " (Id IN (Select Horas.Tarea FROM [Horas] Horas WHERE Horas.Tarea = Tarea AND Horas.Fin is null)) ";
	}
}
// ****************************************************FILTRO ADICIONAL

// ****************************************************FILTRO ADICIONAL DE TIPO 4
if ( 1 == 1 ) {
	
	if ( $tipo_deseado_4 == '1' ) {
		// No hacemos nada adicional
	}
	if ( $tipo_deseado_4 == '2' ) {
		// Solamente las que estan activas
		if ( $filtro_adicional == '' ) {
			$filtro_adicional = ' WHERE ';
		} else {
			$filtro_adicional = $filtro_adicional . ' AND ';
		}
		$filtro_adicional = $filtro_adicional . "   (Estado IN (SELECT Descripcion FROM [Tipos] WHERE Pendiente = 1 AND Tipo = 5)) ";
	}
}
// ****************************************************FILTRO ADICIONAL

// ****************************************************FILTRO ADICIONAL DE TIPO 5
if ( 1 == 1 ) {
	
	if ( $tipo_deseado_5 == '1' ) {
		// No hacemos nada adicional
	}
	if ( $tipo_deseado_5 == '2' ) {
		// Solamente las que son recientes
		if ( $filtro_adicional == '' ) {
			$filtro_adicional = ' WHERE ';
		} else {
			$filtro_adicional = $filtro_adicional . ' AND ';
		}
		
		$filtro_adicional = $filtro_adicional . "   (Id IN (
		
			SELECT HorasReciente.Tarea 
			FROM [Horas] HorasReciente
			WHERE			
			(HorasReciente.Inicio BETWEEN 
			(CAST(CONVERT(varchar(120),GETDATE()-ISNULL((SELECT TOP 1 CAST([Valor] as int)
			FROM [Configuracion] tiemporeciente
			WHERE tiemporeciente.PArametro = 'tiemporeciente' AND tiemporeciente.USuario = '"./*utf8_decode*/( $id_usuario ) . "'),3),105)+ ' 00:00:00.000' as datetime))
			AND
			GETDATE()
			OR
			HorasReciente.Fin BETWEEN 
			(CAST(CONVERT(varchar(120),GETDATE()-ISNULL((SELECT TOP 1 CAST([Valor] as int)
			FROM [Configuracion] tiemporeciente
			WHERE tiemporeciente.PArametro = 'tiemporeciente' AND tiemporeciente.USuario = '"./*utf8_decode*/( $id_usuario ) . "'),3),105)+ ' 00:00:00.000' as datetime))
			AND
			GETDATE())
		
		)) ";
	}
}
// ****************************************************FILTRO ADICIONAL

// ****************************************************FILTRO ADICIONAL DE TIPO 6
if ( 1 == 1 ) {
	
	if ( $tipo_deseado_6 == '1' ) {
		// No hacemos nada adicional
	}
	if ( $tipo_deseado_6 == '2' ) {
		// Solamente las que son recientes
		if ( $filtro_adicional == '' ) {
			$filtro_adicional = ' WHERE ';
		} else {
			$filtro_adicional = $filtro_adicional . ' AND ';
		}
		
		$filtro_adicional = $filtro_adicional . "   ([Asignado a] = '') ";
	}
}
// ****************************************************FILTRO ADICIONAL

// ****************************************************FILTRO ADICIONAL DE TIPO 7
if ( 1 == 1 ) {
	
	if ( $tipo_deseado_7 == '1' ) {
		// No hacemos nada adicional
	}
	if ( $tipo_deseado_7 == '2' ) {
		// Solamente las que son recientes
		if ( $filtro_adicional == '' ) {
			$filtro_adicional = ' WHERE ';
		} else {
			$filtro_adicional = $filtro_adicional . ' AND ';
		}
		
		$filtro_adicional = $filtro_adicional . "   (Estado IN (SELECT Descripcion FROM [Tipos] WHERE pendiente = 1 AND Tipo = 5) AND Valoración <> 'Desestimado') ";
	}
}
// ****************************************************FILTRO ADICIONAL

// ****************************************************FILTRO ADICIONAL DE TIPO 8
if ( 1 == 1 ) {
	
	if ( $tipo_deseado_8 == '1' ) {
		// No hacemos nada adicional
	}
	if ( $tipo_deseado_8 == '2' ) {
		// Solamente las que son recientes
		if ( $filtro_adicional == '' ) {
			$filtro_adicional = ' WHERE ';
		} else {
			$filtro_adicional = $filtro_adicional . ' AND ';
		}
		
		$filtro_adicional = $filtro_adicional . "    ([Fecha objetivo] < CONVERT(varchar(20),getdate(),105)
				AND [Estado] NOT IN 
				(SELECT Descripcion FROM [Tipos] WHERE Finalizado = 1 AND Tipo = 5))";
	}
}
// ****************************************************FILTRO ADICIONAL
// ****************************************************FILTRO ADICIONAL DE TIPO 9
if ( 1 == 1 ) {
	
	if ( $tipo_deseado_9 == '1' ) {
		// No hacemos nada adicional
	}
	if ( $tipo_deseado_9 == '2' ) {
		// Solamente las que son recientes
		if ( $filtro_adicional == '' ) {
			$filtro_adicional = ' WHERE ';
		} else {
			$filtro_adicional = $filtro_adicional . ' AND ';
		}
		
		$filtro_adicional = $filtro_adicional . "    ([Teletrabajo] = 'SI')";
	}
}
// ****************************************************FILTRO ADICIONAL
// ****************************************************FILTRO ADICIONAL DE TIPO PERSONAL
if ( 1 == 1 ) {
	if ( $lista_personalizada != "" ) {
		$query = DBSelect ( ( "SELECT [Consulta] FROM [Consultas_Personalizadas] where numero IN (" . $lista_personalizada . ")" ) );
		for(; DBNext ( $query ) ;) {
			$Consultas_Personalizada = utf8_encode ( DBCampo ( $query , "Consulta" ) );
			// Solamente las que estan activas
			if ( $filtro_adicional == '' ) {
				$filtro_adicional = ' WHERE ';
			} else {
				$filtro_adicional = $filtro_adicional . ' AND ';
			}
			$filtro_adicional = $filtro_adicional . " (" . $Consultas_Personalizada . ") ";
		}
		DBFree ( $query );
	}
}
// ****************************************************FILTRO ADICIONAL

// ****************************************************FILTRO ADICIONAL DE TIPO A
if ( 1 == 1 ) {
	
	if ( $tipo_deseado_A == '-1' ) {
		// No hacemos nada adicional
	} else {
		if ( $tipo_deseado_A == '0' ) {
			// Solamente las que estan activas
			if ( $filtro_adicional == '' ) {
				$filtro_adicional = ' WHERE ';
			} else {
				$filtro_adicional = $filtro_adicional . ' AND ';
			}
			$filtro_adicional = $filtro_adicional . "  (Cola IN (SELECT Colas.[Descripcion]     
			FROM [Colas_Usuarios] Usuarios INNER JOIN [Colas] Colas ON Usuarios.Cola = Colas.Numero
			WHERE Usuarios.Usuario = '"./*utf8_decode*/( $id_usuario ) . "') 
			
			OR
			
			(Cola IS NULL AND  (SELECT COUNT(*) FROM [Colas_Usuarios] Usuarios INNER JOIN [Colas] Colas ON Usuarios.Cola = Colas.Numero WHERE Colas.[Descripcion] = '' AND Usuarios.Usuario = '"./*utf8_decode*/( $id_usuario ) . "'  ) > 0)
			
			 )";
		} else {
			// Solamente las que estan activas
			if ( $filtro_adicional == '' ) {
				$filtro_adicional = ' WHERE ';
			} else {
				$filtro_adicional = $filtro_adicional . ' AND ';
			}
			$filtro_adicional = $filtro_adicional . "  (Cola IN (SELECT Colas.[Descripcion]     
			FROM [Colas_Usuarios] Usuarios INNER JOIN [Colas] Colas ON Usuarios.Cola = Colas.Numero
			WHERE Usuarios.Usuario = '"./*utf8_decode*/( $id_usuario ) . "' AND Colas.Numero = " . $tipo_deseado_A . ")
			
			OR
			
			(Cola IS NULL AND  (SELECT COUNT(*) FROM [Colas_Usuarios] Usuarios INNER JOIN [Colas] Colas ON Usuarios.Cola = Colas.Numero WHERE Colas.[Descripcion] = '' AND Usuarios.Usuario = '"./*utf8_decode*/( $id_usuario ) . "' AND Colas.Numero = " . $tipo_deseado_A . " ) > 0)
			
			 )";
		}
		// echo $filtro_adicional;
	}
}
// ****************************************************FILTRO ADICIONAL

// ****************************************************SOLO HISTORIAL
if ( 2 == 2 ) {
	if ( $solo_el_historial == true ) {
		$filtro_adicional = " WHERE Id IN 
		(SELECT TOP (" . $solo_el_historial_cantidad . ") 
	   [Tarea]
        FROM [Historico]
	    where 
	    Usuario = '"./*utf8_decode*/( $id_usuario ) . "'   
	    order by prioridad asc,fecha desc) ";
	}
}
// ****************************************************SOLO HISTORIAL

// ****************************************************FILTRO ADICIONAL FIJO 1
if ( $idvisualizacion ==  - 1 ) {
	
	// Solamente las que son recientes
	if ( $filtro_adicional == '' ) {
		$filtro_adicional = ' WHERE ';
	} else {
		$filtro_adicional = $filtro_adicional . ' AND ';
	}
	
	$filtro_adicional = $filtro_adicional . "  
	(	
	YEAR([Fecha alta]) >= ISNULL ( ( SELECT TOP 1 CASE ISNUMERIC([Valor]) 
											WHEN 1 THEN	CAST([Valor] as int) 
											ELSE 2000 END
									FROM [Configuracion]
									WHERE Parametro = 'usuariohistorico')
								,2000)	
	) ";
}
// ****************************************************FILTRO ADICIONAL FIJO 1

// ****************************************************FILTRO ADICIONAL FIJO
if ( 1 == 1 ) {
	
	// Solamente las que son recientes
	if ( $filtro_adicional == '' ) {
		$filtro_adicional = ' WHERE ';
	} else {
		$filtro_adicional = $filtro_adicional . ' AND ';
	}
	
	$filtro_adicional = $filtro_adicional . "  ([Control] = 0 OR [Control] is null) ";
}
// ****************************************************FILTRO ADICIONAL FIJO

// ****************************************************FILTRO ADICIONAL DE AMBITOS
// CUANDO EL USUARIO ENTRA COMO EXTERNO SOLO DEBERÁ VER LAS TAREAS DEL DEPARTAMENTO
// EXTERNO EN LAS QUE SEA EL SOLICITANTE, INDEPENDIENTEMENTE DEL ASIGNADO QUE SEA
// *** OBTENER DATOS ***
$oficinas = '';
$ambito = 0;
$ambito_global = 0;
$query = "SELECT oficina, ambito, ambito_global FROM [GestionPDT].[dbo].Configuracion_Usuarios WHERE usuario = '$id_usuario'";
$query = DBSelect ( utf8_decode ( $query ) );
for(; DBNext ( $query ) ;) {
	$oficinas = utf8_encode ( DBCampo ( $query , "oficina" ) );
	$ambito = utf8_encode ( DBCampo ( $query , "ambito" ) );
	$ambito_global = utf8_encode ( DBCampo ( $query , "ambito_global" ) );
}
DBFree ( $query );

// LOS USUARIOS QUE ENTRAN COMO EXTERNO TIENEN LA OFICINA EXTERNO, AMBITOS A 0 Y FILTRO DE SOLICITADO A EL MISMO
if ($externo != ''){
	$oficinas = $externo;
	$ambito = 0;
	$ambito_global = 0;

	if ( $filtro_adicional == '' ) {
		$filtro_adicional = ' WHERE ';
	} else {
		$filtro_adicional = $filtro_adicional . ' AND ';
	}
	
	$filtro_adicional = $filtro_adicional . "  [Solicitado] = '$nombreusuariovalidado' ";
}

// *** FILTRO DE AMBITO INDIVIDUAL ***
if ( $ambito == 1 ) {
	
	if ( $filtro_adicional == '' ) {
		$filtro_adicional = ' WHERE ';
	} else {
		$filtro_adicional = $filtro_adicional . ' AND ';
	}
	
	$filtro_adicional = $filtro_adicional . " ( [Asignado a] = '$nombreusuariovalidado' OR  0 < (SELECT COUNT(*) FROM Colaboradores AS COL WHERE COL.Estado = 'Activo' AND COL.Id_Tarea = TyP.Id AND COL.Colaborador = '$id_usuario' and COL.acceso='Permitido' )  )    ";
}

// *** FILTRO DE AMBITO GLOBAL ***
if ( $ambito_global == 0 ) {
	
	$oficinas = str_replace ( "," , "','" , $oficinas );
	$oficinas = "'" . $oficinas . "'";
	
	if ( $filtro_adicional == '' ) {
		$filtro_adicional = ' WHERE ';
	} else {
		$filtro_adicional = $filtro_adicional . ' AND ';
	}
	
	$filtro_adicional = $filtro_adicional . " ( [Oficina] IN ($oficinas) OR 0 < (SELECT COUNT(*) FROM Colaboradores AS COL WHERE COL.Estado = 'Activo' AND COL.Id_Tarea = TyP.Id AND COL.Colaborador = '$id_usuario' and COL.acceso='Permitido' )  )          ";
}

// ****************************************************FILTRO ADICIONAL DE AMBITOS

// ****************************************************OBTENEMOS LAS INCIDENCIAS
if ( 2 == 2 ) {

	// //////////////////////////////////////////////////////////////////////////////////////////////
	// //////////////////////////////////////////////////////////////////////////////////////////////
	// //////////////////////////////////////////////////////////////////////////////////////////////
	
	$q = "
	SELECT 
		[Oficina],
		Evaluaciones_Pendientes,
		--CASE ISNULL([Estrategico],0) WHEN 0 THEN 'NO' ELSE 'SI' END AS Estrategico,
		Estrategico,
		CAST(CASE ISNULL([Tarea / Proyecto],0) WHEN 0 THEN CAST('' as varchar(20)) ELSE
				CAST([Tarea / Proyecto] as varchar(20)) + ' - ' +
				ISNULL((SELECT Proyecto.[Título]
						FROM [Tareas y Proyectos] Proyecto
						WHERE Proyecto.id = ISNULL(Resultado.[Tarea / Proyecto],-3)),'')
		END as varchar(MAX)) as [Tarea / Proyecto],
	
		(SELECT COUNT(*)
			FROM [Historico] Historico
			WHERE Historico.Tarea = Id 
				AND Historico.Marcada = 1
				AND Historico.Usuario = '$id_usuario') AS MARCADA_BANDERA,
		CONVERT(varchar(20), [Fecha Solicitud],105) as [Fecha Solicitud],
		CAST([Departamento Solicitud] as varchar(250)) as [Departamento Solicitud],
		CAST([Programa Solicitud] as varchar(250)) as [Programa Solicitud],
		CAST([Motivo bap] as varchar(250)) as [Motivo bap],
		CAST([Referencia] as varchar(250)) as Referencia,
		[Horas estimadas] as [Horas estimadas],
		CAST( 	CASE (	SELECT COUNT(*)
						FROM [Configuracion]
						WHERE USuario = '$id_usuario'   and Parametro = 'mi_colormio' AND Valor <> '') 
				WHEN 1 THEN ISNULL((SELECT Parametro
									FROM [Configuracion]
									WHERE USuario = '$id_usuario' 
										AND Parametro = 'mi_colormio' 
										AND Valor <> '' 
										AND ([Asignado a] = '$usuario_deseado' OR '$id_usuario' IN (SELECT Horas.Usuario FROM [Horas] AS Horas WHERE Horas.Tarea = [Id]))
									),'sincolor_0')	
				WHEN 0 THEN CASE (	SELECT COUNT(*) 
									FROM [Configuracion] 
									WHERE Parametro IN (CAST((	SELECT TOP 1 Tipos.[Numero]
																FROM [Tipos] Tipos
																WHERE Tipos.tipo = 4
																	AND Descripcion = [Prioridad]) as varchar(250))+'_colorprioridad')
										AND usuario = '$id_usuario'  
										AND valor <> '' ) 
							WHEN 0 THEN ISNULL('colorestado_'+CAST((	SELECT TOP 1 Tipos.[Numero]
																		FROM [Tipos] Tipos
																		WHERE Tipos.tipo = 5
																			AND Tipos.oficina = (select Top 1 Oficina from Configuracion_Usuarios where Usuario = '$id_usuario' and Baja = 0)	
																			AND Descripcion = [Estado]) AS varchar(250)),'sincolor_0')	
							WHEN 1 THEN 'colorprioridad_'+CAST((	SELECT TOP 1 Tipos.[Numero]
																	FROM [Tipos] Tipos
																	WHERE Tipos.tipo = 4
																			AND Tipos.oficina = (select Top 1 Oficina from Configuracion_Usuarios where Usuario = '$id_usuario' and Baja = 0)	
																	AND Descripcion = [Prioridad]) as varchar(250))
							END 
				END as varchar(250)) AS CLASE_COLOR,
		CAST(Cola as varchar(250)) as [Cola],
		CONVERT(varchar(20), [Fecha alta],105) AS [Fecha alta],
		CONVERT(varchar(20), [Fecha objetivo],105) AS [Fecha objetivo],
		CAST(Usuario as varchar(250)) as [Usuario],
		CAST(Subcategoría as varchar(250)) as [Subcategoría], 
		CAST(Actividad as varchar(250)) as [Actividad], 
		CAST(Solicitado as varchar(250)) as [Solicitado],
		CAST(Estado as varchar(250)) as [Estado],
		CAST(Prioridad as varchar(250)) as [Prioridad],
		CAST([Asignado a] as varchar(250)) as [Asignado a],
		CAST([Destinatario] as varchar(250)) as [Destinatario],
		CAST(Tipo as varchar(250)) as [Tipo],
		CAST(Título as varchar(250)) as [Título],
		CAST(Categoría as varchar(250)) as [Categoría],
		[Id],
		[Fila],
		CASE (Select COUNT(*) FROM [Horas] Horas WHERE Horas.Tarea = Id AND Horas.Fin is null)
			WHEN 0 THEN 0 ELSE 1 END AS [Iniciado_SN],
		(Select COUNT(*) FROM [Horas] Horas WHERE Horas.Tarea = Id AND Horas.Fin is null AND Horas.Usuario = '$id_usuario' ) AS [Iniciado_Usuario],
		CASE (SELECT COUNT(*) FROM Colaboradores AS COL WHERE COL.Estado = 'Activo' AND COL.Id_Tarea = Resultado.Id AND COL.Colaborador = '$id_usuario' and COL.acceso='Permitido') 
			WHEN 0 THEN 0 ELSE 1 END AS [Usuario_colabora_sn],
		[AporteOrganizacion],
		[AporteEmpresa],
		[AporteJefe],
		Evaluable,
		Planificada,
		ISNULL(HorasReales,0) 		AS HorasReales,
		Num_Registros 
	
	FROM (SELECT 
			[Oficina],
			[Estrategico],
			[Tarea / Proyecto],
			[Fecha Solicitud],
			[Departamento Solicitud],
			[Programa Solicitud],
			[Motivo bap],
			[Referencia],
			[Horas estimadas],
			CAST([Cola] as varchar(250)) as Cola,
			[Fecha alta],
			[Fecha objetivo],
			[Usuario],
			[Subcategoría],
			[Actividad],
			[Solicitado],
			[Estado],
			[Prioridad],
			[Asignado a],
			[Destinatario],
			Tipo,
			Título,
			Categoría, 
			Id,
			ROW_NUMBER() OVER($sortQuery) AS Fila,
			[AporteOrganizacion],
			[AporteEmpresa],
			[AporteJefe],
			Evaluable,
	  		(SELECT COUNT(*) FROM Planificador WHERE Tarea = TyP.Id AND Estado = 'Activo') AS Planificada,
			(SELECT SUM(Minutos) 
				FROM Horas HORAS 
				WHERE TyP.Id = HORAS.Tarea) 		AS HorasReales,
			(	SELECT COUNT(EVA.Id) as Evaluaciones 
				FROM Evaluaciones AS EVA 
				WHERE Usuario_Nombre = TyP.[Asignado a]
					AND EVA.Fecha_Usuario IS NULL --NO ESTÁ EVALUADA AL COMPLETO
					AND EVA.Tarea = TyP.[Id]
					AND EVA.Automatica = 0
					AND EVA.Periodo = (	SELECT Evaluacion FROM Configuracion_Oficinas AS OFI WHERE OFI.Oficina = TyP.Oficina)) AS Evaluaciones_Pendientes,
			(SELECT COUNT(*) FROM [Tareas y Proyectos] AS TyP $filtro_adicional ) AS Num_Registros 
		FROM [Tareas y Proyectos] AS TyP $filtro_adicional  ) AS Resultado 
		WHERE Fila > ($cur_page * $records_per_page ) - $records_per_page AND Fila <= $cur_page * $records_per_page   $sortQuery2  ";
	//die($filtro_adicional);
	//die(utf8_decode($q));
	$q = DBSelect ( utf8_decode ( $q ) );
	
	for(; DBNext ( $q ) ;) {
		$oficina = utf8_encode ( DBCampo ( $q , utf8_decode ( "Oficina" ) ) );
		$Evaluaciones_Pendientes = utf8_encode ( DBCampo ( $q , utf8_decode ( "Evaluaciones_Pendientes" ) ) );
		$Evaluable = utf8_encode ( DBCampo ( $q , utf8_decode ( "Evaluable" ) ) );
		$CLASE_COLOR = utf8_encode ( DBCampo ( $q , utf8_decode ( "CLASE_COLOR" ) ) );
		$id = DBCampo ( $q , "Id" );
		$referencia = utf8_encode ( DBCampo ( $q , utf8_decode ( "Referencia" ) ) );
		$Estrategico = utf8_encode ( DBCampo ( $q , utf8_decode ( "Estrategico" ) ) );
		$departamentosolicitud = utf8_encode ( DBCampo ( $q , utf8_decode ( "Departamento Solicitud" ) ) );
		$programasolicitud = utf8_encode ( DBCampo ( $q , utf8_decode ( "Programa Solicitud" ) ) );
		$marcadabandera = DBCampo ( $q , "MARCADA_BANDERA" );
		$fechasol = utf8_encode ( DBCampo ( $q , utf8_decode ( "Fecha Solicitud" ) ) );
		$TareaProyecto = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tarea / Proyecto" ) ) );
		$motivobap = utf8_encode ( DBCampo ( $q , utf8_decode ( "Motivo bap" ) ) );
		$cola = utf8_encode ( DBCampo ( $q , utf8_decode ( "Cola" ) ) );
		$titulo = utf8_encode ( DBCampo ( $q , utf8_decode ( "Título" ) ) );
		$tipo = utf8_encode ( DBCampo ( $q , utf8_decode ( "Tipo" ) ) );
		$filas_totales = DBCampo ( $q , utf8_decode ( "Num_Registros" ) );
		$categoria = utf8_encode ( DBCampo ( $q , utf8_decode ( "Categoría" ) ) );
		$asignadoa = utf8_encode ( DBCampo ( $q , utf8_decode ( "Asignado a" ) ) );
		$destinatario = utf8_encode ( DBCampo ( $q , utf8_decode ( "Destinatario" ) ) );
		$prioridad = utf8_encode ( DBCampo ( $q , utf8_decode ( "Prioridad" ) ) );
		$estado = utf8_encode ( DBCampo ( $q , utf8_decode ( "Estado" ) ) );
		$solicitado = utf8_encode ( DBCampo ( $q , utf8_decode ( "Solicitado" ) ) );
		$subcategoria = utf8_encode ( DBCampo ( $q , utf8_decode ( "Subcategoría" ) ) );
		$Actividad = utf8_encode ( DBCampo ( $q , utf8_decode ( "Actividad" ) ) ); // alberto (19510) 2017/10/18
		$usuario = utf8_encode ( DBCampo ( $q , utf8_decode ( "Usuario" ) ) );
		$fechaobj = utf8_encode ( DBCampo ( $q , utf8_decode ( "Fecha objetivo" ) ) );
		$fechaalt = utf8_encode ( DBCampo ( $q , utf8_decode ( "Fecha alta" ) ) );
		$AporteOrganizacion = utf8_encode ( DBCampo ( $q , utf8_decode ( "AporteOrganizacion" ) ) ); // alberto(14204)(2015/10/28)
		$AporteEmpresa = utf8_encode ( DBCampo ( $q , utf8_decode ( "AporteEmpresa" ) ) ); // alberto (19510) 2017/10/18
		$AporteJefe = utf8_encode ( DBCampo ( $q , utf8_decode ( "AporteJefe" ) ) ); // alberto (19510) 2017/10/18
		$iniciado_sn = DBCampo ( $q , "Iniciado_SN" );
		$iniciado_usuario = DBCampo ( $q , "Iniciado_Usuario" ); // alberto 2017/09/18
		$Usuario_colabora_sn = DBCampo ( $q , "Usuario_colabora_sn" ); // alberto 2017/09/19
		$Planificada = DBCampo ( $q , "Planificada" ); // alberto 2020/12/04
		
		$HorasReales = utf8_encode ( DBCampo ( $q , utf8_decode ( "HorasReales" ) ) ); // alberto (24892) 2018/03/06
		$HorasReales = number_format(round($HorasReales / 60, 2), 2, '.', '');// alberto (24892) 2018/03/06
		$horasestimadas = DBCampo ( $q , "Horas estimadas" );// alberto (24892) 2018/03/06
		$horasestimadas = number_format(round($horasestimadas , 2), 2, '.', '');// alberto (24892) 2018/03/06
		

		$color_horas_reales = '';
		if($HorasReales > $horasestimadas){
			$color_horas_reales = 'pqcell_rojo';
		}
		
		$pq_cellcls = array (
				"HorasReales" => $color_horas_reales
		);
		
		$tareas [ $contador ] = array (
				"pq_rowcls" => $CLASE_COLOR ,
				"pq_cellcls" => $pq_cellcls ,
				"AporteOrganizacion" => $AporteOrganizacion ,
				"AporteEmpresa" => $AporteEmpresa ,
				"AporteJefe" => $AporteJefe ,
				"HorasReales" => $HorasReales , 
				"Estrategico" => $Estrategico , 
				"Motivo bap" => $motivobap , 
				"MARCADA_BANDERA" => $marcadabandera , 
				"Tarea / Proyecto" => $TareaProyecto , 
				"Fecha Solicitud" => $fechasol , 
				"Horas estimadas" => $horasestimadas , 
				"Referencia" => $referencia , 
				"Departamento Solicitud" => $departamentosolicitud , 
				"Programa Solicitud" => $programasolicitud , 
				"Id" => $id , 
				"Oficina" => $oficina , 
				"Evaluaciones_Pendientes" => $Evaluaciones_Pendientes , 
				"Evaluable" => $Evaluable,
				"Título" => $titulo , 
				"Categoría" => $categoria , 
				"Tipo" => $tipo ,
				"Asignado a" => $asignadoa ,
				"Destinatario" => $destinatario , 
				"Prioridad" => $prioridad , 
				"Estado" => $estado , 
				"Solicitado" => $solicitado ,
				"Subcategoría" => $subcategoria ,
				"Actividad" => $Actividad , 
				"Usuario" => $usuario , 
				"Fecha objetivo" => $fechaobj , 
				"Fecha alta" => $fechaalt , 
				"iniciado_sn" => $iniciado_sn , 
				"iniciado_usuario" => $iniciado_usuario ,
				"Usuario_colabora_sn" => $Usuario_colabora_sn ,
				"Planificada" => $Planificada,
				"Cola" => $cola 
		);
		$contador = $contador + 1;
	}
}
function dameDescAporte( $param ) {
	$ret = "";
	switch ( $param ) {
		case "1" :
			$ret = 'Bajo';
			break;
		case "2" :
			$ret = "Medio";
			break;
		case "3" :
			$ret = "Alto";
			break;
		default :
			$ret = 'N/A';
	}
	return $ret;
}
// ****************************************************OBTENEMOS LAS INCIDENCIAS

DBFree ( $q );
DBClose ();
$json_arr = array (
		'totalRecords' => $filas_totales , 
		'curPage' => $cur_page , 
		'data' => $tareas 
);
$php_json = json_encode ( $json_arr );

echo $php_json;
// echo $nombreusuariovalidado;
?>