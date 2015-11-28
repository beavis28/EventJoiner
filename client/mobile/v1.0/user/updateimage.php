<?php
/*
 * @author DJ110
 * @copyright 2015 Eventjoiner
 *
 *  This is to save image from user post
 *
 *  http://eventjoiner.io/client/mobile/v1.0/user/updateimage.php
 *
 *  POST : access_token image (form/multipart)
 */

include_once("../../common/validation.php");
include_once("../../common/dbconfig.php");
include_once("../../common/database.php");
include_once("../../common/image.php");
include_once("../../common/handleresponse.php");

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    // image, access_token
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
            }
            else {

                // Limitation 1MB
                if (!isset($_FILES['profile']['error']) || is_array($_FILES['profile']['error'])) {
                    $ret = HandleResponse::badRequestReturn("Failed to upload iamge");
                }
                else if ($_FILES['profile']['size'] > 1000000) {  // 1MB limitation
                    $ret = HandleResponse::badRequestReturn("Image is too big");
                }
                else {
                    $fp = fopen($_FILES["profile"]["tmp_name"], "rb");            // name=image
                    $imgdat = fread($fp, filesize($_FILES["profile"]["tmp_name"]));
                    fclose($fp);

                    if ($imgdat != null) {
                        // Image Check
                        if (!ImageUtil::isSupport($imgdat)) {
                            $ret = HandleResponse::badRequestReturn("Invalid image(please upload png or jpg");
                        }
                        else {
                            // Save Image
                            $ret = updateImage($conn, $user_id, $imgdat);
                        }
                    }
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