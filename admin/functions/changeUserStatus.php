<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
if($userAgent !== "pubgUserAgent"){
    exit(0);
}

if(isset($_GET["username"]) && !empty($_GET["username"]) && isset($_GET["status"]) && !empty($_GET["status"]) && isset($_GET["owner"]) && !empty($_GET["owner"])){
    $owner = $_GET["owner"];

    $username = $_GET["username"];
    $status = $_GET["status"];

    $userPath = "../users/".$username.".json";
    if(is_file($userPath)){
        $userDetails = file_get_contents($userPath);
        $userDetails = json_decode($userDetails,true);

        if(isset($userDetails["owner"]) && $userDetails["owner"] == $owner){
            $userDetails["status"] = $status;

            file_put_contents($userPath,json_encode($userDetails));
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