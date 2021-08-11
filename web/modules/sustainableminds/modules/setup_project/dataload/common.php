<?php
$errorFile = 'dataload_errors/load_errors.txt';
$db = new sbom_db();

function sendError($error) {
	global $errorFile,$errorTag, $filename,$noprint;
	if (!$noprint) file_put_contents($errorFile, "$error\t$errorTag\t$filename\n",FILE_APPEND);
}

function db_refresh($c = 0) {
	global $dbname, $output, $db_url; 
	if (!$c) mysql_close();
	if ($c) $output = '';
	
	$url = parse_url($db_url['default']);
	//$link = mysql_connect('localhost', 'sustaina_crw', 'MidoriSQL!2009', true, 131074);
	$link = mysql_connect('localhost', urldecode($url['user']), urldecode($url['pass']), true, 131074);
	mysql_select_db($dbname, $link);
}

function trim_arr(&$value) { 
  $value = preg_replace('/ {2,}/',' ',$value);
  $value = trim(trim(trim($value),'" ')); 
}

function trim_arr_low(&$value) { 
  trim_arr($value);
  $value = strtolower($value); 
}

function echos($var) {
	global $noprint, $output;
	if (!$noprint) $output .= $var . '<br/>';
}

function get_version($filename, $data) {
	$version = '';
	$row = trim($data);
	if (stristr($row, 'version:')) {
		$version = trim(str_ireplace("version:","",$row));
		//$db->add_dataset_version($version);
	} else {
		$msg = 'The file ' . $filename . ' is missing the dataset version. File must contain a dataset version on row 1.';
		if (!in_array($msg, drupal_get_messages('error'))) drupal_set_message($msg, 'error');
	}
	return $version;
}

function get_revision($filename, $data) {
	$revision = '';
	$row = trim($data);
	if (stristr($row, 'revision:')) {
		$revision = trim(str_ireplace("revision:","",$row));
		//$db->update_dataset_revision($version, $revision);
	} else {
		$msg = 'The file ' . $filename . ' is missing the dataset revision. File must contain a dataset revision on row 1.';
		if (!in_array($msg, drupal_get_messages('error'))) drupal_set_message($msg, 'error');
	}
	return $revision;
}

function get_description($version, $filename, $data) {
	$description = '';
	$row = trim($data);
	if (stristr($row, 'description:')) {
		$description = trim(str_replace("description:","",$row));
		//$db->update_dataset_description($version, $description);
	} 
	return $description;
}

ini_set('auto_detect_line_endings', true);
?>
