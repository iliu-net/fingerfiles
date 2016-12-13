<?php
function read_catalogue() {
	$res = [];
	if (!file_exists(CAT_STORE)) return $res;
	if (($inp = file_get_contents(CAT_STORE)) === FALSE) return $res;
	foreach (explode("\n",$inp) as $ln) {
		$ln = explode(":",trim($ln),2);
		if (count($ln) != 2) continue;
		list($object,$secret) = $ln;
		$res[$object] = $secret;
	}
	return $res;
}

function save_catalogue(array $cat) {
	$fp = fopen(CAT_STORE,"w");
	if ($fp === FALSE) throw new Exception('Error writing to "'.CAT_STORE.'"');
	foreach ($cat as $i=>$j) {
		fputs($fp,implode(":",[$i,$j])."\n");
	}
	fclose($fp);
}
