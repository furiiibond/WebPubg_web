<?php


if(isset($_GET["getSettings"]) && !empty($_GET["getSettings"])){
    $settingsFiles = glob("../settings/*_*.json");
    if(count($settingsFiles)>0){
        foreach ($settingsFiles as $key => $settingsFile) {
            $settingsDetail = file_get_contents($settingsFile);
            $settingsDetail = json_decode($settingsDetail,true);
    
            if(is_array($settingsDetail)){
                $owner = $settingsDetail["owner"];

                $users = glob("../users/*.json");
                $usersForOwner = [];
                foreach ($users as $key => $user) {
                    $userDetail = file_get_contents($user);
                    $userDetail = json_decode($userDetail,true);
        
                    if($userDetail["owner"] == $owner){
                        $usersForOwner[] = $userDetail["username"];
                    }
                }
                $codes = glob("../codes/*.json");
                $codesForOwner = [];
                foreach ($codes as $key => $code) {
                    $codeDetail = file_get_contents($code);
                    $codeDetail = json_decode($codeDetail,true);
        
                    if($codeDetail["owner"] == $owner){
                        $codesForOwner[] = $codeDetail["code"];
                    }
                }

                if(isset($usersForOwner) && count($usersForOwner)>0 && isset($codesForOwner) && count($codesForOwner)>0){
                    $preparedSettingsFiles[] = json_encode($settingsDetail);
                }
            }else{
                //echo "Settings file is corrupted!";
            }
        }

        if(isset($preparedSettingsFiles) && !empty($preparedSettingsFiles) && count($preparedSettingsFiles)>0){
            echo $preparedSettingsFiles[array_rand($preparedSettingsFiles)];
        }else{
            echo "null";
            //echo "There's no user or no code for that user!";
        }
    }else{
        echo "null";
        //echo "Settings file does not exist!";
    }
}
?>