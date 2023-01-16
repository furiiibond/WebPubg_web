<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
if($userAgent !== "pubgUserAgent"){
    exit(0);
}

if(isset($_GET["code"]) && strlen($_GET["code"]) > 0 && isset($_GET["owner"]) && !empty($_GET["owner"])){
    $owner = $_GET["owner"];

    $code = $_GET["code"];
    $codePath = "../codes/".$code.".json";
    if(is_file($codePath)){
        $codeDetails = file_get_contents($codePath);
        $codeDetails = json_decode($codeDetails,true);

        if(isset($codeDetails["owner"]) && $codeDetails["owner"] == $owner){
            rename($codePath,str_replace("codes","wrongCodes",$codePath));
        }else{
            echo "null";
            //echo "Owner has no access to this code!";
        }
    }else{
        echo "null";
        //echo "There's no code with this code!";
    }
}
?>