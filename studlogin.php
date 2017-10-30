<?php
// Start the session
session_start();
?>
<HTML>
	<HEAD>
		<TITLE>TA Help Seek - Student Login Page</TITLE>
	</HEAD>
	<BODY>
		<a align=right href="talogin.php">Go to TA login page</a>
		<H1 ALIGN=CENTER>Student Login Page</H1>

		<form action="studlogin.php" method="post">
		<table ALIGN=CENTER>
			<tr>
			  <td>
			    <input id="accessCode" name="accessCode" type="text" placeholder="Access Code"/>
			  </td>
			</tr>
			<tr>
			  <td>
			    <input id="studname" name="studname" type="text" placeholder="User Name"/>
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
			function checkSessionID()
			{
				global $conn;
				if(isset($_POST["accessCode"]) && $_POST["accessCode"] != "")
				{
					
					$stmt = $conn->prepare("SELECT * FROM labsessions WHERE accessCode = ?");
					$stmt->bind_param("s", $_POST["accessCode"]);
					$stmt->execute();
					$result = $stmt->get_result();
					$result = $result->fetch_assoc();
					if(sizeof($result) == 0)
					{
						echo "</br><b>Lab session not found</b>";
					}
					else
					{
						$_SESSION["studentName"] = $_POST["studname"];
						$_SESSION["sessionId"] = $result["sessionId"];
						$_SESSION["sessionName"] = $result["sessionName"];
						header("location: studentSession.php");
					}
					//TODO: Check Database for 
					//ALWAYS USE PREPARED STATEMENTS WHEN SENDING USER
					//	INPUT TO A MYSQL PROMPT
				}
				else
				{
					echo "</br>Please enter the session ID you want to see.</br>";
				}

			}
			checkSessionID();
		?>

	</BODY>
</HTML>
