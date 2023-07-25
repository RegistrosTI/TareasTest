<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id        =$_GET["Id"];
$Titulo    =$_GET["Titulo"];
$Tipo      =$_GET["Tipo"];
$diainicio =$_GET["diainicio"];
$diafin    =$_GET["diafin"];
$horainicio=$_GET["horainicio"];
$horafin   =$_GET["horafin"];
$usuario   =$_GET["usuario"];
$tarea     =$_GET["tarea"];
$Gasto = "";
if ( isset ( $_GET["gasto"] ) ) {
	$Gasto     =$_GET["gasto"];	
}
$Aporte = "";
if ( isset ( $_GET["aporte"] ) ) {
	$Aporte    =$_GET["aporte"];	
}
$AporteEmpresa = "";
if ( isset ( $_GET["aporte"] ) ) {
	$AporteEmpresa    =$_GET["aporteempresa"];
}
$teletrabajo = '';
if ( isset ( $_GET["teletrabajo"] ) ) {
	$teletrabajo      =$_GET["teletrabajo"];
}


// limpieza de textos
$Titulo = strip_tags($Titulo);
$Titulo = str_replace("'", "''", ($Titulo));
$Titulo = str_replace('"', "\"", ($Titulo));

$Tipo = strip_tags($Tipo);
$Tipo = str_replace("'", " ", ($Tipo));
$Tipo = str_replace('"', " ", ($Tipo));


if(strlen($diafin)<=10) 
{
	$diayhorafin=$diafin.' '.$horafin;
}
else
{
	$diayhorafin=$diafin;
}
if(strlen($diainicio)<=10) 
{
	$diayhorainicio=$diainicio.' '.$horainicio;
}
else
{
	$diayhorainicio=$diainicio;
}


DBConectar($GLOBALS["cfg_DataBase"]);

// OBTENER LAS OFICINAS Y AMBITO DEL USUARIO
$oficinas = '';
$ambito = 1;
$q = "SELECT Oficina, Ambito FROM Configuracion_Usuarios WHERE Usuario = '$usuario' ";
$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$oficinas = utf8_encode ( DBCampo ( $q , utf8_decode ( "Oficina" ) ) );
	$ambito = utf8_encode ( DBCampo ( $q , utf8_decode ( "Ambito" ) ) );
}
$oficinas = "'" . str_replace ( "," , "','" , $oficinas ) . "'";
DBFree ( $q );

// SI EL AMBITO ES 1 SOLO PUEDE FICHAR SI ES EL ASIGNADO
$filtro_adicional = '';
if ( $ambito == 1 ) {
	$filtro_adicional = " AND [Asignado a] = dbo.SF_OBTENER_NOMBRE_DOMINIO('$usuario') ";
}


// COMPROBAR QUE LA FICHADA ES DE UNA TAREA DE MI OFICINA O QUE COLABORO
$filtro_colaboro = "
	(SELECT COUNT(*) 
		FROM Colaboradores AS COL 
		WHERE COL.Estado = 'Activo' 
			AND COL.Id_Tarea = TYP.Id 
			AND COL.Colaborador = '$usuario' 
			AND COL.acceso='Permitido' )";

$encontrado = 0;
$q = "
	SELECT count(Id) as encontrado
	FROM [GestionPDT].[dbo].[Tareas y Proyectos] AS TYP
	WHERE Id = $tarea AND ( ( Oficina IN ($oficinas) $filtro_adicional) OR 0 < $filtro_colaboro )";
//die($q);
$q = DBSelect ( utf8_decode ( $q ) );
for(; DBNext ( $q ) ;) {
	$encontrado = utf8_encode ( DBCampo ( $q , utf8_decode ( "encontrado" ) ) );
}
DBFree ( $q );

// SI NO ENCONTRAMOS SALIMOS Y MOSTRAMOS EL ERROR
if ( $encontrado == 0 ) {
	die ( 'No tiene permisos para fichar en esta tarea o la tarea no existe.' );
}

// T#28318 Alberto. Si la tarea se reabre hay que avisar al usuario
$q = "
  SELECT COUNT(*) AS ABIERTA
  FROM Tipos
  WHERE TIPO = 5
	AND Predeterminado = 2
	AND Oficina		= (SELECT Oficina FROM [Tareas y Proyectos] WHERE Id = $tarea )
	AND Descripcion = (SELECT Estado  FROM [Tareas y Proyectos] WHERE Id = $tarea )
";
$q = utf8_decode($q);
$q = DBSelect($q);
$ABIERTA=(DBCampo($q,"ABIERTA"));
echo $ABIERTA;
// T#28318 Alberto. Si la tarea se reabre hay que avisar al usuario

if($diafin=='')
{
	$query ="
	BEGIN TRANSACTION 				
		INSERT INTO [Log_Horas] 
			(	Numero
				,Tarea
				,Inicio
				,Fin
				,Usuario
				,Minutos
				,Grupo
				,Comentario
				,Tipo
				,TareaAnterior
				,InicioAnterior
				,FinAnterior
				,USuarioAnterior
				,MinutosAnterior
				,GrupoAnterior
				,ComentarioAnterior
				,TipoAnterior
				,UsuarioCambia
				,FechaCambio
				,Gasto
				,GastoAnterior
				,Aporte
				,AporteAnterior
				,AporteEmpresa
				,AporteEmpresaAnterior
				,Teletrabajo
			) 
			
			SELECT 
				Numero
				,Tarea
				,'".$diayhorainicio."'
				,NULL
				,USuario
				,0
				,Grupo
				,'$Titulo'
				,'$Tipo'
				,$tarea
				,Inicio
				,Fin
				,USuario
				,Minutos
				,Grupo
				,Comentario
				,Tipo
				,'".($usuario)."'
				,GETDATE() 
				,'$Gasto'
				,Gasto
				,'$Aporte'
				,Aporte
				,'$AporteEmpresa'
				,AporteEmpresa
				,'$teletrabajo'
			FROM [Horas] 
			WHERE
				( 	('$Titulo' NOT LIKE [Comentario]) 
					OR	('$Tipo' <> [Tipo]) 
					OR	('$Gasto' <> [Gasto]) 
					OR	('$Aporte' <> [Aporte]) 
					OR	('$diayhorainicio' <> [Inicio])
					OR	([Fin] is not null ) )
					OR  ('$teletrabajo' <> [Teletrabajo])
				AND Numero = ".$id." ;
			
			
			UPDATE [Horas] 
			SET
				[Comentario] = '$Titulo'
				,[Tipo] = '$Tipo'
				,[Gasto] = '$Gasto'
				,[Aporte] = '$Aporte'
				,[AporteEmpresa] = '$AporteEmpresa'
				,[Inicio] = '$diayhorainicio'
				,[Tarea] = $tarea
				,[Fin] = null
				,[Minutos] = 0 
				,[Anyo_ISO] 	= DBO.ISOYear('$diayhorainicio')
				,[Semana_ISO] 	= DATEPART(ISO_WEEK,'$diayhorainicio')
				,[Anyo] 		= DATEPART(YEAR,'$diayhorainicio')
				,[Mes] 			= DATEPART(MONTH,'$diayhorainicio')
				,[Teletrabajo] = '$teletrabajo'
			WHERE Numero = $id ;
			
			UPDATE TAREAS
			SET 
				Estado = (	SELECT TOP 1 [Descripcion] 
							FROM [GestionPDT].[dbo].[Tipos] as T
							WHERE Tipo = 5 AND predeterminado = 2
								AND T.Oficina = TAREAS.Oficina)
			FROM [GestionPDT].[dbo].[Tareas y Proyectos] AS TAREAS
			WHERE Id = $tarea ;	
			
			EXECUTE [GestionPDT].[dbo].[INICIAR_EVALUACIONES] $tarea
			
		COMMIT TRANSACTION ;
	";
	
	if ($_SERVER['REMOTE_ADDR'] == '10.5.30.19'){
		//die($query);
	}
	
	DBSelect(utf8_decode($query));
	
}
else
{

	$query ="
	BEGIN TRANSACTION 				
	
		INSERT INTO [Log_Horas]
			(	Numero
				,Tarea
				,Inicio
				,Fin
				,Usuario
				,Minutos
				,Grupo
				,Comentario
				,Tipo
				,TareaAnterior
				,InicioAnterior
				,FinAnterior
				,USuarioAnterior
				,MinutosAnterior
				,GrupoAnterior
				,ComentarioAnterior
				,TipoAnterior
				,UsuarioCambia
				,FechaCambio
				,Gasto
				,GastoAnterior
				,Aporte
				,AporteAnterior
				,AporteEmpresa
				,AporteEmpresaAnterior
				,Teletrabajo
			) 
			
			SELECT 
				Numero
				,Tarea
				,'$diayhorainicio'
				,'$diayhorafin'
				,USuario
				,DATEDIFF(mi,'$diayhorainicio', '$diayhorafin')
				,Grupo
				,'$Titulo'
				,'$Tipo'
				,$tarea
				,Inicio
				,Fin
				,USuario
				,Minutos
				,Grupo
				,Comentario
				,Tipo
				,'$usuario'
				,GETDATE() 
				,'$Gasto'
				,Gasto
				,'$Aporte'
				,Aporte
				,'$AporteEmpresa'
				,AporteEmpresa
				,'$teletrabajo'
			FROM [Horas]  
			WHERE
				(	('$Titulo' NOT LIKE [Comentario]) OR 
					('$Tipo' <>  [Tipo]) OR 
					('$Gasto' <>  [Gasto]) OR 
					('$Aporte' <>  [Aporte]) OR 					
					('$diayhorainicio' <> [Inicio]) OR 
					('$diayhorafin' <> [Fin] ) OR
					('$teletrabajo' <> [Teletrabajo])
				) AND Numero = $id ;
						
						
			UPDATE [Horas] 
			SET 
				[Comentario] = '$Titulo'
				,[Tipo] = '$Tipo'
				,[Gasto] = '$Gasto'
				,[Aporte] = '$Aporte'				
				,[AporteEmpresa] = '$AporteEmpresa'
				,[Tarea] = $tarea
				,[Inicio] = '$diayhorainicio'
				,[Fin] = '$diayhorafin'
				,[Minutos] 		= DATEDIFF(mi,'$diayhorainicio', '$diayhorafin')
				,[Anyo_ISO] 	= DBO.ISOYear('$diayhorainicio')
				,[Semana_ISO] 	= DATEPART(ISO_WEEK,'$diayhorainicio')
				,[Anyo] 		= DATEPART(YEAR,'$diayhorainicio')
				,[Mes] 			= DATEPART(MONTH,'$diayhorainicio')
				,[Teletrabajo] = '$teletrabajo'
			WHERE Numero = $id ;
			
			UPDATE TAREAS
			SET 
				Estado = (	SELECT TOP 1 [Descripcion] 
							FROM [GestionPDT].[dbo].[Tipos] as T
							WHERE Tipo = 5 AND predeterminado = 2
								AND T.Oficina = TAREAS.Oficina)
			FROM [GestionPDT].[dbo].[Tareas y Proyectos] AS TAREAS
			WHERE Id = $tarea ;	
			
			EXECUTE [GestionPDT].[dbo].[INICIAR_EVALUACIONES] $tarea

		COMMIT TRANSACTION ;
			
	
	";
	if ($_SERVER['REMOTE_ADDR'] == '10.5.30.19'){  
		//die($query);
	}
	DBSelect(utf8_decode($query));
}




$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_HISTORICO] '$usuario',$tarea,2"));
DBFree($insert);
$insert=DBSelect(utf8_decode("EXECUTE [INICIAR_PALABRAS_NUMERO] $tarea,3,".$id.",'$Titulo' "));
DBFree($insert);	
DBClose();

?>
