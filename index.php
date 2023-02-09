<?php

$message = "";
if(isset($_POST) && !empty($_POST)){
    if(isset($_POST["username"]) && !empty($_POST["username"]) && isset($_POST["password"]) && !empty($_POST["password"])){
        $username = $_POST["username"];
        $password = $_POST["password"];

        $adminFiles = glob("admin/admins/*_*.json");
        foreach ($adminFiles as $key => $adminFile) {
            if(is_file($adminFile)){
                $adminFile = file_get_contents($adminFile);
                $adminFile = json_decode($adminFile,true);
        
                if(is_array($adminFile) && !empty($adminFile)){
                    $checkUsername = $adminFile["username"];
                    $checkPassword = $adminFile["password"];
    
                    if($username === $checkUsername && $password === $checkPassword){
                        session_start();
    
                        $message = '
                        <div class="card mb-4 py-3 border-left-success">
                            <div class="card-body">
                                Successfully logged in! Redirecting...
                            </div>
                        </div>';
    
                        $_SESSION["admin"] = $username;
                        header("Location: admin/index.php");
                    }else{
                        $message = '
                        <div class="card mb-4 py-3 border-left-danger">
                            <div class="card-body">
                                Wrong credentials!
                            </div>
                        </div>';
                    }
                }else{
                    $message = '
                    <div class="card mb-4 py-3 border-left-danger">
                        <div class="card-body">
                            Admin file corrupted!
                        </div>
                    </div>';
                }
            }else{
                $message = '
                <div class="card mb-4 py-3 border-left-danger">
                    <div class="card-body">
                        Admin file does not exist!
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

    <title>Login</title>

    <!-- Custom fonts for this template-->
    <link href="admin/theme/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="admin/theme/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="admin/theme/img/undraw_profile.png">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-400 mb-4">Welcome Back!</h1>
                                    </div>
                                    <form class="user" method="POST" action="index.php">
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
                                            Login
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

            <!-- Contact Us Section -->
            <section class="" id="contact">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <h4 class="section-heading text-uppercase">Contact Us</h4>
                            <p class="section-subheading text-muted">Reach out to us for any queries or assistance at this phone number : +966 50 980 9822</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="admin/theme/vendor/jquery/jquery.min.js"></script>
    <script src="admin/theme/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="admin/theme/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="admin/theme/js/sb-admin-2.min.js"></script>

</body>

</html>