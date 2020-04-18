<?php
include('config.php');
require_once 'Encryption_Setting.php';
if(isset($_GET['User']))
{
	$UserEmail=strip_tags(trim(stripslashes($_GET['User'])));
	$UserEmail=mysqli_real_escape_string($con,$UserEmail);
	$UserEmail=openssl_encrypt($UserEmail,$ciphering,$encryption_key,$options,$encryption_iv);
	$Sql="UPDATE `users` SET `UserAccount_status`='1' WHERE Email='$UserEmail'";
	mysqli_query($con,$Sql);
	if(mysqli_affected_rows($con)>0)
	{
	   header("Location: index.php");
	   exit();
	} 
	
}


?>