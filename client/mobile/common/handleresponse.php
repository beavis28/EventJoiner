<?php
/*
 * @author DJ110
 * @copyright 2015 Eventjoiner
 *
 *  This is utility to handle response
 */

class HandleResponse {

    public static function badRequestReturn($message) {
        $ret = array();
        http_response_code(400);
        $ret['IsSuccess'] = false;
        $ret['Msg'] = $message;
        return $ret;
    }

    public static function badRequest($message) {
        $ret = array();
        http_response_code(400);
        $ret['IsSuccess'] = false;
        $ret['Msg'] = $message;
        print json_encode($ret);
    }
}
?>