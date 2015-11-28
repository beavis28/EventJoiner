<?php
/*
 * @author DJ110
 * @copyright 2015 Eventjoiner
 *
 *  This is to create user with minimum requirements
 *
 *  http://eventjoiner.io/client/mobile/v1.0/user/create.php
 *
 *  POST : email, password, firstname, lastname, birthday(YYYY-mm-dd)
 */


include_once("../../common/validation.php");
include_once("../../common/dbconfig.php");
include_once("../../common/database.php");
include_once("../../common/handleresponse.php");

if ($_SERVER["REQUEST_METHOD"] == "POST"){

    // TODO : Request Validation by UA


    $ret = array();
    // email, password, firstname, lastname, birthday  (required)
    $email = $_POST["email"];
    $password = $_POST["password"];
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $birthday = $_POST["birthday"];

    if (Validation::includeBlank($email, $password, $firstname, $lastname, $birthday)) {
        HandleResponse::badRequest("Parameters are blank");

    }  // Email Validation
    else if (!Validation::isValidEmail($email)) {
        HandleResponse::badRequest("Email is invalid");

    }  // Password Validation
    else if (!Validation::isValidPassword($password)) {
        HandleResponse::badRequest("Password should be over 6!");
    }
    else if (!Validation::isValidTime($birthday)) {
        HandleResponse::badRequest("Birthday style is wrong!");
    }
    else {
        $conn = null;
        try {
            $db = new DBConnection();
            $conn = $db->getConnection();

            mysqli_query($conn, "set autocommit = 0");
            mysqli_query($conn, "begin");

            if (isExistUser($conn, $email)) {
                $ret = HandleResponse::badRequestReturn("This email is already exist");
            }
            else {
                $ret = createUser($conn, $email, $password, $firstname, $lastname, $birthday);
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