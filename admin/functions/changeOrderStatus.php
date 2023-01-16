<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
if($userAgent !== "pubgUserAgent"){
    exit(0);
}

$timeZone = 0;
$settingsFile = glob("../settings_*.json")[0];
if(is_file($settingsFile)){
    $settingsFile = file_get_contents($settingsFile);
    $settingsFile = json_decode($settingsFile,true);
    if(is_array($settingsFile)){
        $timeZone = $settingsFile["timeZone"];
    }
}

if(isset($_GET["orderID"]) && !empty($_GET["orderID"]) && isset($_GET["status"]) && !empty($_GET["status"]) && isset($_GET["description"]) && !empty($_GET["description"]) && isset($_GET["owner"]) && !empty($_GET["owner"])){
    $owner = $_GET["owner"];

    $orderID = $_GET["orderID"];
    $status = $_GET["status"];
    $description = $_GET["description"];

    $orderPath = "../orders/".$orderID.".json";
    if(is_file($orderPath)){
        $orderDetails = file_get_contents($orderPath);
        $orderDetails = json_decode($orderDetails,true);
        
        if(isset($orderDetails["owner"]) && $orderDetails["owner"] == $owner){
            $currentStatus = $orderDetails["status"];
            if($currentStatus != "Completed" || $currentStatus != "Failed"){
                $orderDetails["status"] = $status;
                $orderDetails["description"] = $description;
                $orderDetails["date"] = time()+($timeZone*3600);
    
                file_put_contents($orderPath,json_encode($orderDetails));
            }else{
                echo "null";
                //echo "This order already ended!";
            }
        }else{
            echo "null";
            //echo "Owner has no access to this order!";
        }
    }else{
        echo "null";
        //echo "There's no order with this ID!";
    }
}
?>