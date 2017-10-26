<!DOCTYPE html>
<html>
	<body>

		<h1>Hello User</h1>
		<h2>Click here to logout</h2>

		<div class="newSession">
			<form>
				<h2>Create a New Session</h2>
				<input type="text" name="AccessCode" placeholder="Access Code">
				<button type="button" name="generateCode" style="color:red">Generate Code</button> 
				<br>
				<input type="text" name="SessionName" placeholder="Session Name">
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
					<th>Status</th>
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
			//create new session in the database

			//TODO FUNCTION
			//get sessions that belong to this TA


		?>

	</body>
</html>