<?php

/*
 * @author DJ110
 * @copyright 2015 Eventjoiner
 *
 *  This is to show user image
 *
 *  http://eventjoiner.io/client/mobile/v1.0/user/image.php?key=xxxxx
 *
 *  GET : key : key is user UUID(not secure user id)
 */

include_once("../../common/validation.php");
include_once("../../common/dbconfig.php");
include_once("../../common/database.php");
include_once("../../common/image.php");
include_once("../../common/handleresponse.php");

if ($_SERVER["REQUEST_METHOD"] == "GET"){
    $uuid = $_GET['key'];

    if (Validation::includeBlank($uuid)) {
        HandleResponse::badRequest("Parameters are blank");
    }
    else {
        // Retrieve image data and analysis and show
        $conn = null;
        try {
            $db = new DBConnection();
            $conn = $db->getConnection();
            mysqli_query($conn, "set autocommit = 0");
            mysqli_query($conn, "begin");

            $image = getUserImage($conn, $uuid);
            mysqli_query($conn, "commit");
            if ($image != null && ImageUtil::isSupport($image)) {
                header( "Content-Type: ". ImageUtil::contentType($image) );
                echo $image;
            }
            else {
                // default image
                header( "Content-Type: ". 'image/png' );
                $im = imagecreatefrompng("../../resources/defaultuser.png");
                imagepng($im);
                imagedestroy($im);
            }
        }
        catch (Exception $e) {
            mysqli_query($conn, "rollback");
            HandleResponse::badRequest($e->getMessage());
        }
    }
}
else {
    // NOT GET request
    http_response_code(404);
}
?>