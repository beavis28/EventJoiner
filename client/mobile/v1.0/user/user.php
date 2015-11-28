<?php

/*
 * @author DJ110
 * @copyright 2015 Eventjoiner
 *
 *  This API is to get user data using accesstoken
 *
 *  http://eventjoiner.io/client/mobile/v1.0/user.php
 *
 *  POST : access_token
 *
 *  return : image url, first name, last name, email, phone number, address, postal code, sex, occupation
*/

include_once("../../common/validation.php");
include_once("../../common/appconfig.php");
include_once("../../common/dbconfig.php");
include_once("../../common/database.php");
include_once("../../common/handleresponse.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // access_token
    $ret = array();
    $access_token = $_POST["access_token"];

    if (Validation::includeBlank($access_token)) {
        HandleResponse::badRequest("Parameters are blank");
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
                $ret = getUser($conn, $user_id);
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