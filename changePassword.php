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
		<TITLE>TA Help Seek - Change Password</TITLE>
	</HEAD>
	<body>
	</br>
	<a href="talogin.php">Return to Login Page</a>
	<h1 ALIGN=CENTER>Change Password</h1>
	<form method="post" action="changePassword.php">
		<input type="text" id="username" name="username" placeholder="Username"/>
		</br>
		<input type="password" id="opw" name="opw" placeholder="Old Password"/>
		</br>
		<input type="password" id="npw" name="npw" placeholder="New Password"/>
		</br>
		<input type="password" id="cnpw" name="cnpw" placeholder="Confirm New Password"/>
		</br>
		<input type="submit" value="Change Password"/>
		</br>

		<?php
	
			if($_POST["username"] != "" && $_POST["opw"] != "" && $_POST["npw"] != "" && $_POST["cnpw"] != "")
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
	</form>
	
	</body>

</html>
