
<HTML>
	<HEAD>
		<TITLE>TA Help Seek - TA Login Page</TITLE>
	</HEAD>
	<BODY>
		<a align=right href="studlogin.php">Go to STUDENT login page</a>
		<H1 ALIGN=CENTER>TA Login Page</H1>

		<form action="talogin.php" method="post">
		<table ALIGN=CENTER>
			<tr>
			  <td>
			    <input id="taid" name="taid" type="text" placeholder="TA User ID"/>
			  </td>
			</tr>
			<tr>
			  <td>
			    <input id="tapw" name="tapw" type="password" placeholder="Password"/>
			  </td>
			</tr>
			<tr>
			  <td align="center">
			    <input id="submit" type="submit" value="Submit"/>
			  </td>
			</tr>
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
			echo "Connected to Database Server";


			//Check login info, if legitimate, go on to TA dashboard
			function checkLogin()
			{
				global $conn;
				if(isset($_POST["taid"]))
				{
					if($_POST["taid"] != "admin")
					{
						//TODO: Check Database for TAID
						//ALWAYS USE PREPARED STATEMENTS WHEN SENDING USER
						//	INPUT TO A MYSQL PROMPT
						$stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
						$stmt->bind_param("s", $_POST["taid"]);
						$stmt->execute();
						$result = $stmt->get_result();
						//$result = $conn->query($stmt);
						$result = $result->fetch_assoc();
						if(!$result){
							echo "user does not exist";
						}
						else {
							if(isset($_POST["tapw"]))
							{
								if($result["password"] == $_POST["tapw"]) {
									header("location: taDashboard.php");
									echo "successful login";
								}	
								else {
									echo "<b></br>Incorrect username or password</b>";
								}
							}
							else
							{
								echo "</br>Please enter a password";
							}
						}
					}
					else
					{
						//TODO: Verify admin password
						$stmt = "SELECT * FROM user WHERE username =\"admin\"";
						$result = $conn->query($stmt);
						$result = $result->fetch_assoc();
						if($result["password"] == $_POST["tapw"])
						{
							$_POST["userId"]= $result["userId"];
							$_POST["username"]= $result["username"];
							header("location: admin.php");
						}
						else
						{
							echo "<b></br>Incorrect username or password</b>";
						}

					}
				}
				else
				{
					echo "</br>Please enter a username</br>";
				}

			}

			checkLogin();
			$conn->close();
		?>

	</BODY>
</HTML>
