<?php

include_once("uuid.php");

function fail($conn, $ret) {
    $ret['IsSuccess'] = false;
    $ret['Msg'] = mysqli_error($conn);
}

function createUser($conn, $email, $password, $firstname, $lastname, $birthday) {
    // Insert data
    $ret = array();
    $hashed_passwd = password_hash($password, PASSWORD_BCRYPT);

    $timestamp = strtotime($birthday);

    $sql = "insert into `user` (`email`, `password`, `firstname`, `lastname`, `birthday`, `uuid`, `status`) values ('"
    . mysqli_real_escape_string($conn, $email) . "', '"
    . $hashed_passwd  . "', '"
    . mysqli_real_escape_string($conn, $firstname)  . "', '"
    . mysqli_real_escape_string($conn, $lastname)  . "', '"
    . date("Y-m-d H:i:s", $timestamp) . "', '"
    . UUID::v4() . "', '"
    . 1 . "' )";

    if (!mysqli_query($conn, $sql)) {
        $ret['IsSuccess'] = false;
        $ret['Msg'] = mysqli_error($conn);
    }
    else {
        $ret['IsSuccess'] = true;
        $ret['ID'] = mysqli_insert_id($conn);
        $ret['email'] = $email;
        $ret['password'] = $password;
    }

    return $ret;
}

function isExistUser($conn, $email) {
    $sql = "select count(*) as count from `user` where `email`='" . mysqli_real_escape_string($conn, $email) ."';";
    $handle = mysqli_query($conn, $sql) or die('Error querying database.');
    $data = mysqli_fetch_assoc($handle);
    $count=$data['count'];
    return $count > 0;
}

function matchUser($conn, $email, $password) {
    $id = -1;
    $pass = '';
    $sql = "select id, password from `user` where `email`='" . mysqli_real_escape_string($conn, $email) ."';";
    $result = mysqli_query($conn, $sql) or die('Error querying database.');
    while ($row = mysqli_fetch_array($result)){
        $id = $row['id'];
        $pass = $row['password'];
        break;
    }
    if (password_verify($password, $pass)) {
        return $id;
    }
    return -1;
}

function checkPassword($conn, $user_id, $password) {
    $sql = "select password from `user` where `id`='" . $user_id ."';";
    $pass = '';
    $result = mysqli_query($conn, $sql) or die('Error querying database.');
    while ($row = mysqli_fetch_array($result)){
        $pass = $row['password'];
        break;
    }
    return password_verify($password, $pass);
}

function getExistingToken($conn, $userid, $device) {
    $sql = "select * from `user_access_token` where `user_id`='" . $userid . "' and `device`='" . mysqli_real_escape_string($conn, $device) . "';";
    $result = mysqli_query($conn, $sql);
    $res = null;
    while ($row = mysqli_fetch_array($result)){
        $res = $row;
    }
    return $res;
}

function getUserIdFromToken($conn, $token) {
    $sql = "select user_id from `user_access_token` where `access_token`='" . mysqli_real_escape_string($conn, $token) . "';";
    $result = mysqli_query($conn, $sql);
    $res = null;
    while ($row = mysqli_fetch_array($result)){
        $res = $row['user_id'];
    }
    return $res;
}

function updateToken($conn, $user_id, $device, $token) {
    $ret = array();
    $sql = "update `user_access_token` set `access_token`='" . $token . "', `updated`=now() where `user_id`='" . $user_id . "' and `device`='" . mysqli_real_escape_string($conn, $device) . "';";

    if (!mysqli_query($conn, $sql)) {
        $ret['IsSuccess'] = false;
        $ret['Msg'] = mysqli_error($conn);
    }
    else {
        $ret['IsSuccess'] = true;
        $ret['device'] = $device;
        $ret['accesstoken'] = $token;
    }
    return $ret;
}

function generateToken($conn, $userid, $device, $token){
    $ret = array();
    $sql = "insert into `user_access_token` (`user_id`, `access_token`, `device`) values ('"
        . $userid . "', '"
        . $token . "', '"
        . $device . "' )";

    if (!mysqli_query($conn, $sql)) {
        $ret['IsSuccess'] = false;
        $ret['Msg'] = mysqli_error($conn);
    }
    else {
        $ret['IsSuccess'] = true;
        $ret['ID'] = mysqli_insert_id($conn);
        $ret['device'] = $device;
        $ret['accesstoken'] = $token;
    }
    return $ret;
}

function invalidToken($conn, $device) {
    $ret = array();
    $sql = "delete from `user_access_token` where `device`='" . mysqli_real_escape_string($conn, $device) . "';";

    if (!mysqli_query($conn, $sql)) {
        $ret['IsSuccess'] = false;
        $ret['Msg'] = mysqli_error($conn);
    }
    else {
        $ret['IsSuccess'] = true;
    }
    return $ret;
}

function updateImage($conn, $user_id, $image) {
    $ret = array();
    $sql = "update `user` set `image`='" . mysqli_real_escape_string($conn, $image) . "', `updated`=now() where `id`='" . $user_id . "';";

    if (!mysqli_query($conn, $sql)) {
        echo "Fail";
        $ret['IsSuccess'] = false;
        $ret['Msg'] = mysqli_error($conn);
        echo $ret['Msg'];
    }
    else {
        echo "Success";
        $ret['IsSuccess'] = true;
    }
    return $ret;
}

function getUserImage($conn, $uuid) {
    $sql = "select image from `user` where `uuid`='" . mysqli_real_escape_string($conn, $uuid) . "';";
    $result = mysqli_query($conn, $sql);
    $res = null;
    while ($row = mysqli_fetch_array($result)){
        $res = $row['image'];
    }
    return $res;
}

function updatePhoneNumber($conn, $user_id, $phone) {
    $ret = array();
    $sql = "update `user` set `phonenumber`='" . mysqli_real_escape_string($conn, $phone) . "', `updated`=now() where `id`='" . $user_id . "';";
    if (!mysqli_query($conn, $sql)) {
        $ret['IsSuccess'] = false;
        $ret['Msg'] = mysqli_error($conn);
    }
    else {
        $ret['IsSuccess'] = true;
        $ret['phonenumber'] = $phone;
    }
    return $ret;
}

function updateFirstname($conn, $user_id, $firstname) {
    $ret = array();
    $sql = "update `user` set `firstname`='" . mysqli_real_escape_string($conn, $firstname) . "', `updated`=now() where `id`='" . $user_id . "';";
    if (!mysqli_query($conn, $sql)) {
        $ret['IsSuccess'] = false;
        $ret['Msg'] = mysqli_error($conn);
    }
    else {
        $ret['IsSuccess'] = true;
        $ret['firstname'] = $firstname;
    }
    return $ret;

}

function updateLastname($conn, $user_id, $lastname) {
    $ret = array();
    $sql = "update `user` set `lastname`='" . mysqli_real_escape_string($conn, $lastname) . "', `updated`=now() where `id`='" . $user_id . "';";
    if (!mysqli_query($conn, $sql)) {
        $ret['IsSuccess'] = false;
        $ret['Msg'] = mysqli_error($conn);
    }
    else {
        $ret['IsSuccess'] = true;
        $ret['lastname'] = $lastname;
    }
    return $ret;
}

function updatePassword($conn, $user_id, $newpass) {
    $ret = array();
    $hashed_passwd = password_hash($newpass, PASSWORD_BCRYPT);

    $sql = "update `user` set `password`='" . $hashed_passwd . "', `updated`=now() where `id`='" . $user_id . "';";
    if (!mysqli_query($conn, $sql)) {
        $ret['IsSuccess'] = false;
        $ret['Msg'] = mysqli_error($conn);
    }
    else {
        $ret['IsSuccess'] = true;
    }
    return $ret;
}

function updateAddress($conn, $user_id, $address) {
    $ret = array();
    $sql = "update `user` set `address`='" . mysqli_real_escape_string($conn, $address) . "', `updated`=now() where `id`='" . $user_id . "';";
    if (!mysqli_query($conn, $sql)) {
        $ret['IsSuccess'] = false;
        $ret['Msg'] = mysqli_error($conn);
    }
    else {
        $ret['IsSuccess'] = true;
        $ret['address'] = $address;
    }
    return $ret;
}

function updateOccupation($conn, $user_id, $occupation) {
    $ret = array();
    $sql = "update `user` set `occupation`='" . mysqli_real_escape_string($conn, $occupation) . "', `updated`=now() where `id`='" . $user_id . "';";
    if (!mysqli_query($conn, $sql)) {
        fail($conn, $ret);
    }
    else {
        $ret['IsSuccess'] = true;
        $ret['occupation'] = $occupation;
    }
    return $ret;
}

function updatePostalcode($conn, $user_id, $postalcode) {
    $ret = array();
    $sql = "update `user` set `postalcode`='" . mysqli_real_escape_string($conn, $postalcode) . "', `updated`=now() where `id`='" . $user_id . "';";
    if (!mysqli_query($conn, $sql)) {
        fail($conn, $ret);
    }
    else {
        $ret['IsSuccess'] = true;
        $ret['postalcode'] = $postalcode;
    }
    return $ret;
}

function updateSex($conn, $user_id, $sex) {
    $ret = array();
    $sql = "update `user` set `sex`='" . intval($sex) . "', `updated`=now() where `id`='" . $user_id . "';";
    if (!mysqli_query($conn, $sql)) {
        fail($conn, $ret);
    }
    else {
        $ret['IsSuccess'] = true;
        $ret['sex'] = $sex;
    }
    return $ret;
}

function getUser($conn, $user_id) {
    $ret = array();
    $sql = "select email, firstname, lastname, birthday, phonenumber, address, sex, status, uuid, postalcode, updated from `user` where `id`='" . $user_id . "';";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_array($result)){
        $ret['IsSuccess'] = true;
        $ret['email'] = $row['email'] == null ? '' : $row['email'];
        $ret['firstname'] = $row['firstname'] == null ? '' : $row['firstname'];
        $ret['lastname'] = $row['lastname'] == null ? '' : $row['lastname'];
        $ret['birthday'] = $row['birthday'] == null ? '' :  date("Y-m-d", strtotime($row['birthday']));
        $ret['phonenumber'] = $row['phonenumber'] == null ? '' : $row['phonenumber'];
        $ret['image'] = $row['uuid'] == null ? '' : AppConfig::IMAGE_URL_V1 . $row['uuid'];
        $ret['address'] = $row['address'] == null ? '' : $row['address'];
        $ret['postalcode'] = $row['postalcode'] == null ? '' : $row['postalcode'];
        $ret['sex'] = $row['sex'] == null ? '' : $row['sex'];
        $ret['status'] = $row['status'] == null ? '' : $row['status'];
        $ret['updated'] = $row['updated'] == null ? '' : date("Y-m-d H:i:s", strtotime($row['updated']));
    }
    return $ret;
}

?>