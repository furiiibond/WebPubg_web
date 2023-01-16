<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
if($userAgent !== "pubgUserAgent"){
    exit(0);
}

if(isset($_GET["username"]) && !empty($_GET["username"]) && isset($_GET["owner"]) && !empty($_GET["owner"])){
    $owner = $_GET["owner"];

    $username = $_GET["username"];
    $userPath = "../users/".$username.".json";
    if(is_file($userPath)){
        $userDetails = file_get_contents($user);
        $userDetails = json_decode($userDetails,true);
        
        if(isset($userDetails["owner"]) && $userDetails["owner"] == $owner){
            unlink($userPath);
        }else{
            echo "null";
            //echo "Owner has no access to this user!";
        }
    }else{
        echo "null";
        //echo "There's no user with this username!";
    }
}
?>