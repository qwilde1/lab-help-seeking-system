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
echo "Connected successfully";
?>
<html>
	<head>
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
		</br><a align=right href="talogin.php">Return to TA login page</a>
		<h1>Hello <?php echo $_SESSION["taid"]; ?></h1>

		<div class="newSession">
			<form method="post" action="taDashboard.php">
				<h2>Create a New Session</h2>
				<input type="text" id="SessionName" name="SessionName" placeholder="Session Name">
				</br>
				<input type="text" id= "AccessCode" name="AccessCode" placeholder="Custom Access Code (optional)">
				</br>
				<input type="submit" name="Submit">
			</form>
		</div>
		<br>
		<form method="post">
			<h2>Access Session</h2>
			<input type="text" id="sessionInput" name="sessionInput" placeholder="Access Code">
			<input type="submit" id="submit" value="Submit">
		</form>
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
				$tableHTML = "<div class=\"sessions\" style=\"padding:20px\">
						<table border=\"1\">
							<tr>
								<th>Session Name</th>
								<th>Session Id</th>
								<th>Date Created</th>
								<th>Access Code</th>
								<th>Status</th>
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
					$tableHTML .=  "<td>" . $row['sessionId'] . "</td>";
					$tableHTML .=  "<td>" . $row['dateCreated'] . "</td>";
					$tableHTML .=  "<td>" . $row['accessCode'] . "</td>";
					$tableHTML .=  "<td>" . $row['status'] . "</td>";
					$tableHTML .= "<td><form method=\"post\" action=\"taDashboard.php\"><input type=\"hidden\" name=\"delSessionID\" value=\"" . $row['sessionId'] ."\"><input type=\"submit\" name=\"Delete\" value=\"Delete\"></form></td>";
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

	</body>
</html>
