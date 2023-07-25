<?php
include_once "../conf/config_filtros.php";
include_once "../php/funciones.php";
include_once "../conf/config.php";
include_once "../conf/config_MENU.php";
include_once "../../soporte/DB.php";
include_once "../../soporte/funcionesgenerales.php";

$id = $_GET [ "Id" ];
$Titulo = $_GET [ "Titulo" ];
$diainicio = $_GET [ "diainicio" ];
$diafin = $_GET [ "diafin" ];
$horainicio = $_GET [ "horainicio" ];
$horafin = $_GET [ "horafin" ];
$usuario = $_GET [ "usuario" ];
$tarea = $_GET [ "tarea" ];
$tipo = $_GET [ "Tipo" ];
$Importe = $_GET [ "Importe" ];
if ( strlen ( $diafin ) <= 10 ) {
	$diayhorafin = $diafin . ' ' . $horafin;
} else {
	$diayhorafin = $diafin;
}
if ( strlen ( $diainicio ) <= 10 ) {
	$diayhorainicio = $diainicio . ' ' . $horainicio;
} else {
	$diayhorainicio = $diainicio;
}
if ( $Importe == '' ) {
	$Importe = '0';
}
DBConectar ( $GLOBALS [ "cfg_DataBase" ] );

$Titulo = str_replace ( "'" , " " , ( $Titulo ) );
$tipo = str_replace ( "'" , " " , ( $tipo ) );

if ( str_replace ( " " , "" , ( $diafin ) ) == '' ) {
	$q = "
			INSERT INTO [Log_Costes] (
				Numero
				,Tarea
				,Inicio
				,Fin
				,Usuario
				,Importe
				,Grupo
				,Comentario
				,Tipo
				,TareaAnterior
				,InicioAnterior
				,FinAnterior
				,USuarioAnterior
				,ImporteAnterior
				,GrupoAnterior
				,ComentarioAnterior
				,TipoAnterior
				,UsuarioCambia
				,FechaCambiodatetime) 
			SELECT 
				Numero
				,Tarea
				,'" . $diayhorainicio . "'
				,NULL
				,USuario
				,'" . $Importe . "'
				,Grupo
				,'" . $Titulo . "'
				,'" . $tipo . "'
				,Tarea
				,Inicio
				,Fin
				,USuario
				,Importe
				,Grupo
				,Comentario
				,Tipo
				,'" . ( $usuario ) . "',GETDATE()	
			FROM [Costes]  
			WHERE (('" . $Titulo . "' NOT LIKE [Comentario]) OR	('" . $Importe . "' NOT LIKE [Importe]) OR	('" . $tipo . "' NOT LIKE [Tipo]) OR	('" . $diayhorainicio . "' <> [Inicio]) OR ([Fin] is not null )) AND Numero = " . $id . "	
		
			UPDATE [Costes] 
			SET [Comentario] = '" . $Titulo . "'
				,[Inicio] = '" . $diayhorainicio . "'
				,[Fin] = null
				,[Importe] = '" . $Importe . "'
				,[Tipo] = '" . $tipo . "' 
			WHERE Numero = " . $id . " ";

	// DBSelect(utf8_decode("insert into [Log_Costes] ( Numero, Tarea, Inicio, Fin,Usuario,Importe,Grupo,Comentario,Tipo,TareaAnterior,InicioAnterior,FinAnterior,USuarioAnterior,ImporteAnterior,
	// GrupoAnterior,ComentarioAnterior,TipoAnterior,UsuarioCambia,FechaCambiodatetime) SELECT Numero,Tarea,'".$diayhorainicio."',NULL,USuario,'".$Importe."',Grupo,'".str_replace("'", "''", ($Titulo))."',
	// '".str_replace("'", "''", ($tipo))."',Tarea,Inicio,Fin,USuario,Importe,Grupo,Comentario,Tipo,'".($usuario)."',GETDATE() FROM [Costes] WHERE
	// (('".str_replace("'", "''", ($Titulo))."' NOT LIKE [Comentario]) OR ('".$Importe."' NOT LIKE [Importe]) OR ('".str_replace("'", "''", ($tipo))."' NOT LIKE [Tipo]) OR ('".$diayhorainicio."' <> [Inicio]) OR
	// ([Fin] is not null )) AND Numero = ".$id."
	// UPDATE [Costes] SET [Comentario] = '".str_replace("'", "''", ($Titulo))."',[Inicio] = '".$diayhorainicio."',[Fin] = null,[Importe] = '".$Importe."'
	// ,[Tipo] = '".str_replace("'", "''", ($tipo))."' WHERE Numero = ".$id." "));
} else {
	
	$q = "
			INSERT INTO [Log_Costes] (
				Numero
				,Tarea
				,Inicio
				,Fin
				,Usuario
				,Importe
				,Grupo
				,Comentario
				,Tipo
				,TareaAnterior
				,InicioAnterior
				,FinAnterior
				,USuarioAnterior
				,ImporteAnterior
				,GrupoAnterior
				,ComentarioAnterior
				,TipoAnterior
				,UsuarioCambia
				,FechaCambiodatetime) 
			SELECT 
				Numero
				,Tarea
				,'" . $diayhorainicio . "'
				,'" . $diayhorafin . "'
				,USuario
				,'" . $Importe . "'
				,Grupo
				,'" . $Titulo . "'
				,'" . $tipo . "'
				,Tarea
				,Inicio
				,Fin
				,USuario
				,Importe
				,Grupo
				,Comentario
				,Tipo
				,'" . ( $usuario ) . "',GETDATE() 
			FROM [Costes]  
			WHERE (('" . $Titulo . "' NOT LIKE [Comentario]) OR	('" . $Importe . "' NOT LIKE [Importe]) OR ('" . $tipo . "' NOT LIKE [Tipo]) OR ('" . $diayhorainicio . "' <> [Inicio]) OR	('" . $diayhorafin . "' <> [Fin]))	AND Numero = " . $id . "

			UPDATE [Costes] 
			SET [Comentario] = '" . $Titulo . "'
				,[Tipo] = '" . $tipo . "'
				,[Inicio] = '" . $diayhorainicio . "'
				,[Fin] = '" . $diayhorafin . "'
				,[Importe] =  '" . $Importe . "' 
			WHERE Numero = " . $id . " ";
	
// 	DBSelect ( utf8_decode ( "insert into [Log_Horas] (	Numero,	Tarea,	Inicio,	Fin,Usuario,Importe,Grupo,Comentario,Tipo,TareaAnterior,InicioAnterior,FinAnterior,USuarioAnterior,ImporteAnterior,
// 						GrupoAnterior,ComentarioAnterior,TipoAnterior,UsuarioCambia,FechaCambiodatetime) SELECT Numero,Tarea,'" . $diayhorainicio . "',	'" . $diayhorafin . "',	USuario,
// 						'" . $Importe . "',Grupo,'" . str_replace ( "'" , "''" , ( $Titulo ) ) . "','" . str_replace ( "'" , "''" , ( $tipo ) ) . "',
// 						Tarea,Inicio,Fin,USuario,Importe,Grupo,Comentario,Tipo,'" . ( $usuario ) . "',GETDATE() FROM [Costes]  WHERE  
// 						(('" . str_replace ( "'" , "''" , ( $Titulo ) ) . "' NOT LIKE [Comentario]) OR	('" . $Importe . "' NOT LIKE [Importe]) OR
// 						('" . str_replace ( "'" , "''" , ( $tipo ) ) . "' NOT LIKE [Tipo]) OR
// 						('" . $diayhorainicio . "' <> [Inicio]) OR	('" . $diayhorafin . "' <> [Fin]))	AND Numero = " . $id . "
// 						UPDATE [Costes] SET [Comentario] = '" . str_replace ( "'" , "''" , ( $Titulo ) ) . "',[Tipo] = '" . str_replace ( "'" , "''" , ( $tipo ) ) . "'
// 						,[Inicio] = '" . $diayhorainicio . "',[Fin] = '" . $diayhorafin . "',[Importe] =  '" . $Importe . "' WHERE Numero = " . $id . "	" ) );
}

DBSelect ( utf8_decode ( $q ) );

$insert = DBSelect ( utf8_decode ( "EXECUTE [INICIAR_HISTORICO] '" . $usuario . "'," . $tarea . ",33001" ) );
DBFree ( $insert );
$insert = DBSelect ( utf8_decode ( "EXECUTE [INICIAR_PALABRAS_NUMERO] " . $tarea . ",14," . $id . ",'" . str_replace ( "'" , "''" , ( $Titulo ) ) . "' " ) );
DBFree ( $insert );
DBClose ();
?>
