<?php

/*
 * @author DJ110
 * @copyright 2015 Eventjoiner
 *
 *  This is to signin(check passowrd and publish accesstoken for mobile player)
 *
 *  http://eventjoiner.io/client/mobile/v1.0/user/signin.php
 *
 *  POST : email, password, deviceid(iOS, Android)
 */

include_once("../../common/validation.php");
include_once("../../common/dbconfig.php");
include_once("../../common/database.php");
include_once("../../common/handleresponse.php");

if ($_SERVER["REQUEST_METHOD"] == "POST"){

    // TODO : Request Validation

    $ret = array();
    // email, password, device_id  (required)
    $email = $_POST["email"];
    $password = $_POST["password"];
    $deviceid = $_POST["device"];

    // Validation
    if (Validation::includeBlank($email, $password, $deviceid)) {
        HandleResponse::badRequest("Parameters are blank");
    }
    else if (!Validation::isValidEmail($email)) {
        HandleResponse::badRequest("Email is invalid");
    }
    else {
        $conn = null;

        try {
            $db = new DBConnection();
            $conn = $db->getConnection();

            mysqli_query($conn, "set autocommit = 0");
            mysqli_query($conn, "begin");

            $user_id = matchUser($conn, $email, $password);

            if ($user_id == -1) {
                $ret = HandleResponse::badRequestReturn("Email or Password is wrong");
            }
            else {
                $token_data = getExistingToken($conn, $user_id, $deviceid);
                $access_token = UUID::v4();
                if ($token_data != null) {
                    // Update token
                    $ret = updateToken($conn, $user_id, $deviceid, $access_token);
                }
                else {
                    // Generate new token
                    $ret = generateToken($conn, $user_id, $deviceid, $access_token);
                }
            }
            mysqli_query($conn, "commit");
        }
        catch (Exception $e) {
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