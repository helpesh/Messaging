<?php
//Connection to Database
global $PasswordFeedback;
require_once "config.php";
require_once "Encryption_Setting.php";
session_start();
$message='';
$requirements='';
if(isset($_POST['submit']))
{
	
	$FirstName=strip_tags(trim(stripslashes($_POST['FirstName'])));
	$FirstName=mysqli_real_escape_string($con,$FirstName);
	$FirstName=openssl_encrypt($FirstName,$ciphering,$encryption_key,$options,$encryption_iv);
	
	$MiddleName=strip_tags(trim(stripslashes($_POST['MiddleName'])));
	$MiddleName=mysqli_real_escape_string($con,$MiddleName);
	$MiddleName=openssl_encrypt($MiddleName,$ciphering,$encryption_key,$options,$encryption_iv);
	
	$LastName=strip_tags(trim(stripslashes($_POST['LastName'])));
	$LastName=mysqli_real_escape_string($con,$LastName);
	$LastName=openssl_encrypt($LastName,$ciphering,$encryption_key,$options,$encryption_iv);
	
	$email=strip_tags(trim(stripslashes($_POST['Email'])));
	$email=mysqli_real_escape_string($con,$email);
	$email=openssl_encrypt($email,$ciphering,$encryption_key,$options,$encryption_iv);

	$Mobile=strip_tags(trim(stripslashes($_POST['Mobile'])));
	$Mobile=mysqli_real_escape_string($con,$Mobile);
	$Mobile=openssl_encrypt($Mobile,$ciphering,$encryption_key,$options,$encryption_iv);
	
	$username=strip_tags(trim(stripslashes($_POST['username'])));
	$username=mysqli_real_escape_string($con,$username);
	$username=openssl_encrypt($username,$ciphering,$encryption_key,$options,$encryption_iv);
	
	$password=strip_tags(trim(stripslashes($_POST['password'])));
	$password=mysqli_real_escape_string($con,$password);
	$RetypePassword=strip_tags(trim(stripslashes($_POST['RetypePassword'])));
	$RetypePassword=mysqli_real_escape_string($con,$RetypePassword);
	
	$userID=uniqid(mt_rand());
	//check if the user is registered or not
	$query="SELECT * FROM users WHERE Email='$email' OR UserName='$username'";
	$result=mysqli_query($con,$query);
	if(mysqli_num_rows($result)>0)
	{
		$message='<label class="text-danger">A user with the same email or Username Already Exist</label><br/><br/>';
	}
	else
	{
		if($password==$RetypePassword)
		{
			//check if the password is strong or not
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
			}
			else 
			{
				        $User_Email=$_POST['Email'];
						$User_FirstName=$_POST['FirstName'];
						$User_MiddleName=$_POST['MiddleName'];
						$db_LastName=$_POST['LastName'];	
						$VerificationLink='http://localhost/CapastoneProject/VerifyUserAccount.php?User='.$User_Email;
						$subject="Please Verify your account";
						$mailContent='Dear '.$User_FirstName.' '.$User_MiddleName.',
						<br/>We hope you are alright, please sir you need to verify your account in order to use our system.
						If this was a mistake, just ignore this email and nothing will happen.
						<br/> to verify your account, please visit the following link:<br/>
						<a href="'.$VerificationLink.'">'.$VerificationLink.'</a>
						<br/><br/>Best regards,
						<br/>messaging system.';
						//set content-type header for sending HTML email
						$headers="MINE-Version: 1.0". "\r\n";
						$headers.="Content-type:text/html;charset=UTF-8" . "\r\n";
						//additional headers
						$headers.='From: MessagingSystem<MessagingSystem.com@gmail.com>' ."\r\n";
						$send_mail=mail($User_Email,$subject,$mailContent,$headers);
						if($send_mail==true)
						{
							$password=openssl_encrypt($password,$ciphering,$encryption_key,$options,$encryption_iv);
							$Sql="INSERT INTO `users`(`userID`,`UserName`, `pass`, `First_Name`, `Middle_Name`, `Last_Name`, `Mobile`, `Email`) VALUES ('$userID','$username','$password','$FirstName','$MiddleName','$LastName','$Mobile','$email')";
							mysqli_query($con,$Sql);
							if($query)
							{
								$message="<label class='verify'>please verify your account, there is a link to your email,<br/>
								you need to use it to activate your account.</label>";
								
							}			
						}
						else
						{
							$feedback="<label>Error has occured</label>";
						}
						
			}			
		}
		else
		{
			$PasswordFeedback="Mismatch passwords";
		}
	}
	mysqli_close($con);
}
?>
<html lang="eng">
	<head>
		<title>Messaging system</title>
		<meta charset="utf-8"/>
		<meta name="viewort" content="width=device-width, intial-scale=1.0"/>
		<meta name="keywords" content="messaging system"/>
		<meta name="author" content="Mahmoud Magdy Abdel Whab"/>
		<link rel="stylesheet" type="text/css" href="Register.css"/>
	</head>
<body>
    
	<div class="register">
	    <img src="register.png" class="RegisterIcon"/>
		<form method="post"  action="?"> 
		    <fieldset>
				<legend>Creat a new account</legend>
				<?php echo $message; ?>
				<label for="FirstName">First Name:</label>
				<input type="text" name="FirstName" placeholder="Enter your First Name" required/>
				<label for="MiddleName">Middle Name:</label>
				<input type="text" name="MiddleName" placeholder="Enter your Middle Name" required/>
				<label for="LastName">Last Name:</label>
				<input type="text" name="LastName" placeholder="Enter your Last Name" required/>
				<label for="Email">Email:</label>
				<input type="text" name="Email" placeholder="Enter your Email" required/>
				<label for="Mobile">Mobile Number:</label>
				<input type="text" name="Mobile" placeholder="Enter your Mobile Number" required/>
				<label for="username">Username:</label>
				<input type="text" name="username" placeholder="Enter your username" required/>
				<label for="password">Password:</label>
				<input type="password" name="password" placeholder="Enter your password" required/>
				<label for="RetypePassword">Re Enter your password</label>
				<input type="password" name="RetypePassword" placeholder="Verify your password" required/>
				<p><?php echo $PasswordFeedback;?></p>
				<?php echo $requirements;?>
				<input type="submit" name="submit" value="Sign up"/>
			</fieldset>
		</form>
	</div>
</body>

</html>