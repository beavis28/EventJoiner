<?php
/*
 * @author DJ110
 * @copyright 2015 Eventjoiner
 *
 *  This is to update postalcode API for mobile
 *
 *  http://eventjoiner.io/client/mobile/v1.0/updatesex.php
 *
 *  POST : access_token sex 0: nothing, 1: male, 2: female
*/

include_once("../../common/validation.php");
include_once("../../common/dbconfig.php");
include_once("../../common/database.php");
include_once("../../common/handleresponse.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // access_token, occupation
    $ret = array();
    $access_token = $_POST["access_token"];
    $sex = $_POST["sex"];

    if (Validation::includeBlank($access_token, $sex)) {
       HandleResponse::badRequest("Parameters are blank");
    }
    else if (!Validation::isValidSex($sex)) {
        HandleResponse::badRequest("Invalid user sex");
    }
    else {
        $conn = null;
        try {
            $db = new DBConnection();
            $conn = $db->getConnection();
            mysqli_query($conn, "set autocommit = 0");
            mysqli_query($conn, "begin");

            $user_id = getUserIdFromToken($conn, $access_token);
            if ($user_id == null) {
                $ret = HandleResponse::badRequestReturn("Invalid Accesstoken");
            } else {
                // Update postal code
                $ret = updateSex($conn, $user_id, $sex);
            }
            mysqli_query($conn, "commit");
        } catch (Exception $e) {
            $ret = HandleResponse::badRequestReturn($e->getMessage());
        }
        print json_encode($ret);
    }
}
else {
    // NOT POST request
    http_response_code(404);
}
?>