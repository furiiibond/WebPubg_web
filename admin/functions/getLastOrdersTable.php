<?php
session_start();

if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"])) {
    $adminUsername = $_SESSION["admin"];

    $settingsFile = glob("../settings/" . $adminUsername . "_*.json")[0];
    $adminFile = glob("../admins/" . $adminUsername . "_*.json")[0];

    if (is_file($settingsFile) && is_file($adminFile)) {
        $settingsFile = file_get_contents($settingsFile);
        $settingsFile = json_decode($settingsFile, true);

        $adminFile = file_get_contents($adminFile);
        $adminFile = json_decode($adminFile, true);

        if (is_array($adminFile) && !empty($adminFile)) {
            $timeZone = $settingsFile["timeZone"];
            $checkUsername = $adminFile["username"];

            if ($adminUsername === $checkUsername) {
                $orders = glob("../orders/*.json");
                $orderDetails = [];
                if (is_array($orders) && !empty($orders)) {
                    $currentTime = time()+($timeZone*3600);
                    foreach ($orders as $key => $order) {
                        $orderDetail = file_get_contents($order);
                        $orderDetail = json_decode($orderDetail, true);

                        if (isset($orderDetail["owner"]) && $orderDetail["owner"] == $adminUsername) {
                            if($currentTime-$orderDetail["date"]<=600) {
                                $orderDetails[] = [
                                    "id" => $orderDetail["id"],
                                    "userID" => $orderDetail["userID"],
                                    "quantity" => $orderDetail["quantity"],
                                    "quantityTotal" => $orderDetail["quantityTotal"],
                                    "quantitySent" => $orderDetail["quantitySent"],
                                    "date" => $orderDetail["date"],
                                    "status" => $orderDetail["status"],
                                    "description" => $orderDetail["description"]
                                ];
                            }
                        }
                    }
                }
            }
        }
    }
}
$data = array();
if(isset($orderDetails) && !empty($orderDetails)){
    foreach ($orderDetails as $key => $orderDetail) {
        $data[] = array(
            "ID" => $orderDetail["id"],
            "User ID" => $orderDetail["userID"],
            "Quantity" => $orderDetail["quantity"],
            "Status" => $orderDetail["status"],
        );
    }
}

header('Content-Type: application/json');
echo json_encode($data);
?>