<?php

require __DIR__.'/vendor/autoload.php'; 
    
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
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
            'scope' => 'https://outlook.office365.com/IMAP.AccessAsUser.All offline_access',
            'state' => $_SESSION['state']
        );
        header('Location: ' . $login_url . '?' . http_build_query($params));
    }


    if (array_key_exists('access_token', $_POST)) {
        $jsonView = json_encode($_POST);
        dd($jsonView);
        die();
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
       // header('Location: https://tareastest.sp-berner.com/oauth/EntradaCorreo.php');
    }

if ($_GET['action'] == 'logout') {
    unset($_SESSION['msatg']);
    header('Location: https://tareastest.sp-berner.com/oauth/EntradaCorreo.php');
}
    
function getEmail($access_token){
        
    //$cm = new ClientManager($options = ["options" => ["debug" => true]]);                     
    $cm = new ClientManager();                      
    $client = $cm->make([
        'host'          => 'outlook.office365.com',                
        'port'          => 993,
        'encryption'    => 'ssl',
        'validate_cert' => true,
        'username'      => 'soporte.ti@sp-berner.com',
        'password'      => $access_token,
        'protocol'      => 'imap',
        'authentication' => "oauth"
    ]);

    
    try {
        //Connect to the IMAP Server
        $client->connect();
        $folder = $client->getFolder('INBOX');
        $all_messages = $folder->query()->all()->get();
        //DONE ! :D     
    } catch (Exception $e) {
        echo 'Exception : ',  $e->getMessage(), "\n";
    }
}


*/
$access_token="eyJ0eXAiOiJKV1QiLCJub25jZSI6IlduM0JRSHl6VW1NMGZESVJuc3JlelNFX2w1VS1sQ05RYUI5SkF0dkN6QmciLCJhbGciOiJSUzI1NiIsIng1dCI6Ii1LSTNROW5OUjdiUm9meG1lWm9YcWJIWkdldyIsImtpZCI6Ii1LSTNROW5OUjdiUm9meG1lWm9YcWJIWkdldyJ9.eyJhdWQiOiJodHRwczovL291dGxvb2sub2ZmaWNlMzY1LmNvbSIsImlzcyI6Imh0dHBzOi8vc3RzLndpbmRvd3MubmV0Lzg3Mzc1ZTAyLTUyMjEtNDFhYy05ZGI2LTk5NDljNDkwZjA3Mi8iLCJpYXQiOjE2NzIxMzA4NDgsIm5iZiI6MTY3MjEzMDg0OCwiZXhwIjoxNjcyMTM1MzI1LCJhY2N0IjowLCJhY3IiOiIxIiwiYWlvIjoiQVRRQXkvOFRBQUFBcWhQMFpXY2x0SGJ4L2NzN2I0bUR1YVRESzQyZm1wdWViYVkrdjdMVXJHRGpZZEhEMng4RVlFcDhIV2g2bm9zUyIsImFtciI6WyJwd2QiXSwiYXBwX2Rpc3BsYXluYW1lIjoidGFyZWFzdGVzdC1wcnVlYmEiLCJhcHBpZCI6IjM4OTY3Njg4LTIwM2YtNDNkYi1iNzBmLWI1M2IyYzkyMGM3NiIsImFwcGlkYWNyIjoiMSIsImVuZnBvbGlkcyI6W10sImZhbWlseV9uYW1lIjoibW9yZW5vIiwiZ2l2ZW5fbmFtZSI6Ikpvc2UgTWlndWVsIiwiaXBhZGRyIjoiNzcuMjMxLjEyNC4xNjQiLCJuYW1lIjoiU1AgQkVSTkVSIFBMQVNUSUMgR1JPVVAsIFMuTC4iLCJvaWQiOiJkNDNmZDY0NS0wMmI4LTQwZTQtODU2ZC05ZjdmN2YzY2FmNWIiLCJwdWlkIjoiMTAwMzdGRkVBOTlCOTM5OCIsInJoIjoiMC5BUjhBQWw0M2h5RlNyRUdkdHBsSnhKRHdjZ0lBQUFBQUFQRVB6Z0FBQUFBQUFBQWZBSkUuIiwic2NwIjoiSU1BUC5BY2Nlc3NBc1VzZXIuQWxsIiwic2lkIjoiZjdiZjA2OTUtMGVkZS00MmI1LWI1NDctNjFiOTkyOWY5YTQwIiwic3ViIjoiZzVWd1g3eXU5MFV0MmwydXYwWS1RUF9JcUdjUEE2OWItYVExamVOWGVQbyIsInRpZCI6Ijg3Mzc1ZTAyLTUyMjEtNDFhYy05ZGI2LTk5NDljNDkwZjA3MiIsInVuaXF1ZV9uYW1lIjoiYWRtaW5Ac3AtYmVybmVyLmNvbSIsInVwbiI6ImFkbWluQHNwLWJlcm5lci5jb20iLCJ1dGkiOiJPZjNUYUd5a3VrVzBhUEtRaVFDOEFBIiwidmVyIjoiMS4wIiwid2lkcyI6WyI2MmU5MDM5NC02OWY1LTQyMzctOTE5MC0wMTIxNzcxNDVlMTAiLCJiNzlmYmY0ZC0zZWY5LTQ2ODktODE0My03NmIxOTRlODU1MDkiXX0.e7f8dsnGl7x5nprpyPoiip01SBVpZ8SP58S_ovgZvfnQDn-jdCsxkfD4SoAUSdJrjQgcIuZFdyaiiiBzEu2d59gcQA18iQJtMDtB0GcUfnsejTznbhjBFe7WvMsy13d6XUy7wMPAOxNvnx77BKnhx-cK-8Z_cfKXqcg70T9rgMYhpwDNDhAKk4xhSrNel5rhxtNiN3uHCM2rB5h_ztgXSV9mxMgvBNvcPOBAKKeToEUskjrgzkBCIfu04F-0i7sznAGMZGQkz73wGUECMB9fKpEM9MiDnQvjj1eIcnfhrPlbYDbKhrqkcC5hyMCtSreqckch86P7cfiXW6r20IXb-A";
    
$cm = new ClientManager($options = ["options" => ["debug" => true]]);                     
//$cm = new ClientManager();                      
$client = $cm->make([
    'host'          => 'outlook.office365.com',                
    'port'          => 993,
    'encryption'    => 'ssl',
    'validate_cert' => false,
    'username'      => 'admin@sp-berner.com',
    'password'      => $access_token,
    'protocol'      => 'imap',
    'authentication' => "oauth"
]);

try {
    //Connect to the IMAP Server
    $client->connect();
    $client->checkConnection();
    $folders = $client->getFolders();

    //Loop through every Mailbox
    /** @var \Webklex\PHPIMAP\Folder $folder */
    foreach($folders as $folder){
        //echo $folder;
    
        //Get all Messages of the current Mailbox $folder
        /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
        //$messages = $folder->messages()->all()->get();
    
        /** @var \Webklex\PHPIMAP\Message $message */
        /*foreach($messages as $message){
            echo $message->getSubject().'<br />';
            echo 'Attachments: '.$message->getAttachments()->count().'<br />';
            echo $message->getHTMLBody();*/
    
            //Move the current Message to 'INBOX.read'
            /*if($message->move('INBOX.read') == true){
                echo 'Message has ben moved';
            }else{
                echo 'Message could not be moved';
            }*/
        //}
    }
 


    //$folder = $client->getFolders('INBOX');
    //$messages = $folder->messages()->all()->get();
    //$all_messages = $folder->query()->all()->get();
    //DONE ! :D     
} catch (Exception $e) {
    echo 'Exception : ',  $e->getMessage(), "\n";
}


