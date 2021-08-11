<?php

/* Colum headers: 'material', 'category', 'parent' ('category' & 'parent' seem to be equivalent)
   Tables updated: SM_LCA_MPCLinks
   Assigns the material in each row to its category
   
   Material name and category are added to temp table, and then load_matproc inserts into SM_LCA_MatProcs
*/

function sustainable_minds_dataload_load_categorization($fileArr, $dbname_in='sm_test', $noprint_in=0) {
	global $dbname, $errorTag, $errorFile, $errorTag, $filename, $noprint, $output;
	$dbname = $dbname_in;
	$output = '';
	include_once('common.php');
	$errorTag = 'load cat>mat';
	//$fileArr =array('data/parcatmat.txt');
	$db = new sbom_db();
	
	//set up db
	foreach($fileArr as $filename) {
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
		
		$colNames = explode("\t", $data_array[2]);
		array_walk($colNames, 'trim_arr_low');
		$colNames = array_flip($colNames);

		// load data from file into array, starting after the header lines
		for ($i = 3; $i < count($data_array); $i++) {
			$material_array[$i] = explode("\t",$data_array[$i]);
			array_walk($material_array[$i],'trim_arr');
		}
		
		$childParent = array();
		
		// load data from file into db
		foreach ($material_array as $lin) {
			$material = $lin[$colNames['material']];
			$category = $lin[$colNames['category']];
			$categoryparent = $lin[$colNames['parent']];
			$categoryID = '0';
			if (empty($category) && !empty($categoryparent)) {
				$category = $categoryparent; // parent can be in either column if one is empty
				$categoryparent = ''; 
			}
			
			if (!empty($category) ) {
				// bmagee - check for matproc category with this version 
				$categoryID = $db->get_matproc_category_by_name_version($category, $categoryparent, $version)	;
				if ($categoryID > 0) {
					echos('Category found for ' . $material  . ': '. $category . ' with ID '. $categoryID);
				} else {
					sendError("Category not pre-entered\t$category");
					echos("Error: material " . $material . " has a category, ".$category .", that isn't inserted yet. Entering category under root for now.");
					$categoryID = $db->add_matproc_category($category);
					// bmagee - add record for dataset version
					$db->add_dataset_version($version, $revision, 'matProcCategory', $categoryID);
				}
			} else {
				sendError("No category for\t$material");
				echos("Missing category for " . $material . ".");
				continue;
			}
			
			if (empty($material)) {
			 	sendError("No material for\t$category");
			 	echos('Missing material but category listed: ' . $category );
			 	continue;
			}

			// bmagee - check for matproc alias with this version
			if ($row = $db->get_name_matproc_alias_version($material, $version)) {
				echos("$material is an alias, real name is: " . $row['name']);
				$material = $row['name'];
			}
			
			// put material-category info in temp table to be retrieved in next file load step (matproc)
			// see if category is in temp table
			if ($row = $db->get_matproccategory_temp_version($categoryID, $material, $version)) {
				$noprintfinal = true;
	 		} else {
				// delete if category information is in database already, so new information will overide
				//$db->delete_matProcCategory_temp_by_name($material);  //if we are here, doesn't that mean the info is not in the table?
				//add category to temp table
				$matID = $db->add_matproc_category_temp($categoryID, $material);
				// bmagee - add record for dataset version
				// version, revision, record type, record id
				$db->add_dataset_version($version, $revision, 'matProcCategoryTemp', $matID);
				echos('Temporary infomation for '.$material .' inserted and given the id of ' . $matID);
			}
			
			//$row = $db->get_matproc_by_name($material);
			$row = $db->get_matproc_by_name_version($material, $version);
			if ($row) {
			  //replace matproc's category with new one
				$db->delete_mpclink_by_mp($row['matProcID']);
				$did =$db->add_mpc_to_matproc($row['matProcID'], $categoryID);
				if (!$did) {
					echos("Error updating matProc link for $myname.<br/>");
					sendError("Error updating link.\t$myname\t$mpcid");
				}
				//i don't think these need versions
				//$db->add_dataset_version($version, $revision, 'MPCLink', $did);
				//$db->update_dataset_version_recordID($version, $revision, 'MPCLink', $did);
			}
			
			//echos('<br />');
		}
	}

	if (empty($version) || empty($revision)) {
    return;
  }
	
	// update the datasetInfo and datasetFiles tables
	$db->add_datasetFiles($version, $revision, 'materials', $fileshortname);

  return $output;
}
?>