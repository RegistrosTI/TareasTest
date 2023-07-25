<?php
$TENANT="87375e02-5221-41ac-9db6-9949c490f072";
$CLIENT_ID="38967688-203f-43db-b70f-b53b2c920c76";
$SCOPE="https://outlook.office365.com/IMAP.AccessAsUser.All";
$REDIRECT_URI="https://tareastest.sp-berner.com/oauth/EntradaCorreo.php";

$authUri = 'https://login.microsoftonline.com/' . $TENANT
           . '/oauth2/v2.0/authorize?client_id=' . $CLIENT_ID
           . '&scope=' . $SCOPE
           . '&redirect_uri=' . urlencode($REDIRECT_URI)
           . '&response_type=code'
           . '&approval_prompt=auto';

echo($authUri);

/*$appid = "38967688-203f-43db-b70f-b53b2c920c76";
$tennantid = "87375e02-5221-41ac-9db6-9949c490f072";
$secret = "L0R8Q~mb.vulXLlsv8d1mk1RzAafwD428oARqdoZ";
$login_url = "https://login.microsoftonline.com/" . $tennantid . "/oauth2/v2.0/authorize";
session_start();
//$_SESSION['t'] = isset($_POST['access_token']) ? $_POST['access_token'] : null;

$_SESSION['state'] = session_id();
echo "<h1>MS OAuth2.0 Demo </h1><br>";
if (isset($_SESSION['msatg'])) {
    echo "<h2>Authenticated " . $_SESSION["t"] . " </h2><br> ";
    echo '<p><a href="?action=logout">Log Out</a></p>';
} //end if session
else   echo '<h2><p>You can <a href="?action=login">Log In</a> with Microsoft</p></h2>';

    if ($_GET['action'] == 'login') {
        $params = array(
            'client_id' => $appid,
            'redirect_uri' => 'https://tareastest.sp-berner.com/oauth/EntradaCorreo.php',
            'response_type' => 'token',
            'response_mode' => 'form_post',
            'scope' => 'https://graph.microsoft.com/IMAP.AccessAsUser.All offline_access',
            'state' => $_SESSION['state']
        );
        header('Location: ' . $login_url . '?' . http_build_query($params));
    }


    if (array_key_exists('access_token', $_POST)) {

        $_SESSION['t'] = $_POST['access_token'];
        $t = $_SESSION['t'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $t,
            'Conent-type: application/json'
        ));
        curl_setopt($ch, CURLOPT_URL, "https://login.microsoftonline.com/$tennantid/oauth2/v2.0/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $rez = json_decode(curl_exec($ch), 1);
        if (array_key_exists('error', $rez)) {
            var_dump($rez['error']);
            die();
        } else {
            $_SESSION['msatg'] = 1;  //auth and verified
             $_SESSION['uname'] = $rez["displayName"];
              $_SESSION['id'] = $rez["id"];
        }
        curl_close($ch);
        header('Location: https://tareastest.sp-berner.com/oauth/EntradaCorreo.php');
    }

if ($_GET['action'] == 'logout') {
    unset($_SESSION['msatg']);
    header('Location: https://tareastest.sp-berner.com/oauth/EntradaCorreo.php');
}*/



