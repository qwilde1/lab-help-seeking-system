<html>
	<HEAD>
		<TITLE>TA Help Seek - Admin</TITLE>
	</HEAD>
	<BODY>
		<h1 align=center>Admin Page</h1>
		<form action="admin.php" method="post">
		<table align=center>
			<tr><td><input id="newtaid" name="newtaid" type="text" placeholder="New TA Username"/></td></tr>
			<tr><td><input id="newtapw" name="newtapw" type="password" placeholder="New TA Password"/></td></tr>
			<tr><td><input id="pwconf" name="pwconf" type="password" placeholder="Confirm Password"/></td></tr>
			<tr><td align=center><input id="submit" name="submit" type="submit" value="Submit"/></td></tr>
		</table>		
		</form>
		<?php
			//DB info
			$servername = "dbserver.engr.scu.edu";
			$username = "qwilde";
			$password = "00001094499";
			$dbname = "sdb_qwilde";
			//create connection
			$conn = new mysqli($servername, $username, $password, $dbname);

			// Check connection
			if ($conn->connect_error) {
			    die("Connection failed: " . $conn->connect_error);
			} 
			echo "</br>Connected to Database Server";
	
			function passwordsMatch()
			{
				return ($_POST["newtapw"] == $_POST["pwconf"]);
			}
			
			function createTA(){
				if(strtolower($_POST["newtaid"]) == "admin")
				{
					echo "Username cannot be \"admin\"";
				}
				else
				{
					global $conn;
					//insert new TA
					$stmt = $conn->prepare("INSERT INTO user (username, password) VALUES(?, ?)");
					$stmt->bind_param("ss", $newta, $newpw);
					$newta = $_POST["newtaid"];
					$newpw = $_POST["newtapw"];
					$stmt->execute();

					//insert default session for this ta
					$stmt = $conn->prepare("SELECT userId FROM user WHERE username=\"$newta\"");
					$stmt->execute();
					$result = $stmt->get_result();
					$result = $result->fetch_assoc();
					$userId = $result["userId"];
					$stmt = $conn->prepare("INSERT INTO labsessions(accessCode, dateCreated, sessionName, status, userId) VALUES(\"default\", CURDATE(), \"Default Session\", 1, ?)");
					$stmt->bind_param("s", $userId);
					$stmt->execute();
				}
			}

			if(isset($_POST["newtaid"]) && isset($_POST["newtapw"]) && isset($_POST["pwconf"]))
			{
				if(passwordsMatch())
					createTA();
				else
					echo "</br><b>Passwords do not match</b>";
			}

		?>
	</BODY>
</html>
