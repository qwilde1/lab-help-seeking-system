<!DOCTYPE html>
<html>
	<body>

		<h1>Hello <?php echo $_POST["studentName"] ?></h1>
		<h2>Session: <?php echo $_POST["sessionId"] ?></h2>

		<div class="queue" style="padding:20px">
			<table>
				<tr>
					<th>questionData</th>
					<th>studentName</th>
					<th>resolved</th>
				</tr>
			</table>
		</div>
		<div class="studentInput" style="padding:20px">
			<form method="post">
				<input type="text" id="questionData" name="questionData" placeholder="Insert Question">
				<input type="submit" name="Submit">
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


			//TODO FUNCTION
			//get the queue for this particular session
			function getQueue(){
				global $conn;
				$sql = $conn->prepare("SELECT questionData, studentName FROM questions WHERE sessionId = ? and resolved = 0");
				$sql->bind_param("s", $_POST["sessionId"]);
				$result = $conn->query($sql);
				$result = $result->fetch_assoc();
				if ($result->num_rows > 0) {
				    // output data of each row
				    while($row = $result->fetch_assoc()) {
				    	$tempName = $row["studentName"];
				    	$tempQuestion = $row["questionData"];
				    	$resolved = $row["resolved"];
				    }
				} else {
				    echo "Empty Queue";
				}

			}

			//TODO FUNCTION
			//insert new quesiton into the queue
			function insertQuestion() {
				global $conn;
				if(isset($_POST["questionData"])){
					$sql = $conn->prepare("INSERT INTO questions (sessionId, questionData, resolved, studentName) VALUES (?,?,?,?)");
					$sql->bind_param("sssss", $_POST["sessionId"], $_POST["questionData"], 0, $_POST["studentName"]);
					$result = $conn->query($stmt);
					$result = $result->fetch_assoc();
					echo $result;

					while($row = $result->fetch_assoc()){
						echo "<tr>"
						echo "<td> $row['questionData'] </td>"
						echo "<td> $row['studentName'] </td>"
						echo "<td> $row['resolved'] </td>"
						echo "</tr>"
					}

				}
				getQueue();
			}

			//TODO FUNCTION 
			//resolve a question in the queue


		?>

	</body>
</html>
