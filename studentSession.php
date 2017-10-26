<!DOCTYPE html>
<html>
	<body>

		<h1>Hello User</h1>
		<h2>Session: Lab1</h2>

		<div class="queue" style="padding:20px">
			<table>
				<tr>
					<th>questionData</th>
					<th>studentName</th>
				</tr>
			</table>
		</div>
		<div class="studentInput" style="padding:20px">
			<form>
				<input type="text" name="questionData" placeholder="Insert Question">
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
				$sql = "SELECT questionData, studentName FROM questions WHERE sessionId = ? and resolved = 0";


			}

			//TODO FUNCTION
			//insert new quesiton into the queue


			//TODO FUNCTION 
			//resolve a question in the queue


		?>

	</body>
</html>
