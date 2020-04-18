<?php
$message='';	
include('Encryption_Setting.php');
include('config.php');
session_start();
if(!isset($_SESSION['userID']))
{
	header("Location: index.php");
	exit();
}
 
$userID=$_SESSION['userID'];
$query="SELECT `First_Name`, `Middle_Name`, `Last_Name`, `Email` FROM `users` WHERE userID='".$userID."'";
$result=mysqli_query($con,$query);
if(mysqli_affected_rows($con)>0)
{
	$row=mysqli_fetch_array($result);
	$SenderEmail=openssl_decrypt($row['Email'],$ciphering,$encryption_key,$options,$encryption_iv);
	$First_Name=openssl_decrypt($row['First_Name'],$ciphering,$encryption_key,$options,$encryption_iv);
	$Middle_Name=openssl_decrypt($row['Middle_Name'],$ciphering,$encryption_key,$options,$encryption_iv);
	$Full_Name=$First_Name." ".$Middle_Name;
}
if(isset($_POST['submit']))
{
	//Use Openssl_encrypt() function 
	$MessageID=rand(10000,1000000);
	$MessageID=hash("sha512",$MessageID,false);
    $RecievedEmail=strip_tags(trim(stripslashes($_POST['email'])));
	$Subject=strip_tags(trim(stripslashes($_POST['subject'])));
    $Subject=openssl_encrypt($Subject,$ciphering,$encryption_key,$options,$encryption_iv);
	$message=strip_tags(trim(stripslashes($_POST['message'])));
	$message=openssl_encrypt($message,$ciphering,$encryption_key,$options,$encryption_iv);
	$Sent_From=$Full_Name;
	$Sent_From=openssl_encrypt($Sent_From,$ciphering,$encryption_key,$options,$encryption_iv);
	$SenderEmail=openssl_encrypt($SenderEmail,$ciphering,$encryption_key,$options,$encryption_iv);
	$Date=date("d/m/Y");
	$Date=openssl_encrypt($Date,$ciphering,$encryption_key,$options,$encryption_iv);
	$time=time();
	$RecievedEmail=openssl_encrypt($RecievedEmail,$ciphering,$encryption_key,$options,$encryption_iv);
	$query="SELECT `userID` FROM `users` WHERE `Email`='$RecievedEmail'";
	$result=mysqli_query($con,$query);
	if(mysqli_num_rows($result)==1)
	{
		$row=mysqli_fetch_assoc($result);
		$Recieved_UserID=$row['userID'];	
		$sql="INSERT INTO `messages`(`MessageID`, `Sent_From`, `email`, `subject`, `message`, `date`, `time`,`userID`)
		VALUES ('$MessageID','$Sent_From','$SenderEmail','$Subject','$message','$Date','$time','$Recieved_UserID')";
		mysqli_query($con,$sql);
		$message='<label>Message has been sent</label>';
		
	}else
	{
		$message='<label>This Email does not exist</label>';
	}
	
}

?>
<!Doctype html>
<html lang="eng">
  <head>
    <title>Messaging system</title>
    <meta charset="utf-8"/>
    <meta name="viewort" content="width=device-width, intial-scale=1.0"/>
    <meta name="keywords" content="messaging system"/>
    <meta name="author" content="Mahmoud Magdy Abdel Whab"/>
    <link rel="stylesheet" type="text/css" href="Contact.css"/>
  </head>
<body>
<ul class="nav">
<li><a href="inbox.php">Inbox</a></li>
<li><a href="index.php">Log out</a></li>
</ul>
    <div class="contact-title">
      <h1>Say Hello</h1>
	  <h2>We are always ready to serve you</h2>
    </div>
	
	<div class="contact-form"> 
	    <form method="post" action="Send messages.php">
			<fieldset>
				<legend>Create your own message</legend>
				<?php echo $message;?>
				<input type="text" name="email" class="form-control" placeholder="Enter the Email" required /><br>
				<input type="text" name="subject" class="form-control" placeholder="Write Subject" required/><br>
				<textarea name="message" class="form-control" row="20" placeholder="Write your message" required/></textarea><br>
				<input type="submit" class="form-control submit" name="submit" value="Send message"/>
			</fieldset>
	    </form>
	</div>
</body>

</html>