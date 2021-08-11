<?php

/* Column headers: 'name', 'description'
   Tables updated: SM_LCA_MatcProc
   Assigns the description in each row to its matproc
*/
function sustainable_minds_dataload_load_matproc_desc($fileArr, $dbname_in='sm_test', $noprint_in=0) {
	global $dbname, $errorTag, $errorFile, $errorTag, $filename, $noprint, $output;
	$output = '';
	$dbname = $dbname_in;
	include_once('common.php');
	$errorTag = 'lod matproc desc';
	$phaseid = 1;
	$db = new sbom_db();
	
	foreach($fileArr as $filename) {
		echos( "Uploading " .$filename . "");
		/*db_refresh(1);*/

    $data_array = file($filename, FILE_IGNORE_NEW_LINES);
		
		/*****************************************************/
		/* get the dataset version, revision and description */
		/*****************************************************/
		$version = get_version($filename, $data_array[0]);
		if (empty($version)) break;
		$revision = get_revision($filename, $data_array[1]);
		if (empty($revision)) break;
    $row = $db->find_datasetInfo($version, $revision);
    if (!$row) {
      sendError('Version or revision not found');
      echos('Version and revision headers in file must match those values of an existing dataset version. <a href="/add_dataset/">Create new verion here.</a> Skipping file.');
      continue;
    }

		$fileshortname = basename($filename);

    //map columns to impact ids.  column zero contains matprocname
		$colNames =preg_split("/[ ]?[\t][ ]?[\n]?/", $data_array[2]);
		array_walk($colNames,'trim_arr_low');
		if (count($colNames) > count(array_unique($colNames))) {
			sendError('File with same named columns\t');
		}
		
		$colNames = array_flip($colNames);
		//$result = mysql_query("CALL SM_LCA_List_Impact()");
		if (!isset($colNames['description']) || !isset($colNames['name'])) {
			echos('Missing column description or name for file' . $filename);
			sendError('Missing column description or name for file '.$filename);
			continue;
		}
		
		$parts_array = array();
		/* load data from file into array) */
		for ($i = 3; $i < count($data_array); $i++) {
			$parts_array[$i] = explode("\t",$data_array[$i]);
			array_walk($parts_array[$i],'trim_arr');	
		}
		
		/* load data from file into db */
		foreach ($parts_array as $mat) {
			$myname = $mat[$colNames['name']];
			$desc =$mat[$colNames['description']];	
			// Step 1: See if is an alias ;
			/* bmagee - check for matproc alias with this version */
			//$row = $db->get_name_matproc_alias($myname);
			$row = $db->get_name_matproc_alias_version($process, $version);
			if ($row) { // myname IS an alias, asign it to real name
				$mpid = $row['matProcID'];
				$realname= $row['name'];
			} else {
			  /* bmagee - check for matproc name with this version */
				//$row = $db->get_matproc_by_name($myname);
			  $row = $db->get_matproc_by_name_version($process, $version);
				$mpid = $row['matProcID'];
				$realname = $myname; 
			}
			
			if ($mpid) {
				$db->update_matproc_desc($mpid,$desc);
				echos('Updated '.$realname . ' to description: '.$desc);
			} else {
			echos('error finding matproc '.$myname.' for desc');
				sendError('error finding matproc '.$myname.' for desc');
			}
		}
	}
	
	if (empty($version) || empty($revision)) return;
	
	/* update the datasetFiles table */
	$db->add_datasetFiles($version, $revision, 'matProcDesc', $fileshortname);
	
	return $output;
}
?>