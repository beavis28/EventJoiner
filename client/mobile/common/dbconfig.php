<?php
class DBConnection{
    function getConnection(){
        //change to your database server/user name/password
        $conn = mysqli_connect("localhost","eventjoiner","Eventjoiner123", 'jqcalendar') or
        die("Could not connect: " . mysqli_error());
        return $conn;
    }
}
?>