<?php
/*
 * @author DJ110
 * @copyright 2015 Eventjoiner
 *
 *  This is update first name API for mobile
 *
 *  http://eventjoiner.io/client/mobile/v1.0/updatefirstname.php
 *
 *  POST : access_token firstname
 */

include_once("../../common/validation.php");
include_once("../../common/dbconfig.php");
include_once("../../common/database.php");
include_once("../../common/handleresponse.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // image, access_token, firstname
    $ret = array();
    $access_token = $_POST["access_token"];
    $first_name = $_POST["firstname"];

    if (Validation::includeBlank($access_token, $first_name)) {
        HandleResponse::badRequest("Parameters are blank");
    }
    else if (!Validation::isValidUserName($first_name)) {
        HandleResponse::badRequest("Invalid user firstname");
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
                // Update first name
                $ret = updateFirstname($conn, $user_id, $first_name);
            }
            mysqli_query($conn, "commit");
        } catch (Exception $e) {
            mysqli_query($conn, "rollback");
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