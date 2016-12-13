<?php
if($_SERVER["HTTPS"] != "on") {
	header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	exit();
}

function value($attr) {
	if (!isset($_POST[$attr])) return '';
	return 'value="'.htmlspecialchars($_POST[$attr]).'"';
}

$msg = '';
try {
	if (isset($_POST['uname']) && isset($_POST['psw']) && isset($_POST['psw1'])) {
		if ($_POST['psw'] != $_POST['psw1']) throw new Exception('Passwords do not match!');
		if (!preg_match('/^[-\._a-z0-9]+$/',$uname = strtolower($_POST['uname']))) throw new Exception('Invalid user name');
		if (strlen($psw = $_POST['psw']) < 8) throw new Exception('Password too short (Min: 8 characters)');
		file_put_contents(CREDS_STORE,implode("\n", [
											implode(":",[ $uname, password_hash($psw,PASSWORD_DEFAULT) ]),
											"",
											]));
		?>
			<html>
				<head>
					<title>Info</title>
				</head>
				<body>
					<h1>Info</h1>
					<p>Admin credentials created...</p>
					<a href="<?= $_SERVER['SCRIPT_NAME'] ?>">Continue...</a>
				</body>
			</html>
		<?php
		exit;
	}
} catch (Exception $e) {
	$msg = $e->getMessage();
}
#
#
?>
<html>
	<head>
		<title>FingerFile Installer</title>
		<style type="text/css">
			form  { display: table;      }
			p     { display: table-row;  }
			label { display: table-cell; }
			input { display: table-cell; }
		</style>
	</head>
	<body>
		<h1>FingerFile Installer</h1>
	<?php
		if ($msg) {
			echo '<p><strong>'.$msg.'</strong></p>';
		}
		?>
		Create the default admin account:
		<hr/>
		<form method="POST">
			<p>
				<label for="uname"><b>Username: </b></label>
				<input type="text" placeholder="Enter Username" id="uname" name="uname" <?=value('uname')?> required>
			</p>
			<p>
				<label for="psw"><b>Password: </b></label>
				<input type="password" placeholder="Enter Password" id="psw" name="psw" <?=value('psw')?> required>
			</p>
			<p>
				<label for="psw1"><b>Re-type Password: </b></label>
				<input type="password" placeholder="Retype Password" id="psw1" name="psw1" <?=value('psw1')?> required>
			</p>
			<button type="submit">Login</button>
		</form>
	</body>
</html>
