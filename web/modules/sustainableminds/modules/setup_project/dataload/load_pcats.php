<?php

	$errorFile = 'load_errors.txt';
	$fileNum = $_GET['fileNum'];
	if (empty($fileNum)) $fileNum = 0;
	//setup file name, defaults
	//$fileArr =array('Test Data Set 2008-10-08 Folder/Preprocessed/Okala_Materials_Categorization_v1.1 (2008-10-02).txt');
	$fileArr =array('productCategory.txt');
	$filename = $fileArr[$fileNum];
	//setup db
	if (!isset($dbname)) $dbname = $_GET['dbname'] ;
	if (!isset($dbname))$dbname = 'sm_staging';
	
	$link = mysql_connect('localhost', 'root', 'planet2k', true, 131074);
	mysql_select_db($dbname, $link);
	
	//map columns to impact ids.  column zero contains matprocname
	
	ini_set('auto_detect_line_endings', true);
	$data_array = file($filename, FILE_IGNORE_NEW_LINES );
	
	/*****************************************************/
	/* get the dataset version, revision and description */
	/*****************************************************/
	$version = get_version($filename, $data_array[0]);
	if (empty($version)) break;
	$revision = get_revision($filename, $data_array[1]);
	if (empty($revision)) break;
	$fileshortname = basename($filename);

	$colNames =  explode("\t",$data_array[2]);
	array_walk($colNames,'trim_arr4');
	$colNames = array_change_key_case(array_flip($colNames));
	
	/* load data from file into array) */
	for($i = 3; $i < count($data_array); $i++ ) {
		$material_array[$i] = explode("\t",$data_array[$i]);	
		array_walk($material_array[$i],'trim_arr');
	}
	
	$childParent = array(); 
	
	/* load data from file into db */
	foreach ($material_array as $row){
		$category = $row[$colNames['product category']];
		$desc = $row[$colNames['description']];
		$query = 'CALL SM_SBOM_Add_PCategory("'. $category.'","'.$desc.'",1)';
		$matID = mysql_result(mysql_query($query),0);
		echo $category .' inserted and given the id of ' . $matID . '<br/><br/>';
		mysql_close();
		$link = mysql_connect('localhost', 'root', 'planet2k', true, 131074);
		mysql_select_db($dbname, $link);
	}

?>