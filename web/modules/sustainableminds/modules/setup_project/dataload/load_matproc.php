<?php

/*	File headers (case insensitive): 'name' or 'impact category', 'material unit', 'is end of life', 'is transportation', 'is process', 'description', <impacts from SM_LCA_Impacts>
	Tables updated: SM_LCA_MatProcs, SM_LCA_Units, SM_LCA_MatProcImpacts	*/

function sustainable_minds_dataload_load_matproc($fileArr, $dbname_in='sm_test', $noprint_in=0) {
	global $dbname, $errorTag, $errorFile, $errorTag, $filename, $noprint, $output, $version, $revision;
	$error_return = FALSE;
	//$last_filename = '';
	$output = '';
	$dbname = $dbname_in;
	include_once('common.php');
	$errorTag = 'lod matproc info';
	$phaseid = 1;
	$db = new sbom_db();
	
	$counter = 0;
	foreach ($fileArr as $filename) {
		//$last_filename = trim(strtolower($filename));

		echos("Uploading " .$filename . "");
	
		//$data_array = file($filename, FILE_IGNORE_NEW_LINES);
		$file = fopen($filename, "r");
		while(!feof($file)) {
			$data_array[] = fgetcsv($file, 0, "	");
		}
		fclose($file);
		
		// get the dataset version, revision and description
		//$version = get_version($filename, $data_array[0]);
		$version = $data_array[0][1];
		if (empty($version)) break;
		//$revision = get_revision($filename, $data_array[1]);
		$revision = $data_array[1][1];
		if (empty($revision)) break;
    $row = $db->find_datasetInfo($version, $revision);
    if (!$row) {
      sendError('Version or revision not found');
      echos('Version and revision headers in file must match those values of an existing dataset version. <a href="/add_dataset/">Create new version here.</a> Skipping file.');
      continue;
    }

		$fileshortname = basename($filename);

		// map columns to impact ids.  column zero contains matprocname - row[2]
		//$colNames =preg_split("/[ ]?[\t][ ]?[\n]?/", $data_array[2]);
		$colNames = $data_array[2];
		array_walk($colNames,'trim_arr_low');
		if (count($colNames) > count(array_unique($colNames))) {
			sendError('File with same named columns\t');
		}
		//check if impacts in DB exist in column names
		$colNames = array_flip($colNames);
//		$t_cols = $db->list_impacts();	//all impacts in DB (no version for these??)
		$t_cols = $db->list_impacts_version($version);	//all impacts in DB (no version for these??)
		foreach ($t_cols as $key=>$t_imp) {
			$a = $colNames[strtolower($t_imp['name'])];
			if (isset($a) && !empty($a)) {
				$cols[$a] = $t_imp['impactID'];
			} else {
				echos($t_imp['name'] . ' exists in database but not in this file.');
			}
		}
		
		$matunitcol = $colNames["material unit"];
		if (!isset($matunitcol)) {
			sendError("unit column missing\t$filename");
			echos("Error: material unit column is missing");
		}
		
		// load data from file into array() - start on row[3]
		$parts_array = array();
		for ($i = 3; $i < count($data_array); $i++) {
			//$parts_array[$i] = explode("\t", $data_array[$i]);
			$parts_array[$i] = $data_array[$i];
			array_walk($parts_array[$i], 'trim_arr');
		}
		
		$mpcidProc = $db->get_matproc_category_by_name('Process');
		$mpcidNone = $db->get_matproc_category_by_name('None');

		// load data from file into db
		foreach ($parts_array as $mat) {
			//load matproc
			//get matproc name
			if (isset($colNames['name'])) {
				$namecol = $colNames['name']; 
			} elseif (isset($colNames['impact category'])) {
				$namecol = $colNames['impact category'];
			} else {
				sendError("name column missing\t$filename");
				echos("Sorry, but the name column is missing or misnamed.<br/>Skipping file.");
				break;
			}
			
			$myname = $mat[$namecol];
			
			//try to normalize units
			$matUnit = $mat[$matunitcol];
			$divisor = 1;
			
			if (!$matUnit) {
				sendError("Unit information missing\t$myname");
				echos("Unit information missing for $myname. Skipping. <br/>");
				continue;
			}
			
			switch ($matUnit) {
				case 'ton-mile':
					$divisor = 2000;
					$matUnit = 'miles';
					break;
				case 'tonn':
				case 'ton':
					$divisor = 2000;
					$matUnit = 'lbs';
					break;
				case 'pound':
					$divisor = 1;
					$matUnit='lbs';
					break;
				case 'lb':
					$divisor = 1;
					$matUnit='lbs';
					break;
				case 'foot2':
					$divisor = 1;
					$matUnit='ft2';
					break;
				case 'foot':
					$divisor = 1;
					$matUnit='ft';
					break;
				case 'foot3':
					$divisor = 1;
					$matUnit='ft3';
					break;
				case 'min':
					$matUnit='minute';
					break;
				case 'hr':
					$matUnit='hour';
					break;
				case 'gallon':
					$matUnit='gal';
					break;
				case 'mi':
					$matUnit='miles';
					break;
			}
			
			//see if unit already entered
			$unitID = $db->get_unit_by_name($matUnit);
			if (!$unitID) {
				//$unitID = -1;
				echos("Unit ".$matUnit." does not exist.<br/>Adding it.<br/>");
				$unitID = $db->add_unit($matUnit);
			}
			
			if (strpos($mat[$colNames['is transportation']],'y') !== false) {
				$enum = 'transportation';
				//$phaseid = 3;
			} elseif (strpos($mat[$colNames['is process']],'y') !== false)
				$enum = 'process';
			else {
				$enum = 'material';
			}
			
			if (strpos($mat[$colNames['is end of life']],'y') !== false) {
				$endoflife = 1;
				$enum = 'process';
				//$phaseid = 4;
			} else {
				$endoflife = 0;
			}
			
			/*if ($endoflife && $enum != 'process') {
				sendError("Item marked as end of life is not marked as a process:\t$myname");
				echos("Item marked as end of life is not marked as a process: $myname. Skipping. <br/>");
				continue;
			}*/
			
			$desc = $mat[$colNames['description']];

			/* GET matproc categoryID. Materials have been added as temporary categories. 
			   Get their parent ID and delete the entry in SM_LCA_MatProcCategories */
				
			// Get Alias information. 
			
			// Step 1: See if is an alias
			/* bmagee - check for alias with this version */
			// $row = $db->get_name_matproc_alias($myname);
			$row = $db->get_name_matproc_alias_version($myname, $version);
drupal_set_message('name: '.$myname.'<br />version: '.$version);
			if ($row) { // myname IS an alias, assign it to real name
				$myname = $row['name'];
			}
			
			// Step 2: get all possible names
			// bmagee - check for alias with this version
			// $pNames = $db->list_matproc_alias($myname);
			$pNames = $db->list_matproc_alias_version($myname, $version);
drupal_set_message('pNames: '.$pNames.'<br />version: '.$version);
      //may be faster if this is inserted at beginning of array
			$pNames[] = $myname; // single array with all possible values, including real name
		
			if ($enum == 'process') {
				$mpcid = $mpcidProc;
			} else {
				$mpcid = 0;
				//look for the matproc category id
				foreach ($pNames as $pname) {
					// bmagee - check for temp category with this version
					// $row = $db->get_matproc_category_by_name_temp($pname);
					$row = $db->get_matproc_category_by_name_temp_version($pname, $version);
					if ($row) {
						$mpcid = $row['parentID'];
				  } else {
				    //get mpcid from SM_LCA_MPCLinks (if we don't do this, Category Information Missing error will appear)
				    $mpcid = $db->get_MPC_for_matproc_version($pname, $version);
				  }
				  
					if ($mpcid > 0) {
				    $db->delete_matproc_category_by_name_temp_version($pname, $version);
					  break;
				  }
				}
				
				if ($mpcid < 1 ) {
					sendError("Category Information Missing\t$myname");
					echos("<table width='100%'><tr><td>Error, problem finding category information for this material, $myname. 
								Inserting under None. <br/>Please fix any typos (use alias/load alias).</td>
								<td><a href='/admin/settings/lca_admin/mat_proc/add/1?eol=0'>Add MatProc</a></td>
								<td><a href='admin/settings/lca_admin/category/add/1'>Add Category</a></td>
								<td><a href='/admin/settings/lca_admin/category/1'>Edit Manufacturing</a></td>
								</tr></table>");
					$mpcid = $mpcidNone;
					//continue;
				}
			}
			
			//look for matching matproc name
			foreach ($pNames as $pname) {
				// bmagee - check for matproc with this version
				//$row = $db->get_matproc_by_name($pname);
				$row = $db->get_matproc_by_name_version($pname, $version);
				if ($row) {
				  break;
				}
			}

			// matproc already exists, so just update
			if ($row) {
				echos("$myname was already in database; updating with any changes");
				// UPDATE row in case information has changed.
				$did = $db->update_matproc($row['matProcID'], $myname, $desc, $unitID, $enum, $endoflife);
				if (!$did) {
					echos("Error updating matProc info for $myname.<br/>");
					sendError("Error updating.\t$myname");
				}
				
				// Update category information in case has changed. SP doesn't change insert if already there.
				// delete MPCLinks;
				$db->delete_mpclink_by_mp($row['matProcID']);	//assumes old links should be cleared and all desired ones are listed in dataload files
				if ($mpcid > 0 && $mpcidNone != $mpcid) {
					$did = $db->add_mpc_to_matproc($row['matProcID'], $mpcid);
					if (!$did) {
						echos("Error updating matProc link for $myname.<br/>");
						sendError("Error updating link.\t$myname\t$mpcid");
					}
					// bmagee - add record for dataset version
					//$db->add_dataset_version($version, $revision, 'MPCLink', $did); //natalie - don't think version needed for MPCLinks
				}
				
				$up = true; // set to update mode for impact insert
				$matProcID = $row['matProcID'];
			}
			//matproc doesn't exist yet; add it
			else {
				echos('Adding '.$myname.' using matproc category id '. $mpcid);
				$matProcID = $db->add_matproc($myname, $desc, $unitID, $mpcid, $enum, $endoflife);
				if (!$matProcID) {
					sendError("Error inserting.\t$myname");
					echos("Error inserting, skipping.");
					continue;
				}
				// bmagee - add record for dataset version
				$db->add_dataset_version($version, $revision, 'matProc', $matProcID);
				echos("Inserted $myname <br/>");
			}

			//set impacts
			foreach ($cols as $key => $col) {
				if (!is_numeric($mat[$key])) {
					sendError("Impact data not numeric\t$myname");
					continue;
				}
				
				$val = $mat[$key];
				if ($divisor > 1) {
					$val = $val / $divisor;
				}

				$row = '';
				if ($up) {
					// bmagee - check for matproc with this version
					//$row = $db->get_matproc_impact_by_mp_i($matProcID, $col);
					$row = $db->get_matproc_impact_by_mp_i_version($matProcID, $col, $version);
				}

				if (!$up) {  // || empty($row)) { bmagee- removed this because it caused impacts to be added everytime!!!
					$matProcImpactID = $db->add_matproc_impact($matProcID, $col, $val, $phaseid);
					// bmagee - add record for dataset version
					$db->add_dataset_version($version, $revision, 'matProcImpact', $matProcImpactID);
				} elseif ($row && $val != $row['impactFactor']) { //can this be moved to inside if ($up) ?
					$db->update_matproc_impact($row['matProcImpactID'], $matProcID, $col, $val, $phaseid);
				}
			}
			
			$up = false;
		}
	}
	
	if (empty($version) || empty($revision)) {
    return;
  }
	
	// update the datasetFiles table
	$db->add_datasetFiles($version, $revision, 'matProc', $fileshortname);

	return $output;
}
?>