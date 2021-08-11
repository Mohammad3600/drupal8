<?php

/* Assign subcategories to categories (as seen in the category tree: the leftmost window in the material/process selector)
  This data is in SM_LCA_MatProcCategories
  The headers in the file are 'Parent' and 'Category' */

function sustainable_minds_dataload_load_cats($fileArr, $dbname_in='sm_test', $noprint_in=0) {
	global $dbname, $errorTag, $errorFile, $errorTag, $filename, $noprint, $output, $version, $revision;
	$error_return = FALSE;
	$dbname = $dbname_in;
	$errorTag = 'load categories';
	include_once('common.php');
	$output = '';
	drupal_set_message("Loading Categories...");
	$db = new sbom_db();

	foreach ($fileArr as $filename) {
		$data_array = file($filename, FILE_IGNORE_NEW_LINES );
		// get the dataset version, revision and description
		$version = get_version($filename, $data_array[0]);
		if (empty($version)) break;
		$revision = get_revision($filename, $data_array[1]);
		if (empty($revision)) break;
		
		echos("Version: ".$version." Revision: ".$revision);
		
    $row = $db->find_datasetInfo($version, $revision);
      
    if (!$row) {
      sendError('Version or revision not found');
      echos('Version and revision headers in file must match those values of an existing dataset version. <a href="/add_dataset/">Create new verion here.</a> Skipping file.');
      break;
    }

		$fileshortname = basename($filename);

		// load column headings into array
		$colNames =  explode("\t",$data_array[2]);
		array_walk($colNames,'trim_arr_low');
		$colNames = array_flip($colNames);

		// load data from file into array
		for($i = 3; $i < count($data_array); $i++ ) {
			$material_array[$i] = explode("\t",$data_array[$i]);
			array_walk($material_array[$i],'trim_arr');
		}

		$childParent = array();

		// load data from file into db
		foreach ($material_array as $row) {
			$category = $row[$colNames['category']];
			$parent = $row[$colNames['parent']];
			$parentID = '0';
			
		  //see if parent already in SM_LCA_MatProcCategories, add if not
			if (!empty($parent)) {
				/* bmagee - check for category with this version */
				//$parentID = $db->get_matproc_category_by_name($parent);
				$parentID = $db->get_matproc_category_by_name_version($parent, '', $version);
				if ($parentID > 0) {
				
				} else {
					echos("Category " . $category . " has a parent, ".$parent .", that isn't inserted yet. Entering parent.");
					$parentID = $db->add_matproc_category($parent);
					/* bmagee - add record for dataset version */
					$db->add_dataset_version($version, $revision, 'matProcCategory', $parentID);
		      echos($parent .' inserted and given the id of ' . $parentID);
					//continue;
				}
			} else {
				//sendError("Missing parent\t$category");
				//echos("Missing parent for " . $category . ".<br/>");
				continue;
			}
			
			if (empty($category)) {
			 continue;
		  }
			
			/* bmagee - check for category with this version */
			//$row = $db->get_matproc_category_by_name_parentID($category, $parentID);
			$row = $db->get_matproc_category_by_name_parentID_version($category, $parentID, $version);
			if ($row) {
				echos("Parent $parent and $category already connected, skipping.");
				continue;
			}

      //add category
			$categoryID = $db->add_matproc_category($category, $parentID);
			/* bmagee - add record for dataset version */
			$db->add_dataset_version($version, $revision, 'matProcCategory', $categoryID);
			echos($category .' inserted and given the id of ' . $categoryID);
		}
	}
	
	if (empty($version) || empty($revision)) {
    return;
  }
	
	// update the datasetFiles table
	$db->add_datasetFiles($version, $revision, 'categories', $fileshortname);

	return $output ;
}
?>
