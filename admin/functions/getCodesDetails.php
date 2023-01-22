<?php
session_start();
if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"])) {
    $adminUsername = $_SESSION["admin"];

    $codes = glob("../codes/*.json");
    $codeDetails = [];
    foreach ($codes as $key => $code) {
        $codeDetail = file_get_contents($code);
        $codeDetail = json_decode($codeDetail, true);

        if (isset($codeDetail["owner"]) && $codeDetail["owner"] == $adminUsername) {
            $codeDetails[] = $codeDetail["amount"];
        }
    }
    $codeDetails = array_count_values($codeDetails);
    ksort($codeDetails);


    if (isset($codeDetails) && !empty($codeDetails)) {
        foreach ($codeDetails as $key => $codeDetail) {
            echo "
                                                    <tr>
                                                        <td>" . $key . " UC</td>
                                                        <td>" . $codeDetail . "</td>
                                                    </tr>";
        }
    }
}
