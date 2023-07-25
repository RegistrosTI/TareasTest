<?php
// UTF ñáç
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar ( $GLOBALS [ "cfg_DataBase" ] );
$descrip_con_imagen;
$select_acciones_extras = '';
$arraycampos = Crea_Array_Campos ();
$arraycamposlog = Crea_Array_Campos_Log ();
$select_tareas_y_proyectos = GetUpdateTyP ( $arraycampos );
$select_tareas_y_proyectos_log = GetUpdateTyPLog ( $arraycamposlog );
$select_tareas_y_proyectos_ant = GetUpdateTyPAnt ( $arraycampos , $arraycamposlog );
$select_acciones_extras = $select_acciones_extras . accionesextras ( $arraycampos );

$consulta = $select_tareas_y_proyectos_log . ' ' . $select_tareas_y_proyectos_ant . ' ' . $select_tareas_y_proyectos . ' ' . $select_acciones_extras;

//die($consulta);

if ($_SERVER['REMOTE_ADDR'] == '10.5.30.19'){
	//echo $select_tareas_y_proyectos_log.' <br><br> '.$select_tareas_y_proyectos_ant.' <br><br> '.$select_tareas_y_proyectos.' <br><br> '.$select_acciones_extras;
	//die ();
}

$consulta = DBSelect ( utf8_decode ( $consulta ) );



///
//T#28631 JUAN ANTONIO ABELLAN - (08/02/19) (CCARRASCOSA)

$coste = "Update 
					dbo.[Tareas y Proyectos] 
					SET CosteTarea =  
										(CAST(ISNULL( ( SELECT SUM(CAST(ISNULL(Total_Importes.Importe,0) as decimal(20,2))) 
											FROM (SELECT SUM(Minutos)*(SELECT ISNULL(MAX(Importes.[importe]),0)
											FROM [Importe_Horas] Importes where Importes.usuario=Horas.Usuario) as Importe FROM [Horas] Horas
											WHERE Horas.tarea = Id group by Horas.Usuario) Total_Importes),0) as varchar(120)))
				 WHERE Id=".$_GET [ 'tarea' ];

$consulta2 = DBSelect ( utf8_decode ( $coste ) );

DBClose ();
///

function GetUpdateTyPLog( $arraycampos ) {

	$select_anterior = '';
	$select_no_anterior = '';
	$select = 'INSERT INTO [Log_Tareas_y_Proyectos] (';
	
	foreach ( $arraycampos as $campo ) {
		if ( substr ( $campo [ 'COLUMN_NAME' ] ,  - 8 ) == 'Anterior' ) {
			$select_anterior = $select_anterior . ',[' . $campo [ 'COLUMN_NAME' ] . ']';
		} else {
			$select_no_anterior = $select_no_anterior . ',[' . $campo [ 'COLUMN_NAME' ] . ']';
		}
	}
	
	//die($select . trim ( $select_anterior , "," ) . ',' . trim ( $select_no_anterior , "," ) . ')');
	
	return $select . trim ( $select_anterior , "," ) . ',' . trim ( $select_no_anterior , "," ) . ')';
}
function GetUpdateTyPAnt( $arraycampos , $arraycamposlog ) {
	$select = 'SELECT ';
	$select_anterior = '';
	$select_posterior = '';
	foreach ( $arraycamposlog as $campolog ) {
		if ( substr ( $campolog [ 'COLUMN_NAME' ] ,  - 8 ) == 'Anterior' ) {
			$entre = false;
			foreach ( $arraycampos as $campo ) {
				if ( $campo [ 'COLUMN_NAME' ] == substr ( $campolog [ 'COLUMN_NAME' ] , 0 ,  - 8 ) ) {
					$entre = true;
					$select_anterior = $select_anterior . ',[' . $campo [ 'COLUMN_NAME' ] . ']';
				}
			}
			if ( $entre == false ) {
				if ( $campolog [ 'IS_NULLABLE' ] == 'YES' ) {
					$select_anterior = $select_anterior . ',NULL';
				} else {
					if ( $campolog [ 'DATA_TYPE' ] == 'varchar' || $campolog [ 'DATA_TYPE' ] == 'nvarchar' || $campolog [ 'DATA_TYPE' ] == 'text' || $campolog [ 'DATA_TYPE' ] == 'ntext' ) {
						$select_anterior = $select_anterior . ',\'\'';
					}
					if ( $campolog [ 'DATA_TYPE' ] == 'float' || $campolog [ 'DATA_TYPE' ] == 'int' ) {
						$select_anterior = $select_anterior . ',\'0\'';
					}
					if ( $campolog [ 'DATA_TYPE' ] == 'datetime' ) {
						$select_anterior = $select_anterior . ',\'01/01/1750\'';
					}
				}
			}
		}
	}
	foreach ( $arraycamposlog as $campolog ) {
		if ( substr ( $campolog [ 'COLUMN_NAME' ] ,  - 8 ) == 'Anterior' ) {
		} else {
			$entre = false;
			if ( 'Id' == $campolog [ 'COLUMN_NAME' ] || 'FechaCambio' == $campolog [ 'COLUMN_NAME' ] || 'UsuarioCambia' == $campolog [ 'COLUMN_NAME' ] ) {
				$entre = true;
				if ( 'Id' == $campolog [ 'COLUMN_NAME' ] ) {
					$select_anterior = $select_anterior . ',[Id]';
				}
				if ( 'FechaCambio' == $campolog [ 'COLUMN_NAME' ] ) {
					$select_anterior = $select_anterior . ',GETDATE()';
				}
				if ( 'UsuarioCambia' == $campolog [ 'COLUMN_NAME' ] ) {
					$select_anterior = $select_anterior . ',\'' . $_GET [ 'usuario' ] . '\'';
				}
			} else {
				foreach ( $arraycampos as $campo ) {
					if ( $campo [ 'COLUMN_NAME' ] == $campolog [ 'COLUMN_NAME' ] ) {
						$entre = true;
						if ( $campo [ 'Existe' ] == 1 ) {
							$collation = '';
							if ( $campo [ 'COLLATION_NAME' ] != '' ) {
								$collation = ' COLLATE ' . $campo [ 'COLLATION_NAME' ];
							}
							$select_anterior = $select_anterior . ',\'' . str_replace ( "'" , "''" , ( $campo [ 'Valor' ] ) ) . '\'' . $collation;
							$operador = ' <> ';
							if ( $campo [ 'DATA_TYPE' ] == 'nvarchar' || $campo [ 'DATA_TYPE' ] == 'ntext' ) {
								$operador = ' NOT LIKE ';
							}
							$select_posterior = $select_posterior . '(\'' . str_replace ( "'" , "''" , ( $campo [ 'Valor' ] ) ) . '\' ' . $operador . ' [' . $campo [ 'COLUMN_NAME' ] . ']) OR';
						} else {
							$select_anterior = $select_anterior . ',[' . $campo [ 'COLUMN_NAME' ] . ']';
						}
					}
				}
			}
			if ( $entre == false ) {
				if ( $campolog [ 'IS_NULLABLE' ] == 'YES' ) {
					$select_anterior = $select_anterior . ',NULL';
				} else {
					if ( $campolog [ 'DATA_TYPE' ] == 'varchar' || $campolog [ 'DATA_TYPE' ] == 'nvarchar' || $campolog [ 'DATA_TYPE' ] == 'text' || $campolog [ 'DATA_TYPE' ] == 'ntext' ) {
						$select_anterior = $select_anterior . ',\'\'';
					}
					if ( $campolog [ 'DATA_TYPE' ] == 'float' || $campolog [ 'DATA_TYPE' ] == 'int' ) {
						$select_anterior = $select_anterior . ',\'0\'';
					}
					if ( $campolog [ 'DATA_TYPE' ] == 'datetime' ) {
						$select_anterior = $select_anterior . ',\'01/01/1750\'';
					}
				}
			}
		}
	}
	$select = $select . trim ( $select_anterior , ',' ) . ' FROM [Tareas y Proyectos] WHERE Id = ' . $_GET [ 'tarea' ] . ' AND (' . trim ( $select_posterior , 'OR' ) . ')';
	
	//die($select);
	return $select;
	
}
function GetUpdateTyP( $arraycampos ) {
	$select = 'UPDATE [Tareas y Proyectos] SET [Control] = 0 ';
	foreach ( $arraycampos as $campo ) {
		if ( $campo [ 'Existe' ] == 1 ) {
			$collation = '';
			if ( $campo [ 'COLLATION_NAME' ] != '' ) {
				$collation = ' COLLATE ' . $campo [ 'COLLATION_NAME' ];
			}
			if ( $campo [ 'IS_NULLABLE' ] == true && $campo [ 'Valor' ] == '' ) {
				$select = $select . ',[' . $campo [ 'COLUMN_NAME' ] . '] = NULL ';
			} else {
				$select = $select . ',[' . $campo [ 'COLUMN_NAME' ] . '] = \'' . str_replace ( "'" , "''" , ( $campo [ 'Valor' ] ) ) . '\'' . $collation;
			}
		}
	}
	$select = $select . ' WHERE Id = ' . $_GET [ 'tarea' ];

	return $select;
}
function Crea_Array_Campos() {
	$array_campos = array ();
	$campos = "
			SELECT CAST(ISNULL(COLLATION_NAME,'') as varchar(250)) as COLLATION_NAME
				,COLUMN_NAME
				,ORDINAL_POSITION
				,IS_NULLABLE
				,DATA_TYPE
				,variablepost 
			FROM INFORMATION_SCHEMA.COLUMNS 
			INNER JOIN ventana 
			ON ventana.camposql = ORDINAL_POSITION 
			WHERE TABLE_NAME = 'Tareas y Proyectos' 
			order by ORDINAL_POSITION"; 
	
	$campos = DBSelect ( ( $campos ) );
	
	for(; DBNext ( $campos ) ;) {
		$array_campos [ DBCampo ( $campos , ( "ORDINAL_POSITION" ) ) ] = array (
				"COLLATION_NAME" => utf8_encode ( DBCampo ( $campos , ( "COLLATION_NAME" ) ) ) , 
				"COLUMN_NAME" => utf8_encode ( DBCampo ( $campos , ( "COLUMN_NAME" ) ) ) , 
				"IS_NULLABLE" => DBCampo ( $campos , ( "IS_NULLABLE" ) ) , 
				"DATA_TYPE" => DBCampo ( $campos , ( "DATA_TYPE" ) ) , 
				"Variable" => utf8_encode ( DBCampo ( $campos , ( "variablepost" ) ) ) , 
				"Valor" => null , 
				"Existe" => false 
		);
	}
	DBFree ( $campos );
		
	
	foreach ( $array_campos as &$campo ) {
		
		if ( isset ( $_POST [ $campo [ 'Variable' ] ] ) ) {
			$campo [ 'Existe' ] = true;
			
			// *********TRATAMIENTO DE CAMPOS ESPECIALES
			// *********TRATAMIENTO DE CAMPOS ESPECIALES
			
			if ( false ) {
				// Para listar los nombres de todas las columnas
				echo $campo [ 'Variable' ] . "<br>";
			}
			
			if ( $campo [ 'COLUMN_NAME' ] == 'Tarea / Proyecto' ) {
				
				$valor = addslashes ( htmlspecialchars ( $_POST [ $campo [ 'Variable' ] ] ) );
				if ( $campo [ 'COLUMN_NAME' ] == 'Tarea / Proyecto' ) {
					$valores = explode ( " " , $valor );
					if ( is_array ( $valores ) == true ) {
						$valor = $valores [ 0 ];
					}
					if ( is_numeric ( $valor ) == false ) {
						$valor = '';
					}
				}
				$campo [ 'Valor' ] = $valor;
			} else {
				if ( $campo [ 'COLUMN_NAME' ] == 'Descripción' ) {
					
					global $descrip_con_imagen;
					
					// Alberto 12/09/18 Por el problema que las comillas se reemplazan por más comillas detectamos que es por esta función
					// Despues de probar dos meses, addslashes para todos 
					$campo [ 'Valor' ] = /*addslashes*/ ( htmlspecialchars ( ( $_POST [ $campo [ 'Variable' ] ] ) ) );
					
					if ( strpos ( $campo [ 'Valor' ] , 'data:image' ) !== false || 
							strpos ( $campo [ 'Valor' ] , 'WordDocument' ) !== false || 
							strpos ( $campo [ 'Valor' ] , 'LsdException' ) !== false) {
						$descrip_con_imagen = "SI";
					}else{
						$descrip_con_imagen = "NO";
					}

				} else { // RESTO DE CAMPOS
					
					$campo [ 'Valor' ] = addslashes ( htmlspecialchars ( ( $_POST [ $campo [ 'Variable' ] ] ) ) );
				}
			}
		}
	}
	
	//var_dump($array_campos);
	
	return $array_campos;
}

function Crea_Array_Campos_Log() {
	$array_campos = array ();
	$campos = DBSelect ( ( "SELECT CAST(ISNULL(COLLATION_NAME,'') as varchar(250)) as COLLATION_NAME,COLUMN_NAME,ORDINAL_POSITION,IS_NULLABLE,DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'Log_Tareas_y_Proyectos' order by ORDINAL_POSITION" ) );
	for(; DBNext ( $campos ) ;) {
		$array_campos [ DBCampo ( $campos , ( "ORDINAL_POSITION" ) ) ] = array (
				"COLLATION_NAME" => utf8_encode ( DBCampo ( $campos , ( "COLLATION_NAME" ) ) ) , 
				"COLUMN_NAME" => utf8_encode ( DBCampo ( $campos , ( "COLUMN_NAME" ) ) ) , 
				"IS_NULLABLE" => DBCampo ( $campos , ( "IS_NULLABLE" ) ) , 
				"DATA_TYPE" => DBCampo ( $campos , ( "DATA_TYPE" ) ) 
		);
	}
	DBFree ( $campos );
	return $array_campos;
}

function accionesextras( $arraycampos ) {
	global $descrip_con_imagen;
	$acciones_extras = '';
	if ( isset ( $arraycampos [ 35 ] ) ) {
		if ( $arraycampos [ 35 ] [ 'Existe' ] == false ) {
			$acciones_extras = $acciones_extras . ( 'UPDATE [Tareas y Proyectos] SET [Cola] = (SELECT TOP 1 ISNULL([Cola],(SELECT TOP 1 [Descripcion] FROM [Colas] WHERE [Predeterminado] = 1)) cola FROM [Tipos] where tipo = 2 and  descripcion = [Tareas y Proyectos].[Subcategoría]) FROM [Tareas y Proyectos] WHERE [Tareas y Proyectos].Id = ' ) . $_GET [ 'tarea' ];
		}
	}
	if ( isset ( $arraycampos [ 2 ] ) ) {
		if ( $arraycampos [ 2 ] [ 'Existe' ] == true ) {
			$acciones_extras = $acciones_extras . "DECLARE @titulo varchar(MAX) set @titulo = (SELECT [Título] FROM [Tareas y Proyectos] where id = " . $_GET [ 'tarea' ] . ") EXECUTE [INICIAR_PALABRAS] " . $_GET [ 'tarea' ] . ",1,@titulo ";
		}
	}
	if ( isset ( $arraycampos [ 5 ] ) ) { // SE EJECUTA EL PROCEDIMIENTO ALMACENADO [INICIAR_PALABRAS] CUANDO LA DESCRIPCION NO TENGA IMAGENES
		if ( $arraycampos [ 5 ] [ 'Existe' ] == true && $descrip_con_imagen == 'NO') {
			//ALBERTO: ESTE PROCEDIMIENTO ES MUY LENTO Y NO USAMOS LA FUNCIÓN DE BUSCAR, LO QUITO EN 21/05/2019
			//$acciones_extras = $acciones_extras . "DECLARE @descripcion nvarchar(MAX) set @descripcion = (SELECT [Descripción] FROM [Tareas y Proyectos] where id = " . $_GET [ 'tarea' ] . ") EXECUTE [INICIAR_PALABRAS] " . $_GET [ 'tarea' ] . ",2,@descripcion ";
		}
	}
	// ++ T25876
	if ( isset ( $arraycampos [ 53 ] ) && isset ( $arraycampos [ 4 ] )) {
		if ( $arraycampos [ 53 ] [ 'Existe' ] == true &&  $arraycampos [ 4 ] [ 'Existe' ] == true ) {
			if($arraycampos [ 4 ] [ 'Valor' ] == 'Incidencia Seguridad'){
				$acciones_extras = $acciones_extras . "
					IF (SELECT COUNT(Numero) FROM Mail WHERE Tarea = " . $_GET [ 'tarea' ] . " AND Tipo = 1) = 0
					BEGIN
						INSERT INTO Mail (Tipo,Tarea,Fecha,Usuario,Direccion,Procesado,Envio,Actor) 
						values (1," . $_GET [ 'tarea' ] . ",GETDATE(),'" . $_GET [ 'usuario' ] . "',(SELECT Valor FROM Configuracion WHERE Parametro = 'aviso_incidencias_seguridad'),0,(SELECT MAX(Envio)+1 FROM Mail),1)
					END
					";
			}
			//$acciones_extras = $acciones_extras . "DECLARE @titulo varchar(MAX) set @titulo = (SELECT [Título] FROM [Tareas y Proyectos] where id = " . $_GET [ 'tarea' ] . ") EXECUTE [INICIAR_PALABRAS] " . $_GET [ 'tarea' ] . ",1,@titulo ";
		}
	}
	// -- T25876
	
	$TAREA = $_GET [ 'tarea' ];
	$USUARIO = $_GET [ 'usuario' ];
	$acciones_extras = $acciones_extras . " EXECUTE [INSERTAR_ALERTA] @TAREA = $TAREA, @TIPO = N'alerta_nueva_asignacion', @USUARIO_ALTA = N'$USUARIO'";
	$acciones_extras = $acciones_extras . " EXECUTE [INICIAR_HISTORICO] '$USUARIO'," . $TAREA . ",5";
	$acciones_extras = $acciones_extras . " EXECUTE [INICIAR_OBJETIVO_INICIO] $TAREA ";
	
	return $acciones_extras;
}
?>