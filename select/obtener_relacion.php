<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$esajax = false;
if(isset($_POST['departamento']))
{
	$esajax               = true;
	$departamento_programa=$_POST['departamento'];
}
else
    $departamento_programa=$DEPARTAMENTO;
if(isset($_POST['programa']))
{
	$esajax           = true;
	$programa_programa=$_POST['programa'];
}
else
    $programa_programa=$PROGRAMA;

if ($esajax == true)
{
	DBConectar($GLOBALS["cfg_DataBase"]);
}

$u_programa=DBSelect(utf8_decode("SELECT * FROM OPENQUERY (NavisionSQL,'SELECT COUNT(*) AS Resultado FROM [NAVSQL].[dbo].[Relación dpto_ y prog_] Relacion WHERE Relacion.Departamento = ''".$departamento_programa."''')"));
$RegistrosDepartamento=DBCampo($u_programa,"Resultado");		
DBFree($u_programa);

$selectdepartamento_programa='<select id="selectprograma" name="selectprograma" style="width:460;">';	

$i_programa = 1;
while ($i_programa <= $RegistrosDepartamento) 
{
	$u_programa=DBSelect(utf8_decode("SELECT Departamento,Programa,Nombre_Departamento,Nombre_Programa,Bloqueado,Fila FROM (SELECT *,ROW_NUMBER() OVER(ORDER BY Departamento,Programa) Fila FROM OPENQUERY (NavisionSQL,'SELECT Relacion.Departamento,Relacion.Programa,	Departamento.Nombre AS Nombre_Departamento,		Programa.Nombre AS Nombre_Programa,		CASE Relacion.Bloqueado WHEN 1 THEN 1 ELSE CASE Programa.Bloqueado WHEN 1 THEN 1 ELSE 0 END END AS Bloqueado FROM [NAVSQL].[dbo].[Relación dpto_ y prog_] Relacion INNER JOIN Departamento Departamento ON Departamento.Código = Relacion.Departamento INNER JOIN 		Programa Programa ON Programa.Código = Relacion.Programa WHERE Relacion.Departamento = ''".$departamento_programa."''')) Consulta WHERE Fila BETWEEN ".$i_programa." AND ".(($i_programa+200)-1).""));
	for(;DBNext($u_programa);)
	{
		$CodigoDepartamento_programa   =(DBCampo($u_programa,utf8_decode("Departamento")));		
		$NombreDepartamento_programa   =(DBCampo($u_programa,"Nombre_Departamento"));
		$CodigoPrograma_programa       =(DBCampo($u_programa,utf8_decode("Programa")));		
		$NombrePrograma_programa       =(DBCampo($u_programa,"Nombre_Programa"));
		$BloqueadoDepartamento_programa=(DBCampo($u_programa,"Bloqueado"));			
		if ($programa_programa == utf8_encode($CodigoPrograma_programa))
		{
			$selectdepartamento_programa=$selectdepartamento_programa.'<option value="'.utf8_encode($CodigoPrograma_programa).'" selected>'.utf8_encode($CodigoPrograma_programa).' ('.utf8_encode($NombrePrograma_programa).')</option>';
		}
		else
		{
			if (utf8_encode($BloqueadoDepartamento_programa) == 0)
			{
				$selectdepartamento_programa=$selectdepartamento_programa.'<option value="'.utf8_encode($CodigoPrograma_programa).'">'.utf8_encode($CodigoPrograma_programa).' ('.utf8_encode($NombrePrograma_programa).')</option>';
			}
		}						
	}
	DBFree($u_programa);	
	$i_programa=$i_programa+200;  
}
$selectdepartamento_programa=$selectdepartamento_programa.'</select>';	

if ($esajax == true)
{
	echo $selectdepartamento_programa;
}
else
{
	echo '<div id="div_programa">'.$selectdepartamento_programa.'</div>';
}
if ($esajax == true)
{
	DBClose();
}
?>