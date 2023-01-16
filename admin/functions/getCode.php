<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
if($userAgent !== "pubgUserAgent"){
    exit(0);
}

if(isset($_GET["requestedCodes"]) && !empty($_GET["requestedCodes"]) && isset($_GET["owner"]) && !empty($_GET["owner"])){
    $owner = $_GET["owner"];

    $codes = glob("../codes/*.json");
    if(is_array($codes) && count($codes)>0){
        $requestedCodes = $_GET["requestedCodes"];
        $requestedCodes = explode(" | ",$requestedCodes);
        foreach ($requestedCodes as $key => $requestedCode) {
            list($amount,$quantity) = explode("*",$requestedCode);

            $countCode = 0;
            foreach ($codes as $key => $code) {
                $codeDetails = file_get_contents($code);
                $codeDetails = json_decode($codeDetails,true);
    
                if(isset($codeDetails["owner"]) && $codeDetails["owner"] == $owner){
                    if(is_array($codeDetails)){
                        $currentAmount = $codeDetails["amount"];
                        if($currentAmount == $amount){
                            if($countCode < $quantity){
                                $output[] = [
                                    "code"=>$codeDetails["code"],
                                    "owner"=>$codeDetails["owner"]
                                ];
        
                                $countCode++;
                            }
                        }
                    }
                }
            }
        }
        if(isset($output) && !empty($output) && count($output)>0){
            echo json_encode($output);
        }else{
            echo "null";
            //echo "There's no more code!";
        }
    }else{
        echo "null";
        //echo "There's no more code!";
    }
}
?>