<?php
session_start();

if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"])){
    $adminUsername = $_SESSION["admin"];
    if(strpos($adminUsername,"admin")!==false){
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

                    $message = "";
                    if(isset($_POST) && !empty($_POST)){
                        if(isset($_POST["username"]) && !empty($_POST["username"]) && isset($_POST["password"]) && !empty($_POST["password"])){
                            $username = $_POST["username"];
                            if(!is_file("admins/".$username."_".md5($username).".json")){
                                $password = $_POST["password"];

                                $settingsDetails = [
                                    "adminKey"=>md5($username),
                                    "settingsKey"=>md5($username),
                                    "orderDuration"=>$settingsFile["orderDuration"],
                                    "threadCount"=>$settingsFile["threadCount"],
                                    "timeZone"=>$settingsFile["timeZone"],
                                    "owner"=>$username,
                                ];
                                $adminDetails = [
                                    "username"=>$username,
                                    "password"=>$password
                                ];
            
                                file_put_contents("settings/".$username."_".md5($username).".json",json_encode($settingsDetails));
                                file_put_contents("admins/".$username."_".md5($username).".json",json_encode($adminDetails));
            
                                $message = '
                                <div class="card mb-4 py-3 border-left-success">
                                    <div class="card-body">
                                        Admin created successfully!
                                    </div>
                                </div>';
                            }else{
                                $message = '
                                <div class="card mb-4 py-3 border-left-danger">
                                    <div class="card-body">
                                        This admin already created!
                                    </div>
                                </div>';
                            }
                        }else{
                            $message = '
                            <div class="card mb-4 py-3 border-left-danger">
                                <div class="card-body">
                                    Fill all fields!
                                </div>
                            </div>';
                        }
                    }

                    if(isset($_GET["username"]) && $_GET["username"] != $adminUsername){
                        $chosenAdmin = "admins/".$_GET["username"]."_".md5($_GET["username"]).".json";
                        $chosenSettings = "settings/".$_GET["username"]."_".md5($_GET["username"]).".json";
                        if(is_file($chosenAdmin) && is_file($chosenSettings)){
                            unlink($chosenAdmin);
                            unlink($chosenSettings);
                        }
                    }

                    $admins = glob("admins/*.json");
                    $totalAdmins = 0;

                    $adminDetails = [];
                    if(is_array($admins) && !empty($admins)){
                        foreach ($admins as $key => $admin) {
                            $adminDetail = file_get_contents($admin);
                            $adminDetail = json_decode($adminDetail,true);

                            $adminDetails[] = [
                                "username"=>$adminDetail["username"],
                                "password"=>$adminDetail["password"]
                            ];

                            $totalAdmins++;
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

    <title>Admins</title>

    <!-- Custom fonts for this template -->
    <link href="theme/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="theme/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="theme/img/undraw_profile.png">

    <!-- Custom styles for this page -->
        <link href="theme/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="theme/css/barmenu.css">

</head>
<script src="https://code.jquery.com/jquery-3.1.1.js"></script>
<script src="theme/js/demo/datatables-custom.js"></script>

<body id="page-top">

                <div class="hero__phone">
                    <section class="menu__body">
                        <div class="menu__links d-flex align-items-center"><!-- Sidebar - Brand -->
                            <a class="sidebar-brand align-items-center justify-content-center text-center" href="index.php">
                                <div class="sidebar-brand-icon phone_icon rotate-n-15">
                                    <i class="fas fa-skull-crossbones"></i>
                                </div>
                                <p style="font-size: small">Home</p>
                            </a>
                            <a class="sidebar-brand align-items-center justify-content-center text-center" href="orders.php">
                                <div class="phone_icon">
                                    <i class="fas fa-fw fa-list"></i>
                                </div>
                                <p style="font-size: small">Orders</p>
                            </a>
                            <a class="sidebar-brand align-items-center justify-content-center text-center" href="users.php">
                                <div class="phone_icon">
                                    <i class="fas fa-fw fa-user"></i>
                                </div>
                                <p style="font-size: small">M. Users</p>
                            </a>

                            <a class="sidebar-brand align-items-center justify-content-center text-center" href="codes.php">
                                <div class="phone_icon">
                                    <i class="fas fa-fw fa-code"></i>
                                </div>
                                <p style="font-size: small">Codes</p>
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

            <div class="sidebar-heading">Midasbuy account section</div>

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

                    <!-- Sidebar Toggle (Topbar)
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                     -->

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
                                            <!-- If is admin user then show admin panel -->
                                            <?php
                                            if(strpos($adminUsername,"admin")!==false){
                                                echo '
                                                    
                                                    <a class="dropdown-item" href="admins.php">
                                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400 fa-user-secret"></i>
                                                        Admins
                                                    </a>
                                                    
                                                    ';
                                            }
                                            ?>
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

                    <div class="row">
                        <div class="col-xl-12 col-md-12 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Total Admins</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalAdmins ?></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-user fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-fw fa-plus"></i> New Admin</h6>
                        </div>
                        <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <form class="user" method="POST" action="admins.php">
                                        <div class="form-group">
                                            <input type="text" class="form-control"
                                                name="username" aria-describedby="usernameHelp"
                                                placeholder="Username">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control"
                                                name="password" placeholder="Password">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            Submit
                                        </button>
                                        <hr>
                                        <?php echo $message; ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-fw fa-user"></i> Admins</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Password</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Username</th>
                                            <th>Password</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                    <?php
                                    
                                    if(isset($adminDetails) && !empty($adminDetails)){
                                        foreach ($adminDetails as $key => $adminDetail) {
                                            if($adminDetail["username"] !== $adminUsername){
                                                $action = "<td><a href='?username=".$adminDetail["username"]."'>Delete Admin</a></td>";
                                            }else{
                                                $action = "<td>-</td>";
                                            }
                                            echo "
                                            <tr>
                                                <td>".$adminDetail["username"]."</td>
                                                <td>".$adminDetail["password"]."</td>
                                                ".$action."
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
                        <span aria-hidden="true">??</span>
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
    <script src="theme/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="theme/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="theme/js/demo/datatables-demo.js"></script>

    <!-- Page barmenu -->
    
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
}else{
    header("Location: logout.php");
}
?>