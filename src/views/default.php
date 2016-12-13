<html>
  <head>
    <title>FingerFile</title>
  </head>
  <body>
    <h1>FingerFile</h1>
<?php
require_once(LIBDIR.'catalogue.php');
require_once(LIBDIR.'units.php');

$cat = read_catalogue();
if (count($cat)) {
  echo '<table>';
  echo '<tr><th align="left">Object</th><th>Size</th><th>Date/Time</th></tr>';
  foreach ($cat as $i=>$j) {
    echo '<tr>';
    if (file_exists(INCOMING_DIR.$i)) {
      echo '<td>';
      echo '<a href="'.$_SERVER['SCRIPT_NAME'].'/repo/'.urlencode($i).'">';
      echo htmlspecialchars($i).'</a>';
      echo '</td>';
      echo '<td>';
      echo FileSizeConvert(filesize(INCOMING_DIR.$i));
      echo '</td>';
      echo '<td>';
      echo date('Y-m-d H:i',filemtime(INCOMING_DIR.$i));
      echo '</td>';
    } else {
      echo '<td>'.htmlspecialchars($i).'</td>';
      echo '<td colspan="2">Missing!</td>';
    }
    echo '</td>';
    echo '</tr>';
  }
  echo '</table>';
}

?>
    <p>
	    <a href="<?= $_SERVER['SCRIPT_NAME'].'/manage' ?>">Manage</a>
    </p>
  </body>
</html>
