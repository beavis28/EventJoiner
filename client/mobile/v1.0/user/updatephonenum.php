<?php
    /*
     * @author DJ110
     * @copyright 2015 Eventjoiner
     *
     *  This is update phone API for mobile
     *
     *  http://eventjoiner.io/client/mobile/v1.0/updatephonenum.php
     *
     *  POST : access_tokenm phone_number
     */

include_once("../../common/validation.php");
include_once("../../common/dbconfig.php");
include_once("../../common/database.php");
include_once("../../common/handleresponse.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // image, access_token, phone number
    $ret = array();
    $access_token = $_POST["access_token"];
    $phone_number = $_POST["phone_number"];

    if (Validation::includeBlank($access_token, $phone_number)) {
        HandleResponse::badRequest("Parameters are blank");
    }
    else if (!Validation::isValidPhonenumber($phone_number)) {
        HandleResponse::badRequest("Invalid phone number");
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
                // Update Phone number
                $ret = updatePhoneNumber($conn, $user_id, $phone_number);
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