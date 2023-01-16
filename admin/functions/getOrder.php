<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
if($userAgent !== "pubgUserAgent"){
    exit(0);
}

if(isset($_GET["getOrder"]) && !empty($_GET["getOrder"]) && isset($_GET["owner"]) && !empty($_GET["owner"])){
    $owner = $_GET["owner"];

    if(isset($_GET["orderAmount"])){
        $orderAmount = $_GET["orderAmount"];
    }else{
        $orderAmount = 1;
    }
    $orders = glob("../orders/*.json");
    if(is_array($orders) && count($orders)>0){
        $countOrder = 0;
        foreach ($orders as $key => $order) {
            $orderDetails = file_get_contents($order);
            $orderDetails = json_decode($orderDetails,true);

            if(isset($orderDetails["owner"]) && $orderDetails["owner"] == $owner){
                if(is_array($orderDetails)){
                    $status = $orderDetails["status"];
                    if($status == "Pending"){
                        # check if a code can respond to the quantity
                        $codes = glob("../codes/*.json");
                        if(is_array($codes) && count($codes)>0){


                        }
                        if($countOrder < $orderAmount){
                            $orderID = $orderDetails["id"];
                            $userID = $orderDetails["userID"];
                            $quantity = $orderDetails["quantity"];
                            $owner = $orderDetails["owner"];
                            
                            $output[] = [
                                "orderID"=>$orderID,
                                "userID"=>$userID,
                                "quantity"=>$quantity,
                                "owner"=>$owner
                            ];
        
                            $countOrder++;
                        }
                    }
                }
            }
        }
        if(isset($output) && !empty($output) && count($output)>0){
            echo json_encode($output);
        }else{
            echo "null";
            //echo "There's no more order!";
        }
    }else{
        echo "null";
        //echo "There's no more order!";
    }
}
?>