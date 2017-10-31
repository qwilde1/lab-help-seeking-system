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
	<body>

		<h1>Hello <?php echo $_SESSION["studentName"] ?></h1>
		<h2>Session: <?php echo $_SESSION["sessionName"] ?></h2>

		<?php
			if(isset($_POST["questionData"]) && $_POST["questionData"] != "")
			{
				insertQuestion();
				$_POST["questionData"];
				$updatetext = "";
			}
			else
			{
				$updatetext = "</br><b>Type in a question before you submit it.</b>";
			}
			echo displayQuestions();
			
		?>
		
		<div class="studentInput" style="padding:20px">
			<form method="post" action="studentSession.php">
				<input type="text" id="questionData" name="questionData" placeholder="Insert Question">
				<input type="submit" name="Submit">
			</form>
			
		</div>
		
		<?php
			//DB info
			//$servername = "dbserver.engr.scu.edu";
			//$username = "qwilde";
			//$password = "00001094499";
			//$dbname = "sdb_qwilde";

			//create connection
			//$conn = new mysqli($servername, $username, $password, $dbname);

			// Check connection
			//if ($conn->connect_error) {
			    //die("Connection failed: " . $conn->connect_error);
			//} 
			//echo "Connected successfully";


			echo $updatetext;			

			function insertQuestion() {
				global $conn;
				$quickcheck = $conn->prepare("SELECT * FROM questions WHERE questionData = ?");
				$quickcheck->bind_param("s", $questionData);
				$questionData = $_POST["questionData"];
				$quickcheck->execute();
				$result = $quickcheck->get_result();
				if($result->num_rows > 0)
				{
					echo "</br><b>That question has already been asked.</b>";
					return;
				}
				$sql = $conn->prepare("INSERT INTO questions 
						(sessionId, 
						questionData,
						resolved,
						studentName,
						whenAsked) 
						VALUES (?,?,?,?,NOW())");
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
				global $conn;
				$tableHTML = "<div class=\"queue\" style=\"padding:20px\">
						<table border=\"1\">
							<tr>
								<th>Student</th>
								<th>Question</th>
							</tr>";
				$sql = $conn->prepare("SELECT * FROM questions WHERE sessionId = ? and resolved = 0");
				$sql->bind_param("s", $_SESSION["sessionId"]);
				$sql->execute();
				$result = $sql->get_result();
				while($row = $result->fetch_assoc()){
					$tableHTML .=  "<tr>";
					$tableHTML .=  "<td>" . $row['studentName'] . "</td>";
					$tableHTML .=  "<td>" . $row['questionData'] . "</td>";
					$tableHTML .=  "</tr>";
				}
				$tableHTML .= "</table>
					</div>";
				if($result->num_rows == 0)
					$tableHTML = "</br>There are no questions in this session yet";
				return $tableHTML;
			}

			//TODO FUNCTION 
			//resolve a question in the queue

		?>

	</body>
</html>
