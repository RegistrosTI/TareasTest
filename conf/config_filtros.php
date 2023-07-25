<?php
class ColumnHelper
{    
	public static function isValidColumn($dataIndx)    
	{
		return true;
		if (preg_match('/^[a-z,A-Z]*$/', $dataIndx))        
		{            
			return true;        
		}        
		else        
		{            
			return false;        
		}    
	}
}
class SortHelper
{  
	public static function deSerializeSort_Simple($pq_sort)    
	{  
		$sorters = json_decode($pq_sort);        
		$columns = array();        
		$sortby = "";        
		foreach ($sorters as $sorter)
		{            
			$dataIndx = $sorter->dataIndx;  
			$dir = $sorter->dir;    
		
			if ($dir == "up")            
			{                
				$dir = "asc";            
			}            
			else            
			{                
				$dir = "desc";            
			}            
			if (ColumnHelper::isValidColumn($dataIndx))            
			{
				$columns[] = '['. $dataIndx . ']' . " " . $dir;            
			}            
			else
			{                
				throw new Exception("invalid column ".$dataIndx);            
			}        
		}        
		if (sizeof($columns) > 0)        
		{            
			$sortby = " order by " . join(", ", $columns);        
		}        
		return $sortby;    
	}
	public static function deSerializeSort($pq_sort,$table)    
	{  
		$sorters = json_decode($pq_sort);        
		$columns = array();        
		$sortby = "";        
		foreach ($sorters as $sorter)
		{            
			$dataIndx = $sorter->dataIndx;  
			$dir = $sorter->dir;    
		
			if ($dir == "up")            
			{                
				$dir = "asc";            
			}            
			else            
			{                
				$dir = "desc";            
			}            
			if (ColumnHelper::isValidColumn($dataIndx))            
			{
				$columns[] = $table.'['. $dataIndx . ']' . " " . $dir;            
			}            
			else
			{                
				throw new Exception("invalid column ".$dataIndx);            
			}        
		}        
		if (sizeof($columns) > 0)        
		{            
			$sortby = " order by " . join(", ", $columns);        
		}        
		return $sortby;    
	}
}
class FilterHelper
{
    public static function deSerializeFilter($pq_filter)
	{
		
		$filterObj = json_decode($pq_filter);
		$mode = $filterObj->mode;
		$filters = $filterObj->data;
		$fc = array();
		$param= array();
		foreach ($filters as $filter)        
		{
			$dataIndx = $filter->dataIndx; 
			if (ColumnHelper::isValidColumn($dataIndx) == false)            
			{
				throw new Exception("Invalid column name");            
			}            
			$text = $filter->value;            
			$condition = $filter->condition;                         
			if ($condition == "contain")            
			{ 		
				include_once "../php/funciones.php";	
				include_once "../conf/config.php";
				include_once "../conf/config_".curPageName().".php";
				include_once "../../soporte/DB.php";
				include_once "../../soporte/funcionesgenerales.php";			
				DBConectar($GLOBALS["cfg_DataBase"]);			      
				$nav=DBSelect(("SELECT [GestionPDT].[dbo].[CONVERT_NAV_FILTER] ('".'['.$dataIndx . ']'."','". $text."') AS Resultado"));
				for(;DBNext($nav);)
				{
					$fc[] = DBCampo($nav,("Resultado"));
					$param[] = $text;       
				}
				DBFree($nav);	
				DBClose();
			}            
			else if ($condition == "notcontain")            
			{                
				$fc[] = '['.$dataIndx . ']'. " not like ('%".$text."%')";                 
				$param[] = $text;                            
			}            
			else if ($condition == "begin")            
			{                
				$fc[] = '['.$dataIndx . ']'. " like ('".$text."%')";               
				$param[] = $text;                                            
			}            
			else if ($condition == "end")            
			{                
				$fc[] = '['.$dataIndx . ']'. " like ('%".$text."')";                  
				$param[] = $text;                                            
			}            
			else if ($condition == "equal")            
			{                
				$fc[] = '['.$dataIndx . ']'. " = '".$text."'" ;                
				$param[] = $text;                                            
			}            
			else if ($condition == "notequal")            
			{                
				$fc[] = '['.$dataIndx . ']'. " <> '".$text."'" ;                
				$param[] = $text;                                            
			}            
			else if ($condition == "empty")            
			{                             
				$fc[] = "ifnull(" . '['.$dataIndx . ']'. ",'')=''";                            
			}            
			else if ($condition == "notempty")            
			{                
				$fc[] = "ifnull(" . '['.$dataIndx . ']'. ",'')!=''";                            
			}            
			else if ($condition == "less")            
			{                
				$fc[] = '['.$dataIndx . ']'. " < ?";                
				$param[] = $text;                                                            
			}            
			else if ($condition == "great")            
			{                
				$fc[] = '['.$dataIndx . ']'. " > ?";                
				$param[] = $text;                                                                            
			}  			
		}        
		$query = "";        
		if (sizeof($filters) > 0)        
		{            
			$query = " where " . join(" ".$mode." ", $fc);        
		}         
		$ds = new stdClass();       
		$ds->query = $query;        
		$ds->param = $param;        
		return $ds;    
	}
}
?>