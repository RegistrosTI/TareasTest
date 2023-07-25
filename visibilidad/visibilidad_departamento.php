<?php
include_once "./php/funciones.php";
include_once "./conf/config.php";
include_once "./conf/config_".curPageName().".php";
include_once "../soporte/DB.php";
include_once "../soporte/funcionesgenerales.php";

DBConectar($GLOBALS["cfg_DataBase"]);
$departamento_usuario_validado = '';
$nombre_usuario_validado       = '';
$oficina_usuario_validado      = '';
$oficina_usuario_validado_options      = '';
$evaluador = 0;
$planificador_comun = 0;


$q="SELECT 
		department
		,sAMAccountName
		,Name
		,physicalDeliveryOfficeName  
	FROM	(SELECT 
				physicalDeliveryOfficeName
				,department
				,sAMAccountName 
				,Name
				,mail
				,CASE WHEN ((2 & userAccountControl) = 2 ) THEN 0 ELSE 1 END AS Activo  
			FROM OpenQuery(ADSI, '	SELECT 
										physicalDeliveryOfficeName
										,department
										,sAMAccountName
										,Name
										,mail
										,userAccountControl 
									FROM ''LDAP://DC=sp-berner,DC=local'' 
									WHERE objectCategory=''user'' AND objectClass=''user''  AND sAMAccountName = ''"./*utf8_decode*/($usuario)."''')) AS Consulta";
//die($q);
$q=DBSelect(/*utf8_decode*/($q));
for(;DBNext($q);)
{		
	$departamento_usuario_validado = utf8_encode(DBCampo($q,"department"));
	$nombre_usuario_validado       = utf8_encode(DBCampo($q,"Name"));
	//$oficina_usuario_validado      = /*utf8_encode*/(DBCampo($q,"physicalDeliveryOfficeName"));
}
DBFree($q);	

$q=DBSelect(utf8_decode("INSERT INTO [Configuracion_Avisos] ([Usuario],[Id],[Descripcion],[Activo])	SELECT '".utf8_encode($usuario)."',[Id],[Descripcion],1 FROM [Lista_Avisos] WHERE Id NOT IN (SELECT Id FROM [Configuracion_Avisos] WHERE Usuario = '".utf8_encode($usuario)."')"	));


$query = "
	SELECT 
		oficina
		,COALESCE(Evaluador,0) AS Evaluador 
		,COALESCE((SELECT PlanificadorComun from Configuracion_Oficinas where Oficina = u.Oficina ) , 0) AS PlanificadorComun
	FROM Configuracion_Usuarios U
	WHERE usuario = '$usuario'
";

//$query = "SELECT oficina,COALESCE(Evaluador,0) AS Evaluador FROM Configuracion_Usuarios WHERE usuario = '$usuario'";
$query = DBSelect ( /*utf8_decode*/ ( $query ) );
for(; DBNext ( $query ) ;) {
	$oficina_usuario_validado = /*utf8_encode*/ ( DBCampo ( $query , "oficina" ) );
	$evaluador = /*utf8_encode*/ ( DBCampo ( $query , "Evaluador" ) );
	$planificador_comun = /*utf8_encode*/ ( DBCampo ( $query , "PlanificadorComun" ) );
}
DBFree ( $query );

$oficina_usuario_validado_options = "<option value=-1></option>";
$tmp = explode ( ',' , $oficina_usuario_validado );
foreach ( $tmp as $oficina ) {
	
	$oficina_usuario_validado_options .= "<option value='$oficina' >$oficina</option>";
}

DBClose();

?>