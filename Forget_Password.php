<?php
	session_start();
	include('config.php');
	require_once 'Encryption_Setting.php';
	$feedback='';
	if(isset($_POST['submit']))
	{
			$forgot_password_identity=uniqid(mt_rand());
			$username=$_POST['username'];
			$username=strip_tags(trim(stripcslashes($username)));
			$username=mysqli_real_escape_string($con,$username);
			$email=$_POST['email'];
			$email=strip_tags(trim(stripcslashes($email)));
			$email=mysqli_real_escape_string($con,$email);
			$email=openssl_encrypt($email,$ciphering,$encryption_key,$options,$encryption_iv);
			$username=openssl_encrypt($username,$ciphering,$encryption_key,$options,$encryption_iv);
			$query="SELECT * FROM `users` WHERE `Email`='".$email."' AND `UserName`='".$username."'";		
			$ResultSET=mysqli_query($con,$query);
			if(mysqli_num_rows($ResultSET)>0)
			{
				while($row=mysqli_fetch_assoc($ResultSET))
				{
				
					if(($email==$row['Email']) && ($username==$row['UserName']))
					{
						$db_email=$row['Email'];
						$db_FirstName=$row['First_Name'];
						$db_MiddleName=$row['Middle_Name'];	
						$resetPassLink='http://localhost/CapastoneProject/resetPassword.php?fp_code='.$forgot_password_identity;
						$to=openssl_decrypt($db_email,$ciphering,$encryption_key,$options,$encryption_iv);
						$db_FirstName=openssl_decrypt($db_FirstName,$ciphering,$encryption_key,$options,$encryption_iv);
						$db_MiddleName=openssl_decrypt($db_MiddleName,$ciphering,$encryption_key,$options,$encryption_iv);
						$subject="Reset password Request";
						$mailContent='Dear '.$db_FirstName.',
						<br/>We hope you are alright, recently a request was submitted to reset a password for your account.
						If this was a mistake, just ignore this email and nothing will happen.
						<br/> to reset your password, please visit the following link:<br/>
						<a href="'.$resetPassLink.'">'.$resetPassLink.'</a>
						<br/><br/>Best regards,
						<br/>messaging system.';
						//set content-type header for sending HTML email
						$headers="MINE-Version: 1.0". "\r\n";
						$headers.="Content-type:text/html;charset=UTF-8" . "\r\n";
						//additional headers
						$headers.='From: MessagingSystem<MessagingSystem.com@gmail.com>' ."\r\n";
						$send_mail=mail($to,$subject,$mailContent,$headers);
						if($send_mail==true)
						{
							$feedback="<label>please check your email, we have sent
							a password reset link to your registered email</label><br/>";			
							$query="UPDATE users SET forgot_pass_identity='".$forgot_password_identity."' WHERE Email='".$email."'";
							mysqli_query($con,$query);
						}
						else
						{
							$feedback="<label>Error has occured</label>";
						}
					}
				
				}
			}else
			{
				$feedback="<label>username or Email does not associate with any account</label>";
			}
			mysqli_free_result($ResultSET);
		mysqli_close($con);
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
	<link rel="stylesheet" type="text/css" href="Forget_Password.css"/>
</head>
<body>
    <h2>Enter the required</h2>
	<form action="Forget_Password.php" method="POST" >
		<fieldset>
			<legend>Reset your password</legend>
			<?php echo $feedback;?>
			<label for="username">Enter your username</label>
			<input type="text" size="100" name="username" placeholder="Enter your username" required/><br/>
			<label for="email">Enter your Email</label>
			<input type="text" size="32" name="email" placeholder="Enter your Email" required/><br/>
			<input type="submit" name="submit" value="submit" />
		</fieldset>	 
	</form>
</body>
</html>