<?php
class Validation{
    public static function isBlank($param){
        return is_null($param) || empty($param);
    }

    public static function isEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function isPhoneNumber($phone_number) {
        return preg_match("/^[0-9]+$/", $phone_number);
    }

    public static function isValidMinStr($param, $min) {
        return strlen($param) >= $min;
    }

    public static function isValidMaxStr($param, $max) {
        return strlen($param) < $max;
    }


    public static function isValidTime($param) {
        try {
            if(!ini_get('date.timezone')) {
                date_default_timezone_set('Asia/Singapore');
            }
            new DateTime($param);
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
        return true;
    }

    public static function includeBlank() {
        $args = func_get_args();
        foreach ($args as $v) {
            $res = self::isBlank($v);
            if ($res) {
                return true;
            }
        }
        return false;
    }

    public static function isValidPostalSG($postal) {      // 6-digit
        return preg_match('/^([0-9]{6})$/', $postal);
    }


    // Specific validation for Eventjoiner

    public static function isValidEmail($email) {
        return self::isEmail($email) && self::isValidMaxStr($email, 256);
    }

    public static function isValidPassword($password) {
        return self::isValidMinStr($password, 6);
    }

    public static function isValidPhonenumber($phone_number) {
        return self::isPhoneNumber($phone_number) && self::isValidMaxStr($phone_number, 40);
    }

    public static function isValidUserName($name) {
        return self::isValidMaxStr($name, 256);
    }

    public static function isMatchPassword($oldpass, $newpass) {
        return strcmp($oldpass, $newpass) == 0;
    }

    public static function isValidAddress($address) {
        return true;        // No limitation so far
    }

    public static function isValidOccupation($occupation) {
        return self::isValidMaxStr($occupation, 256);
    }

    public static function isValidPostalcode($postalcode) {
        return self::isValidPostalSG($postalcode);
    }

    public static function isValidSex($sex) {
        return strcmp($sex, '0') == 0 || strcmp($sex, '1') == 0 || strcmp($sex, '2') == 0;
    }
}
?>