<?php
if (ini_get('phar.readonly')) {
	$cmd = escapeshellarg(PHP_BINARY);
	$cmd .= ' -d phar.readonly=0';
	foreach ($argv as $i) {
		$cmd .= ' '.escapeshellarg($i);
	}
	passthru($cmd,$rv);
	exit($rv);
}

define('CMD',array_shift($argv));
error_reporting(E_ALL);

function usage() {
	die("Usage:\n\t".CMD." <output.phar> <src_directory>\n");
}
$path = ".";

if (count($argv) != 2) usage();

$path = array_shift($argv);
if (!isset($path)) die("Must specify output phar\n");
$path = preg_replace('/\.phar$/i',"",$path).'.phar';
if (file_exists($path)) die("$path: output phar already exists\n");

$srcdir = array_shift($argv);
if (!isset($srcdir)) usage();
$srcdir = preg_replace('/\/*$/',"",$srcdir).'/';
if (!is_dir($srcdir)) die("$srcdir: directory doesn't exist!\n");


echo ("Generating PHAR\n");


$phar = new Phar($path);
/*
$pharname = $attr["NAME"]."_".$attr["VERSION"].".phar";
$phar->setMetadata([
	"name" => $attr["NAME"],
	"version" => $attr["VERSION"],
	"api" => $attr["API_VERSION"],
	"minecraft" => $attr["MINECRAFT_VERSION"],
	"protocol" => $attr["CURRENT_PROTOCOL"],
	"creationDate" => time(),
]);
*/
$phar->setStub('<?php define("PHAR_FILE", "phar://". __FILE__ ."/"); require_once("phar://". __FILE__ ."/index.php");  __HALT_COMPILER();');
$phar->setSignatureAlgorithm(Phar::SHA1);
$phar->startBuffering();

$cnt = 0;
foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($srcdir)) as $file){
	if (is_dir($file)) continue;
	$fpath = str_replace("\\","/",substr($file,strlen($srcdir)));
	$phar->addFile($file,$fpath);
	++$cnt;
}

$phar->compressFiles(Phar::GZ);
$phar->stopBuffering();
echo ("Created: $path\n");
