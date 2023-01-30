<?php
/**
 * @param $requestedCodes
 * @param $owner
 * @return boolean
 * check if there's enough code for the user
 */
function isEnoughCode($requestedCodes, $owner)
{
    $codes = glob("codes/*.json");
    if (is_array($codes) && count($codes) > 0) {
        $requestedCodes = explode(" - ", $requestedCodes);
        $totalQuantity = 0;
        foreach ($requestedCodes as $key => $requestedCode) {
            list($amount, $quantity) = explode("*", $requestedCode);
            $totalQuantity += $quantity;
            $countCode = 0;
            foreach ($codes as $key => $code) {
                $codeDetails = file_get_contents($code);
                $codeDetails = json_decode($codeDetails, true);

                if (isset($codeDetails["owner"]) && $codeDetails["owner"] == $owner) {
                    if (is_array($codeDetails)) {
                        $currentAmount = $codeDetails["amount"];
                        if ($currentAmount == $amount) {
                            if ($countCode < $quantity) {
                                $output[] = [
                                    "code" => $codeDetails["code"],
                                    "owner" => $codeDetails["owner"]
                                ];

                                $countCode++;
                            }
                        }
                    }
                }
            }
        }
        if (isset($output) && !empty($output) && count($output) > 0 && count($output) >= $totalQuantity) {
            return true;
        }
    }
    return false;
}