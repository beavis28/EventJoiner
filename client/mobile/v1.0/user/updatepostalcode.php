<?php
/*
 * @author DJ110
 * @copyright 2015 Eventjoiner
 *
 *  This is to update postalcode API for mobile
 *
 *  http://eventjoiner.io/client/mobile/v1.0/updatepostalcode.php
 *
 *  POST : access_token postalcode
*/

include_once("../../common/validation.php");
include_once("../../common/dbconfig.php");
include_once("../../common/database.php");
include_once("../../common/handleresponse.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // access_token, occupation
    $ret = array();
    $access_token = $_POST["access_token"];
    $postalcode = $_POST["postalcode"];

    if (Validation::includeBlank($access_token, $postalcode)) {
        HandleResponse::badRequest("Parameters are blank");
    }
    else if (!Validation::isValidPostalcode($postalcode)) {
        HandleResponse::badRequest("Invalid user postalcode");
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
                $ret = updatePostalcode($conn, $user_id, $postalcode);
            }
            mysqli_query($conn, "commit");
        } catch (Exception $e) {
            mysqli_query($conn, "rollback");
            $ret = HandleResponse::badRequestReturn("Invalid Accesstoken");
        }
        print json_encode($ret);
    }

}
else {
    // NOT POST request
    http_response_code(404);
}
?>