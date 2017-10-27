
<HTML>
	<HEAD>
		<TITLE>TA Help Seek - Student Login Page</TITLE>
	</HEAD>
	<BODY>
		<a align=right href="talogin.php">Go to TA login page</a>
		<H1 ALIGN=CENTER>Student Login Page</H1>

		<form action="studlogin.php" method="post">
		<table ALIGN=CENTER>
			<tr>
			  <td>
			    <input id="sessid" name="sessid" type="text" placeholder="Session ID"/>
			  </td>
			</tr>
			<tr>
			  <td>
			    <input id="studname" name="studname" type="text" placeholder="User Name"/>
			  </td>
			</tr>
			<tr>
			  <td align="center">
			    <input id="submit" type="submit" value="Submit"/>
			  </td>
			</tr>
		</table>				
		</form>

		<?php
			//DB info
			$servername = "dbserver.engr.scu.edu";
			$username = "qwilde";
			$password = "00001094499";
			$dbname = "sdb_qwilde";

			//Check login info, if legitimate, go on to TA dashboard
			function checkSessionID()
			{
				if(isset($_POST["sessid"]))
				{
					$_POST["studentName"] = $_POST["studname"];
					$_POST["sessionId"] = $_POST["sessid"];
					$_POST["s"]
					//TODO: Check Database for TAID
					//ALWAYS USE PREPARED STATEMENTS WHEN SENDING USER
					//	INPUT TO A MYSQL PROMPT
				}
				else
				{
					echo "Please enter the session ID you want to see.</br>";
				}

			}
			checkSessionID();
		?>

	</BODY>
</HTML>
