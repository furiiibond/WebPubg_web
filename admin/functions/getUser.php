<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
if($userAgent !== "pubgUserAgent"){
    exit(0);
}

if(isset($_GET["getUser"]) && !empty($_GET["getUser"]) && isset($_GET["owner"]) && !empty($_GET["owner"])){
    $owner = $_GET["owner"];

    if(isset($_GET["userAmount"])){
        $userAmount = $_GET["userAmount"];
    }else{
        $userAmount = 1;
    }
    $users = glob("../users/*.json");
    if(is_array($users) && count($users)>0){
        $countUser = 0;
        foreach ($users as $key => $user) {
            $userDetails = file_get_contents($user);
            $userDetails = json_decode($userDetails,true);

            if(isset($userDetails["owner"]) && $userDetails["owner"] == $owner){
                if(is_array($userDetails)){
                    if($userDetails["status"] == "inactive"){
                        if($countUser < $userAmount){
                            $username = $userDetails["username"];
                            $password = $userDetails["password"];
                            $owner = $userDetails["owner"];
                            
                            $output[] = [
                                "username"=>$username,
                                "password"=>$password,
                                "owner"=>$owner
                            ];
        
                            $countUser++;
                        }
                    }
                }
    
                //$userDetails["status"] = "active";
                //file_put_contents($user,json_encode($userDetails));
            }
        }
        if(isset($output) && !empty($output) && count($output)>0){ // If there's any user
            echo json_encode($output); // Return the user
        }else{
            echo "null";
            //echo "There's no more user!";
        }
    }else{
        echo "null";
        //echo "There's no more user!";
    }
}
?>