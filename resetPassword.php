<?php
	session_start();
	require_once 'config.php';
	require_once 'Encryption_Setting.php';
	$feedback='';
	$PasswordFeedback=''; 
	$requirements='';
	if(isset($_GET['fp_code']) && !empty($_GET['fp_code']))
	{
		$forgot_pass_identity=$_GET['fp_code'];
		$Sql="SELECT * FROM users WHERE forgot_pass_identity='$forgot_pass_identity'";
		$result=mysqli_query($con,$Sql);
		if($row=mysqli_fetch_assoc($result))
		{
			$_SESSION['userID']=$row['userID'];
		}
	}
	
?>
<!DOCTYPE html>
<html lang="eng">
	<head>
		<title>Messaging system</title>
		<meta charset="utf-8"/>
		<meta name="viewort" content="width=device-width, intial-scale=1.0"/>
		<meta name="keywords" content="messaging system"/>
		<meta name="author" content="Mahmoud Magdy Abdel Whab"/>
		<link rel="stylesheet" type="text/css" href="Forget_Password.css">
	</head>
<body>
    
	
    <?php
	if(isset($_POST['ForgetSubmit']))
	{
	    $password=$_POST['NewPassword'];
	    $password=strip_tags(trim(stripcslashes($password)));
	    $password=mysqli_real_escape_string($con,$password);
	    $RetypePassword=$_POST['RetypePassword'];
	    $RetypePassword=strip_tags(trim(stripcslashes($RetypePassword)));
        $RetypePassword=mysqli_real_escape_string($con,$RetypePassword);
		if($password==$RetypePassword)
		{	
	        $uppercase=preg_match('@[A-Z]@',$password);
			$lowercase=preg_match('@[a-z]@',$password);
			$number=preg_match('@[0-9]@',$password);
			$specialChars=preg_match('@[^\w]@',$password);
			if(!$uppercase||!$lowercase||!$number||!$specialChars||(strlen($password)<8))
			{
				$PasswordFeedback='<label class="text-danger">passwords must meet certain requirements</label>';
			    $requirements='<label class="password-feedback">password must have at least one uppercase letter
				<br/>at least one number<br/>at least length 8 characters<br/>
				at least one lowercase letter<br/>
				at least one symbol</label><br/>'; 
			}else
			{
				$password=openssl_encrypt($password,$ciphering,$encryption_key,$options,$encryption_iv);
				$userID=$_SESSION['userID'];
				$Query="SELECT forgot_pass_identity FROM users WHERE userID='$userID'";
				$result=mysqli_query($con,$Query);
				$row=mysqli_fetch_assoc($result);
				$forgot_pass_identity=$row['forgot_pass_identity'];
				$Sql="UPDATE `users` SET `pass`='$password' WHERE `forgot_pass_identity`='$forgot_pass_identity'";
				mysqli_query($con,$Sql);
				if(mysqli_affected_rows($con)>0)
				{
					$PasswordFeedback="<label class='ChangePassword'>you have successfully changed the password</label><br/>";
				}
				else
				{
					$PasswordFeedback='<label class="text-danger">You have not changed the password</label>';
				}	
			}
				
				
		}
		else
		{			
		    $feedback='<label class="text-danger">Missmatch passwords</label><br/>';	
		}
	}
	
	?>
	<h2>Enter the required</h2>
	<form action="resetPassword.php" method="POST" >
		<fieldset>
			<legend>Reset your Account Password</legend>
			<?php echo $PasswordFeedback;?>
			<?php echo $feedback;?>
			<label for="password">Enter your new password</label><br/>
			<input type="password" name="NewPassword" size="32" placeholder="Enter your new password" required/><br/>
			<label for="password">Confirm password</label><br/>
			<input type="password" name="RetypePassword" size="32" placeholder="Confirm your password" required/><br/>
			<?php echo $requirements;?>
			<input type="submit" name="ForgetSubmit" value="Reset password"/>
		</fieldset>	 
	</form>
	
</body>
</html>