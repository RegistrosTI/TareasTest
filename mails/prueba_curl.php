<?php
$TENANT="87375e02-5221-41ac-9db6-9949c490f072";
$url= "https://login.microsoftonline.com/$TENANT/oauth2/v2.0/token";
// Crear un nuevo recurso cURL
$ch = curl_init();

// Configurar URL y otras opciones apropiadas
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);

// Capturar la URL y pasarla al navegador
curl_exec($ch);

// Cerrar el recurso cURL y liberar recursos del sistema
curl_close($ch);
?>