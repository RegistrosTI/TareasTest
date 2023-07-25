<?php
$URL_TO_CODE = 'https://login.microsoftonline.com/87375e02-5221-41ac-9db6-9949c490f072/oauth2/v2.0/authorize?client_id=4d719aac-7878-421d-bdef-520fae86499e&scope=https://outlook.office365.com/IMAP.AccessAsUser.All&redirect_uri=https%3A%2F%2Ftareas.sp-berner.com%2Fmails%2Foauth%2FEntradaCorreo.php&response_type=code&approval_prompt=auto';
header('Location: ' . $URL_TO_CODE);
exit();
/***
 * Este codigo solo nos ayudara a generar la URL que usara EnvioFirstRequest.php para obtener el CODE que luego intercambiaremos por el token
 * @author Herman Martinez
 * @date 27/12/2022 (3 dias para la finalizazión del plazo microsoft)
 */
/* $TENANT="87375e02-5221-41ac-9db6-9949c490f072";
$CLIENT_ID="4d719aac-7878-421d-bdef-520fae86499e";
$SCOPE="https://outlook.office365.com/IMAP.AccessAsUser.All";
$REDIRECT_URI="https://tareas.sp-berner.com/mails/oauth/EntradaCorreo.php";

$authUri = 'https://login.microsoftonline.com/' . $TENANT
           . '/oauth2/v2.0/authorize?client_id=' . $CLIENT_ID
           . '&scope=' . $SCOPE
           . '&redirect_uri=' . urlencode($REDIRECT_URI)
           . '&response_type=code'
           . '&approval_prompt=auto';

echo'Se llama a:'.$authUri.'<\br>'; */