<?php
session_start();
if(!isset($_SESSION['userID']))
{
	header("Location: index.php");
	exit();
}
require_once 'config.php';
require_once 'time.php';
?>
<!Doctype html>
<html lang="eng">
    <?php
	
	if(isset($_GET['remove']) && !empty($_GET['remove']))
	{
		$MessageID=strip_tags(trim(stripslashes($_GET['remove'])));
		$MessageID=mysqli_real_escape_string($con,$MessageID);
		$sql="DELETE FROM `messages` WHERE `MessageID`='".$MessageID."'";
		mysqli_query($con,$sql);
		if(mysqli_affected_rows($con)>0)
		{
		  header("Location: inbox.php");
		  exit();
		}
		else
		{
			die("Please Refresh the page");
		}
		exit();
	}
	
	?>
	<head>
		<title>Messaging system</title>
		<meta charset="utf-8"/>
		<meta name="viewort" content="width=device-width, intial-scale=1.0"/>
		<meta name="keywords" content="messaging system"/>
		<meta name="author" content="Mahmoud Magdy Abdel Whab"/>
		<link rel="stylesheet" type="text/css" href="inbox.css"/>
    </head>
    <body>
    <?php
		require_once 'Encryption_Setting.php';
        if(isset($_GET['msg']) && !empty($_GET['msg']))
		{
			$MessageID=strip_tags(stripslashes(trim($_GET['msg'])));
			$MessageID=mysqli_real_escape_string($con,$MessageID);
			$update=mysqli_query($con,"UPDATE messages SET open='1' WHERE MessageID='".$MessageID."'");
			$sql="SELECT * FROM messages WHERE MessageID='".$MessageID."'";
			$msg=mysqli_query($con,$sql);
			$row=mysqli_fetch_array($msg);
			$from=openssl_decrypt($row['Sent_From'],$ciphering,$encryption_key,$options,$encryption_iv);
			$email=openssl_decrypt($row['email'],$ciphering,$encryption_key,$options,$encryption_iv);
			$message=openssl_decrypt($row['message'],$ciphering,$encryption_key,$options,$encryption_iv);
			$subject=openssl_decrypt($row['subject'],$ciphering,$encryption_key,$options,$encryption_iv);
			$Date=openssl_decrypt($row['date'],$ciphering,$encryption_key,$options,$encryption_iv);
			$time=time_passed($row['time']);
			$open=$row['open'];
	?>	
	
		<div class="navigation"> 	
			<a href="index.php">Log out</a>
			<a href="inbox.php">Back to inbox</a>
		</div>
		<div class="DisplayMessage"><!--when the system displays the message -->      
			<table>
				<tr>
					<th>From </th>
					<th>Email</th>
					<th>Subject</th>
					<th>Date </th>
					<th>Time </th>
				</tr>
				<tr>
					<td><?php echo $from;?></td>
					<td><?php echo $email;?></td>
					<td><?php echo $subject;?></td>
					<td><?php echo $Date;?></td>
					<td><?php echo $time;?></td>
				</tr>
			</table>
			<pre><?php echo $message;?></pre>		
			<a class="remove" href="?remove=<?php echo $MessageID;?>">Delete This Message</a>
	    </div>
		<?php
		}
		?>
</body>
</html>