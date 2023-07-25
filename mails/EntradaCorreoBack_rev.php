


<?php



 var_dump($_POST);


if (isset($_SESSION['msatg'])) {
    echo "<h2>Authenticated " . $_SESSION["t"] . " </h2><br> ";
    echo '<p><a href="?action=logout">Log Out</a></p>';
}

if (array_key_exists('access_token', $_POST)) {

    $_SESSION['t'] = $_POST['access_token'];
    $t = $_SESSION['t'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $t,
        'Conent-type: application/json'
    ));
    curl_setopt($ch, CURLOPT_URL, "https://graph.microsoft.com/v1.0/me/mailFolders('Inbox')");
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
    header('Location: https://tareastest.sp-berner.com/mails/EntradaCorreoBack.php/');
}else{
    echo 'log out';
}
/*if ($_GET['action'] == 'logout') {
    unset($_SESSION['msatg']);
    header('Location: https://tareastest.sp-berner.com/mails/EntradaCorreoBack.php/');
}*/