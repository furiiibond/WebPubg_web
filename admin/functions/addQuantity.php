<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
if($userAgent !== "pubgUserAgent"){
    exit(0);
}

if(isset($_GET["orderID"]) && !empty($_GET["orderID"]) && isset($_GET["quantity"]) && !empty($_GET["quantity"]) && isset($_GET["owner"]) && !empty($_GET["owner"])){
    $owner = $_GET["owner"];

    $orderID = $_GET["orderID"];
    $quantity = explode(" ",$_GET["quantity"])[0];

    $orderPath = "../orders/".$orderID.".json";
    if(is_file($orderPath)){
        $orderDetails = file_get_contents($orderPath);
        $orderDetails = json_decode($orderDetails,true);
        $status = $orderDetails["status"];

        if(isset($orderDetails["owner"]) && $orderDetails["owner"] == $owner){
            if($status == "In Progress"){
                $orderDetails["quantitySent"] = $orderDetails["quantitySent"]+$quantity;
    
                file_put_contents($orderPath,json_encode($orderDetails));
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