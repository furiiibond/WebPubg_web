<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
if($userAgent !== "pubgUserAgent"){
    exit(0);
}

if(isset($_GET["ping"]) && !empty($_GET["ping"]) && isset($_GET["owner"]) && !empty($_GET["owner"])){
    $owner = $_GET["owner"];

    $timeZone = 0;
    $settingsFile = "../settings/".$owner."_*.json";
    if(is_file($settingsFile)){
        $settingsFile = file_get_contents($settingsFile);
        $settingsFile = json_decode($settingsFile,true);
        if(is_array($settingsFile)){
            $timeZone = $settingsFile["timeZone"];
        }
    }
    
    $log = [
        "lastResponse"=>time()+($timeZone*3600)
    ];
    
    file_put_contents("../logs/".$owner."_log.json",json_encode($log));
}
?>