<?php
$servername = "dbserver.engr.scu.edu";
$username = "qwilde";
$password = "00001094499";
$dbname = "sdb_qwilde";

//create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully";
?>
<html>
	<HEAD>
		<link rel="stylesheet" type="text/css" href="./styles/passwordStyle.css">
	</HEAD>
	<body>
		<div class="tab-content">
			<H1 ALIGN=CENTER>Change Password</H1>
			<ul class="tab-group">
				<li class="tab active"><a align=right href="changePassword.php">Change Password</a></li>
				<li class="tab"><a align=right href="talogin.php">TA login</a></li>
			</ul>

			<form method="post" action="changePassword.php" class="top-row">
				<table ALIGN=CENTER>
					<tr class="field-wrap">
					  <td>
					    <input type="text" id="username" name="username" placeholder="Username"/><span class="req"></span>
					  </td>
					</tr>
					<tr class="field-wrap">
					  <td>
					    <input type="password" id="opw" name="opw" placeholder="Old Password"/><span class="req"></span>
					  </td>
					</tr>
					<tr class="field-wrap">
					  <td>
					    <input type="password" id="npw" name="npw" placeholder="New Password"/><span class="req"></span>
					  </td>
					</tr>
					<tr class="field-wrap">
					  <td>
					    <input type="password" id="cnpw" name="cnpw" placeholder="Confirm New Password"/><span class="req"></span>
					  </td>
					</tr>
					<tr>
					  <td align="center">
					    <input type="submit" value="Change Password" class="button1 button1-block"/>
					  </td>
					</tr>
				</table>	
			</form>
		</div>
	<?php

		if( isset($_POST["username"]) && $_POST["username"] != "" && $_POST["opw"] != "" && $_POST["npw"] != "" && $_POST["cnpw"] != "")
			changePassword();
		function changePassword()
		{
			global $conn;
			$stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
			$stmt->bind_param("s", $_POST["username"]);
			$stmt->execute();
			$result = $stmt->get_result();
			$result = $result->fetch_assoc();
			if(!newPasswordsMatch())
			{
				echo "</br>Passwords don't match</br>";
				return;
			}
			$hashedNPW = password_hash($_POST["npw"], PASSWORD_DEFAULT);
			$changepwstmt = $conn->prepare("UPDATE user SET password = ? WHERE username = ?");
			$changepwstmt->bind_param("ss", $hashedNPW, $_POST["username"]);
			if(password_verify($_POST["opw"], $result["password"]))
			{
				if($changepwstmt->execute())
					echo "</br>Password successfully changed</br>";
			}
			else
				echo "</br>Old password is incorrect</br>";
		}

		function newPasswordsMatch()
		{
			return ($_POST["npw"] == $_POST["cnpw"]);
		}
	?>
	</body>

</html>
