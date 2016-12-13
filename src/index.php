<?php
define('DATA_DIR','fingerdata/');
define('INCOMING_DIR','fingerdata-incoming/');
define('CREDS_STORE',DATA_DIR.'creds.txt');
define('CAT_STORE',DATA_DIR.'catalogue.txt');
define('LIBDIR',__DIR__.'/lib/');
define('VIEWDIR',__DIR__.'/views/');

date_default_timezone_set('UTC');


if (!is_dir(DATA_DIR)) {
	mkdir(DATA_DIR) || die("Unable to create data directory\n");
	file_put_contents(DATA_DIR.'.htaccess',"Deny from All\n");
}
if (!is_dir(INCOMING_DIR)) {
	mkdir(INCOMING_DIR) || die("Unable to create folder storage\n");
}
if (!file_exists(CREDS_STORE)) {
	require_once(VIEWDIR.'install.php');
} else {
	if (empty($_SERVER['PATH_INFO'])) {
		require_once(VIEWDIR.'default.php');
	} else {
		$path_info = $_SERVER['PATH_INFO'];
		if ($path_info{0} == '/') $path_info = substr($path_info,1);
		$path_info = explode('/',$path_info);
		$view = preg_replace('/[^A-Za-z0-9]/','',array_shift($path_info));
		$view_php = VIEWDIR.$view.'.php';
		if (!file_exists($view_php)) {
			http_response_code(404);
			$error = 'View "'.htmlspecialchars($view).'" does not exist!';
			require_once(VIEWDIR.'error.php');
		} else {
			require_once($view_php);
		}
	}
}
