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
				var int=self.setInterval(function(){reload()},3000);
			}
		</script>
	</head>
	<body onload="init()">
		</br><a align=right href="studlogin.php">Return to STUDENT login page</a>
		<h1>Hello <?php echo $_SESSION["studentName"] ?></h1>
		<h2>Session: <?php echo $_SESSION["sessionName"] ?></h2>

		<?php
			if(isset($_POST["questionData"]) && $_POST["questionData"] != "")
			{
				insertQuestion();
				$updatetext = "";
			}
			else if(isset($_POST['resolveID']) && $_POST['resolveID'] != "")
			{
				updateEntry();
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
				<input type="submit" name="Submit" value="Submit Question">
			</form>
			
		</div>
		
		<?php

			echo $updatetext;	

			function updateEntry(){
				global $conn;
				$quickcheck = $conn->prepare("UPDATE questions SET resolved = 1 where questionId= ?");
				$quickcheck->bind_param("s", $questionId);
				$questionId = $_POST["resolveID"];
				if($quickcheck->execute()){
					echo "</br>question resolved";
				}
				
			}		

			function insertQuestion() {
				global $conn;
				$quickcheck = $conn->prepare("SELECT * FROM questions WHERE questionData = ? AND sessionId = ?");
				$quickcheck->bind_param("si", $questionData, $sessionId);
				$questionData = $_POST["questionData"];
				$sessionId = $_SESSION["sessionId"];
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
								<th>Resolve</th>
							</tr>";
				$sql = $conn->prepare("SELECT * FROM questions WHERE sessionId = ? and resolved = 0");
				$sql->bind_param("s", $_SESSION["sessionId"]);
				$sql->execute();
				$result = $sql->get_result();
				while($row = $result->fetch_assoc()){
					$tableHTML .=  "<tr>";
					$tableHTML .=  "<td>" . $row['studentName'] . "</td>";
					$tableHTML .=  "<td>" . $row['questionData'] . "</td>";
					if($_SESSION['studentName'] == $row['studentName']){
						$tableHTML .= "<td><form method=\"post\" action=\"studentSession.php\"><input type=\"hidden\" name=\"resolveID\" value=\"" . $row['questionId'] ."\"><input type=\"submit\" name=\"resolve\" value=\"Resolve\"></form></td>";
					}else{
						$tableHTML .= "<td></td>";
					} 
					$tableHTML .=  "</tr>";
				}
				$tableHTML .= "</table>
					</div>";
				if($result->num_rows == 0)
					$tableHTML = "</br>There are no questions in this session yet";
				return $tableHTML;
			}

		?>

	</body>
</html>
