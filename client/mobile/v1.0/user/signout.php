<?php

/*
 * @author DJ110
 * @copyright 2015 Eventjoiner
 *
 *  This is to sign out(invalidate accesstoken) from device
 *
 *  http://eventjoiner.io/client/mobile/v1.0/user/signout.php
 *
 *  POST : deviceid(iOS, Android)
 */


include_once("../../common/validation.php");
include_once("../../common/dbconfig.php");
include_once("../../common/database.php");
include_once("../../common/handleresponse.php");

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    // device
    $ret = array();
    $deviceid = $_POST["device"];

    if (Validation::includeBlank($deviceid)) {
        HandleResponse::badRequest("Parameters are blank");
    }
    else {
        $conn = null;
        try {
            $db = new DBConnection();
            $conn = $db->getConnection();
            mysqli_query($conn, "set autocommit = 0");
            mysqli_query($conn, "begin");
            $ret = invalidToken($conn, $deviceid);
            mysqli_query($conn, "commit");
            print json_encode($ret);
        }
        catch (Exception $e) {
            mysqli_query($conn, "rollback");
            HandleResponse::badRequest($e->getMessage());
        }
    }
}
else {
    // NOT POST request
    http_response_code(404);
}
?>