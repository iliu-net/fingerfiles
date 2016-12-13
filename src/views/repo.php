<?php
require_once(LIBDIR.'catalogue.php');
require_once(LIBDIR.'units.php');

try {
  if (!isset($path_info) || count($path_info) < 1) throw new Exception('Path error');
  $iname = array_shift($path_info);
  $cat = read_catalogue();
  if (!isset($cat[$iname])) throw new Exception('Missing object '.htmlspecialchars($iname));
  
  switch ($_SERVER['REQUEST_METHOD']) {
    case 'PUT':
      header('Content-type: text/plain');
      $key = $cat[$iname];
      $sig = array_shift($path_info);
      if (!isset($sig)) die("Missing digital signature\n");

      if (($stream = fopen('php://input', "r")) === FALSE)
	die("Unable to open STDIN\n");

      $tmpname = tempnam(INCOMING_DIR,'upf');
      if (($fp = fopen($tmpname,'w')) === FALSE) {
	unlink($tmpname);
	die("Unable to write to $iname\n");
      }
      $ctx = hash_init('sha256');
      hash_update($ctx,$key."\n");
      while (!feof($stream)) {
	$j = fread($stream,65535);
	fputs($fp,$j);
	hash_update($ctx, $j);
      }
      $hash = hash_final($ctx);
      fclose($fp);
      if ($sig == $hash) {
	echo "Signature verified!\n";
	if (file_exists(INCOMING_DIR.$iname)) unlink(INCOMING_DIR.$iname);
	rename($tmpname,INCOMING_DIR.$iname);
	chmod(INCOMING_DIR.$iname,0666);
	echo 'Uploaded '.FileSizeConvert(filesize(INCOMING_DIR.$iname)).PHP_EOL;
      } else {
	http_response_code(403);
	echo "Unauthenticated upload\n";
	unlink($tmpname);
      }
      fclose($stream);
      break;
    case 'GET':
    case 'HEAD':
      if (!file_exists(INCOMING_DIR.$iname)) throw new Exception('Un-initialized object '.$iname);
      header('Location: '.dirname($_SERVER['SCRIPT_NAME']).'/'.INCOMING_DIR.$iname);
      exit;
      break;
    default:
      throw new Exception('Invalid Verb');
  }
} catch (Exception $e) {
  http_response_code(500);
  $error = $e->getMessage();
  require_once(VIEWDIR.'error.php');
  exit;
}
