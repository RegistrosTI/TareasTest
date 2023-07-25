<?php
	// incluimos la clase PHPMailer
	require_once ( 'PHPMailer/class.phpmailer.php' );
	// CONFIGURACION DE ENVIO DE CORREO ELECTRONICO
	$modo_de_pruebas = true; //Si true los correos se envían a la dirección $correoAdjunto 
	$FROM = "portal@sp-berner.com";
	$username = "portal@sp-berner.local";
	$password = "portal30";
	$smtp_server = "relay.sp-berner.com";
	$smtp_port = 25;
	$correoAdjunto = "alberto.ruiz@sp-berner.com";
	
	
	//$hostname_recibir = '{mail.sp-berner.com:110/pop3}INBOX';
	//$username_recibir = 'soporte.ti';
	//$password_recibir = 'soporte70';

?>