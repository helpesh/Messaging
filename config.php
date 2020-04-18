<?php
$host="localhost";
$user="root";
$password="";
$db="message_database";
$con=mysqli_connect($host,$user,$password,$db);
if(!$con){
  die("Can not connect: ".mysqli_connect_error());
}

?>