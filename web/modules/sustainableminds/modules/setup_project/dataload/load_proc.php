<?php

/* Column headers: 'material', 'process'
   Tables updated: SM_LCA_MatProcLinks
   Assigns the process in each row to its material
*/

function sustainable_minds_dataload_load_process($fileArr, $dbname_in='sm_test', $noprint_in=0) {
	global $dbname, $errorTag, $errorFile, $errorTag, $filename, $noprint, $output, $version, $revision;
	$error_return = FALSE;
	$dbname = $dbname_in;
	$output = '';
	include_once('common.php');
	$errorTag = 'load mat>proc';
	$db = new sbom_db();
	
	foreach ($fileArr as $filename) {
		$data_array = file($filename, FILE_IGNORE_NEW_LINES);
				
		// get the dataset version, revision and description
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

    $colNames = explode("\t",$data_array[2]);
		array_walk($colNames,'trim_arr_low');
		$colNames = array_flip($colNames);
		
		// load data from file into array
		for ($i = 3; $i < count($data_array); $i++ ) {
			$matproc_array[$i] = explode("\t",$data_array[$i]);
			array_walk($matproc_array[$i],'trim_arr_low');
		}
		
		// load data from file into db
		foreach ($matproc_array as $proc) {
			echos('');
			$process = $proc[$colNames['process']];
			$material = $proc[$colNames['material']];
			// Assume that MatProc is now 'real name' based on alias being run on them BEFORE calling add process
			/* bmagee - check for matproc alias with this version */
			//$row = $db->get_name_matproc_alias($process);
			$row = $db->get_name_matproc_alias_version($process, $version);
			if ($row) { // incoming process name IS an alias, asign it to real name
				echos($process . ' was an alias, using ' . $row['name']);
				$process = $row['name'];
			}
			
			/* bmagee - check for matproc alias with this version */
			//$row = $db->get_name_matproc_alias($material);
			$row = $db->get_name_matproc_alias_version($material, $version);
			if ($row) { // incoming material name IS an alias, asign it to real name
				echos($material . ' was an alias, using ' . $row['name'] );
				$material = $row['name'];
			}
			
			/* bmagee - check for matproc by process with this version */
			//$row = $db->get_matproc_by_name($process);
			$row = $db->get_matproc_by_name_version($process, $version);
			if (!$row) {
				sendError("Process missing\t$process \t $material");
				echos('Unable to get process ID for ' .$process .'. Skipping.');
				continue;
			}
			
			$processID = $row['matProcID'];
			echos("Retrieved process id $processID for $process.");
			
			/* bmagee - check for matproc by material with this version */
			//$row = $db->get_matproc_by_name($material);
			$row = $db->get_matproc_by_name_version($material, $version);
			if (!$row) {
				sendError("Material missing\t$material\t$process");
				echos('Unable to get material ID for ' .$material .'. Skipping');
				continue;
			}
			
			$materialID = $row['matProcID'];
			echos("Retrived material id of $materialID for $material.");
			
			/* bmagee - check for matproc by material and process with this version */
			if ($db->get_matproc_link_by_ids($materialID, $processID)) {
			//if ($db->get_matproc_link_by_ids_version($materialID, $processID, $version)) { //version info is superfluous for MatProcLinks
				echos( "$material and $process link has already been inserted, skipping.");
				continue;
			} else {
				echos( "$material and $process link was not there, inserting.");
		  }
			
			$row = $db->add_matproc_link($materialID, $processID);
			
			if (!$row) {
				echos( "Error inserting data");
			} else {
				/* bmagee - add record for dataset version */
				$lastid = $row['matProcLinkID'];
				//$db->add_dataset_version($version, $revision, 'matProcLink', $lastid); //version info is superfluous for MatProcLinks
			  echos( "Process link $lastid between $process and $material successfuly added.");
			}
		}
	}
	
	if (empty($version) || empty($revision)) {
    return;
  }
	
	// update the datasetFiles table
	$db->add_datasetFiles($version, $revision, 'procmat', $fileshortname);

  return $output;
}
?>