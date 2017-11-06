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
		</br><a align=right href="taDashboard.php">Return to Dashboard page</a>
		<h1>Hello <?php echo $_SESSION["username"] ?></h1>
		<h2>Session: <?php echo $_SESSION["sessionName"] ?></h2>

		<?php
			if(isset($_POST['resolveID']) && $_POST['resolveID'] != "")
			{
				updateEntry();
				$updatetext = "";
			}
			echo displayQuestions();

			function updateEntry(){
				global $conn;
				$quickcheck = $conn->prepare("UPDATE questions SET resolved = 1 where questionId= ?");
				$quickcheck->bind_param("s", $questionId);
				$questionId = $_POST["resolveID"];
				if($quickcheck->execute()){
					echo "</br>question resolved";
				}
				
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
					$tableHTML .= "<td><form method=\"post\" action=\"studentSession.php\"><input type=\"hidden\" name=\"resolveID\" value=\"" . $row['questionId'] ."\"><input type=\"submit\" name=\"resolve\" value=\"Resolve\"></form></td>";
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
