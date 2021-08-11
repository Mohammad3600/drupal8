<?php
/*
File Format: name	alias
procedure:
 for each alias
	 if name in SM_LCA_MatProcs is an alias  STEP 1
		 change SM_LCA_MatProcs name to real name 
	 if name is not in SM_LCA_MatProcs   STEP 2
		 throw error, skip
	 else use ID 
	 if name,alias exists STEP 3
		Skip
	INSERT
*/
function sustainable_minds_dataload_load_matproc_alias($fileArr, $dbname_in='sm_test', $noprint_in=0) {
	global $dbname, $errorTag, $errorFile, $errorTag, $filename, $noprint, $output, $version, $revision;
	$error_return = FALSE;
	$dbname = $dbname_in;
	include_once('common.php');
	$output='';
	$errorTag = 'load mp alias';
	$db = new sbom_db();

	foreach ($fileArr as $filename) {
		$data_array = file($filename, FILE_IGNORE_NEW_LINES );
		
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
		
		$colNames =  explode("\t",$data_array[2]);
		array_walk($colNames,'trim_arr_low');
		$colNames = array_flip($colNames);
		
		// load data from file into array)
		for ($i = 3; $i < count($data_array); $i++ ) {
			$matproc_array[$i] = explode("\t",$data_array[$i]);
			array_walk($matproc_array[$i],'trim_arr');
		}
	
		foreach ($matproc_array as $row) {
			$alias = $row[$colNames['alias']];
			$name = $row[$colNames['sm_name']];
			
			if (empty($alias)) {
				echos("Name $name with empty alias.");
				sendError("Name w/o alias\t$name");
				continue;
			}	elseif (empty($name)) {
				echos("Alias $alias with empty name.");
				sendError("alias w/o name\t$alias");
				continue;
			} elseif ($alias == $name) {
				echos("$alias and $name are the same. Skipping ");
				continue;
			}

      // step 1, see if matProc name needs to be changed 
			/* bmagee - check for matproc with this version */
			//$row = $db->get_matproc_by_name($alias);
			$row = $db->get_matproc_by_name_version($alias, $version);
			if ($row) {
				$check = $db->update_matproc_name($row['matProcID'], $name);
				if ($check) {
					echos("Updated a matproc name with $name from $alias.");
					//delete the temp data for this material
			    //$db->delete_matproc_category_by_name_temp_version($name, $version); //don't delete until rerun of load_matproc
				} else {
					sendError("DB error, c alias>name\t$name\t$alias");
					echos("Error updating a matproc name with $name from $alias. Skipping");
					continue;
				}
			}
		
			// step 2 if name is not in SM_LCA_MatProcs
			/* bmagee - check for matproc with this version */
			//$row = $db->get_matproc_by_name($name);
			$row = $db->get_matproc_by_name_version($name, $version);
			if (!$row) {
				sendError("Matproc Missing \t$name");
				echos("Error: no matproc $name (alias:$alias). Skipping");
				continue;
			}
			// step 2.5 fix case.
			if ($name != $row['name']) {
				$check = $db->update_matproc_name($row['matProcID'], $name);
				if ($check) {
				  echos("Updated capitlization of matproc name with $name from ".$row['name'].".");
				}
			}
			
			// step 3 if name, alias exists skip
			/* bmagee - check for matproc with this version */
			//$row=$db->get_matproc_id_alias($matprocid,$alias);
			$matprocid = $row['matProcID'];
			$row = $db->get_matprocid_alias_version($matprocid, $alias, $version);
			if ($row) {
				echos("Name $name and alias $alias already exist, skipping.");
				continue;
			}
			
			$aliasID = $db->add_matproc_alias($matprocid, $alias);
			if ($aliasID) {
				echos("Name $name and alias $alias inserted.");
				/* bmagee - add record for dataset version */
				$db->add_dataset_version($version, $revision, 'matProcAlias', $aliasID);
			}	else {
				sendError("DB error: i alias>name\t$name\t$alias");
				echos("Error: Name $name and alias $alias NOT inserted.");
			}
		}
	}
	
	if (empty($version) || empty($revision)) {
	 return;
  }
	
	/* update the datasetFiles table */
	$db->add_datasetFiles($version, $revision, 'matProcAlias', $fileshortname);

  return $output;
}
?>