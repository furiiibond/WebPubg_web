<?php
session_start();

function getZipFromReceipt($receipt)
{
    // Set the zip file name
    $zipFileName = "receipts.zip";

    // Get all the files to be added to the zip archive
    $files = glob("receipts/" . $receipt ."_*.png");

    // Create a new ZipArchive object
    $zip = new ZipArchive();

    // Open the zip archive for writing
    if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {

        // Add each file to the zip archive
        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }

        // Close the zip archive
        $zip->close();

        // Provide a download link for the zip archive
        return "<a id='download' href='$zipFileName' download='$zipFileName'>Download Receipts</a>";

    } else {
        // Error message if the zip archive could not be created
        return "Error: Zip archive could not be created.";
    }
}
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
            $checkUsername = $adminFile["username"];

            if ($adminUsername === $checkUsername) {
                $orders = glob("../orders/*.json");
                $orderDetails = [];
                if (is_array($orders) && !empty($orders)) {
                    foreach ($orders as $key => $order) {
                        $orderDetail = file_get_contents($order);
                        $orderDetail = json_decode($orderDetail, true);

                        if (isset($orderDetail["owner"]) && $orderDetail["owner"] == $adminUsername) {
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
$data = array();
if(isset($orderDetails) && !empty($orderDetails)){
    foreach ($orderDetails as $key => $orderDetail) {
        if($orderDetail["status"] == "Completed"){
            $action = getZipFromReceipt($orderDetail["id"]);
        }elseif($orderDetail["status"] == "Failed"){
            $action = "<a href='?id=".$orderDetail["id"]."&status=Failed'>Delete Order</a>";
        }else{
            $action = "<a href='?id=".$orderDetail["id"]."&status=Completed'>Complete Order</a>";
        }
        $data[] = array(
            "ID" => $orderDetail["id"],
            "User ID" => $orderDetail["userID"],
            "Quantity" => $orderDetail["quantity"],
            "Quantity Total" => $orderDetail["quantityTotal"],
            "Quantity Sent" => $orderDetail["quantitySent"],
            "Date" => gmdate("d.m.Y - H:i:s", $orderDetail["date"]),
            "Status" => $orderDetail["status"],
            "Description" => $orderDetail["description"],
            "Action" => $action
        );
    }
}

header('Content-Type: application/json');
echo json_encode($data);

?>