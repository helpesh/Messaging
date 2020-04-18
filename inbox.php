<?php
session_start();
if(!isset($_SESSION['userID']))
{
	header("Location: index.php");
	exit();
}
//Ignore warnings
error_reporting(E_ALL & ~E_NOTICE & ~8192);
//Connection to Database
require_once "config.php";
//Days, Hours, Minutes
require_once "time.php";
require_once 'Encryption_Setting.php';

?>

<!DOCTYPE html>
<html lang="eng">
<head>
  <title>Messaging system</title>
  <meta charset="utf-8"/>
  <meta name="viewort" content="width=device-width, intial-scale=1.0"/>
  <meta name="keywords" content="messaging system"/>
  <meta name="author" content="Mahmoud Magdy Abdel Whab"/>
  <link rel="stylesheet" type="text/css" href="inbox.css"/>
</head>
<body>

	<div class="msg">
	  <a href="Send messages.php">Send message</a>
	  <a href="index.php">log out</a>
	</div>
    <table>
		<tr>
			<th>From</th>
			<th>Email</th>
			<th>Subject</th>
			<th>Date</th>
			<th>Status</th>
		</tr>
		<?php
		if(isset($_SESSION['userID']) && !empty($_SESSION['userID']) )
		{
			$userID=$_SESSION['userID'];
			$limit=5;
			$p=$_GET['p'];
			$get_total=mysqli_num_rows(mysqli_query($con,"SELECT * FROM messages WHERE userID='".$userID."'"));	
			$total=ceil($get_total/$limit);
			if(!isset($p) || $p<=0)
			{
				$offset=0;
			}
			else
			{
				$offset=ceil($p-1)*$limit;
			}
			$sql_query="SELECT * FROM messages WHERE userID='".$userID."' LIMIT $offset,$limit ";
			$inbox=mysqli_query($con,$sql_query);
			if(mysqli_num_rows($inbox)>=1)
			{
				//Use Openssl_encrypt() function 
				while($row=mysqli_fetch_assoc($inbox))
				{
					$MessageID=$row['MessageID'];
					//decrypt later.............
					$Sent_From=$row['Sent_From'];
					$Sent_From=openssl_decrypt($Sent_From,$ciphering,$encryption_key,$options,$encryption_iv);		
					$email=openssl_decrypt($row['email'],$ciphering,$encryption_key,$options,$encryption_iv);		
					$subject=$row['subject'];
					$subject=openssl_decrypt($subject,$ciphering,$encryption_key,$options,$encryption_iv);
					$Date=$row['date'];
					$Date=openssl_decrypt($Date,$ciphering,$encryption_key,$options,$encryption_iv);
					$time=time_passed($row['time']);
					$open=$row['open'];
		
					if($row['open']==0)
					{
						$open="Not Opened";
					}
					else
					{
						$open="Opened";
					}
		
					echo '<tr>';
						echo '<td><a href=DisplayMessage.php?msg='.$MessageID.'>'.$Sent_From.'</a></td>';
						echo '<td><a href=DisplayMessage.php?msg='.$MessageID.'>'.$email.'</a></td>';
						echo '<td><a href=DisplayMessage.php?msg='.$MessageID.'>'.$subject.'</a></td>';
						echo '<td><a href=DisplayMessage.php?msg='.$MessageID.'>'.$Date.' - '.$time.'</a></td>';
						echo '<td><a href=DisplayMessage.php?msg='.$MessageID.'>'.$open.'</a></td>';
					echo  '</tr>';
	
				}
			}
			else
			{
		
			echo '<tr>';
				echo '<td colspan="5">No messages have been sent to you until now</td>';
			echo  '</tr>';
			}
	}
	?>
	</table>
	<?php
	if($get_total >= $limit)
	{
	  echo '<div id="pages">';
	  for($i=1;$i<=$total;$i++)
	  {   
		echo ($i == $p)?'<a class="active">'.$i.'</a>':'<a href="?p='.$i.'">'.$i.'</a>';
	  }
	  echo '</div>';
	}
	?>
	
</body>
</html>