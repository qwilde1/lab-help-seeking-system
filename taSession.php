<!DOCTYPE html>
<html>
	<body>

		<h1>Hello User</h1>
		<h2>Click here to logout</h2>
		<h2>Click here to close session</h2>
		<h2>Session: Lab1</h2>

		<div class="queue" style="padding:20px">
			<table>
				<tr>
					<th>questionData</th>
					<th>studentName</th>
				</tr>
			</table>
		</div>
		<?php
			//DB info
			$servername = "dbserver.engr.scu.edu";
			$username = "qwilde";
			$password = "00001094499";
			$dbname = "sdb_qwilde";

			//TODO FUNCTION
			//get the queue for this particular session
			function getQueue(){
				$sql = "SELECT questionData, studentName FROM questions WHERE sessionId = ? and resolved = 0";

				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
				    // output data of each row
				    while($row = $result->fetch_assoc()) {
				    	$tempName = $row["studentName"];
				    	$tempQuestion = $row["questionData"];
				    }
				} else {
				    echo "Empty Queue";
				}
			}

			//TODO FUNCTION 
			//resolve a question in the queue


		?>


	</body>
</html>
