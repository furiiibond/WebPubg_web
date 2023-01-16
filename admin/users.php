<?php
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

                $message = "";
                if(isset($_POST) && !empty($_POST)){
                    if(isset($_POST["username"]) && !empty($_POST["username"]) && isset($_POST["password"]) && !empty($_POST["password"])){
                        $username = $_POST["username"];
                        if(!is_file("users/".$username.".json")){
                            $password = $_POST["password"];

                            /*
                                $users = glob("users/*.json");
                                $userID = count($users)+1;
                            */
                            if(is_file("userID.txt")){
                                $userID = intval(file_get_contents("userID.txt"))+1;
                                file_put_contents("userID.txt",$userID);
                            }else{
                                $userID = 1;
                                file_put_contents("userID.txt",1);
                            }
        
                            $userDetail = [
                                "id"=>$userID,
                                "username"=>$username,
                                "password"=>$password,
                                "status"=>"inactive",
                                "owner"=>$adminUsername
                            ];
        
                            file_put_contents("users/".$username.".json",json_encode($userDetail));
        
                            $message = '
                            <div class="card mb-4 py-3 border-left-success">
                                <div class="card-body">
                                    User created successfully!
                                </div>
                            </div>';
                        }else{
                            $message = '
                            <div class="card mb-4 py-3 border-left-danger">
                                <div class="card-body">
                                    This user already created!
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

                if(isset($_GET["username"]) && !empty($_GET["username"])){
                    if(isset($_GET["status"]) && $_GET["status"] == "Delete"){
                        $chosenUser = "users/".$_GET["username"].".json";
        
                        if(is_file($chosenUser)){
                            $chosenDetails = file_get_contents($chosenUser);
                            $chosenDetails = json_decode($chosenDetails,true);

                            if(isset($chosenDetails["owner"]) && $chosenDetails["owner"] == $adminUsername){
                                unlink($chosenUser);
                            }
                        }
                    }
                }
                if(isset($_GET["username"]) && !empty($_GET["username"]) && isset($_GET["status"]) && !empty($_GET["status"])){
                    if($_GET["status"] == "active"){
                        $chosenUser = "users/".$_GET["username"].".json";
        
                        if(is_file($chosenUser)){
                            $chosenDetails = file_get_contents($chosenUser);
                            $chosenDetails = json_decode($chosenDetails,true);

                            if(isset($chosenDetails["owner"]) && $chosenDetails["owner"] == $adminUsername){
                                $chosenDetails["status"] = "inactive";

                                file_put_contents($chosenUser,json_encode($chosenDetails));
                            }
                        }
                    }
                }

                $users = glob("users/*.json");
                $totalUsers = 0;

                $userDetails = [];
                if(is_array($users) && !empty($users)){
                    foreach ($users as $key => $user) {
                        $userDetail = file_get_contents($user);
                        $userDetail = json_decode($userDetail,true);

                        if(isset($userDetail["owner"]) && $userDetail["owner"] == $adminUsername){
                            if($userDetail["status"] == "active"){
                                $status = "<a href='?username=".$userDetail["username"]."&status=".$userDetail["status"]."'><font style='background-color: #1cc88a; color:white; padding:5px'>".$userDetail["status"]."</font></a>";
                            }else{
                                $status = "<font style='background-color: #f6c23e; color:white; padding:5px'>".$userDetail["status"]."</font>";
                            }
    
                            $userDetails[] = [
                                "id"=>$userDetail["id"],
                                "username"=>$userDetail["username"],
                                "password"=>$userDetail["password"],
                                "status"=>$status
                            ];

                            $totalUsers++;
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

    <title>Users</title>

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

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

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

                    <div class="row">
                        <div class="col-xl-12 col-md-12 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Total Users</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalUsers ?></div>
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
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-fw fa-plus"></i> New User</h6>
                        </div>
                        <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <form class="user" method="POST" action="users.php">
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
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-fw fa-user"></i> Users</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Password</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Password</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                    <?php
                                    
                                    if(isset($userDetails) && !empty($userDetails)){
                                        foreach ($userDetails as $key => $userDetail) {
                                            echo "
                                            <tr>
                                                <td>".$userDetail["id"]."</td>
                                                <td>".$userDetail["username"]."</td>
                                                <td>".$userDetail["password"]."</td>
                                                <td>".$userDetail["status"]."</td>
                                                <td><a href='?username=".$userDetail["username"]."&status=Delete'>Delete User</a></td>
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
    <script src="theme/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="theme/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="theme/js/demo/datatables-demo.js"></script>

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