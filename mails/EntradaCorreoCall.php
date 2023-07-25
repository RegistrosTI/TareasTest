<?php
$appid = "f7489552-579b-46f4-8761-d45a203c7753";
$tennantid = "2ca5163a-16d8-4cf6-a013-af26ed933e89";
$secret = "e4c8Q~q1bHVeSyEV~weUgv5cOusgc9b.3NThJbfv";
$login_url = "https://login.microsoftonline.com/" . $tennantid . "/oauth2/v2.0/authorize";
session_start();
$chf = curl_init();
$_SESSION['state'] = session_id();
echo "<h1>MS OAuth2.0 Demo </h1><br>";
/*if (isset($_SESSION['msatg'])) {
    echo "<h2>Authenticated " . $_SESSION["t"] . " </h2><br> ";
    echo '<p><a href="?action=logout">Log Out</a></p>';
} //end if session
else   echo '<h2><p>You can <a href="?action=login">Log In</a> with Microsoft</p></h2>';
if ($_GET['action'] == 'login') {*/
    $params = array(
        'client_id' => $appid,
        'redirect_uri' => 'https://tareastest.sp-berner.com/mails/EntradaCorreoBack.php/',
        'response_type' => 'token',
        'response_mode' => 'form_post',
        'scope' => 'https://graph.microsoft.com/IMAP.AccessAsUser.All',
        'state' => $_SESSION['state']
    );
    $url = header('Location: ' . $login_url . '?' . http_build_query($params));
    curl_setopt($chf, CURLOPT_URL, $URL);
    curl_exec($chf);