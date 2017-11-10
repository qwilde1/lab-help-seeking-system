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

		<form method="post" action="taSession.php">
			<textarea rows="5" cols="60" id="announcementData" name="announcementData" placeholder="Insert Announcement"></textarea></br>
			<input type="submit" name="Submit" value="Announce"/>
		</form>

		<?php
			if(isset($_POST['resolveID']) && $_POST['resolveID'] != "")
			{
				updateEntry();
				$updatetext = "";
			}
			if(isset($_POST['announcementData']) && $_POST['announcementData'] != "")
			{
				insertAnnouncement();
			}
			echo "<h2>Announcements</h2>";
			echo displayAnnouncements();
			echo "<h2>Questions</h2>";
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
				$sql = $conn->prepare("SELECT * FROM questions WHERE sessionId = ? and resolved = 0 and announcement = 0 ORDER BY whenAsked ASC");
				$sql->bind_param("s", $_SESSION["sessionId"]);
				$sql->execute();
				$result = $sql->get_result();
				while($row = $result->fetch_assoc()){
					$tableHTML .=  "<tr>";
					$tableHTML .=  "<td>" . $row['studentName'] . "</td>";
					$tableHTML .=  "<td>" . $row['questionData'] . "</td>";
					$tableHTML .= "<td><form method=\"post\" action=\"taSession.php\"><input type=\"hidden\" name=\"resolveID\" value=\"" . $row['questionId'] ."\"><input type=\"submit\" name=\"resolve\" value=\"Resolve\"></form></td>";
					$tableHTML .=  "</tr>";
				}
				$tableHTML .= "</table>
					</div>";
				if($result->num_rows == 0)
					$tableHTML = "</br>There are no questions in this session yet";
				return $tableHTML;
			}

			function displayAnnouncements()
			{
				global $conn;
				$tableHTML = "<div class=\"announcements\" style=\"padding:20px\" >
						<table border=\"1\" style=\"max-width:900px;\">
							<tr>
								<th>Time</th>
								<th>Announcement</th>
							</tr>";
				$sql = $conn->prepare("SELECT * FROM questions WHERE announcement = 1 ORDER BY whenAsked DESC");
				$sql->execute();
				$result = $sql->get_result();
				while($row = $result->fetch_assoc()){
					$tableHTML .=  "<tr>";
					$tableHTML .=  "<td align=CENTER width=\"150\">" . $row['whenAsked'] . "</td>";
					$tableHTML .=  "<td>" . $row['questionData'] . "</td>";
					$tableHTML .=  "</tr>";
				}
				$tableHTML .= "</table>
					</div>";
				if($result->num_rows == 0)
					$tableHTML = "</br>You have yet to make an announcement.</br>";
				return $tableHTML;
			}


			function insertAnnouncement() {
				global $conn;
				$sql = $conn->prepare("INSERT INTO questions 
						(sessionId, 
						questionData,
						resolved,
						studentName,
						whenAsked,
						announcement) 
						VALUES (?,?,?,?,NOW(),1)");
				$sql->bind_param("ssis", $sessionId, $questionData, $resolvedNum, $studentName);
				
				$sessionId = $_SESSION["sessionId"];
				$questionData = $_POST["announcementData"];
				$resolvedNum = 0;
				$studentName = $_SESSION["username"];
				
				if($sql->execute())
					echo "</br>Announcement posted";
				else
					echo "</br>Insertion failure";
			}

		?>

	</body>
</html>
