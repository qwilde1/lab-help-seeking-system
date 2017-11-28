<?php
	session_start();
?>
<HTML>
	<HEAD>
		<link rel="stylesheet" type="text/css" href="./styles/loginStyle.css">
	</HEAD>
	<BODY>
		<div class="tab-content">
			<H1 ALIGN=CENTER>TA Login Page</H1>
			<ul class="tab-group">
				<li class="tab active"><a align=right href="talogin.php">TA login</a></li>
				<li class="tab"><a align=right href="studlogin.php">STUDENT login</a></li>
			</ul>
			<form action="talogin.php" method="post" class="top-row">
			<table ALIGN=CENTER>
				<tr class="field-wrap">
				  <td>
				    <input class="in" id="taid" name="taid" type="text" placeholder="TA User ID" autofocus="autofocus"/><span class="req"></span>
				  </td>
				</tr>
				<tr class="field-wrap">
				  <td>
				    <input class="in" id="tapw" name="tapw" type="password" placeholder="Password"/><span class="req"></span>
				  </td>
				</tr>
				<tr>
				  <td align="center">
				    <input id="submit" type="submit" value="Submit" class="button1 button1-block"/>
				  </td>
				</tr>
			</table>				
			</form>
			<form action="changePassword.php" method="post">
				<input id="submit" type="submit" value="Change Password" class="button2 button2-block"/>
			</form>
		</div>
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
			    die("Connection to server failed: " . $conn->connect_error);
			} 
			//echo "Connected to Database Server";


			//Check login info, if legitimate, go on to TA dashboard
			function checkLogin()
			{
				global $conn;
				if(isset($_POST["taid"]))
				{
					if($_POST["taid"] != "admin")
					{
						$_SESSION["taid"] = $_POST["taid"];
						//Check Database for TAID
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
								if(password_verify($_POST["tapw"], $result["password"])) {
									$_SESSION["userId"]= $result["userId"];
									$_SESSION["username"]= $result["username"];
									header("location: taDashboard.php");
									echo "successful login";
								}	
								else {
									echo "<b></br>Incorrect username or password</b>";
								}
							}
							else
							{
								echo "</br>Please enter a password</br>";
							}
						}
					}
					else
					{
						//TODO: Verify admin password
						$stmt = "SELECT * FROM user WHERE username =\"admin\"";
						$result = $conn->query($stmt);
						$result = $result->fetch_assoc();
						if(password_verify($_POST["tapw"], $result["password"]))
						{
							$_SESSION["userId"]= $result["userId"];
							$_SESSION["username"]= $result["username"];
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
					//echo "</br>Please enter a username</br>";
				}

			}

			checkLogin();
			$conn->close();
		?>

	</BODY>
</HTML>
