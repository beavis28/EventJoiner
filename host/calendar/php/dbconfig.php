<?php
class DBConnection{
	function getConnection(){
	  //change to your database server/user name/password
		mysql_connect("localhost","eventjoiner","Eventjoiner123") or
         die("Could not connect: " . mysql_error());
    //change to your database name
		mysql_select_db("eventjoiner") or
		     die("Could not select database: " . mysql_error());
	}
}
?>
