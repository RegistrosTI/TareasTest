<?php
$CLIENT_ID="38967688-203f-43db-b70f-b53b2c920c76";
$CLIENT_SECRET="L0R8Q~mb.vulXLlsv8d1mk1RzAafwD428oARqdoZ";
$TENANT="87375e02-5221-41ac-9db6-9949c490f072";
$SCOPE="https://outlook.office365.com/IMAP.AccessAsUser.All offline_access";
$CODE="0.AR8AAl43hyFSrEGdtplJxJDwcoh2ljg_INtDtw-1OyySDHYfAJE.AgABAAIAAAD--DLA3VO7QrddgJg7WevrAgDs_wQA9P_A1G81GPINJ086KDMZa-iw45CzC3P8tKp9jo8NdS1pLqCpDA17EENVz7Gb8g42pTFIg9LCYu56FLInO1w5GWnO0hkQ-fTHAOAXyDl7gBRt0hPoe2uMmPJQRVYdsfVk7q5Uood9aiD0dKHYaf9g6LFwk5jgMB-4AwVAC5O_7T8eaN2nLIXlxPuf8Vf01sTj8Ofmxwgj0oaGyXqcg0s8WKMIwrsx1HLg7C0ncexmLQnzwvSf3UUzIyC_Sp2ct3lTpMtLH9zpuiRSe1ir3jcKM5irjrEA7lt45gpWFroFD4UDjZf0UhlC-01RHpdpVFOPjrlpKxjrOP9BOY9oJ9xUDjo87EQdclCFDQRWyof6FUbXNL0_-uG8jYZKv6iOUtnrGgJGcai3kR2m4xGHnNFOjvhbUB3a-0Z4cKEzz88jsNWKOynTlocn6aILdtVg5YCYC0-KiyR3vGomE_hIVRoWgVO7eh7FwbJGHV55-1KZ28o3fKrQIOmJVZF3wtnFZqWeIZTy-QrY-0fVvS_suxcwo05zZqFdDlC6EgZjqAMdOytzY5N-QzeNoJGPVcWpiL0GABipc44WtG2GVPdtkK4kqZD1890CZIlBPrG2PZ9b8Cx7jlmtU5qL93x10h4L_pJYAyqEwVAIIvPd8JhK5UdxoGiRc3yb_4m4lidHIFjCBrDqQCYyxRThH-A1ovxTdMuuwFwqpapYrHZY4KpYF1cbfT4P7Mwo_5fMvBUJSpfUMw";
$SESSION="31f877f1-0a80-4ae4-9bd1-6d7131d5c897#";
$REDIRECT_URI="https://tareastest.sp-berner.com/oauth/EntradaCorreo.php";

echo "Trying to authenticate the session..";

$url= "https://login.microsoftonline.com/$TENANT/oauth2/v2.0/token";

$param_post_curl = [ 
 'client_id'=>$CLIENT_ID,
 'scope'=>$SCOPE,
 'code'=>$CODE,
 'session_state'=>$SESSION,
 'client_secret'=>$CLIENT_SECRET,
 'redirect_uri'=>$REDIRECT_URI,
 'grant_type'=>'authorization_code' ];

 //echo http_build_query($param_post_curl);
 //echo json_encode($param_post_curl);

/*$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($param_post_curl));
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, false);

$oResult=curl_exec($ch);*/

header('Location: ' . $login_url . '?' . http_build_query($params));

echo "result : \n";


var_dump($oResult);

