<?php
/*
 * @author DJ110
 * @copyright 2015 Eventjoiner
 *
 *  This is update last name API for mobile
 *
 *  http://eventjoiner.io/client/mobile/v1.0/updatelastname.php
 *
 *  POST : access_token lastname
*/

include_once("../../common/validation.php");
include_once("../../common/dbconfig.php");
include_once("../../common/database.php");
include_once("../../common/handleresponse.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // access_token, lastname
    $ret = array();
    $access_token = $_POST["access_token"];
    $last_name = $_POST["lastname"];

    if (Validation::includeBlank($access_token, $last_name)) {
        HandleResponse::badRequest("Parameters are blank");
    }
    else if (!Validation::isValidUserName($last_name)) {
        HandleResponse::badRequest("Invalid user last name");
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
                $ret= HandleResponse::badRequestReturn("Invalid user last name");
            } else {
                // Update last name
                $ret = updateLastname($conn, $user_id, $last_name);
            }
            mysqli_query($conn, "commit");
        } catch (Exception $e) {
            mysqli_query($conn, "rollback");
            $ret= HandleResponse::badRequestReturn($e->getMessage());
        }
        print json_encode($ret);
    }

}
else {
    // NOT POST request
    http_response_code(404);
}
?>