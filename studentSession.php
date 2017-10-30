<?php
// Start the session
session_start();
?>
<html>
	<body>

		<h1>Hello <?php echo $_SESSION["studentName"] ?></h1>
		<h2>Session: <?php echo $_SESSION["sessionName"] ?></h2>

		<div class="queue" style="padding:20px">
			<table>
				<tr>
					<th>questionData</th>
					<th>studentName</th>
					<th>resolved</th>
				</tr>
				<?php isplayQuestions(); ?>
			</table>
		</div>
		<div class="studentInput" style="padding:20px">
			<form method="post" action="studentSession.php">
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

			if(isset($_POST["questionData"]))
			{
				insertQuestion();
			}

			else
			{
				echo"</br><b>Type in a question before you submit it.</b>";
			}

			//TODO FUNCTION
			//insert new question into the queue
			function insertQuestion() {
				global $conn;
				$sql = $conn->prepare("INSERT INTO questions (sessionId, questionData, resolved, studentName, whenAsked) VALUES (?,?,?,?,NOW())");
				$sql->bind_param("ssis", $sessionId, $questionData, $resolvedNum, $studentName);

				$sessionId = $_SESSION["sessionId"];
				$questionData = $_POST["questionData"];
				$resolvedNum = 0;
				$studentName = $_SESSION["studentName"];
				
				if($sql->execute())
					echo "</br>Question inserted";
				else
					echo "</br>Insertion failure";
			}

			//Call this in a php block in the HTML for the page where you want the questions to appear
			function displayQuestions(){
				$sql = $conn->prepare("SELECT questionData, studentName FROM questions WHERE sessionId = ? and resolved = 0 ORDER BY whenAsked");
				$sql->bind_param("s", $_SESSION["sessionId"]);
				$sql->execute();
				$result = $sql->get_result();
				$result = $result->fetch_assoc();
				//GENERATE TABLE
				while($row -> $result->fetch_assoc()){
					echo "<tr>";
					echo "<td>$row["studentName"]</td>";
					echo "<td>$row["questionData"]</td>";
					echo "</tr>";
				}

			}

			//TODO FUNCTION 
			//resolve a question in the queue

		?>

	</body>
</html>
