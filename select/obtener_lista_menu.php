<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

if(isset($_GET['usuario']))
{
	$usuario=$_GET['usuario'];
}
else
	$usuario='';
	
$contador_tipo_a=0;
$Path_anterior  ='/';
$Tipo_anterior  ='';
$identar        = 0;

DBConectar($GLOBALS["cfg_DataBase"]);

echo '<div id="tree" style="padding-left:40px;">';
echo '<ul id="treeData" style="display: none;">';
echo '<li id="0" class="folder">Informes';
echo '<ul>';

$insert = "
		SELECT Catalogo.[ItemID]
			,Catalogo.[Path]
			,Catalogo.[Name]
			,Catalogo.[ParentID]
			,Catalogo.[Type]
		FROM [Navisionsql].[ReportServer].[dbo].[Catalog] AS Catalogo 
		INNER JOIN [informe_carpetas] AS Carpeta 
			ON Catalogo.[Path] collate Modern_Spanish_CI_AS  like '%'+Carpeta.Carpeta+'%' 
		ORDER BY Catalogo.path";
$insert=DBSelect(utf8_decode($insert));		
for(;DBNext($insert);)
{	
	$contador_tipo_a=$contador_tipo_a+1;
	$Path           =DBCampo($insert,"Path");
	$Type           =DBCampo($insert,"Type");
	$Name           =DBCampo($insert,"Name");
	
	$list_path          = explode('/',$Path);
	$list_path_anterior = explode('/',$Path_anterior);			
	$menor              =0;
	$carpetitas1        = sizeof($list_path); 
	$carpetitas2        = sizeof($list_path_anterior);
	if($carpetitas1<$carpetitas2)
	{
		$menor = $carpetitas1;
	}
	else
	{
		$menor = $carpetitas2;
	}
	$identar = $carpetitas2-$carpetitas1;
	
	$desidentar=0;
	if($identar>=0)
	{	
		$desidentar=0;
		for($i=$menor-1;$i>=0;$i--)
		{	
			if($list_path[$i]!=$list_path_anterior[$i])
			{		
				$desidentar=$desidentar+1;
			}		
		}
	}
	if($identar<0) 
	{	
			$identar = $identar + $desidentar;
	}
	if($identar==0 && $Tipo_anterior == '1') 
	{	
			$identar = $identar + $desidentar;
	}			
	for ($i = 1; $i<=($identar) ; $i++) 
	{						
		echo '</ul></li>';
	}
	if($Type=='1')
	{				
		echo '<li id="'.$contador_tipo_a.'" class="folder">'.$Name.'<ul>';					
	}
	else
	{	
		echo '<li id="'.$contador_tipo_a.'"><div onClick="lanza_informes(\'http://navisionsql/ReportServer/Pages/ReportViewer.aspx?'.$Path.'&rs%3aCommand=Render\')">'.$Name.'</div></li>';				
	}
	$Path_anterior = $Path;
	$Tipo_anterior =$Type;
}
echo '</ul>';
echo '</li>';
echo '</ul>';
echo '</div>';
	
	
DBClose();

?>