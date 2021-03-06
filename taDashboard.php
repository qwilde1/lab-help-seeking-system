<?php
// Start the session
session_start();
//DB info
$servername = "dbserver.engr.scu.edu";
$username = "qwilde";
$password = "00001094499";
$dbname = "sdb_qwilde";

//create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} 
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="./styles/dashboardStyle.css">
		<script type="text/javascript">
			function reload()
			{
				var req = new XMLHttpRequest();
				console.log("Grabbing Value");
				req.onreadystatechange=function() {
				if (req.readyState==4 && req.status==200) {
					document.getElementById('trulyCodesFavouriteNumber').innerText = req.responseText;
				}
			}
			req.open("GET", 'reload.txt', true);
			req.send(null);
			}
			function init()
			{
				reload()
				var int=self.setInterval(function(){reload()},5000);
			}
		</script>
	</head>
	<body onload="init()">
		<div class="top-bar">
			<h1 class="dashboard"><?php echo $_SESSION["taid"]; ?> Dashboard</h1>
			<a class="backButton" align=right href="talogin.php">Return to TA login page</a>
		</div>
		<div class="newSession">
			<form method="post" action="taDashboard.php">
				<h2>Create a New Session</h2>
				<input type="text" size="40" id="SessionName" name="SessionName" placeholder="Session Name">
				</br>
				<input type="text" size="40" maxlength="10" id= "AccessCode" name="AccessCode" placeholder="Access Code (10 char max, optional)">
				</br>
				<input type="submit" name="Submit" value="Create Session" class="button1 button1-block">
			</form>
		</div>
		<?php

			if(isset($_POST["SessionName"]) && $_POST["SessionName"] != "")
			{
				createSession();
			}
			if(isset($_POST["sessionInput"]))
			{
				gotoSession();
			}
			if(isset($_POST['delSessionID']) && $_POST['delSessionID'] != "")
			{
				removeSession();
			}
			getSessions();

			function removeSession() //deletes session and question from both tables
			{
				//delete session in labsessions table
				global $conn;
				$stmt = $conn->prepare("DELETE FROM labsessions WHERE sessionId=?");
				$stmt->bind_param("s", $_POST["delSessionID"]);
				$stmt->execute();
				$result = $stmt->get_result();
			

			//delete all questions of that session in questions table
				$stmt = $conn->prepare("DELETE FROM questions WHERE sessionId=?");
				$stmt->bind_param("s", $_POST["delSessionID"]);
				$stmt->execute();
				$result = $stmt->get_result();
				
			}			

			//TODO FUNCTION
			//create new session in the database
			function createSession()
			{
				global $conn;
				if(!isset($_POST["AccessCode"]) || $_POST["AccessCode"] == "")
				{
					$_POST["AccessCode"] = rand(10000000, 99999999);
				}
				if(!accessCodeExists())
				{
					echo "</br>That access code is already used. (If you did not enter an access code, then just try creating this session again)</br>";
					return;
				}
				$stmt = $conn->prepare("SELECT userId FROM user WHERE username=?");
				$stmt->bind_param("s", $_SESSION["taid"]);
				$stmt->execute();
				$result = $stmt->get_result();
				$result = $result->fetch_assoc();
				$userId = $result["userId"];

				$accessCode = $_POST["AccessCode"];
				$sessionName = $_POST["SessionName"];
				$stmt = $conn->prepare("INSERT INTO labsessions(
							accessCode,
							dateCreated,
							sessionName,
							status,
							userId)
							VALUES(?, CURDATE(), ?, 1, ?)");
				$stmt->bind_param("ssi", $accessCode, $sessionName, $userId);
				if($stmt->execute())
					echo "</br> New session created</br>";
				else
					echo "</br> Session not created</br>";
			}

			function accessCodeExists()
			{
				global $conn;
				$stmt = $conn->prepare("SELECT * FROM labsessions WHERE accessCode = ?");
				$stmt->bind_param("s", $_POST["AccessCode"]);
				$stmt->execute();
				if($stmt->num_rows == 0)
					return TRUE;
				else
					return FALSE;
			}		

			//TODO FUNCTION
			//get sessions that belong to this TA
			function getSessions() {
				global $conn;
				$tableHTML = "<div class=\"sessions\">
						<table>
							<tr>
								<th>Session Name</th>
								<th>Date Created</th>
								<th>Access Code</th>
								<th>Delete</th>
							</tr>";

				$sql = $conn->prepare("SELECT * FROM labsessions WHERE userId = ?");
				$sql->bind_param("s", $_SESSION["userId"]);
				$sql->execute();
				$result = $sql->get_result();
				if($result->num_rows == 0)
					$tableHTML = "</br>There are no sessions yet";
				while($row = $result->fetch_assoc()) {
					$tableHTML .=  "<tr>";
					$tableHTML .=  "<td>" . $row['sessionName'] . "</td>";
					$tableHTML .=  "<td>" . $row['dateCreated'] . "</td>";
					$tableHTML .=  "<td>" . $row['accessCode'] . "</td>";
					$tableHTML .= "<td><form method=\"post\" action=\"taDashboard.php\" onsubmit=\"return confirm('Are you sure you want to delete ".$row['sessionName']."?');\"><input type=\"hidden\" name=\"delSessionID\" value=\"" . $row['sessionId'] ."\"><input type=\"submit\" name=\"Delete\" value=\"Delete\"></form></td>";
					$tableHTML .=  "</tr>";
				}
				$tableHTML .= "</table>
					</div>";
				echo $tableHTML;
				
			}

			//TODO FUNCTION
			//go to session page with inserted sessionName
			function gotoSession()
			{
				global $conn;
				$sql = $conn->prepare("SELECT * FROM labsessions WHERE accessCode = ? and userId = ?");
				$sql->bind_param("ss", $_POST["sessionInput"],$_SESSION["userId"]);
				$sql->execute();
				$result = $sql->get_result();
				$result = $result->fetch_assoc();
				print_r($result);
				echo "</br>Jimajong</br>";
				$sql2 = $conn->prepare("SELECT * FROM user WHERE userId = ?");
				$sql2->bind_param("s", $_SESSION["userId"]);
				$sql2->execute();
				$result2 = $sql2->get_result();
				$result2 = $result2->fetch_assoc();
				if($result){
					$_SESSION["sessionId"]=$result["sessionId"];
					$_SESSION["sessionName"]=$result["sessionName"];
					$_SESSION["username"]=$result2["username"];
					header("location: taSession.php");
				}
				else {
					echo "Lab session does not exist";

				}
			}

			$conn->close();

		?>
		<div class="accessSession">
			<form method="post">
				<h2>Access Session</h2>
				<input type="text" size="25" maxlength="10" id="sessionInput" name="sessionInput" placeholder="Access Code">
				<input type="submit" id="submit" value="View Session">
			</form>
		</div>

	</body>
</html>
