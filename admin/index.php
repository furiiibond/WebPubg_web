<?php
require __DIR__ . '/functions/isEnoughCode.php';
session_start();

if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"])){
    $adminUsername = $_SESSION["admin"];

    $settingsFile = glob("settings/".$adminUsername."_*.json")[0];
    $adminFile = glob("admins/".$adminUsername."_*.json")[0];

    if(is_file($settingsFile) && is_file($adminFile)){
        $settingsFile = file_get_contents($settingsFile);
        $settingsFile = json_decode($settingsFile,true);

        $adminFile = file_get_contents($adminFile);
        $adminFile = json_decode($adminFile,true);

        if(is_array($adminFile) && !empty($adminFile)){
            $checkUsername = $adminFile["username"];

            if($adminUsername === $checkUsername){
                $timeZone = $settingsFile["timeZone"];

                $command = escapeshellcmd("python3 ../../Desktop/testConnect.py");
                $output = shell_exec($command . " 2>&1"); // 2>&1 is for error
                if($output == "ready".PHP_EOL){
                    $lastResponse = '<span style="color: green;font-size: 1.4em;">●</span>' . ' Ready';
                }else{
                    $lastResponse = '<span style="color: red;font-size: 1.4em;">●</span>' . " Not Ready" ;
                }

                $codes = glob("codes/*.json");
                $codeDetails = [];
                foreach ($codes as $key => $code) {
                    $codeDetail = file_get_contents($code);
                    $codeDetail = json_decode($codeDetail,true);

                    if(isset($codeDetail["owner"]) && $codeDetail["owner"] == $adminUsername){
                        $codeDetails[] = $codeDetail["amount"];
                    }
                }
                $codeDetails = array_count_values($codeDetails);
                ksort($codeDetails);

                $orderIDs = file("orderID.txt");
                $lastOrderID = $orderIDs[count($orderIDs)-1];

                $message = "";
                $messages = [];
                if(isset($_POST) && !empty($_POST)){
                    switch ($_POST["action"]) {
                        case "fastOrder":
                            if(isset($_POST["userID"]) && !empty($_POST["userID"]) && !empty($_POST["quantity60"]) || !empty($_POST["quantity325"]) || !empty($_POST["quantity660"]) || !empty($_POST["quantity1800"]) || !empty($_POST["quantity3850"]) || !empty($_POST["quantity8100"]) || !empty($_POST["quantity16200"]) || !empty($_POST["quantity24300"])){
                                $userID = $_POST["userID"];

                                $quantity60 = $_POST["quantity60"];
                                $quantity325 = $_POST["quantity325"];
                                $quantity660 = $_POST["quantity660"];
                                $quantity1800 = $_POST["quantity1800"];
                                $quantity3850 = $_POST["quantity3850"];
                                $quantity8100 = $_POST["quantity8100"];
                                $quantity16200 = $_POST["quantity16200"];
                                $quantity24300 = $_POST["quantity24300"];

                                $quantityLast = "";
                                $quantityTotal = "";
                                if(!empty($quantity60)){
                                    $quantityLast .= "60*".$quantity60." - ";
                                    $quantityTotal .= "(60*".$quantity60.")";
                                }if(!empty($quantity325)){
                                    $quantityLast .= "325*".$quantity325." - ";
                                    $quantityTotal .= "+(325*".$quantity325.")";
                                }if(!empty($quantity660)){
                                    $quantityLast .= "660*".$quantity660." - ";
                                    $quantityTotal .= "+(660*".$quantity660.")";
                                }if(!empty($quantity1800)){
                                    $quantityLast .= "1800*".$quantity1800." - ";
                                    $quantityTotal .= "+(1800*".$quantity1800.")";
                                }if(!empty($quantity3850)){
                                    $quantityLast .= "3850*".$quantity3850." - ";
                                    $quantityTotal .= "+(3850*".$quantity3850.")";
                                }if(!empty($quantity8100)){
                                    $quantityLast .= "8100*".$quantity8100." - ";
                                    $quantityTotal .= "+(8100*".$quantity8100.")";
                                }if(!empty($quantity16200)){
                                    $quantityLast .= "16200*".$quantity16200." - ";
                                    $quantityTotal .= "+(16200*".$quantity16200.")";
                                }if(!empty($quantity24300)){
                                    $quantityLast .= "24300*".$quantity24300." - ";
                                    $quantityTotal .= "+(24300*".$quantity24300.")";
                                }
                                $quantityLast = substr($quantityLast,0,-3);
                                $quantityTotal = eval('return '.$quantityTotal.';');

                                /*
                                    $orders = glob("orders/*.json");
                                    $orderID = count($orders)+1;
                                */
                                $duplicateControl = false;
                                if(is_file("orderID.txt")){
                                    $orderID = intval(file_get_contents("orderID.txt"))+1;
                                    if(isset($_POST["lastOrderID"]) && strlen($_POST["lastOrderID"]) && $_POST["lastOrderID"]+1 < $orderID){
                                        $orderID = $orderID-1;

                                        $message = '
                                        <div class="card mb-4 py-3 border-left-danger">
                                            <div class="card-body">
                                                Order already sent!
                                            </div>
                                        </div>';

                                        $duplicateControl = true;
                                    }else{
                                        $message = '
                                        <div class="card mb-4 py-3 border-left-success">
                                            <div class="card-body">
                                                Order sent successfully!
                                            </div>
                                        </div>';
                                    }
                                    file_put_contents("orderID.txt",$orderID);
                                }else{
                                    $orderID = 1;
                                    file_put_contents("orderID.txt",1);
                                }

                                $orderDetail = [
                                    "id"=>$orderID,
                                    "userID"=>$userID,
                                    "quantity"=>$quantityLast,
                                    "quantityTotal"=>$quantityTotal,
                                    "quantitySent"=>0,
                                    "date"=>time()+($timeZone*3600),
                                    "status"=>"Pending",
                                    "description"=>"-",
                                    "owner"=>$adminUsername
                                ];

                                if($duplicateControl == false){
                                    if (isEnoughCode($quantityLast, $adminUsername)){
                                        file_put_contents("orders/".$orderID.".json",json_encode($orderDetail));
                                        # ADDED start the python script order with venv
                                        # get username and password of user with userID
                                        $status = "active";
                                        # change quantity last from 60*1 - 1800*1 to 60*1,1800*1
                                        $quantityLast = str_replace(" - ",",",$quantityLast);
                                        $command = escapeshellcmd("python3 ../../Desktop/newOrder.py $adminUsername $orderID $userID $quantityLast");
                                        $output = shell_exec($command . " 2>&1"); // 2>&1 is for error
                                        # save output to log file
                                        file_put_contents("logs/".$orderID.".txt",$output);
                                        # ADDED end the python script order
                                    } else {
                                        $message = '
                                        <div class="card mb-4 py-3 border-left-danger">
                                            <div class="card-body">
                                                Not enough code!
                                            </div>
                                        </div>';
                                    }
                                }
                            }else{
                                $message = '
                                <div class="card mb-4 py-3 border-left-danger">
                                    <div class="card-body">
                                        Fill all fields!
                                    </div>
                                </div>';
                            }
                            break;
                    }
                }

                $orders = glob("orders/*.json");

                $orderDetails = [];
                if(is_array($orders) && !empty($orders)){
                    $currentTime = time()+($timeZone*3600);

                    foreach ($orders as $key => $order) {
                        $orderDetail = file_get_contents($order);
                        $orderDetail = json_decode($orderDetail,true);

                        if(isset($codeDetail["owner"]) && $orderDetail["owner"] == $adminUsername){
                            if($currentTime-$orderDetail["date"]<=600){
                                $orderDetails[] = [
                                    "userID"=>$orderDetail["userID"],
                                    "quantity"=>$orderDetail["quantity"],
                                    "status"=>$orderDetail["status"]
                                ];
                            }
                        }
                    }
                }
                ?>

                <!DOCTYPE html>
                <html lang="en">

                <head>

                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                    <meta name="description" content="">
                    <meta name="author" content="">

                    <title>Admin</title>

                    <!-- Custom fonts for this template-->
                    <link href="theme/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
                    <link
                            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
                            rel="stylesheet">

                    <!-- Custom styles for this template-->
                    <link href="theme/css/sb-admin-2.min.css" rel="stylesheet">
                    <link rel="icon" type="image/x-icon" href="theme/img/undraw_profile.png">
                    <link href="theme/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
                    <link rel="stylesheet" href="theme/css/barmenu.css">
                    <style>
                        input[type="number"] {
                            -webkit-appearance: textfield;
                            -moz-appearance: textfield;
                            appearance: textfield;
                        }

                        input[type=number]::-webkit-inner-spin-button,
                        input[type=number]::-webkit-outer-spin-button {
                            -webkit-appearance: none;
                        }

                        .number-input {
                            border: 1px solid #d1d3e2;
                            display: flex;
                        }

                        .number-input,
                        .number-input * {
                            box-sizing: border-box;
                        }

                        .number-input button {
                            outline:none;
                            -webkit-appearance: none;
                            background-color: transparent;
                            border: none;
                            align-items: center;
                            justify-content: center;
                            width: 2rem;
                            height: 2rem;
                            cursor: pointer;
                            margin: 0;
                            position: relative;
                            width: 33.3%;
                        }

                        .number-input button:before,
                        .number-input button:after {
                            display: inline-block;
                            position: absolute;
                            content: '';
                            width: 1rem;
                            height: 2px;
                            background-color: #d1d3e2;
                            transform: translate(-50%, -50%);
                        }
                        .number-input button.plus:after {
                            transform: translate(-50%, -50%) rotate(90deg);
                        }

                        .number-input input[type=number] {
                            font-family: sans-serif;
                            max-width: 5rem;
                            padding: .5rem;
                            border: solid #ddd;
                            border-width: 0 2px;
                            font-size: 1rem;
                            height: 2rem;
                            font-weight: bold;
                            text-align: center;
                            background-color: #262b49;
                            color: #6e707e;
                            min-width: 33.3%;
                        }
                        .user label{
                            display:inherit;
                            text-align: center;
                        }
                    </style>
                </head>
                <script type="text/javascript">
                    var intervalId;
                    intervalId = setInterval(function(){
                        // if there is nothing serached in table
                        if ($('#dataTable_filter > label > input').val() == '') {
                            // hide the pagination
                            $('#orderTable .dataTables_paginate').hide();
                            // hide dataTables_length
                            $('#orderTable .dataTables_length').hide();
                            // display "AUTO REFRESHING" in #dataTable_wrapper > div:nth-child(1) > div:nth-child(1) by add a new div
                            if ($('#orderTable #dataTable_wrapper > div:nth-child(1) > div:nth-child(1) > div').length == 1) {
                                // add loading logo created in css and check button
                                $('#orderTable #dataTable_wrapper > div:nth-child(1) > div:nth-child(1)').append('<div style="display: flex;"><div class="loader"></div><div style="margin-left: 10px;">AUTO REFRESHING</div><div style="margin-left: 10px;"><input type="checkbox" id="check" checked></div></div>');
                            }
                            if(!$("#orderTable #check").is(":checked")) {
                                $('#orderTable #dataTable_wrapper > div:nth-child(1) > div:nth-child(1) > div:nth-child(2) > div:nth-child(1)').hide();
                                return;
                            } else {
                                $('#orderTable #dataTable_wrapper > div:nth-child(1) > div:nth-child(1) > div:nth-child(2) > div:nth-child(1)').show();
                            }
                            $.ajax({
                                url: 'functions/getLastOrdersTable.php',
                                type: 'POST',
                                success: function(data){
                                    $('#ordersTable').html(data);
                                }
                            });
                        } else {
                            // remove "AUTO REFRESHING"
                            $('#orderTable #dataTable_wrapper > div:nth-child(1) > div:nth-child(1) > div:nth-child(2)').remove();
                            // show the pagination
                            $('#orderTable .dataTables_paginate').show();
                            // show dataTables_length
                            $('#orderTable .dataTables_length').show();

                        }}, 1000);
                    setInterval(function(){
                        $.ajax({
                                url: 'functions/getCodesDetails.php',
                                type: 'POST',
                                success: function(data){
                                    $('#dataTable2').html(data);
                                }
                            });
                        }, 1500)
                </script>

                <body id="page-top">

                <div class="hero__phone">
                    <section class="menu__body">
                        <div class="menu__links"><!-- Sidebar - Brand -->
                            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                                <div class="sidebar-brand-icon phone_icon rotate-n-15">
                                    <i class="fas fa-skull-crossbones"></i>
                                </div>
                            </a>
                            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="orders.php">
                                <div class="phone_icon">
                                    <i class="fas fa-fw fa-list"></i>
                                </div>
                            </a>
                            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="users.php">
                                <div class="phone_icon">
                                    <i class="fas fa-fw fa-user"></i>
                                </div>
                            </a>

                            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="codes.php">
                                <div class="phone_icon">
                                    <i class="fas fa-fw fa-code"></i>
                                </div>
                            </a>
                        </div>
                    </section>
                </div>

                <!-- Page Wrapper -->
                <div id="wrapper">

                    <!-- Sidebar -->
                    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion toggled" id="accordionSidebar">

                        <!-- Sidebar - Brand -->
                        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                            <div class="sidebar-brand-icon rotate-n-15">
                                <i class="fas fa-skull-crossbones"></i>
                            </div>
                            <div class="sidebar-brand-text mx-3">Admin</div>
                        </a>

                        <!-- Divider -->
                        <hr class="sidebar-divider my-0">

                        <li class="nav-item active">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-fw fa-tachometer-alt"></i>
                                <span>Home</span></a>
                        </li>

                        <hr class="sidebar-divider">

                        <div class="sidebar-heading">
                            Order Section
                        </div>

                        <li class="nav-item">
                            <a class="nav-link" href="orders.php">
                                <i class="fas fa-fw fa-list"></i>
                                <span>Orders</span></a>
                        </li>

                        <div class="sidebar-heading">
                            User Section
                        </div>

                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-fw fa-user"></i>
                                <span>Users</span></a>
                        </li>

                        <?php

                        if(strpos($adminUsername,"admin")!==false){
                            echo '
                    <li class="nav-item">
                    <a class="nav-link" href="admins.php">
                        <i class="fas fa-fw fa-user-secret"></i>
                        <span>Admins</span></a>
                    </li>
                    ';
                        }

                        ?>

                        <div class="sidebar-heading">
                            Code Section
                        </div>

                        <li class="nav-item">
                            <a class="nav-link" href="codes.php">
                                <i class="fas fa-fw fa-code"></i>
                                <span>Codes</span></a>
                        </li>

                        <hr class="sidebar-divider my-0">

                        <li class="nav-item">
                            <a class="nav-link" href="settings.php">
                                <i class="fas fa-fw fa-wrench"></i>
                                <span>Settings</span></a>
                        </li>

                        <!-- Divider -->
                        <hr class="sidebar-divider d-none d-md-block">

                        <!-- Sidebar Toggler (Sidebar) -->
                        <div class="text-center d-none d-md-inline">
                            <button class="rounded-circle border-0" id="sidebarToggle"></button>
                        </div>

                    </ul>
                    <!-- End of Sidebar -->

                    <!-- Content Wrapper -->
                    <div id="content-wrapper" class="d-flex flex-column">

                        <!-- Main Content -->
                        <div id="content">

                            <!-- Topbar -->
                            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                                <!-- Sidebar Toggle (Topbar) -->
                                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                                    <i class="fa fa-bars"></i>
                                </button>

                                <!-- Topbar Navbar -->
                                <ul class="navbar-nav ml-auto">

                                    <!-- Nav Item - User Information -->
                                    <li class="nav-item dropdown no-arrow">
                                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $checkUsername ?></span>
                                            <img class="img-profile rounded-circle"
                                                 src="theme/img/undraw_profile.png">
                                        </a>
                                        <!-- Dropdown - User Information -->
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                             aria-labelledby="userDropdown">
                                            <a class="dropdown-item" href="settings.php">
                                                <i class="fas fa-wrench fa-sm fa-fw mr-2 text-gray-400"></i>
                                                Settings
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                                Logout
                                            </a>
                                        </div>
                                    </li>

                                </ul>

                            </nav>
                            <!-- End of Topbar -->

                            <!-- Begin Page Content -->
                            <div class="container-fluid">

                                <!-- Content Row -->
                                <div class="row">
                                    <?php
                                    if(!empty($lastResponse)){
                                        ?>
                                        <div class="col-xl-12 col-md-12 mb-4">
                                            <div class="card border-left-primary shadow h-100 py-2">
                                                <div class="card-body">
                                                    <div class="row no-gutters align-items-center">
                                                        <div class="col mr-2">
                                                            <div class="h6 mb-0 font-weight-bold text-default"><?php echo $lastResponse ?></div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <div class="col-lg-12 mb-12">
                                        <div class="card shadow mb-4">
                                            <div class="card-header py-3">
                                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-fw fa-code"></i> Code Statistics</h6>
                                            </div>
                                            <div class="card-body">
                                                <div>
                                                    <table class="table table-bordered" id="dataTable2" width="100%" cellspacing="0">
                                                        <?php
                                                        if(isset($codeDetails) && !empty($codeDetails)){
                                                            echo"<tr>
                                                            <th>Amount:</th>";
                                                            foreach ($codeDetails as $key => $quantity) {
                                                                echo "

                                                                    <td>".$key." UC</td>
                                                                    ";
                                                            }
                                                            echo "</tr>
                                                                <tr>
                                                                    <th>Quantity:</th>";
                                                            foreach ($codeDetails as $key => $quantity) {
                                                                echo "
                                                                    <td>".$quantity."</td>
                                                                    ";
                                                            }
                                                            echo "</tr>";
                                                        }
                                                        ?>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Content Row -->
                                <div class="row">
                                    <div class="col-lg-12 mb-12">
                                        <div class="card shadow mb-4">
                                            <div class="card-header py-3">
                                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-fw fa-plus"></i> Fast Order</h6>
                                            </div>
                                            <div class="p-5">
                                                <form class="user" method="POST" action="index.php">
                                                    <input type="text" name="lastOrderID" value="<?php echo $lastOrderID ?>" hidden></input>
                                                    <input type="text" name="action" value="fastOrder" hidden></input>
                                                    <div class="form-group">
                                                        <label style="font-size:12px">User ID</label>
                                                        <input type="text" class="form-control"
                                                               name="userID" aria-describedby="userIDHelp"
                                                               placeholder="User ID">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-6 col-6">
                                                            <div class="form-group">
                                                                <label style="font-size:12px">60 UC</label>
                                                                <div class="number-input">
                                                                    <button onclick="this.parentNode.querySelector('input[type=number]').stepDown()" ></button>
                                                                    <input class="quantity" min="0" name="quantity60" id="quantity60" value="0" type="number">
                                                                    <button onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus"></button>
                                                                </div>
                                                                <!--
                                                                <input type="number" class="form-control form-control-user"
                                                                    name="quantity60" id="quantity60" placeholder="60 UC">
                                                                -->
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-6">
                                                            <div class="form-group">
                                                                <label style="font-size:12px">325 UC</label>
                                                                <div class="number-input">
                                                                    <button onclick="this.parentNode.querySelector('input[type=number]').stepDown()" ></button>
                                                                    <input class="quantity" min="0" name="quantity325" id="quantity325" value="0" type="number">
                                                                    <button onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus"></button>
                                                                </div>
                                                                <!--
                                                                <input type="number" class="form-control form-control-user"
                                                                    name="quantity325" id="quantity325" placeholder="325 UC">
                                                                -->
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-6">
                                                            <div class="form-group">
                                                                <label style="font-size:12px">660 UC</label>
                                                                <div class="number-input">
                                                                    <button onclick="this.parentNode.querySelector('input[type=number]').stepDown()" ></button>
                                                                    <input class="quantity" min="0" name="quantity660" id="quantity660" value="0" type="number">
                                                                    <button onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus"></button>
                                                                </div>
                                                                <!--
                                                                <input type="number" class="form-control form-control-user"
                                                                    name="quantity660" id="quantity660" placeholder="660 UC">
                                                                -->
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-6">
                                                            <div class="form-group">
                                                                <label style="font-size:12px">1800 UC</label>
                                                                <div class="number-input">
                                                                    <button onclick="this.parentNode.querySelector('input[type=number]').stepDown()" ></button>
                                                                    <input class="quantity" min="0" name="quantity1800" id="quantity1800" value="0" type="number">
                                                                    <button onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus"></button>
                                                                </div>
                                                                <!--
                                                                <input type="number" class="form-control form-control-user"
                                                                    name="quantity1800" id="quantity1800" placeholder="1800 UC">
                                                                -->
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-6">
                                                            <div class="form-group">
                                                                <label style="font-size:12px">3850 UC</label>
                                                                <div class="number-input">
                                                                    <button onclick="this.parentNode.querySelector('input[type=number]').stepDown()" ></button>
                                                                    <input class="quantity" min="0" name="quantity3850" id="quantity3850" value="0" type="number">
                                                                    <button onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus"></button>
                                                                </div>
                                                                <!--
                                                                <input type="number" class="form-control form-control-user"
                                                                    name="quantity3850" id="quantity3850" placeholder="3850 UC">
                                                                -->
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-6">
                                                            <div class="form-group">
                                                                <label style="font-size:12px">8100 UC</label>
                                                                <div class="number-input">
                                                                    <button onclick="this.parentNode.querySelector('input[type=number]').stepDown()" ></button>
                                                                    <input class="quantity" min="0" name="quantity8100" id="quantity8100" value="0" type="number">
                                                                    <button onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus"></button>
                                                                </div>
                                                                <!--
                                                                <input type="number" class="form-control form-control-user"
                                                                    name="quantity8100" id="quantity8100" placeholder="8100 UC">
                                                                -->
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-6">
                                                            <div class="form-group">
                                                                <label style="font-size:12px">16200 UC</label>
                                                                <div class="number-input">
                                                                    <button onclick="this.parentNode.querySelector('input[type=number]').stepDown()" ></button>
                                                                    <input class="quantity" min="0" name="quantity16200" id="quantity16200" value="0" type="number">
                                                                    <button onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus"></button>
                                                                </div>
                                                                <!--
                                                                <input type="number" class="form-control form-control-user"
                                                                    name="quantity16200" id="quantity16200" placeholder="16200 UC">
                                                                -->
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-6">
                                                            <div class="form-group">
                                                                <label style="font-size:12px">24300 UC</label>
                                                                <div class="number-input">
                                                                    <button onclick="this.parentNode.querySelector('input[type=number]').stepDown()" ></button>
                                                                    <input class="quantity" min="0" name="quantity24300" id="quantity24300" value="0" type="number">
                                                                    <button onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus"></button>
                                                                </div>
                                                                <!--
                                                                <input type="number" class="form-control form-control-user"
                                                                    name="quantity24300" id="quantity24300" placeholder="24300 UC">
                                                                -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-lg-3">
                                                            <!--
                                                                <label style="font-size:12px">Total UC</label>
                                                            -->
                                                            <div class="form-group">
                                                                <input id="total" name="total" class="form-control" value="0 UC" disabled></input>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-9">
                                                            <button type="submit" class="btn btn-primary btn-block">
                                                                Submit
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <?php echo $message; ?>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-12">
                                        <div class="card shadow mb-4">
                                            <div class="card-header py-3">
                                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-fw fa-list"></i> Last Orders</h6>
                                            </div>
                                            <div class="card-body" id="orderTable">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                        <thead>
                                                        <tr>
                                                            <th>User ID</th>
                                                            <th>Quantity</th>
                                                            <th>Status</th>
                                                        </tr>
                                                        </thead>
                                                        <tfoot>
                                                        <tr>
                                                            <th>User ID</th>
                                                            <th>Quantity</th>
                                                            <th>Status</th>
                                                        </tr>
                                                        </tfoot>
                                                        <tbody id="ordersTable">
                                                        <?php

                                                        if(isset($orderDetails) && !empty($orderDetails)){
                                                            foreach ($orderDetails as $key => $orderDetail) {
                                                                echo "
                                                    <tr>
                                                        <td>".$orderDetail["userID"]."</td>
                                                        <td>".$orderDetail["quantity"]."</td>
                                                        <td>".$orderDetail["status"]."</td>
                                                    </tr>";
                                                            }
                                                        }

                                                        ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- /.container-fluid -->

                        </div>
                        <!-- End of Main Content -->

                        <!-- Footer -->
                        <footer class="sticky-footer bg-white">
                            <div class="container my-auto">
                                <div class="copyright text-center my-auto">
                                    <span>Copyright &copy; 2022</span>
                                </div>
                            </div>
                        </footer>
                        <!-- End of Footer -->

                    </div>
                    <!-- End of Content Wrapper -->

                </div>
                <!-- End of Page Wrapper -->

                <!-- Scroll to Top Button-->
                <a class="scroll-to-top rounded" href="#page-top">
                    <i class="fas fa-angle-up"></i>
                </a>

                <!-- Logout Modal-->
                <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                <a class="btn btn-primary" href="logout.php">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bootstrap core JavaScript-->
                <script src="theme/vendor/jquery/jquery.min.js"></script>
                <script src="theme/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

                <!-- Core plugin JavaScript-->
                <script src="theme/vendor/jquery-easing/jquery.easing.min.js"></script>

                <!-- Custom scripts for all pages-->
                <script src="theme/js/sb-admin-2.min.js"></script>

                <!-- Page level plugins -->
                <script src="theme/vendor/chart.js/Chart.min.js"></script>

                <!-- Page level custom scripts -->
                <script src="theme/js/demo/chart-area-demo.js"></script>
                <script src="theme/js/demo/chart-pie-demo.js"></script>

                <!-- Page level plugins -->
                <script src="theme/vendor/datatables/jquery.dataTables.min.js"></script>
                <script src="theme/vendor/datatables/dataTables.bootstrap4.min.js"></script>

                <!-- Page level custom scripts -->
                <script src="theme/js/demo/datatables-demo.js"></script>
                <script>
                    $('#quantity60').keyup(function() {
                        updateTotal();
                    });
                    $('#quantity325').keyup(function() {
                        updateTotal();
                    });
                    $('#quantity660').keyup(function() {
                        updateTotal();
                    });
                    $('#quantity1800').keyup(function() {
                        updateTotal();
                    });
                    $('#quantity3850').keyup(function() {
                        updateTotal();
                    });
                    $('#quantity8100').keyup(function() {
                        updateTotal();
                    });
                    $('#quantity16200').keyup(function() {
                        updateTotal();
                    });
                    $('#quantity24300').keyup(function() {
                        updateTotal();
                    });

                    var updateTotal = function () {
                        var input1 = parseInt($('#quantity60').val())*60;
                        var input2 = parseInt($('#quantity325').val())*325;
                        var input3 = parseInt($('#quantity660').val())*660;
                        var input4 = parseInt($('#quantity1800').val())*1800;
                        var input5 = parseInt($('#quantity3850').val())*3850;
                        var input6 = parseInt($('#quantity8100').val())*8100;
                        var input7 = parseInt($('#quantity16200').val())*16200;
                        var input8 = parseInt($('#quantity24300').val())*24300;

                        if (isNaN(input1)) {
                            input1 = 0;
                        }if (isNaN(input2)) {
                            input2 = 0;
                        }if (isNaN(input3)) {
                            input3 = 0;
                        }if (isNaN(input4)) {
                            input4 = 0;
                        }if (isNaN(input5)) {
                            input5 = 0;
                        }if (isNaN(input6)) {
                            input6 = 0;
                        }if (isNaN(input7)) {
                            input7 = 0;
                        }if (isNaN(input8)) {
                            input8 = 0;
                        }
                        $('#total').val(input1 + input2 + input3 + input4 + input5 + input6 + input7 + input8 + " UC");
                    };

                    $('form').on('click', 'button:not([type="submit"])', function(e){
                        e.preventDefault();
                        updateTotal();
                    })
                </script>
                </body>

                </html>

                <?php
            }else{
                header("Location: logout.php");
            }
        }else{
            header("Location: logout.php");
        }
    }else{
        header("Location: logout.php");
    }
}else{
    header("Location: logout.php");
}
?>