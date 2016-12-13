<?php
if($_SERVER["HTTPS"] != "on") {
	header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	exit();
}
require_once(LIBDIR.'catalogue.php');

function url_cmd($txt,$action,$key) {
	return '<a href="'.$_SERVER['SCRIPT_NAME'].
					'/manage?action='.$action.
					'&iname='.urlencode($key).
					'">'.$txt.'</a> ';
}


session_start();
?>
<html>
	<head>
		<title>Manage</title>
		<style type="text/css">
			form  { display: table;      }
			p     { display: table-row;  }
			label { display: table-cell; }
			input { display: table-cell; }
		</style>
	</head>
	<body>
		<h1>Manage</h1>
		<p><a href="<?= $_SERVER['SCRIPT_NAME'] ?>">Home</a></p>
<?php
if (isset($_REQUEST['action'])) {
	$action = $_REQUEST['action'];
} else {
	$action = 'default';
}
if ($action == 'logout') unset($_SESSION['auth_user']);

if (!isset($_SESSION['auth_user'])) {
	$msg = '';
	if (isset($_POST['uname']) && isset($_POST['psw'])) {
		$uname = strtolower($_POST['uname']);
		$txt = file_get_contents(CREDS_STORE);
		foreach (explode("\n",$txt) as $row) {
			$row = explode(":",$row,2);
			if (count($row) != 2) continue;
			list ($row_user,$row_hash) = $row;
			if ($row_user != $uname) continue;
			if (password_verify($_POST['psw'],$row_hash)) {
				$_SESSION['auth_user'] = $uname;
				break;
			}
		}
		if (!isset($_SESSION['auth_user']))	$msg = 'Login failed!';
		$dbg.= '</pre>';
	}
} 
if (!isset($_SESSION['auth_user'])) {
	if ($msg) {
		echo '<p><strong>'.$msg.'</strong></p>';
	}
		?>
			<form method="POST">
			<p>
				<label for="uname"><b>Username: </b></label>
				<input type="text" placeholder="Enter Username" id="uname" name="uname" required/>
			</p>
			<p>
				<label for="psw" class="login"><b>Password: </b></label>
				<input type="password" placeholder="Enter Password" id="psw" name="psw" required/>
			</p>
			<button type="submit">Login</button>
		</form>
<?php
} else {
	$msg = '';

	$cat = read_catalogue();
	
	switch ($action) {
		case 'add':
		  try {
				if (!isset($_POST['iname'])) throw new Exception('Must specify an object name');
				if (!preg_match('/^[-\._A-Za-z0-9]+$/',$iname = $_POST['iname']) || strlen($iname) < 4) throw new Exception('Invalid object name');
				// Create new object...
				if (isset($cat[$iname])) throw new Exception('Object "'.$iname.'"already exist!');
				$cat[$iname] = bin2hex(openssl_random_pseudo_bytes(32));
				save_catalogue($cat);
				$msg = 'Created "'.$iname.'"...';
			} catch (Exception $e) {
				$msg = $e->getMessage();
			}
			break;
		case 'del':
		  try {
				if (!isset($_REQUEST['iname'])) throw new Exception('Must specify an object name');
				$iname = $_REQUEST['iname'];
				if (!isset($cat[$iname])) throw new Exception('Object "'.$iname.'" does NOT exist!');
				unset($cat[$iname]);
				if (file_exists(INCOMING_DIR.$iname)) unlink(INCOMING_DIR.$iname);
				save_catalogue($cat);
				$msg = 'Deleted "'.$iname.'"...';
			} catch (Exception $e) {
				$msg = $e->getMessage();
			}		  
		case 'reset':
		  try {
				if (!isset($_REQUEST['iname'])) throw new Exception('Must specify an object name');
				$iname = $_REQUEST['iname'];
				if (!isset($cat[$iname])) throw new Exception('Object "'.$iname.'" does NOT exist!');
				$cat[$iname] = bin2hex(openssl_random_pseudo_bytes(32));
				save_catalogue($cat);
				$msg = 'Updated "'.$iname.'"...';
			} catch (Exception $e) {
				$msg = $e->getMessage();
			}		  
	}
	if ($msg) {
		echo '<p><strong>'.$msg.'</strong></p>';
	}
	/* Display catalogue */
	if (count($cat)) {
		echo '<table border=1>';
		echo '<tr><th align="left">Object</th><th align="left">Secret</th><th align="left">Actions</th></tr>';
		foreach ($cat as $i=>$j) {
			echo '<tr>';
			echo '<td>';
			echo '<a href="'.$_SERVER['SCRIPT_NAME'].'/repo/'.urlencode($i).'">';
			echo htmlspecialchars($i).'</a></td>';
			echo '<td>'.htmlspecialchars($j).'</td>';
			echo '<td>';
			echo url_cmd('[Delete]','del',$i);
			echo url_cmd('[ReKey]','reset',$i);
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
?>
		<hr/>
		<form method="POST">
			<h2>New Object</h2>
			<p>
				<input type="hidden" name="action" value="add" />
				<label for="iname"><b>object name: </b></label>
				<input type="text" placeholder="Object name" id="iname" name="iname" required />
				<button type="submit">Add</button>
			</p>
		</form>
		<form method="POST">
			<input type="hidden" name="action" value="logout" />
				<button type="submit">Logout</button>
		</form>
<?php
}
?>
	</body>
</html>
