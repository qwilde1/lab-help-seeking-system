<?php
session_start();
?>
<html>
	<body>
		</br><a align=right href="talogin.php">Return to TA login page</a>
		<h1>Hello <?php echo $_SESSION["taid"]; ?></h1>

		<div class="newSession">
			<form method="post" action="taDashboard.php">
				<h2>Create a New Session</h2>
				<input type="text" id= "AccessCode" name="AccessCode" placeholder="Access Code">
				<button type="button" id="generateCode" name="generateCode" style="color:red">Generate Code</button> 
				<br>
				<input type="text" id="SessionName" name="SessionName" placeholder="Session Name">
				<br>
				<input type="submit" name="Submit">
			</form>
		</div>
		<br>

		<div class="previousSessions" style="padding:20px">
			<h2>Previous Sessions</h2>
			<table>
				<tr>
					<th>Session Name</th>
					<th>Date Created</th>
					<th>Access Code</th>
					<th>Status</th>
				</tr>
			</table>
			<form action="taDashboard.php" method="post">
				<h2>Access Session</h2>
				<input type="text" id="sessionName" name="sessionName">
				<input type="submit" id="submit" value="Submit">
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
			    die("Connection failed: " . $conn->connect_error);
			} 
			echo "Connected successfully";

			if(isset($_POST["AccessCode"]) && isset($_POST["SessionName"]))
			{
				createSession();
			}
			//TODO FUNCTION
			//create new session in the database
			function createSession()
			{
				global $conn;
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
				$stmt->execute();
				echo "</br> New session created";
			}

			//TODO FUNCTION
			//get sessions that belong to this TA


			//TODO FUNCTION
			//go to session page with inserted sessionName
			function gotoSession()
			{
				global $conn;
				if(isset($_POST["sessionName"]))
				{
					//IMPORTANT TODO
					//login page need to add userId to POST array
					$sql = $conn->prepare("SELECT * FROM labsessions WHERE sessionName = ? and userId = ?");
					$sql->bind_param("ss", $_POST["sessionName"],$_POST["userId"]);
					$result = $conn->query($sql);
					$result = $result->fetch_assoc();
					if($result){
						//TODO
						//Possibly add session information to the POST array??
						$_POST["sessionId"]=$result["sessionId"];
						$_POST["sessionName"]=$result["sessionName"];
						header("location: taSession.php");
					}
					else {
						echo "lab session does not exist";

					}
				}
				else {
					echo "</br>insert session name</br>";
				}


			}
			
			//gotoSession();
			$conn->close();

		?>

	</body>
</html>
