<?php
//UTF8 Á Ñ O
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$CodDepartamento = '';
$CodPrograma     ='';
if(isset($_GET['usuario'])){
	$usuario=$_GET['usuario'];
} else {
	$usuario='';
}

if(isset($_GET['ti'])){
	$ti=$_GET['ti'];
}else{
	$ti='';
}

if(isset($_GET['altus'])){
	$altus=$_GET['altus'];
}else{
	$altus='0';	
}

$externo = '';
if(isset($_GET['ext'])){
	$externo=$_GET['ext'];
}
	
$IdCreado = 'prueba';

DBConectar($GLOBALS["cfg_DataBase"]);

// SI ELUSUARIO ENTRA COMO EXTERNO LA OFICINA DE LA NUEVA TAREA DEBE DE SER LA EXTERNA
$oficinas='';
if ($externo != ''){
	$oficina = $externo;
}else{
	$query = "SELECT oficina FROM Configuracion_Usuarios WHERE usuario = '$usuario'";
	$query=DBSelect(utf8_decode($query));
	for(;DBNext($query);)	{
		$oficinas=utf8_encode(DBCampo($query,"oficina"));
	}
	DBFree($query);
	$oficinas;
	$oficinas = explode(',', $oficinas);
	$oficina = $oficinas[0];
}


$q="
	SELECT 
		physicalDeliveryOfficeName
		,department
		,sAMAccountName 
		,Name
		,mail
		,Fila
		,Activo 
	FROM (	SELECT 
				physicalDeliveryOfficeName
				,department
				,sAMAccountName 
				,Name
				,mail
				,ROW_NUMBER() OVER(ORDER BY sAMAccountName) Fila
				,CASE WHEN ((2 & userAccountControl) = 2 ) THEN 0 ELSE 1 END AS Activo  
			FROM OpenQuery(ADSI, 'SELECT physicalDeliveryOfficeName,department,sAMAccountName,Name,mail,userAccountControl FROM ''LDAP://DC=sp-berner,DC=local'' WHERE objectCategory=''user'' AND objectClass=''user''  ')) Consulta	
			WHERE sAMAccountName = '"./*utf8_encode*/($usuario)."'"	;

$q         	=DBSelect(utf8_decode($q));
$Name      	=utf8_encode(DBCampo($q,"Name"));		
$Activo    	=utf8_encode(DBCampo($q,"Activo"));
$department	=utf8_encode(DBCampo($q,"department"));			
//$oficina 	=(DBCampo($q,"physicalDeliveryOfficeName"));
DBFree($q);				

// ++ alberto 24/09/18 T#26882 Se quita el departamento-programa
// $u="
// 	SELECT TOP 1 
// 		[Departamento Solicitud] as DEPARTAMENTO
// 		,[Programa Solicitud] as PROGRAMA  
// 	FROM [Tareas y Proyectos] 
// 	WHERE solicitado='".$Name."' AND [Control] <> 1 
// 		AND Oficina = '$oficina' 
// 	order by id desc 
// ";

//$u               = DBSelect(utf8_decode($u));
//$CodDepartamento = DBCampo($u,"DEPARTAMENTO");
//$CodPrograma     = DBCampo($u,"PROGRAMA");	
//DBFree($u);	

// if ($CodDepartamento == '')
// {
// 	if ($department == '')
// 	{
// 		$filtro = '';
// 	}
// 	else
// 	{
// 		$filtro = "nombre like (''%".$department."%'') AND";
// 	}
// 	$u              = DBSelect(utf8_decode("SELECT * FROM OPENQUERY (NavisionSQL,'SELECT CASE (SELECT COUNT(*)  FROM [NAVSQL].[dbo].[Departamento]	where ".$filtro." Bloqueado = 0) WHEN 0 THEN (SELECT TOP 1 Código  FROM [NAVSQL].[dbo].[Departamento] where Bloqueado = 0) ELSE (SELECT TOP 1 Código FROM [NAVSQL].[dbo].[Departamento]	where ".$filtro." Bloqueado = 0) END AS Departamento')"));
// 	$CodDepartamento= DBCampo($u,"Departamento");
// 	DBFree($u);	
// }

// if ($CodPrograma=='')
// {
// 	$u          =DBSelect(utf8_decode("SELECT * FROM OPENQUERY (NavisionSQL,'SELECT * FROM [NAVSQL].[dbo].[Relación dpto_ y prog_] where Departamento = ''".$CodDepartamento."'' AND Bloqueado = 0')"));
// 	$CodPrograma=(DBCampo($u,"Programa"));
// 	DBFree($u);	
// }
// -- alberto 24/09/18 T#26882 Se quita el departamento-programa


$selecttipo         ='';
$selectsubcategoria ='';
$selectcategoria    ='';
$selectvaloracion   ='';
$selectprioridad    ='';
$selectestado       ='';
$selectbap          ='';	
$query              = DBSelect(utf8_decode("(SELECT Tipo,CAST([Descripcion] as varchar(MAX)) AS Descripcion  FROM [Tipos] where Predeterminado = 1 and Oficina = '$oficina')"));	

for(;DBNext($query);)
{
	$Tipo_Tipos       =DBCampo($query,"Tipo");		
	$Descripcion_Tipos=DBCampo($query,"Descripcion");	
	if ($Tipo_Tipos == 0)
	{							
		$selecttipo=utf8_encode($Descripcion_Tipos);
	}
	if ($Tipo_Tipos == 1)
	{
		$selectcategoria=utf8_encode($Descripcion_Tipos);			
	}
	if ($Tipo_Tipos == 2)
	{
		$selectsubcategoria=utf8_encode($Descripcion_Tipos);		
	}	
	if ($Tipo_Tipos == 3)
	{
		$selectvaloracion=utf8_encode($Descripcion_Tipos);			
	}	
	if ($Tipo_Tipos == 4)
	{
		$selectprioridad=utf8_encode($Descripcion_Tipos);			
	}	
	if ($Tipo_Tipos == 5)
	{
		$selectestado=utf8_encode($Descripcion_Tipos);		
	}	
	if ($Tipo_Tipos == 6)
	{
		$selectbap=utf8_encode($Descripcion_Tipos);		
	}			
}
DBFree($query);		

$selectcola = '';
$query          = DBSelect(utf8_decode("SELECT TOP 1 Colas.[Descripcion] FROM [Colas] Colas order by Predeterminado desc"));	
for(;DBNext($query);)
{
	$Descripcion_Cola =DBCampo($query,"Descripcion");			
	$selectcola       =utf8_encode($Descripcion_Cola);
}
DBFree($query);		

$asignadoa=" '' ";
if ($ti=='1')
{
	$asignadoa=" '$Name' COLLATE Modern_Spanish_CI_AS ";
}



//TAREA EVALUABLE DEPENDIENDO DE TABLA Y OFICINA // juan
$query29 = DBSelect("SELECT [Tarea Evaluable] FROM [Configuracion_Oficinas] Where Oficina = '$oficina'");
$evaluabletarea =DBCampo($query29,"Tarea Evaluable");
DBFree($query29);



// PRIMER INSERT EN LA BD, CUANDO SE DE DA ALTA UNA NUEVA TAREA, TIENE MARCA DE CONTROL(BORRADO) EN 1
$insert = "
	INSERT INTO [Tareas y Proyectos] 
		(
		[Control]
		,[Evaluable]
		,[Oficina]
		,[Cola]
		,[Título]
		,[Categoría]
		,[Tipo]
		,[Descripción]
		,[Solicitado]
		,[Fecha Solicitud]
		,[Departamento Solicitud]
		,[Programa Solicitud]
		,[Departamento Origen Fallo]
		,[Programa Origen Fallo]
		,[Fecha alta]
		,[Fecha inicio]
		,[Fecha fin]
		,[Prioridad]
		,[Fecha objetivo]
		,[Horas reales]
		,[Estado]
		,[Valoración]
		,[% completado]
		,[Asignado a]
		,[Tarea / Proyecto]
		,[Referencia]
		,[Resultado final]
		,[Usuario]
		,[Datos adjuntos]
		,[Fecha intermedio]
		,[Horas estimadas]
		,[Subcategoría]
		,[Documentos]
		,[BAP]
		,[Estrategico]
		,[Requiere_Documentacion]
		,[AporteOrganizacion]
		,[AporteEmpresa]
		,[AporteJefe]
		,[Motivo bap]
		,[DocumentacionCompleta]
		,[ConsiderarBI]
		) 
		OUTPUT Inserted.Id 
		VALUES (
			1 -- [Control]
			,'$evaluabletarea' -- [Evaluable]
			,'$oficina'
			,'$selectcola' COLLATE Modern_Spanish_CI_AS -- [Cola]
			,'' -- [Título]
			,'$selectcategoria' COLLATE Modern_Spanish_CI_AS -- [Categoría]
			,'$selecttipo' COLLATE Modern_Spanish_CI_AS -- [Tipo]
			,'' COLLATE Modern_Spanish_CI_AS -- [Descripción]
			,'$Name' COLLATE Modern_Spanish_CI_AS -- [Solicitado]
			,CAST(DAY(GETDATE()) as varchar(20))+'/'+CAST(MONTH(GETDATE()) as varchar(20))+'/'+CAST(YEAR(GETDATE()) as varchar(20)) -- [Fecha Solicitud]
			,'$CodDepartamento' COLLATE Modern_Spanish_CI_AS -- [Departamento Solicitud]
			,'$CodPrograma' COLLATE Modern_Spanish_CI_AS -- [Programa Solicitud]
			,null -- [Departamento Origen Fallo]
			,null -- [Programa Origen Fallo]
			--,CAST(DAY(GETDATE()) as varchar(20))+'/'+CAST(MONTH(GETDATE()) as varchar(20))+'/'+CAST(YEAR(GETDATE()) as varchar(20)) -- [Fecha alta] -- T#30203
			,GETDATE() -- [Fecha alta] T#30203 - Necesitan que se registre la hora exacta a la que le abren una tarea
			,CAST(DAY(GETDATE()) as varchar(20))+'/'+CAST(MONTH(GETDATE()) as varchar(20))+'/'+CAST(YEAR(GETDATE()) as varchar(20)) -- [Fecha inicio]
			,CAST(DAY(GETDATE()) as varchar(20))+'/'+CAST(MONTH(GETDATE()) as varchar(20))+'/'+CAST(YEAR(GETDATE()) as varchar(20)) -- [Fecha fin]
			,'$selectprioridad' COLLATE Modern_Spanish_CI_AS -- [Prioridad]
			,null -- [Fecha objetivo]
			,0 -- [Horas reales]
			,'$selectestado' COLLATE Modern_Spanish_CI_AS -- [Estado]
			,'$selectvaloracion' COLLATE Modern_Spanish_CI_AS -- [Valoración]
			,0 -- [% completado]
			,'' -- [Asignado a]
			,null -- [Tarea / Proyecto]
			,'' -- [Referencia]
			,null -- [Resultado final]
			,'$Name' COLLATE Modern_Spanish_CI_AS -- [Usuario]
			,null -- [Datos adjuntos]
			,null -- [Fecha intermedio]
			,0 -- [Horas estimadas]
			,'$selectsubcategoria' COLLATE Modern_Spanish_CI_AS -- [Subcategoría]
			,null -- [Documentos]
			,'$selectbap' -- [BAP]
			,'NO' -- [Estrategico]
			,'NO' --[Requiere_Documentacion]
			,'N/A' --[AporteOrganizacion]
			,'N/A' --[AporteEmpresa]
			,'N/A' --[AporteJefe]
			,null -- [Motivo bap]
			,'NO' -- [DocumentacionCompleta]
			,'SI' -- [ConsiderarBI]
			
		)
";


//die($insert);

$insert=DBSelect(utf8_decode($insert));
$IdCreado=DBCampo($insert,("Id"));	

$q       =DBSelect(utf8_decode("EXECUTE [INICIAR_HISTORICO] '"./*utf8_encode*/($usuario)."',".$IdCreado.",50"));
	
if($altus=='1')
{
	$al=DBSelect(utf8_decode("DECLARE @TIPO varchar(250) SET @TIPO = (SELECT TOP 1 [Descripcion] FROM [Tipos] WHERE Tipo = 9 AND Predeterminado = 1 AND Visible = 1)
							INSERT INTO [Altas_Usuario] ([Tarea],[Usuario],[Fecha],[Navision_Acceso],[Internet_Acceso],[Internet_Tipo],[Remoto_Acceso],[Telefono_Acceso],[Telefono_Movil_Acceso],[PC_Acceso]
							,[Portatil_Acceso],[Estado]) VALUES (".$IdCreado.",'"./*utf8_encode*/($usuario)."',GETDATE(),0,0,@TIPO,0,0,0,0,0,0)"));	
	$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_HISTORICO] '".$usuario."',".$IdCreado.",66000"));							   	
}

DBClose();
echo $IdCreado;
?>