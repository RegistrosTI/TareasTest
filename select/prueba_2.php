<?
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_CONTABILIDAD.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);
$select_acciones_extras        = '';
$arraycampos                   = Crea_Array_Campos();
$arraycamposlog                = Crea_Array_Campos_Log();
$select_tareas_y_proyectos     = GetUpdateTyP($arraycampos);
$select_tareas_y_proyectos_log = GetUpdateTyPLog($arraycamposlog);
$select_tareas_y_proyectos_ant = GetUpdateTyPAnt($arraycampos,$arraycamposlog);
$select_acciones_extras        = $select_acciones_extras.accionesextras($arraycampos);

$consulta=DBSelect(($select_tareas_y_proyectos_log.' '.$select_tareas_y_proyectos_ant.' '.$select_tareas_y_proyectos.' '.$select_acciones_extras));
DBClose();


function GetUpdateTyPLog($arraycampos)
{
	$select_anterior    = '';
	$select_no_anterior = '';
	$select             = 'INSERT INTO [Log_Tareas_y_Proyectos] (';
	foreach ($arraycampos as $campo)
	{	
		if(substr($campo['COLUMN_NAME'],-8)=='Anterior')
		{			
			$select_anterior    = $select_anterior.',['.$campo['COLUMN_NAME'].']';
		}
		else
		{
			$select_no_anterior = $select_no_anterior.',['.$campo['COLUMN_NAME'].']';
		}
	}	
	return $select.trim($select_anterior, ",").','.trim($select_no_anterior, ",").')';
}
function GetUpdateTyPAnt($arraycampos,$arraycamposlog)
{
	$select             = 'SELECT ';
	$select_anterior    = '';
	$select_posterior   = '';
	foreach ($arraycamposlog as $campolog)
	{	
		if(substr($campolog['COLUMN_NAME'],-8)=='Anterior')
		{	
			$entre = false;
			foreach ($arraycampos as $campo)
			{
				if($campo['COLUMN_NAME']==substr($campolog['COLUMN_NAME'],0,-8))
				{
					$entre           = true;
					$select_anterior = $select_anterior.',['.$campo['COLUMN_NAME'].']';
				}
			}
			if($entre==false)
			{
				if($campolog['IS_NULLABLE']=='YES')
				{
					$select_anterior = $select_anterior.',NULL';
				}
				else
				{
					if($campolog['DATA_TYPE']=='varchar' || $campolog['DATA_TYPE']=='nvarchar' || $campolog['DATA_TYPE']=='text' || $campolog['DATA_TYPE']=='ntext')
					{    
						$select_anterior = $select_anterior.',\'\'';
					}
					if($campolog['DATA_TYPE']=='float' || $campolog['DATA_TYPE']=='int')
					{    
						$select_anterior = $select_anterior.',\'0\'';
					}
					if($campolog['DATA_TYPE']=='datetime')
					{    
						$select_anterior = $select_anterior.',\'01/01/1750\'';
					}				  
				}
			}
		}
	}
	foreach ($arraycamposlog as $campolog)
	{	
		if(substr($campolog['COLUMN_NAME'],-8)=='Anterior')
		{
		}
		else
		{
			$entre = false;
			if('Id'==$campolog['COLUMN_NAME'] || 'FechaCambio'==$campolog['COLUMN_NAME'] || 'UsuarioCambia'==$campolog['COLUMN_NAME'])
			{
				$entre           = true;
				if('Id'==$campolog['COLUMN_NAME'])
				{
					$select_anterior = $select_anterior.',[Id]';
				}
				if('FechaCambio'==$campolog['COLUMN_NAME'])
				{
					$select_anterior = $select_anterior.',GETDATE()';
				}
				if('UsuarioCambia'==$campolog['COLUMN_NAME'])
				{
					$select_anterior = $select_anterior.',\''.$_GET['usuario'].'\'';
				}
			}
			else
			{
				foreach ($arraycampos as $campo)
				{
					if($campo['COLUMN_NAME']==$campolog['COLUMN_NAME'])
					{
						$entre           = true;
						if($campo['Existe']==1)
						{
							$collation = '';
							if($campo['COLLATION_NAME']!='')
							{
								$collation = ' COLLATE '.$campo['COLLATION_NAME'];
							}
							$select_anterior  = $select_anterior.',\''.str_replace("'", "''", ($campo['Valor'])).'\''.$collation;
							$operador         = ' <> ';
							if($campo['DATA_TYPE']=='nvarchar' || $campo['DATA_TYPE']=='ntext')
							{ 
								$operador     = ' NOT LIKE ';
							}
							$select_posterior = $select_posterior.'(\''.str_replace("'", "''", ($campo['Valor'])).'\' '.$operador.' ['.$campo['COLUMN_NAME'].']) OR';							
						}
						else
						{
							$select_anterior = $select_anterior.',['.$campo['COLUMN_NAME'].']';
						}
					}
				}
			}
			if($entre==false)
			{
				if($campolog['IS_NULLABLE']=='YES')
				{
					$select_anterior = $select_anterior.',NULL';
				}
				else
				{
					if($campolog['DATA_TYPE']=='varchar' || $campolog['DATA_TYPE']=='nvarchar' || $campolog['DATA_TYPE']=='text' || $campolog['DATA_TYPE']=='ntext')
					{    
						$select_anterior = $select_anterior.',\'\'';
					}
					if($campolog['DATA_TYPE']=='float' || $campolog['DATA_TYPE']=='int')
					{    
						$select_anterior = $select_anterior.',\'0\'';
					}
					if($campolog['DATA_TYPE']=='datetime')
					{    
						$select_anterior = $select_anterior.',\'01/01/1750\'';
					}				  
				}
			}
		}
	}	
	$select = $select.trim($select_anterior,',').' FROM [Tareas y Proyectos] WHERE Id = '.$_GET['tarea'].' AND ('.trim($select_posterior,'OR').')';
	return $select;
}
function GetUpdateTyP($arraycampos)
{
	$select = 'UPDATE [Tareas y Proyectos] SET [Control] = 0 ';
	foreach ($arraycampos as $campo)
	{
		if($campo['Existe']==1)
		{
			$collation = '';
			if($campo['COLLATION_NAME']!='')
			{
				$collation = ' COLLATE '.$campo['COLLATION_NAME'];
			}
			$select = $select.',['.$campo['COLUMN_NAME'].'] = \''.str_replace("'", "''", ($campo['Valor'])).'\''.$collation;
		}
	}
	$select = $select.' WHERE Id = '.$_GET['tarea'];
	return $select;
}
function Crea_Array_Campos()
{
	$array_campos = array();
	$campos       = DBSelect(("SELECT CAST(ISNULL(COLLATION_NAME,'') as varchar(250)) as COLLATION_NAME,COLUMN_NAME,ORDINAL_POSITION,IS_NULLABLE,DATA_TYPE,variablepost FROM INFORMATION_SCHEMA.COLUMNS INNER JOIN ventana ON ventana.camposql = ORDINAL_POSITION WHERE TABLE_NAME = 'Tareas y Proyectos' order by ORDINAL_POSITION"));	
	for(;DBNext($campos);)
	{
		$array_campos[DBCampo($campos,("ORDINAL_POSITION"))] = array("COLLATION_NAME" => (DBCampo($campos,("COLLATION_NAME"))),"COLUMN_NAME" => (DBCampo($campos,("COLUMN_NAME"))),"IS_NULLABLE" => DBCampo($campos,("IS_NULLABLE")), "DATA_TYPE" => DBCampo($campos,("DATA_TYPE")), "Variable" => (DBCampo($campos,("variablepost"))),"Valor" => null, "Existe" => false);		
	}
	DBFree($campos);	
	foreach ($array_campos as &$campo)
	{	
		if(isset($_GET[$campo['Variable']]))
		{	
			$campo['Existe'] = true;
			if($campo['COLUMN_NAME']=='Tarea / Proyecto')
			{
				//*********TRATAMIENTO DE CAMPOS ESPECIALES
				$valor = addslashes(htmlspecialchars($_GET[$campo['Variable']]));				
				if($campo['COLUMN_NAME']=='Tarea / Proyecto')
				{
					$valores = explode(" ", $valor);
					if (is_array($valores)==true)
					{
						$valor = $valores[0]; 
					}
					if (is_numeric($valor)==false)
					{
						$valor = '';
					}
				}
				$campo['Valor']  = $valor;
			}			
			else
			{
				$campo['Valor']  = addslashes(htmlspecialchars($_GET[$campo['Variable']]));
			}
		}	
	}
	return $array_campos;
}
function Crea_Array_Campos_Log()
{
	$array_campos = array();
	$campos       = DBSelect(("SELECT CAST(ISNULL(COLLATION_NAME,'') as varchar(250)) as COLLATION_NAME,COLUMN_NAME,ORDINAL_POSITION,IS_NULLABLE,DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'Log_Tareas_y_Proyectos' order by ORDINAL_POSITION"));	
	for(;DBNext($campos);)
	{
		$array_campos[DBCampo($campos,("ORDINAL_POSITION"))] = array("COLLATION_NAME" => (DBCampo($campos,("COLLATION_NAME"))),"COLUMN_NAME" => (DBCampo($campos,("COLUMN_NAME"))),"IS_NULLABLE" => DBCampo($campos,("IS_NULLABLE")), "DATA_TYPE" => DBCampo($campos,("DATA_TYPE")));		
	}
	DBFree($campos);	
	return $array_campos;
}
function accionesextras($arraycampos)
{
	if(isset($arraycampos[35]))
	{
		if($arraycampos[35]['Existe']==false)
		{			
			return utf8_decode('UPDATE [Tareas y Proyectos] SET [Cola] = (SELECT TOP 1 ISNULL([Cola],(SELECT TOP 1 [Descripcion] FROM [Colas] WHERE [Predeterminado] = 1)) cola FROM [Tipos] where tipo = 2 and  descripcion = [Tareas y Proyectos].[Subcategoría]) FROM [Tareas y Proyectos] WHERE [Tareas y Proyectos].Id = ').$_GET['tarea'];	
		}
	}
}
//https://private.sp-berner.com/GestionTareas/select/prueba_2.php?tarea=234&titulo=Hola&selectcategoria=una&fechasolicitud=01/01/15
?>