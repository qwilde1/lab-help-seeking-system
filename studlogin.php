<?php
	// Start the session
	session_start();
?>
<HTML>
	<HEAD>
		<link rel="stylesheet" type="text/css" href="./styles/loginStyle.css">
	</HEAD>
	<BODY>
		<div class="tab-content">
			<H1 ALIGN=CENTER>Student Login Page</H1>
			<ul class="tab-group">
				<li class="tab"><a align=right href="talogin.php">TA login</a></li>
				<li class="tab active"><a align=right href="studlogin.php">STUDENT login</a></li>
			</ul>
			<form action="studlogin.php" method="post" class="top-row">
			<table ALIGN=CENTER>
				<tr class="field-wrap">
				  <td>
				    <input id="accessCode" name="accessCode" type="text" placeholder="Access Code" autofocus="autofocus"/><span class="req"></span>
				  </td>
				</tr>
				<tr class="field-wrap">
				  <td>
				    <input id="studname" name="studname" type="text" placeholder="User Name"/><span class="req"></span>
				  </td>
				</tr>
				<tr>
				  <td align="center">
				    <input id="submit" type="submit" value="Submit" class="button1 button1-block"/>
				  </td>
				</tr>
			</table>				
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
			function checkSessionID()
			{
				global $conn;
				if(isset($_POST["accessCode"]) && $_POST["accessCode"] != "" && isset($_POST["studname"]) && $_POST["studname"] != "")
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
					//echo "</br>Please enter the session ID and username.</br>";
				}

			}
			checkSessionID();
		?>
	</BODY>
</HTML>
