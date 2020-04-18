<?php
    session_start();
	$message="Enter username and password";
    require_once 'config.php';
	require_once 'Encryption_Setting.php';
	if(isset($_POST['submit']))
	{
		
		$username=strip_tags(trim(stripslashes($_POST['username'])));
		$username=mysqli_real_escape_string($con,$username);
		$password=strip_tags(trim(stripslashes($_POST['password'])));
		$password=mysqli_real_escape_string($con,$password);
		$username=openssl_encrypt($username,$ciphering,$encryption_key,$options,$encryption_iv);
	    $password=openssl_encrypt($password,$ciphering,$encryption_key,$options,$encryption_iv);
		$sql="SELECT * FROM users WHERE UserName='".$username."' AND pass='".$password."' LIMIT 1";
		$result=mysqli_query($con,$sql);
		if(mysqli_num_rows($result)==1)
		{		        
			$row=mysqli_fetch_array($result);
			if(1==$row['UserAccount_status'])
			{
				$_SESSION['userID']=$row['userID'];
				header("Location: inbox.php");
				exit();
			}
			else
			{
			   $message="<label class='text-danger'>Please verify your account</label>";	
			}
		}
		else
		{
		  $message="<label class='text-danger'>Incorrect username or password</label>";
		}
		mysqli_close($con);
	}
    
?>
<!DOCTYPE html>
<html>

<head>

 <title>Messaging system</title>
 <meta charset="utf-8"/>
 <meta name="viewort" content="width=device-width, intial-scale=1.0"/>
 <meta name="keywords" content="messaging system"/>
 <meta name="author" content="Mahmoud Magdy Abdel Whab"/>
 <link rel="stylesheet" type="text/css" href="style.css"/>
  
</head>

<body>
    <img src="NTL.png" class="NTL"/>
	<div class="login-box">
	  <img src="avatar.jpg" class="avatar"/>
      <h1>Login Here</h1>
	    <form action="index.php" method="POST" >
		  <p><?php echo $message;?></p>
		  <label for="username">Username:</label><font color="red">*</font>
		  <input type="text" name="username" placeholder="Enter Username" required/>
		  <label for="password">Password:</label><font color="red">*</font>
		  <input type="password" name="password" placeholder="Enter password" required/>
		  <input type="submit" name="submit" value="Log in"/>
		  <a href="Forget_Password.php">Forget password</a>
		  <a href="Registration form.php">Sign up</a>
	    </form>
	</div>
</body>
</html>
<?php session_destroy();?>